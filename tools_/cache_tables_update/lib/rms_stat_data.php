<?PHP

/**
 * Extract statistics data from main table to filtered (by time period) table
 *
 * Process #1
 *
 * @param string $log_file
 * @param string $log_file_temp
 * @return number
 */
function cug_rms_extract_stat_data($log_file, $log_file_temp) {
    global $mysqli_rms, $mysqli_rms_cache, $Tables, $stat_table_index, $ERRORS, $TIME_PERIODS;

    $main_stat_table = $Tables[$stat_table_index];
    
    //log
    $log_text = PHP_EOL."START: ".date("Y-m-d H:i:s").PHP_EOL;
    cug_rms_write_log($log_file_temp, $log_text);
    
    //Check if some process is running for longer than $PROCESS_MAX_DURATION
    if(cug_rms_interrupt_long_process($mysqli_rms_cache)) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['PROCESS_WAS_INTERRUPTED'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;        
    }
        

    //check if process is ready to start
    $status = cug_rms_get_process_status($mysqli_rms_cache, $process_id=1);
    if($status != 1) {
        $err_code = ($status == 2) ? $ERRORS['PROCESS_IS_ALREADY_RUNNING'] : $ERRORS['PROCESS_IS_NOT_READY_TO_START'];
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($err_code, $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;
    }
    
    //check if main statistics data table exists
    if(!$mysqli_rms->table_exists($main_stat_table)) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['MAIN_STAT_DATA_TABLE_NOT_EXISTS'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;
    }
    
    //check table new updates (if there are new entries in main statistics table)
    $table_update_status = cug_rms_check_data_table_update($Tables[$stat_table_index]);
    if($table_update_status == 0) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['NO_NEW_DATA_TABLES_UPDATES'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;
    }
    
    //update process status
    cug_rms_edit_process_status($mysqli_rms_cache, $process_id=1, $new_status=2); //Running
    
    
    //start process
    //-------------------
    $total_time_periods = count($TIME_PERIODS);
    $extracted_tables_num = 0;
    
    
    foreach($TIME_PERIODS as $time_period) {
        $filtered_stat_table = $Tables[$stat_table_index."__".strtolower($time_period)];
    
        $time_periods = cug_rms_get_time_periods($time_period);
        $start_date = $time_periods['start_date'];
        $start_timestamp = $time_periods['start_timestamp'];
        $end_timestamp = $time_periods['end_timestamp'];
   
        
        //create new filtered statistics data table (sub table) if it is not exists
        if(!cug_rms_table_exists($mysqli_rms, $filtered_stat_table)) {
            if(!cug_rms_create_table($mysqli_rms, strtolower($stat_table_index), strtolower($time_period), $table_suffix="")) {
                $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['UNABLE_TO_CREATE_TABLE'], $ERRORS).PHP_EOL;
                cug_rms_write_log($log_file_temp, $log_text);
        
                break;
            }
        }
        
        
        //check if there is enough data in main statistics table for current time period
        $enough_data_for_curr_time_period = false;
        
        if(cug_rms_is_enough_data_to_extract($mysqli_rms, $main_stat_table, $start_timestamp, $end_timestamp)) {
            $enough_data_for_curr_time_period = true;
        }      
        //------------------------------
        
        if($enough_data_for_curr_time_period) {
            //check if it is time to extract data for current time period
            if(cug_rms_is_time_to_extract_data($mysqli_rms, $filtered_stat_table, $start_date, $time_period)) {
                //archive subtable (if necessary)
                if(cug_rms_archive_subtable($time_period)) {
                    $log_text = date("Y-m-d H:i:s")." - Action:Archive SubTable - Status:OK".PHP_EOL;
                    cug_rms_write_log($log_file_temp, $log_text);
                }
                
                //drop old filtered statistics data table (sub table) if exists
                if(!$mysqli_rms->drop_table($filtered_stat_table)) {
                    $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['UNABLE_TO_DROP_TABLE'], $ERRORS).PHP_EOL;
                    cug_rms_write_log($log_file_temp, $log_text);
                
                    break;
                }
                
                //create new filtered statistics data table
                if(!cug_rms_create_table($mysqli_rms, strtolower($stat_table_index), strtolower($time_period), $table_suffix="")) {
                    $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['UNABLE_TO_CREATE_TABLE'], $ERRORS).PHP_EOL;
                    cug_rms_write_log($log_file_temp, $log_text);
            
                    break;
                }

                
                //extract data and store in new table
                $query = "INSERT INTO $filtered_stat_table ";
                $query .= "SELECT * FROM $main_stat_table WHERE played_date >= $start_timestamp and played_date <= $end_timestamp";
                
                if($mysqli_rms->query($query)) {               
                    $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:OK".PHP_EOL;
                    cug_rms_write_log($log_file_temp, $log_text);
                    
                    $extracted_tables_num ++;
                }
                else {
                    $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['UNABLE_TO_EXTRACT_DATA'], $ERRORS).PHP_EOL;
                    cug_rms_write_log($log_file_temp, $log_text);
                }
            }
            else {
                $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_EXTRACT_DATA'], $ERRORS).PHP_EOL;
                cug_rms_write_log($log_file_temp, $log_text);
            }
        }
        else {
            $log_text = date("Y-m-d H:i:s")." - Table:".$filtered_stat_table." - Status:".array_search($ERRORS['NOT_ENOUGH_DATA_FOR_CURR_TIME_PERIOD'], $ERRORS).PHP_EOL;
            cug_rms_write_log($log_file_temp, $log_text);
        }
        
    }

    //finalize process
    //-----------------------
    //if($extracted_tables_num == $total_time_periods) {
    if($extracted_tables_num > 0) { //if at least one table was extracted
        $query = "UPDATE {$Tables['update_status']} SET be_used=1 WHERE id={$table_update_status['update_table_id']}";
        $mysqli_rms->query($query);
        
        $query = "UPDATE {$Tables['data_table']} SET prev_update_time={$table_update_status['complete_time']} WHERE id={$table_update_status['data_table_id']}";
        $mysqli_rms_cache->query($query);
        
        //copy stations table
        $stations_status = cug_rms_copy_stations_table();
        
        if($stations_status > 0) {
            cug_rms_finalize_process($mysqli_rms_cache, $curr_process_id=1, $next_process_id=2);
        }
        else {
            $log_text = date("Y-m-d H:i:s")." - Table:".$Tables['station']." - Status:".array_search($stations_status, $ERRORS).PHP_EOL;
            cug_rms_write_log($log_file_temp, $log_text);
        }      
    }
    else {
        //update process status back to 1
        cug_rms_edit_process_status($mysqli_rms_cache, $process_id=1, $new_status=1); //Ready to start
    }
    
    //log
    $log_text = "END: ".date("Y-m-d H:i:s").PHP_EOL;
    cug_rms_write_log($log_file_temp, $log_text);
    cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
    return 1;
    
}


?>