<?PHP
// -- Global Functions
//------------------------------


/**
 * Finalize Process - Change status to 0 for current process and set status to 1 to next process
 * 
 * @param object $mysqli - instance of db connection
 * @param int $curr_process_id
 * @param int $next_process_id
 * @return void
 */
function cug_rms_finalize_process($mysqli, $curr_process_id, $next_process_id) {
    //update current process status to 0
    cug_rms_edit_process_status($mysqli, $curr_process_id, $new_status=0); //Idle
    
    //update next process status to 1
    cug_rms_edit_process_status($mysqli, $next_process_id, $new_status=1); //Ready to start
}



/**
 * Finalize Cache Table Update Process
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param string $temp_table_name
 * @param int $new_status
 * @param int $curr_process_id
 * @param int $next_process_id
 * @param int $rows_completed
 * @param array $time_periods
 * @param string $log_file
 * @param string $log_file_temp
 * @return void
 */
function cug_rms_finalize_cache_table_update($mysqli, $table_name, $temp_table_name, $new_status, $curr_process_id, $next_process_id, $rows_completed, $time_periods, $log_file, $log_file_temp) {
    //update temp cache table status
    cug_rms_edit_cache_table_status($mysqli, $table_name, "status_temp", $new_status); //Finished
    
    //update cache table content's start_time and end_time
    cug_rms_update_cache_table_contents_start_end_time($mysqli, $table_name, $time_periods, $rows_completed);
    
    //check if main process #2 is finished
    if(cug_rms_check_cache_tables_statuses($mysqli, "status_temp")) {
        cug_rms_finalize_process($mysqli, $curr_process_id, $next_process_id);
    }
    
    //log
    $status = ($rows_completed > 0) ? "OK" : "IDLE";
    $log_text = date("Y-m-d H:i:s")." - Table:".$temp_table_name." - Status:$status:".$rows_completed." new entries".PHP_EOL;
    cug_rms_write_log($log_file_temp, $log_text);
    cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
    $log_text = "END: ".date("Y-m-d H:i:s").PHP_EOL;
    cug_rms_write_log($log_file_temp, $log_text);
    cug_rms_copy_log_temp_file($log_file_temp, $log_file);
}


/**
 * Update start_time and end_time for cache table's content
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param array $time_periods
 * @param int $rows_completed
 * @return void
 */
function cug_rms_update_cache_table_contents_start_end_time($mysqli, $table_name, $time_periods, $rows_completed) {
    global $Tables;

    if($rows_completed > 0) {
        $query = "UPDATE {$Tables['cache_table']} SET start_time='{$time_periods['start_time']}', end_time='{$time_periods['end_time']}' WHERE table_name='$table_name'";
        $r = $mysqli->query($query);
    }
}


/**
 * Get start_time and end_time of cache table's content
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @return array
 */
function cug_rms_get_cache_table_start_end_time($mysqli, $table_name) {
    global $Tables;
    $result = array();
    $result['start_time'] = "";
    $result['end_time'] = "";
    
    $query = "SELECT start_time, end_time FROM {$Tables['cache_table']} WHERE table_name='$table_name'";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        
        $result['start_time'] = $row['start_time'];
        $result['end_time'] = $row['end_time'];
    }
    
    return $result;
}


/**
 * Check for new update for specified data table 
 * 
 * @param string $table_name
 * @return number|array
 */
function cug_rms_check_data_table_update($table_name) {
    global $mysqli_rms, $mysqli_rms_cache, $Tables;
    $result = array();
        
    $query = "SELECT id, prev_update_time FROM {$Tables['data_table']} WHERE table_name='$table_name'";
    $r1 = $mysqli_rms_cache->query($query);
    
    if($r1 && $r1->num_rows) {
        $row1 = $r1->fetch_assoc();
        $data_table_id = $row1['id'];
        $prev_update_time = $row1['prev_update_time'];
        
        //--------------------
        $query = "SELECT id, complete_time FROM {$Tables['update_status']} ";
        $query .= "WHERE table_name='$table_name' AND complete_time>$prev_update_time AND success_count>0 ";
        $query .= "ORDER BY complete_time DESC";
        $r = $mysqli_rms->query($query);
        
        if($r && $r->num_rows) {
            $row = $r->fetch_assoc();
            $update_table_id = $row['id'];
            $complete_time = $row['complete_time'];
        
            $result['update_table_id']  = $update_table_id;
            $result['complete_time']    = $complete_time;
            $result['data_table_id']    = $data_table_id;
            
            return $result;
        }
    }
    
    
    return 0;
}


/**
 * Send Notification Email
 * 
 * @param int $process_id
 * @param string $process_name
 * @return void
 */
function cug_rms_send_notification_email($process_id, $process_name) {
    global $RMS_STAT_EMAIL, $RMS_EMAIL_ADDR, $PROCESS_MAX_DURATION;

    //set emaaill adresses
    foreach($RMS_EMAIL_ADDR as $key => $email) {
        if($key == 0)
            $RMS_STAT_EMAIL->addAddress($email);
        else
            $RMS_STAT_EMAIL->addCC($email);
    }
    
    //subject
    $RMS_STAT_EMAIL->Subject = "RMS Statistics Process Failure";
    
    //body
    $body = "Process <b>#$process_id</b> ($process_name) was running more than $PROCESS_MAX_DURATION seconds and was interrupted!";
    $RMS_STAT_EMAIL->Body = $body;
    
    //send email
    $RMS_STAT_EMAIL->send();
    
    //echo PHP_EOL.$RMS_STAT_EMAIL->ErrorInfo.PHP_EOL;   
}

/**
 * Set Configuration Tables Statuses to Default Values
 * 
 * @param object $mysqli - instance of db connection
 * @return void
 */
function cug_rms_set_config_tables_statuses_to_default($mysqli) {
    global $Tables;
    
    //process statuses
    $query = "UPDATE {$Tables['process']} SET status = ";
    $query .= "CASE WHEN id=1 THEN 1 ";
    $query .= "WHEN id=2 THEN 0 ";
    $query .= "WHEN id=3 THEN 0 END";
    $mysqli->query($query);
    
    //temp cache tables statuses
    $query = "UPDATE {$Tables['cache_table']} SET status_temp=0";
    $mysqli->query($query);
}


/**
 * Interrupt Process if it is running for longer than $PROCESS_MAX_DURATION, if so update configuration tables to default values and send notification email
 * 
 * @param object $mysqli - instance of db connection
 * @return bool - (true - process was interrupted; false - process was not interrupted)
 */
function cug_rms_interrupt_long_process($mysqli) {
    global $Tables, $PROCESS_MAX_DURATION;
    $result = false;
    
    $query = "SELECT * FROM {$Tables['process']} WHERE status=2";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        
        $process_id = $row['id'];
        $process_name = $row['title'];
        $update_time = $row['update_time'];
        $curr_time = date("Y-m-d H:i:s");
        
        if( (strtotime($curr_time) - strtotime($update_time)) >= $PROCESS_MAX_DURATION ) {
            cug_rms_set_config_tables_statuses_to_default($mysqli);
            cug_rms_send_notification_email($process_id, $process_name);
            
            $result = true;
        }        
    }
    
    return $result;
}


/**
 * Lock Cache Tables, set 'status' field to 0
 *
 * @param object $mysqli - instance of db connection
 * @return void
 */
function cug_rms_lock_cache_tables($mysqli) {
    global $Tables;

    $query = "UPDATE {$Tables['cache_table']} SET status=0 WHERE enabled=1";
    $r = $mysqli->query($query);
}


/**
 * Unlock Cache Tables, set 'status' field to 1
 *
 * @param object $mysqli - instance of db connection
 * @return void
 */
function cug_rms_unlock_cache_tables($mysqli) {
    global $Tables;

    $query = "UPDATE {$Tables['cache_table']} SET status=1 WHERE enabled=1";
    $r = $mysqli->query($query);
}


/**
 * Unlock Cache Temp Tables, set 'status_temp' field to 0
 *
 * @param object $mysqli - instance of db connection
 * @return void
 */
function cug_rms_unlock_cache_temp_tables($mysqli) {
    global $Tables;

    $query = "UPDATE {$Tables['cache_table']} SET status_temp=0 WHERE enabled=1";
    $r = $mysqli->query($query);
}


/**
 * Get total number of cache tables
 * 
 * @param object $mysqli - instance of db connection
 * @return int
 */
function cug_rms_get_cache_tables_total_num($mysqli) {
    global $Tables;
    $result = 0;

    $query = "SELECT COUNT(id) AS total_num FROM {$Tables['cache_table']} WHERE enabled=1";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $result = $row['total_num'];
    }
    
    return $result;
}


/**
 * Get Cache Table's current status
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param string $field
 * @return int
 */
function cug_rms_get_cache_table_status($mysqli, $table_name, $field) {
    global $Tables;
    $result = 0;
    
    $query = "SELECT $field FROM {$Tables['cache_table']} WHERE table_name='$table_name'";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $result = $row[$field]; 
    }
    
    return $result;
}


/**
 * Get Process Status
 * 
 * @param object $mysqli - instance of db connection
 * @param int $process_id
 * @return int
 */
function cug_rms_get_process_status($mysqli, $process_id) {
    global $Tables;
    $result = 0;

    $query = "SELECT status FROM {$Tables['process']} WHERE id=$process_id";
    $r = $mysqli->query($query);

    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $result = $row['status'];
    }

    return $result;
}


/**
 * Edit Table's current status
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param string $status_field
 * @param int $new_status
 * @return void
 */
function cug_rms_edit_cache_table_status($mysqli, $table_name, $status_field, $new_status) {
    global $Tables;

    $query = "UPDATE {$Tables['cache_table']} SET $status_field=$new_status WHERE table_name='$table_name'";
    $r = $mysqli->query($query);
}


/**
 * Update Process Status
 * 
 * @param object $mysqli - instance of db connection
 * @param int $process_id
 * @param int $new_status
 * @return void
 */
function cug_rms_edit_process_status($mysqli, $process_id, $new_status) {
    global $Tables;

    $query = "UPDATE {$Tables['process']} SET status=$new_status WHERE id=$process_id";
    $r = $mysqli->query($query);
}

/**
 * Check Cache Tables Statuses
 * 
 * @param object $mysqli - instance of db connection
 * @param string $status_field
 * @return number (1 - if all tables statuses are 1, otherwise - 0) 
 */
function cug_rms_check_cache_tables_statuses($mysqli, $status_field) {
    global $Tables;
    $result = 0;
    
    $query = "SELECT COUNT(id) FROM {$Tables['cache_table']} WHERE enabled=1";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_array();
        $total_cache_tables = $row[0];
        
        $query = "SELECT COUNT(id) FROM {$Tables['cache_table']} WHERE $status_field=1 AND enabled=1";
        $r1 = $mysqli->query($query);
        
        if($r1 && $r1->num_rows) {
            $row1 = $r1->fetch_array();
            $updated_cache_tables = $row1[0];
            
            if($total_cache_tables == $updated_cache_tables)
                $result = 1;
        }
    }
    
    return $result;
}


/**
 * Create Table
 *
 * @param object $mysqli - instance of db connection
 * @param string $filter - index of the global '$RMS_QUERY_MAKE_TABLE' array
 * @param string $time_period
 * @param string $suffix - Table Suffix, Optional
 * @return boolean
 */
function cug_rms_create_table($mysqli, $filter, $time_period, $suffix="") {
    global $Tables, $RMS_QUERY_MAKE_TABLE;

    $table_index = $filter."__".$time_period;
    $query = "CREATE TABLE IF NOT EXISTS {$Tables[$table_index]}$suffix ".$RMS_QUERY_MAKE_TABLE[$filter];

    if($mysqli->query($query))
        return true;
    else
        return false;
}


/**
 * Checek if Table exists
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @return boolean
 */
function cug_rms_table_exists($mysqli, $table_name) {
    $query = "SHOW TABLES LIKE '$table_name'";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows)
        return true;
    else
        return false;
}


/**
 * Check if it is time to extract data
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param string $start_date - YYYY-MM-DD
 * @param string $time_period
 * @return boolean
 */
function cug_rms_is_time_to_extract_data($mysqli, $table_name, $start_date, $time_period) {
    global $TIME_PERIODS;
    
    switch(strtoupper($time_period)) {
        case $TIME_PERIODS[6]: //THIS_YEAR
        case $TIME_PERIODS[7]: //THIS_MONTH
            return true;
        break;    
    }
    //-------------------------------------
        
    $query = "SELECT MIN(played_date) AS min_date FROM $table_name";
    $r = $mysqli->query($query);
    
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $min_timestamp = $row['min_date'];
        $min_date = date("Y-m-d", $min_timestamp / 1000);
        
        if($min_date != $start_date)
            return true;
        else 
            return false;
    }
    else {
        return true;
    }
    
    return false;
}


/**
 * Check if it is time to update cache table
 * 
 * @param object $mysqli - instance of db connection
 * @param object $mysqli_cache - instance of db connection
 * @param string $sub_table
 * @param string $cache_table
 * @param array $time_periods
 * @return boolean
 */
function cug_rms_is_time_to_update_cache_table($mysqli, $mysqli_cache, $sub_table, $cache_table, $time_periods) {
    global $TIME_PERIODS;
    $result = true;
    
    if(!empty($time_periods['time_period']) && (strtoupper($time_periods['time_period']) == $TIME_PERIODS[6] || strtoupper($time_periods['time_period']) == $TIME_PERIODS[7])) { //THIS_YEAR or THIS_MONTH
        $result = true;
    }
    else {    
        if($mysqli_cache->table_exists($cache_table) && $mysqli_cache->get_table_num_rows($cache_table) > 0) {    
            $query = "SELECT MIN(played_date) AS min_date, MAX(played_date) AS max_date FROM $sub_table";       
            $r = $mysqli->query($query);
            
            if($r && $r->num_rows) {
                $row = $r->fetch_assoc();
                $min_timestamp = $row['min_date'];
                $max_timestamp = $row['max_date'];
                
                $subtable_min_date = date('Y-m-d', $min_timestamp / 1000);
                $subtable_max_date = date('Y-m-d', $max_timestamp / 1000);
                
                //get start_time and end_time of the cache table's content
                $arr = cug_rms_get_cache_table_start_end_time($mysqli_cache, $cache_table);
                $cache_table_start_date = date('Y-m-d', strtotime($arr['start_time']));
                $cache_table_end_date = date('Y-m-d', strtotime($arr['end_time']));
                
                if($subtable_min_date == $cache_table_start_date)
                    $result = false;
            }
            else 
                $result = false;
        }
    }
    
    return $result;
}


/**
 * Check if there is enough data to extract
 * 
 * @param object $mysqli - instance of db connection
 * @param string $table_name
 * @param int $start_timestamp
 * @param int $end_timestamp
 * @return boolean
 */
function cug_rms_is_enough_data_to_extract($mysqli, $table_name, $start_timestamp, $end_timestamp) {
    $result = false;
    $query = "SELECT MIN(played_date) AS min_date FROM $table_name";
    $r = $mysqli->query($query);

    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $min_timestamp = $row['min_date'];

        if($min_timestamp <= $start_timestamp) {
            $query = "SELECT MAX(played_date) AS max_date FROM $table_name";
            $r1 = $mysqli->query($query);
            
            if($r1 && $r1->num_rows) {
                $row1 = $r1->fetch_assoc();
                $max_timestamp = $row1['max_date'];
                
                if($max_timestamp >= $end_timestamp)
                    $result = true;
            }
        }

    }

    return $result;
}



/**
 * Get Time Periods (StartTime and EndTime)
 *
 * @param string $ACTION - Like: 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_WEEK', 'LAST_MONTH', ...
 * @return array
 */
function cug_rms_get_time_periods($ACTION) {
    $result = array();


    switch(strtoupper($ACTION)) {
        //---------------------
        case 'LAST_7_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-7 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        break;

        //---------------------
        case 'LAST_30_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-30 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));//-1
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        break;
        
        //---------------------
        case 'LAST_365_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-365 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
        
            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        break;        
               
        //---------------------
        case 'LAST_YEAR':
        //---------------------
            $result['start_date'] = date("Y-01-01", strtotime('last year'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
        
            $result['end_date'] = date("Y-12-31", strtotime('last year'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        break;
                
        //---------------------
        case 'LAST_MONTH':
        //---------------------
            $result['start_date'] = date("Y-m-01", strtotime('last month'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
        
            $result['end_date'] = date("Y-m-t", strtotime('last month'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
       break; 
             
       //---------------------
       case 'LAST_WEEK':
       //---------------------
           /*
           //PHP v5.5.9
           //check this, because strtotime('last week monday') returns Monday of the current week (not of the last week) if today is Sunday
           if(strtoupper(date('l', strtotime('now'))) == "SUNDAY") {
               $result['start_date'] = date("Y-m-d", strtotime('last week monday -7 day'));
               $result['start_time'] = $result['start_date']." 00:00:00";
               
               $result['end_date'] = date("Y-m-d", strtotime('last week sunday -7 day'));
               $result['end_time'] = $result['end_date']." 23:59:59";
           }
           else {
               $result['start_date'] = date("Y-m-d", strtotime('last week monday'));
               $result['start_time'] = $result['start_date']." 00:00:00";
               
               $result['end_date'] = date("Y-m-d", strtotime('last week sunday'));
               $result['end_time'] = $result['end_date']." 23:59:59";
           }
           */
           
           //PHP v5.6.30
           $result['start_date'] = date("Y-m-d", strtotime('last week monday'));
           $result['start_time'] = $result['start_date']." 00:00:00";
            
           $result['end_date'] = date("Y-m-d", strtotime('last week sunday'));
           $result['end_time'] = $result['end_date']." 23:59:59";
           //------------------------
           
           $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
           $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
       break;       
       
       //---------------------
       case 'THIS_YEAR':
       //---------------------
           $result['start_date'] = date("Y-01-01", strtotime('now'));
           $result['start_time'] = $result['start_date']." 00:00:00";
           $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
       
           $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
           $result['end_time'] = $result['end_date']." 23:59:59";
           $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
       break; 
       
       
       //---------------------
       case 'THIS_MONTH':
       //---------------------
           /*
            * this code is disabled because at first day of the current month for 'this_month' period we will have same data as for previous month, so better don't have any data (for 'this_month' period) at first day of the current month
           if(date("d", strtotime('this month')) == '01') { //if first day of the current month 
               $result['start_date'] = date("Y-m-01", strtotime('last month'));
           }
           else {
               $result['start_date'] = date("Y-m-01", strtotime('now'));
           }
           */           
           $result['start_date'] = date("Y-m-01", strtotime('now'));
           $result['start_time'] = $result['start_date']." 00:00:00";
           $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
           $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
           $result['end_time'] = $result['end_date']." 23:59:59";
           $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        break;       
    }

    return $result;
}



/**
 * Get Log File Name
 * 
 * @param string $ACTION
 * @param string $temp_log_file - Optional
 * @param string $FILTER - Optional
 * @param string $TIME_PERIOD - Optional
 * @return string
 */
function cug_rms_get_log_file_name($ACTION, $temp_log_file=0, $FILTER="", $TIME_PERIOD="") {
    global $LOG_FILE_DIR;
    $result = "";
    
    $log_file_last_part = ($temp_log_file > 0) ? "_".uniqid().".log" : ".log";
    
    //log folder
    $log_folder = $LOG_FILE_DIR."/".$ACTION;    
    if(!file_exists($log_folder))
        mkdir($log_folder);
    
    //generate log file name    
    if(!$FILTER)
        $log_file_name = $ACTION.$log_file_last_part;
    else {
        if($TIME_PERIOD)
            $log_file_name = $FILTER."__".$TIME_PERIOD.$log_file_last_part;
        else
            $log_file_name = $FILTER.$log_file_last_part;
    }
    
    //log file
    $log_file = $log_folder."/".$log_file_name;
    
    if(!file_exists($log_file)) {
        if($f = fopen($log_file, 'w')) {
            fclose($f);
            $result = $log_file;
        }
    }
    else
        $result = $log_file;
    
    return $result;    
}


/**
 * Write Log
 * 
 * @param string $log_file
 * @param string $log_text
 * @param string $mode - Optional
 * @return void
 */
function cug_rms_write_log($log_file, $log_text, $mode="a") {
    if($log_file) {
        if($f = fopen($log_file, $mode)) {
            fwrite($f, $log_text);
            fclose($f);
        }
    }
}


/**
 * Copy Temp Log File's content into Log File
 * 
 * @param string $log_file_temp
 * @param string $log_file
 * @return void
 */
function cug_rms_copy_log_temp_file($log_file_temp, $log_file) {
    if($log_file_temp && $log_file) {
        $text = file_get_contents($log_file_temp);
        
        if($text !== false) {
            if($f = fopen($log_file, "a")) {
                fwrite($f, $text);
                fclose($f);
                
                unlink($log_file_temp);
            }
        }
    }
}


/**
 * Split Cache Table Name
 * 
 * @param string $table_name
 * @param string $delimiter (Optional, default: '__')
 * @return array
 */
function cug_rms_split_cache_table_name($table_name, $delimiter="__") {
    $result = array();
    
    $arr = explode($delimiter, $table_name);
    $result['filter'] = !empty($arr[0]) ? $arr[0] : "";
    $result['time_period'] = !empty($arr[1]) ? $arr[1] : "";
    
    return $result;
}



/**
 * Archive Cache Table
 * 
 * @param string $filter
 * @param string $time_period
 * @param int $temp_table_rows_count
 * @param string $delimiter (Optional, default: '__')
 * @return void
 */
function cug_rms_archive_cache_table($filter, $time_period, $temp_table_rows_count, $delimiter="__") {
    global $mysqli_rms_cache, $TIME_PERIODS, $temp_table_suffix;
    
    $cache_temp_table = $filter.$delimiter.$time_period.$temp_table_suffix;
    
    if($temp_table_rows_count > 0) {
        switch(strtoupper($time_period)) {
            // LAST_MONTH
            case $TIME_PERIODS[4]:
            //----------------------------
                $last_month = date("m", strtotime('last month'));                
                $archive_cache_table = $filter.$delimiter."month_".$last_month;
                
                //delete archive cache table if exists
                $mysqli_rms_cache->drop_table($archive_cache_table);
                
                //copy table
                $mysqli_rms_cache->copy_table($cache_temp_table, $archive_cache_table);
                          
            break;
            
            
            // LAST_YEAR
            case $TIME_PERIODS[3]:
            //----------------------------
                $last_year = date("Y", strtotime('last year'));                                    
                $archive_cache_table = $filter.$delimiter."year_".$last_year;
                
                //delete archive cache table if exists
                $mysqli_rms_cache->drop_table($archive_cache_table);
                
                //copy table
                $mysqli_rms_cache->copy_table($cache_temp_table, $archive_cache_table);
                
            break;
        }
    }
}


/**
 * Archive Year (copy all cache tables for last year to archive db)
 * 
 * @return number
 */
function cug_rms_archive_year() {
    global $mysqli_rms_cache, $mysqli_rms_cache_global, $Tables, $DB, $ERRORS;
    $result = $ERRORS['NO_TIME_YET_TO_ARCHIVE_YEAR'];
    
    $last_year = date("Y", strtotime('last year'));
    
    //if db archive (for last year) is not exists
    if(!cug_rms_is_db_archive($last_year)) {
        //collect archive tables
        $archive_tables = cug_rms_collect_archive_tables($last_year);
        
        if(count($archive_tables) > 0) {//only if all archive tables are created
            //start archive process
            //--------------------------
            
            //create archive db
            $archive_db = $DB['archive_db_prefix'].$last_year;
            
            if($mysqli_rms_cache_global->create_db($archive_db)) {
                $archive_tables_success = 0;
                
                foreach($archive_tables as $archive_table) {
                    //copy cache table to archive db
                    $source_table = $DB['curadio_cache'].".".$archive_table;
                    $dest_table = $archive_db.".".$archive_table;
                    
                    if($mysqli_rms_cache_global->copy_table($source_table, $dest_table)) {
                        //delete cache table in main cache db
                        $mysqli_rms_cache_global->drop_table($source_table);
                        
                        $archive_tables_success ++;
                    }
                }
                //---------------
                
                //check result
                if(count($archive_tables) == $archive_tables_success)
                    $result = 1; //DONE
                else 
                    $result = $ERRORS['ARCHIVE_YEAR_DONE_PARTIALLY'];
                
                //insert new entry in '_db_archive_list' table
                $query = "INSERT INTO {$Tables['db_archive']} (db_name,db_year) VALUES('$archive_db',$last_year)";
                $mysqli_rms_cache->query($query);
            }
            else {
                $result = $ERRORS['UNABLE_TO_CREATE_ARCHIVE_DB'];
            }
        }
        else {
            $result = $ERRORS['NOT_ENOUGH_ARCHIVE_TABLES'];
        }
    }
    
    return $result;
}


/**
 * Collect Cache Tables to be Archived
 * 
 * @param int $last_year
 * @return array
 */
function cug_rms_collect_archive_tables($last_year) {
    global $mysqli_rms_cache, $Tables, $TIME_PERIODS;
    $result = array();
    $index = 0;
    $total_archive_tables = 0;
    
    //get cache tables list
    $query = "SELECT table_name FROM {$Tables['cache_table']} WHERE enabled=1";
    $r = $mysqli_rms_cache->query($query);
    
    if($r && $r->num_rows) {
        //collect archive table names
        while($row = $r->fetch_assoc()) {
            $cache_table = $row['table_name'];
            $arr = cug_rms_split_cache_table_name($cache_table, $delimiter="__");
            
            if(strtoupper($arr['time_period']) == $TIME_PERIODS[4]) { //LAST_MONTH
                for($i=1; $i<=12; $i++) {
                    $month_num = ($i<10) ? "0".$i : $i;
                    $archive_table = $arr['filter']."__month_".$month_num;
                    $total_archive_tables ++;
                    
                    if($mysqli_rms_cache->table_exists($archive_table)) {
                        $result[$index] = $archive_table;
                        $index ++;
                    } 
                }
            }
            elseif(strtoupper($arr['time_period']) == $TIME_PERIODS[3]) { //LAST_YEAR
                $archive_table = $arr['filter']."__year_".$last_year;
                $total_archive_tables ++;
                
                if($mysqli_rms_cache->table_exists($archive_table)) {
                    $result[$index] = $archive_table;
                    $index ++;
                }
                
            }
        }
    }
    //--------------------------

    if($total_archive_tables > 0 && $total_archive_tables == count($result)) {
        return $result;
    }
    else {
        return array();
    }
}


/**
 * Check id DB Arhcive exists
 * 
 * @param int $year
 * @return boolean
 */
function cug_rms_is_db_archive($year) {
    global $mysqli_rms_cache, $Tables;
    
    $arr = $mysqli_rms_cache->get_field_val($Tables['db_archive'], "id", "db_year=$year");
    if(!empty($arr[0]['id']))
        return true;
    else 
        return false;
}


/**
 * Archive subtable (Copy subtable to archive)
 * 
 * @param string $time_period
 * @return bool
 */
function cug_rms_archive_subtable($time_period) {
    global $mysqli_rms, $Tables, $stat_table_index, $TIME_PERIODS;
    $result = false;
    
    switch(strtoupper($time_period)) {    
        // LAST_YEAR
        case $TIME_PERIODS[3]:
        //----------------------------
            $archive_year = date("Y", strtotime('now')) - 2;
            
            $archive_subtable = $Tables[$stat_table_index]."__year_".$archive_year;
            $subtable = $Tables[$stat_table_index.'__last_year'];
    
            if(!$mysqli_rms->table_exists($archive_subtable)) {//if archive subtable does not exists
                //copy table
                $mysqli_rms->copy_table($subtable, $archive_subtable);
                
                $result = true;
            }
    
        break;
    }
    
    return $result;
}


/**
 * Copy Radio Stations Table
 * 
 * @return int
 */
function cug_rms_copy_stations_table() {
    global $mysqli_rms, $mysqli_rms_cache, $RMS_QUERY_MAKE_TABLE, $Tables, $ERRORS;
    
    $source_table = $Tables['station'];
    $dest_table = $Tables['station'];
    $dest_table_tmp = $Tables['station']."_tmp";
    
    //delete temp table if exists
    if($mysqli_rms_cache->drop_table($dest_table_tmp)) {
        //create temp table
        $query = "CREATE TABLE IF NOT EXISTS $dest_table_tmp ".$RMS_QUERY_MAKE_TABLE['stations'];
        if($mysqli_rms_cache->query($query)) {
            //copy data from source table to temp table
            $query = "SELECT * FROM $source_table ORDER BY id";
            $r = $mysqli_rms->query($query);
            
            $rows_inserted = 0;
            while($row = $r->fetch_assoc()) {
                foreach($row as $key=>$val) {
                    $row[$key] = "'".$mysqli_rms->escape_str($val)."'";
                }
                
                $values = implode(",", $row);
                $query = "INSERT INTO $dest_table_tmp VALUES($values)";
                
                if($mysqli_rms_cache->query($query))
                    $rows_inserted ++;
            }
            
            
            //rename temp table
            if($rows_inserted > 0) {
                $mysqli_rms_cache->drop_table($dest_table);

                if($mysqli_rms_cache->rename_table($dest_table_tmp, $dest_table)) {
                    return 1;
                }
                else {
                    return $ERRORS['UNABLE_TO_RENAME_TEMP_TABLE'];
                }
            }
            else {
                return $ERRORS['UNABLE_TO_COPY_TABLE_DATA'];
            }
        }
        else {
            return $ERRORS['UNABLE_TO_CREATE_TEMP_TABLE'];
        }
    }
    else {
        return $ERRORS['UNABLE_TO_DROP_TEMP_TABLE'];
    }
}
?>