<?PHP
/**
 * Rename Cache Temp Tables
 * 
 * Process #3
 * 
 * @param string $log_file
 * @param string $log_file_temp
 * @return number
 */
function cug_rms_rename_cache_temp_tables($log_file, $log_file_temp) {
    global $mysqli_rms_cache, $Tables, $temp_table_suffix, $ERRORS;
    $result = array();
    
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
    $status = cug_rms_get_process_status($mysqli_rms_cache, $process_id=3);
    if($status != 1) {
        $err_code = ($status == 2) ? $ERRORS['PROCESS_IS_ALREADY_RUNNING'] : $ERRORS['PROCESS_IS_NOT_READY_TO_START'];
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($err_code, $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
        return 0;
    }
    
    //check temp cache tables statuses, just check this condition too, better to have one more condition
    $status = cug_rms_check_cache_tables_statuses($mysqli_rms_cache, "status_temp");
    if($status == 0) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['UPDATE_CACHE_TEMP_TABLES_NOT_FINISHED_YET'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;
    }
    
        
    //update process status
    cug_rms_edit_process_status($mysqli_rms_cache, $process_id=3, $new_status=2); //Running
    
       
    //start process
    //-------------------------   
    //lock cache tables, so they could not be usaed in API
    cug_rms_lock_cache_tables($mysqli_rms_cache);
    
    //get total numbers of cache tables
    $total_tables_num = cug_rms_get_cache_tables_total_num($mysqli_rms_cache);
    $tables_processed = 0;
    
    $query = "SELECT table_name FROM {$Tables['cache_table']} WHERE enabled=1";
    $r = $mysqli_rms_cache->query($query);
    
    while($row = $r->fetch_assoc()) {
        $table_cache = $row['table_name'];
        $table_temp = $table_cache.$temp_table_suffix;
        $status = "FAILED";
    
        $cache_table_rows_count = $mysqli_rms_cache->get_table_num_rows($table_cache);
        $temp_table_rows_count = $mysqli_rms_cache->get_table_num_rows($table_temp);
        
        if($cache_table_rows_count > 0 && $temp_table_rows_count == 0) {
            $mysqli_rms_cache->drop_table($table_temp); //drop temp table if exists because it's empty
            $status = "IDLE";
            $tables_processed ++;
        }
        else {
            //archive cache table (if necessary)
            $arr = cug_rms_split_cache_table_name($table_cache, $delimiter="__");
            cug_rms_archive_cache_table($arr['filter'], $arr['time_period'], $temp_table_rows_count, $delimiter="__");            
            
            //drop current cache table (if exists)
            $mysqli_rms_cache->drop_table($table_cache);
            
            //rename temp table
            if($mysqli_rms_cache->rename_table($table_temp, $table_cache)) {
                $status = "OK";
                $tables_processed ++;
            }                    
        }
        
        //log
        $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:$status".PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    }
    
    //---------------------
    
    
    //finalize process
    if($tables_processed == $total_tables_num) {
        //archive year (just if it is time)
        $archive_status = cug_rms_archive_year();        
        $archive_status = ($archive_status == 1) ? "OK" : array_search($archive_status, $ERRORS);
        
        //log
        $log_text = date("Y-m-d H:i:s")." - Action:Archive Year - Status:".$archive_status.PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);

        
        //unlock cache tables, so they could be used again in API
        cug_rms_unlock_cache_tables($mysqli_rms_cache);
        //unlock cache temp tables, so they could be used in process #2
        cug_rms_unlock_cache_temp_tables($mysqli_rms_cache);
        
        cug_rms_finalize_process($mysqli_rms_cache, $curr_process_id=3, $next_process_id=1);
    }

    
    //log
    $log_text = "END: ".date("Y-m-d H:i:s").PHP_EOL;
    cug_rms_write_log($log_file_temp, $log_text);
    cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
    return 1;   
}


/**
 * Prepare Cache Tables before update
 * 
 * Process #2
 * 
 * @param string $filter
 * @param string $time_period - Like: 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_WEEK', 'LAST_MONTH', ...
 * @param string $log_file
 * @param string $log_file_temp
 * @return number|array
 */
function cug_rms_prepare_cache_tables($filter, $time_period, $log_file, $log_file_temp) {
    global $mysqli_rms, $mysqli_rms_cache, $Tables, $temp_table_suffix, $stat_table_index, $ERRORS;
    $result = array();
    
    $table_index = strtolower($filter)."__".strtolower($time_period);
    
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
        
    
    //check table index
    if(empty($Tables[$table_index])) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['UNKNOWN_TABLE_INDEX'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
        return 0;
    }
    else {
        $table      = $Tables[$table_index];
        $table_temp = $Tables[$table_index].$temp_table_suffix;
        $filtered_stat_table = $Tables[$stat_table_index."__".strtolower($time_period)];
    }
    
    
    //check if filtered statistics data table (sub table) exists
    if(!$mysqli_rms->table_exists($filtered_stat_table)) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['FILTERED_STAT_DATA_TABLE_NOT_EXISTS'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
        return 0;
    }
    
    
    //check if calculation for current cache table is enabled
    $table_eabled = cug_rms_get_cache_table_status($mysqli_rms_cache, $table, "enabled");
    if(!$table_eabled) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['CACHE_TABLE_IS_DISABLED'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        return 0;
    }
    
    //create new cache table if it is not exists
    if(!cug_rms_table_exists($mysqli_rms_cache, $table)) {       
        if(!cug_rms_create_table($mysqli_rms_cache, strtolower($filter), strtolower($time_period), $suffix="")) {
            $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:".array_search($ERRORS['UNABLE_TO_CREATE_TABLE'], $ERRORS).PHP_EOL;
            cug_rms_write_log($log_file_temp, $log_text);
            cug_rms_copy_log_temp_file($log_file_temp, $log_file);
            return 0;
        }
    }
    
    
    //check if process is ready to start
    //this process should start only if $process_status is 1 and $table_status is 0
    $process_status = cug_rms_get_process_status($mysqli_rms_cache, $process_id=2);
    $table_status = cug_rms_get_cache_table_status($mysqli_rms_cache, $table, "status_temp");
    
    if($process_status == 0) {
        $log_text = date("Y-m-d H:i:s")." - Status:".array_search($ERRORS['PROCESS_IS_NOT_READY_TO_START'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
    
        return 0;
    }
    elseif($process_status == 2) { // process is running
        //check temp table status
        if($table_status > 0) {
            $err_code = ($table_status == 2) ? $ERRORS['TABLE_UPDATE_IS_ALREADY_RUNNING'] : $ERRORS['TABLE_UPDATE_IS_ALREADY_FINISHED'];
            $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:".array_search($err_code, $ERRORS).PHP_EOL;
            cug_rms_write_log($log_file_temp, $log_text);
            cug_rms_copy_log_temp_file($log_file_temp, $log_file);
            
            return 0;            
        }
    }
    elseif($process_status == 1) { //Ready to start
        //check temp table status, for some reasons check table status here too
        if($table_status > 0) {
            $err_code = ($table_status == 2) ? $ERRORS['TABLE_UPDATE_IS_ALREADY_RUNNING'] : $ERRORS['TABLE_UPDATE_IS_ALREADY_FINISHED'];
            $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:".array_search($err_code, $ERRORS).PHP_EOL;
            cug_rms_write_log($log_file_temp, $log_text);
            cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
            return 0;
        }       
    }
    
    
    //update process status
    cug_rms_edit_process_status($mysqli_rms_cache, $process_id=2, $new_status=2); //Running
    
    //update table status
    cug_rms_edit_cache_table_status($mysqli_rms_cache, $table, "status_temp", $new_status=2); //Running
    
    
    //start process
    //-------------------               
    //drop temp table if exists
    if(!$mysqli_rms_cache->drop_table($table_temp)) {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:".array_search($ERRORS['UNABLE_TO_DROP_TEMP_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        //update table status back to 0 (Idle)
        cug_rms_edit_cache_table_status($mysqli_rms_cache, $table, "status_temp", $new_status=0); //Idle
        
        return 0;
    }

        
    //create temp table
    if(!cug_rms_create_table($mysqli_rms_cache, strtolower($filter), strtolower($time_period), $temp_table_suffix)) {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table_temp." - Status:".array_search($ERRORS['UNABLE_TO_CREATE_TEMP_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
        cug_rms_copy_log_temp_file($log_file_temp, $log_file);
        
        //update table status back to 0 (Idle)
        cug_rms_edit_cache_table_status($mysqli_rms_cache, $table, "status_temp", $new_status=0); //Idle
        
        return 0;
    }

         
    $time_periods = cug_rms_get_time_periods($time_period);
       
    $result['tables']['table'] = $table;
    $result['tables']['table_temp'] = $table_temp;
    $result['tables']['stat_table'] = $filtered_stat_table;
    
    $result['time_periods']['start_date'] = $time_periods['start_date'];
    $result['time_periods']['start_time'] = $time_periods['start_time'];
    
    $result['time_periods']['end_date'] = $time_periods['end_date'];
    $result['time_periods']['end_time'] = $time_periods['end_time'];
    
    $result['time_periods']['time_period'] = $time_period;
    
    return $result;
}


/**
 * Update Tables started with 'track_played_total'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_total($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS, $TIME_PERIODS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
   
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique track ids
        //$query = cug_rms_query_get_distinct_track_ids($stat_table);
     
        //$r = $mysqli_rms->query($query);
    
        //if($r && $r->num_rows) {

            $query = "INSERT INTO $table_temp (";
            $query .= "SELECT null,r.cugate_track_id ,r.shenzhen_track_id,COUNT( r.cugate_track_id ) AS played_num,null,(r.duration * COUNT( r.cugate_track_id )) AS airtime ,null ";
            $query .= "FROM ".$stat_table." AS r ";
            $query .= "INNER JOIN  {$Tables['station']}  AS s ON r.station_id = s.id ";
            $query .= "WHERE r.cugate_track_id IS NOT NULL AND duration > 0 AND cugate_track_id > 0 ";
            $query .= "GROUP BY r.cugate_track_id";
            $query .= ")";
            $mysqli_rms->query($query);


            /*
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
    
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, COUNT(r.cugate_track_id) AS played_num, (r.duration * COUNT(r.cugate_track_id)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0";
                    print_log("cug_rms_update_track_played_total",$query);
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
    
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,$played_num,$airtime)";
                            print_log("cug_rms_update_track_played_total",$query);
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     
    
                }
    
            }*/
        //}


        if($result > 0) {
            //update rank numbers in temp table
            $query = "SELECT id, played_num FROM $table_temp ";
            $query .= "ORDER BY played_num DESC";
            $r = $mysqli_rms_cache->query($query);
    
            if($r && $r->num_rows) {
                $rank_num = 0;
                $prev_played_num = 0;
    
                while($row = $r->fetch_assoc()) {
                    $id = $row['id'];
                    $played_num = $row['played_num'];
        
                    if($played_num != $prev_played_num)
                        $rank_num ++;
            
                    $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                    $prev_played_num = $played_num;
                }    
            } 
            
            //update track charts table
            if($update_track_charts)
                cug_rms_update_track_charts($table_temp, $config_arr['time_periods']['time_period']);
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
      
    return $result;
}


/**
 * Update Tables started with 'track_played_by_continent'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
    
  
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {

        $query = "INSERT INTO $table_temp (";
        $query .= "SELECT null,r.cugate_track_id,r.shenzhen_track_id, s.continent_code, s.continent_name, COUNT(s.continent_code) AS played_num, null,(r.duration * COUNT(s.continent_code)) AS airtime,null ";
        $query .= "FROM $stat_table AS r ";
        $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
        $query .= "WHERE r.cugate_track_id > 0  ";
        $query .= "AND r.duration>0 ";
        $query .= "GROUP BY r.cugate_track_id,s.continent_code ";
        $query .= "ORDER BY played_num DESC)";
        $mysqli_rms->query($query);

        //select all unique track ids
       /* $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
    
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];               
    
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, s.continent_code, s.continent_name, COUNT(s.continent_code) AS played_num, (r.duration * COUNT(s.continent_code)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.continent_code ";
                    $query .= "ORDER BY played_num DESC";
                    print_log("",$query);
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
    
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,continent_code,continent_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',$played_num,$airtime)";
    
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     
    
                }
    
            }
        }*/
    
        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);
    
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];
    
                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);
    
                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;
    
                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];
    
                            if($played_num != $prev_played_num)
                                $rank_num ++;
    
                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
    
    return $result;
}




/**
 * Update Tables started with 'track_played_by_country'
 * 
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;
    
    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
    
    
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {

        $query = "INSERT INTO $table_temp (";
        $query .= "SELECT null,r.cugate_track_id,r.shenzhen_track_id, s.continent_code, s.country_code, s.country_name, COUNT(s.country_code) AS played_num,null, (r.duration * COUNT(s.country_code)) AS airtime,null ";
        $query .= "FROM $stat_table AS r ";
        $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
        $query .= "WHERE r.cugate_track_id>0 ";
        $query .= "AND r.duration>0 ";
        $query .= "GROUP BY r.cugate_track_id,s.country_code ";
        $query .= "ORDER BY played_num DESC)";
        $mysqli_rms->query($query);
        //select all unique track ids
       /* $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
        
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                
                
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.country_name, COUNT(s.country_code) AS played_num, (r.duration * COUNT(s.country_code)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code ";
                    $query .= "ORDER BY played_num DESC";
                    
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,continent_code,country_code,country_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',$played_num,$airtime)";
                            
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
               
                    
                }
                
            }
        }   */
        
        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);
        
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
        
                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);
        
                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;
        
                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];
                            
                            if($played_num != $prev_played_num)
                                $rank_num ++;
                            
                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");                       
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------    
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
    
    return $result;
}


/**
 * Update Tables started with 'track_played_by_subdivision'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {

        $query = "INSERT INTO $table_temp (";
        $query .= "SELECT null,r.cugate_track_id,r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, COUNT(s.subdivision_code) AS played_num,null, (r.duration * COUNT(s.subdivision_code)) AS airtime,null ";
        $query .= "FROM $stat_table AS r ";
        $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
        $query .= "WHERE r.cugate_track_id>0 ";
        $query .= "AND r.duration>0 ";
        $query .= "GROUP BY r,cugate_track_id,s.country_code, s.subdivision_code ";
        $query .= "ORDER BY played_num DESC)";
        $mysqli_rms->query($query);
        //select all unique track ids
        /*$query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
    
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                
    
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, COUNT(s.subdivision_code) AS played_num, (r.duration * COUNT(s.subdivision_code)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.subdivision_code ";
                    $query .= "ORDER BY played_num DESC";
    
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
    
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,continent_code,country_code,subdivision_code,subdivision_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',$played_num,$airtime)";
    
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     
    
                }
    
            }
        }*/
    
        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);
    
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];
    
                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);
    
                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;
    
                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];
    
                            if($played_num != $prev_played_num)
                                $rank_num ++;
    
                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
    
    return $result;
}



/**
 * Update Tables started with 'track_played_by_city'
 * 
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {

        $query = "INSERT INTO $table_temp (";
        $query .= "SELECT null,r.cugate_track_id,r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, COUNT(s.city) AS played_num,null, (r.duration * COUNT(s.city)) AS airtime,null ";
        $query .= "FROM $stat_table AS r ";
        $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
        $query .= "WHERE r.cugate_track_id>0 ";
        $query .= "AND r.duration>0 ";
        $query .= "GROUP BY r.cugate_track_id,s.country_code, s.city ";
        $query .= "ORDER BY played_num DESC)";

        $mysqli_rms->query($query);
        //select all unique track ids
        /*$query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
    
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                
    
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, COUNT(s.city) AS played_num, (r.duration * COUNT(s.city)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.city ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
    
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,continent_code,country_code,subdivision_code,city,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$played_num,$airtime)";
                            
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     
    
                }
    
            }
        }*/
    
        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);
    
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city = $row['city'];
    
                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city)."' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);
    
                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;
    
                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];
    
                            if($played_num != $prev_played_num)
                                $rank_num ++;
    
                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }   
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
          
    return $result;
}


/**
 * Update Tables started with 'track_played_by_station' table
 * 
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {

        $query = "INSERT INTO $table_temp (";
        $query .= "SELECT null,r.cugate_track_id,r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, s.id AS station_id, COUNT(s.id) AS played_num,null, (r.duration * COUNT(s.id)) AS airtime,null ";
        $query .= "FROM $stat_table AS r ";
        $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
        $query .= "WHERE r.cugate_track_id>0 ";
        $query .= "AND r.duration>0 ";
        $query .= "GROUP BY r.cugate_track_id,s.id ";
        $query .= "ORDER BY played_num DESC)";
        $mysqli_rms->query($query);
        //select all unique track ids
        /*$query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
    
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                
    
                //select data
                if($cugate_track_id > 0) {
                    $query = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, s.id AS station_id, COUNT(s.id) AS played_num, (r.duration * COUNT(s.id)) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.id ";
                    $query .= "ORDER BY played_num DESC";
    
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $station_id = $row1['station_id'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
    
                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_track_id,shenzhen_track_id,continent_code,country_code,subdivision_code,city,station_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$station_id,$played_num,$airtime)";
    
                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     
    
                }
    
            }
        }*/
        
        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);
    
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];
    
                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);
    
                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;
    
                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];
    
                            if($played_num != $prev_played_num)
                                $rank_num ++;
    
                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }   
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);        
    
    return $result;
}


/**
 * Update Tables started with 'track_played_by_daytime...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
    //select all unique track ids
    $query = cug_rms_query_get_distinct_track_ids($stat_table);
    $r = $mysqli_rms->query($query);

    if($r && $r->num_rows) {
        while($row = $r->fetch_assoc()) {
            $cugate_track_id = $row['cugate_track_id'];
            

            //select data
            if($cugate_track_id > 0) {

                //generate query to get all played numbers by hours              
                $query_start = "SELECT ";
                $query_played_num = "";
                $query_airtime = "";
                $query_middle = "";
                $query_end = " FROM $stat_table AS r ";
                $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                $query_end .= "WHERE cugate_track_id=$cugate_track_id ";
                $query_end .= "AND r.duration>0";
                
                for($i=0; $i<24; $i++) {
                    $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                    $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";
                    
                    $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                    $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                }
                
                $query_airtime = rtrim($query_airtime, ",");
                $query_middle = "shenzhen_track_id," . $query_played_num . $query_airtime;
                $query = $query_start . $query_middle . $query_end;
                
                //execute query and store all played numbers and airtimes (by hours) in $played_nums_airtime_arr array
                $played_nums_airtime_arr = array();
                $r1 = $mysqli_rms->query($query);
                
                if($r1 && $r1->num_rows) {
                    $played_nums_airtime_arr = $r1->fetch_assoc();
                    $shenzhen_track_id = $played_nums_airtime_arr['shenzhen_track_id'];
                }

                //calculate total played number and total airtime
                $total_played_num = 0;
                $total_airtime = 0;
                for($i=0; $i<24; $i++) {
                    $index1 = "played_num_".$i;
                    $index2 = "airtime_".$i;

                    $total_played_num += !empty($played_nums_airtime_arr[$index1]) ? $played_nums_airtime_arr[$index1] : 0;
                    $total_airtime += (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2] : 0;
                }
                
                //generate insert query
                if($total_played_num > 0) { //insert only such of tracks which are played at least once
                    $query_start = "INSERT INTO $table_temp ";
                    $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_percent = "";
                    $query_end = "NOW())";
                    
                    for($i=0; $i<24; $i++) {
                        $index1 = "played_num_".$i;
                        $index2 = "airtime_".$i;
                        
                        $query_played_num .= $played_nums_airtime_arr[$index1].",";
                        $query_airtime .= (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2]."," : "0,";
                        $query_percent .= "NULL,";
                    }
                    
                    $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                    //-----------------------------------
                    
                    //insert data in temp table
                    if($mysqli_rms_cache->query($query))
                        $result ++;
                }
            }
            
        }
    }

    //calculate percetages by hours
    if($result > 0) {
        //calculate summarized played numbers for each hour
        $fields = "";
        for($i=0; $i<24; $i++) {
            $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
        }
        $fields = rtrim($fields, ",");
        
        $query = "SELECT $fields FROM $table_temp";       
        $r = $mysqli_rms_cache->query($query);
        
        $played_num_sum = array();
        if($r && $r->num_rows) {
            $played_num_sum = $r->fetch_array();  
        }
        //-----------------------------
        
        //calculate percentages
        if(count($played_num_sum) > 0) {
            $query = "SELECT * FROM $table_temp ORDER BY id";
            $r = $mysqli_rms_cache->query($query);
            
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $id = $row['id'];
                    $percentages = array();
                    
                    //calculate percentages and store in $percentages array
                    for($i=0; $i<24; $i++) {
                        $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                        $played_num = $row[$field];
                        
                        $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                        $percentages[] = number_format($percentage, 4, '.', '');
                    }
                    //-----------------------
                    
                    //generate update query
                    $query_start = "UPDATE $table_temp SET ";
                    $query_middle = "";
                    $query_end = " WHERE id=$id";
                    
                    for($i=0; $i<24; $i++) {
                        $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                    }
                    
                    $query_middle = rtrim($query_middle, ",");
                    $query = $query_start . $query_middle . $query_end;
                    //-------------------------
                    
                    //update table
                    $mysqli_rms_cache->query($query);
                }
            }
        } 
    }
    //-------------------------------    
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);
    
    return $result;
}


/**
 * Update Tables started with 'track_played_by_daytime_continent...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
    
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {  
        //select all unique track ids
        $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                

                //select data
                if($cugate_track_id > 0) {
                    //generate query to get all played numbers by hours grouped by continent_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_track_id, s.continent_code, s.continent_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.continent_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) { 
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            
                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;
                                
                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }
        
                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";
        
                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;
                                    
                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }
        
        
                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------
        
                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE continent_code='$continent_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //------------------------------- 
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'track_played_by_daytime_country...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique track ids
        $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);
    
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                
            
                //select data
                if($cugate_track_id > 0) {                                              
                    //generate query to get all played numbers by hours grouped by country_code
                    $query_arr = array();
                    
                    $query_start = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.country_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code";
    
                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";
                        
                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                    }
                    
                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;               
                    $query = $query_start . $query_middle . $query_end;
    
                    //execute query
                    $r1 = $mysqli_rms->query($query);                    
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) { 
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            
                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;
                                
                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }
                            
                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";
                
                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;
                                    
                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }
                
                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------
                
                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;  
                            } 
                        }//while
                    }
                }
    
            }
        }
    
        
        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);
            
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    
                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");
            
                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);
            
                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------
            
                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE country_code='$country_code' ";
                        $r1 = $mysqli_rms_cache->query($query);
            
                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();
            
                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];
            
                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------
            
                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";
            
                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }
            
                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------
            
                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'track_played_by_daytime_subdivision...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique track ids
        $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                

                //select data
                if($cugate_track_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and subdivision_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.subdivision_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) { 
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;
                                
                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }
        
                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";
        
                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;
                                    
                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }
        
                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------
        
                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }// while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
        
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'track_played_by_daytime_city...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique track ids
        $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                

                //select data
                if($cugate_track_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.city";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            
                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;
                                
                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                            }
        
                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";
        
                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;
                                    
                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= ($row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }
        
                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------
        
                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city_name = $row['city'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
        
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'track_played_by_daytime_station...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_daytime_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique track ids
        $query = cug_rms_query_get_distinct_track_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_track_id = $row['cugate_track_id'];
                

                //select data
                if($cugate_track_id > 0) {
                    //generate query to get all played numbers by hours grouped by city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_track_id, s.continent_code, s.country_code, s.subdivision_code, s.city, r.station_id, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_track_id=$cugate_track_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY r.station_id";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "(COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) * duration) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $played_nums_arr = array();
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $station_id = $row1['station_id'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            
                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;
                                
                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }
        
                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_track_id,$shenzhen_track_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',$station_id,";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";
        
                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;
                                    
                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }
        
                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------
        
                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE station_id=$station_id";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
        
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }
    
    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'track_played_by_artist...'
 * 
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_track_played_by_artist($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique artist ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];
                

                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT shenzhen_member_id, cugate_track_id, shenzhen_track_id, COUNT(cugate_track_id) AS played_num ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY cugate_track_id ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $cugate_track_id = $row1['cugate_track_id'];
                            $shenzhen_track_id = $row1['shenzhen_track_id'];
                            $played_num = $row1['played_num'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,cugate_track_id,shenzhen_track_id,played_num) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,$cugate_track_id,$shenzhen_track_id,$played_num)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(cugate_member_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $cugate_member_id = $row['cugate_member_id'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE cugate_member_id=$cugate_member_id ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tracks Charts
 * 
 * @param string $cache_table
 * @param string $time_period
 * @return void
 */
function cug_rms_update_track_charts($cache_table, $time_period) {
    global $mysqli_rms_cache, $mysqli_cug,  $Tables, $TIME_PERIODS;
    
    if(strtoupper($time_period) == $TIME_PERIODS[0]) { //LAST_7_DAYS
        $chart_data = array();
        $index = 0;
        $LIMIT = 20;
        
        $query = "SELECT cugate_track_id FROM $cache_table ORDER BY played_num DESC LIMIT 40";
        $r = $mysqli_rms_cache->query($query);
        
        if($r && $r->num_rows) {
            $chart_position = 0;
            while($row = $r->fetch_assoc()) {
                if($index == $LIMIT) {
                    break;
                }
                
                $track_id = $row['cugate_track_id'];
                
                //get album id
                $query = "SELECT album_id FROM {$Tables['track_album_rel']} WHERE track_id=$track_id LIMIT 1";
                $r1 = $mysqli_cug->query($query);
                
                if($r1 && $r1->num_rows) {
                    $row1 = $r1->fetch_assoc();
                    $album_id = $row1['album_id'];
                    
                    //get primary artist id
                    $query = "SELECT member_id FROM {$Tables['track_member_rel']} WHERE track_id=$track_id AND isprimary=1 LIMIT 1";
                    $r2 = $mysqli_cug->query($query);
                    
                    if($r2 && $r2->num_rows) {
                        $row2 = $r2->fetch_assoc();
                        $member_id = $row2['member_id'];
                        
                        //collect chart data
                        $chart_position ++;
                        $status = 1;
                        $chart_type_id = 1; //id from 'chart_type_list' table
                        
                        $chart_data[$index]['chart_type_id'] = $chart_type_id;
                        $chart_data[$index]['track_id'] = $track_id;
                        $chart_data[$index]['album_id'] = $album_id;
                        $chart_data[$index]['member_id'] = $member_id;
                        $chart_data[$index]['pos'] = $chart_position;
                        $chart_data[$index]['status'] = $status;
                        
                        $index ++;
                    }
                }
            }
        }
        
        if(count($chart_data) > 0) {
            //empty chart table
            $query = "DELETE FROM {$Tables['chart_tracks_alt']};";
            if($mysqli_cug->query($query)) {
                $mysqli_cug->query("ALTER TABLE {$Tables['chart_tracks_alt']} AUTO_INCREMENT=1");
                
                //insert chart data
                foreach($chart_data as $chart) {
                    $query = "INSERT INTO {$Tables['chart_tracks_alt']} (chart_type_id,track_id,album_id,member_id,pos,status) ";
                    $query .= "VALUES({$chart['chart_type_id']},{$chart['track_id']},{$chart['album_id']},{$chart['member_id']},{$chart['pos']},{$chart['status']})";
                    $mysqli_cug->query($query);
                }
            }
        }
        
    }
}




/**
 * Update Tables started with 'artist_played_total'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_total($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS, $TIME_PERIODS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
     
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];

                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, COUNT(r.cugate_member_id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }


        if($result > 0) {
            //update rank numbers in temp table
            $query = "SELECT id, played_num FROM $table_temp ";
            $query .= "ORDER BY played_num DESC";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                $rank_num = 0;
                $prev_played_num = 0;

                while($row = $r->fetch_assoc()) {
                    $id = $row['id'];
                    $played_num = $row['played_num'];

                    if($played_num != $prev_played_num)
                        $rank_num ++;

                    $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                    $prev_played_num = $played_num;
                }
            }

        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_continent'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];

                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, s.continent_code, s.continent_name, COUNT(s.continent_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.continent_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,continent_code,continent_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'artist_played_by_country'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.country_name, COUNT(s.country_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,continent_code,country_code,country_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_subdivision'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, COUNT(s.subdivision_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.subdivision_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,continent_code,country_code,subdivision_code,subdivision_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'artist_played_by_city'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.city, COUNT(s.city) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.city ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,continent_code,country_code,subdivision_code,city,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city = $row['city'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city)."' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'artist_played_by_station' table
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    $query = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.city, s.id AS station_id, COUNT(s.id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.id ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $station_id = $row1['station_id'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_member_id,shenzhen_member_id,continent_code,country_code,subdivision_code,city,station_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$station_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                            $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                            $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {

                    //generate query to get all played numbers by hours
                    $query_start = "SELECT ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = "shenzhen_member_id," . $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query and store all played numbers and airtimes (by hours) in $played_nums_airtime_arr array
                    $played_nums_airtime_arr = array();
                    $r1 = $mysqli_rms->query($query);

                    if($r1 && $r1->num_rows) {
                        $played_nums_airtime_arr = $r1->fetch_assoc();
                        $shenzhen_member_id = $played_nums_airtime_arr['shenzhen_member_id'];
                    }

                    //calculate total played number and total airtime
                    $total_played_num = 0;
                    $total_airtime = 0;
                    for($i=0; $i<24; $i++) {
                        $index1 = "played_num_".$i;
                        $index2 = "airtime_".$i;

                        $total_played_num += !empty($played_nums_airtime_arr[$index1]) ? $played_nums_airtime_arr[$index1] : 0;
                        $total_airtime += (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2] : 0;
                    }

                    //generate insert query
                    if($total_played_num > 0) { //insert only such of members which are played at least once
                        $query_start = "INSERT INTO $table_temp ";
                        $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,";
                        $query_played_num = "";
                        $query_airtime = "";
                        $query_percent = "";
                        $query_end = "NOW())";

                        for($i=0; $i<24; $i++) {
                            $index1 = "played_num_".$i;
                            $index2 = "airtime_".$i;

                            $query_played_num .= $played_nums_airtime_arr[$index1].",";
                            $query_airtime .= (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2]."," : "0,";
                            $query_percent .= "NULL,";
                        }

                        $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                        //-----------------------------------

                        //insert data in temp table
                        if($mysqli_rms_cache->query($query))
                            $result ++;
                    }
                }

            }
        }

        //calculate percetages by hours
        if($result > 0) {
            //calculate summarized played numbers for each hour
            $fields = "";
            for($i=0; $i<24; $i++) {
                $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
            }
            $fields = rtrim($fields, ",");

            $query = "SELECT $fields FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            $played_num_sum = array();
            if($r && $r->num_rows) {
                $played_num_sum = $r->fetch_array();
            }
            //-----------------------------

            //calculate percentages
            if(count($played_num_sum) > 0) {
                $query = "SELECT * FROM $table_temp ORDER BY id";
                $r = $mysqli_rms_cache->query($query);

                if($r && $r->num_rows) {
                    while($row = $r->fetch_assoc()) {
                        $id = $row['id'];
                        $percentages = array();

                        //calculate percentages and store in $percentages array
                        for($i=0; $i<24; $i++) {
                            $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                            $played_num = $row[$field];

                            $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                            $percentages[] = number_format($percentage, 4, '.', '');
                        }
                        //-----------------------

                        //generate update query
                        $query_start = "UPDATE $table_temp SET ";
                        $query_middle = "";
                        $query_end = " WHERE id=$id";

                        for($i=0; $i<24; $i++) {
                            $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                        }

                        $query_middle = rtrim($query_middle, ",");
                        $query = $query_start . $query_middle . $query_end;
                        //-------------------------

                        //update table
                        $mysqli_rms_cache->query($query);
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime_continent...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    //generate query to get all played numbers by hours grouped by continent_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_member_id, s.continent_code, s.continent_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.continent_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }


                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE continent_code='$continent_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime_country...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.country_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE country_code='$country_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime_subdivision...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and subdivision_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.subdivision_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }// while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime_city...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.city, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.city";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= ($row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city_name = $row['city'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'artist_played_by_daytime_station...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_artist_played_by_daytime_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique member ids
        $query = cug_rms_query_get_distinct_member_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_member_id = $row['cugate_member_id'];


                //select data
                if($cugate_member_id > 0) {
                    //generate query to get all played numbers by hours grouped by city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_member_id, s.continent_code, s.country_code, s.subdivision_code, s.city, r.station_id, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_member_id=$cugate_member_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY r.station_id";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $played_nums_arr = array();
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $station_id = $row1['station_id'];
                            $shenzhen_member_id = $row1['shenzhen_member_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_member_id,$shenzhen_member_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',$station_id,";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE station_id=$station_id";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}





/**
 * Update Tables started with 'label_played_total'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_total($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS, $TIME_PERIODS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
     
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];

                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, COUNT(r.cugate_label_id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }


        if($result > 0) {
            //update rank numbers in temp table
            $query = "SELECT id, played_num FROM $table_temp ";
            $query .= "ORDER BY played_num DESC";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                $rank_num = 0;
                $prev_played_num = 0;

                while($row = $r->fetch_assoc()) {
                    $id = $row['id'];
                    $played_num = $row['played_num'];

                    if($played_num != $prev_played_num)
                        $rank_num ++;

                        $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                        $prev_played_num = $played_num;
                }
            }

        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}



/**
 * Update Tables started with 'label_played_by_continent'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];

                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, s.continent_code, s.continent_name, COUNT(s.continent_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.continent_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,continent_code,continent_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_country'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.country_name, COUNT(s.country_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,continent_code,country_code,country_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_subdivision'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, COUNT(s.subdivision_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.subdivision_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,continent_code,country_code,subdivision_code,subdivision_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_city'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.city, COUNT(s.city) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.city ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,continent_code,country_code,subdivision_code,city,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city = $row['city'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city)."' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_station' table
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    $query = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.city, s.id AS station_id, COUNT(s.id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.id ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $station_id = $row1['station_id'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_label_id,shenzhen_label_id,continent_code,country_code,subdivision_code,city,station_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$station_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {

                    //generate query to get all played numbers by hours
                    $query_start = "SELECT ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = "shenzhen_label_id," . $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query and store all played numbers and airtimes (by hours) in $played_nums_airtime_arr array
                    $played_nums_airtime_arr = array();
                    $r1 = $mysqli_rms->query($query);

                    if($r1 && $r1->num_rows) {
                        $played_nums_airtime_arr = $r1->fetch_assoc();
                        $shenzhen_label_id = $played_nums_airtime_arr['shenzhen_label_id'];
                    }

                    //calculate total played number and total airtime
                    $total_played_num = 0;
                    $total_airtime = 0;
                    for($i=0; $i<24; $i++) {
                        $index1 = "played_num_".$i;
                        $index2 = "airtime_".$i;

                        $total_played_num += !empty($played_nums_airtime_arr[$index1]) ? $played_nums_airtime_arr[$index1] : 0;
                        $total_airtime += (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2] : 0;
                    }

                    //generate insert query
                    if($total_played_num > 0) { //insert only such of labels which are played at least once
                        $query_start = "INSERT INTO $table_temp ";
                        $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,";
                        $query_played_num = "";
                        $query_airtime = "";
                        $query_percent = "";
                        $query_end = "NOW())";

                        for($i=0; $i<24; $i++) {
                            $index1 = "played_num_".$i;
                            $index2 = "airtime_".$i;

                            $query_played_num .= $played_nums_airtime_arr[$index1].",";
                            $query_airtime .= (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2]."," : "0,";
                            $query_percent .= "NULL,";
                        }

                        $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                        //-----------------------------------

                        //insert data in temp table
                        if($mysqli_rms_cache->query($query))
                            $result ++;
                    }
                }

            }
        }

        //calculate percetages by hours
        if($result > 0) {
            //calculate summarized played numbers for each hour
            $fields = "";
            for($i=0; $i<24; $i++) {
                $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
            }
            $fields = rtrim($fields, ",");

            $query = "SELECT $fields FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            $played_num_sum = array();
            if($r && $r->num_rows) {
                $played_num_sum = $r->fetch_array();
            }
            //-----------------------------

            //calculate percentages
            if(count($played_num_sum) > 0) {
                $query = "SELECT * FROM $table_temp ORDER BY id";
                $r = $mysqli_rms_cache->query($query);

                if($r && $r->num_rows) {
                    while($row = $r->fetch_assoc()) {
                        $id = $row['id'];
                        $percentages = array();

                        //calculate percentages and store in $percentages array
                        for($i=0; $i<24; $i++) {
                            $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                            $played_num = $row[$field];

                            $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                            $percentages[] = number_format($percentage, 4, '.', '');
                        }
                        //-----------------------

                        //generate update query
                        $query_start = "UPDATE $table_temp SET ";
                        $query_middle = "";
                        $query_end = " WHERE id=$id";

                        for($i=0; $i<24; $i++) {
                            $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                        }

                        $query_middle = rtrim($query_middle, ",");
                        $query = $query_start . $query_middle . $query_end;
                        //-------------------------

                        //update table
                        $mysqli_rms_cache->query($query);
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime_continent...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    //generate query to get all played numbers by hours grouped by continent_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_label_id, s.continent_code, s.continent_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.continent_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }


                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE continent_code='$continent_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime_country...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.country_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE country_code='$country_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime_subdivision...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and subdivision_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.subdivision_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }// while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime_city...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.city, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.city";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= ($row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city_name = $row['city'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'label_played_by_daytime_station...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_label_played_by_daytime_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique label ids
        $query = cug_rms_query_get_distinct_label_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_label_id = $row['cugate_label_id'];


                //select data
                if($cugate_label_id > 0) {
                    //generate query to get all played numbers by hours grouped by city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_label_id, s.continent_code, s.country_code, s.subdivision_code, s.city, r.station_id, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_label_id=$cugate_label_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY r.station_id";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $played_nums_arr = array();
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $station_id = $row1['station_id'];
                            $shenzhen_label_id = $row1['shenzhen_label_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_label_id,$shenzhen_label_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',$station_id,";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE station_id=$station_id";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_total'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_total($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS, $TIME_PERIODS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];
     
    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];

                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, COUNT(r.cugate_publisher_id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }


        if($result > 0) {
            //update rank numbers in temp table
            $query = "SELECT id, played_num FROM $table_temp ";
            $query .= "ORDER BY played_num DESC";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                $rank_num = 0;
                $prev_played_num = 0;

                while($row = $r->fetch_assoc()) {
                    $id = $row['id'];
                    $played_num = $row['played_num'];

                    if($played_num != $prev_played_num)
                        $rank_num ++;

                        $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                        $prev_played_num = $played_num;
                }
            }

        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_continent'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];

                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, s.continent_code, s.continent_name, COUNT(s.continent_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.continent_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,continent_code,continent_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_country'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];


    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.country_name, COUNT(s.country_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,continent_code,country_code,country_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_subdivision'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, COUNT(s.subdivision_code) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.subdivision_code ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,continent_code,country_code,subdivision_code,subdivision_name,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_city'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.city, COUNT(s.city) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.country_code, s.city ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,continent_code,country_code,subdivision_code,city,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city = $row['city'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city)."' AND country_code='$country_code' ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_station' table
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    $query = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.city, s.id AS station_id, COUNT(s.id) AS played_num, SUM(r.duration) AS airtime ";
                    $query .= "FROM $stat_table AS r ";
                    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query .= "AND r.duration>0 ";
                    $query .= "GROUP BY s.id ";
                    $query .= "ORDER BY played_num DESC";

                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city = $row1['city'];
                            $station_id = $row1['station_id'];
                            $played_num = $row1['played_num'];
                            $airtime = !empty($row1['airtime']) ? $row1['airtime'] : 0;
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //insert data in temp table
                            $query = "INSERT INTO $table_temp ";
                            $query .= "(cugate_publisher_id,shenzhen_publisher_id,continent_code,country_code,subdivision_code,city,station_id,played_num,airtime) ";
                            $query .= "VALUES($cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city)."',$station_id,$played_num,$airtime)";

                            if($mysqli_rms_cache->query($query))
                                $result ++;
                        }
                    }
                     

                }

            }
        }

        //update rank numbers in temp table
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    $query = "SELECT id, played_num FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id ORDER BY played_num DESC";
                    $r1 = $mysqli_rms_cache->query($query);

                    if($r1 && $r1->num_rows) {
                        $rank_num = 0;
                        $prev_played_num = 0;

                        while($row1 = $r1->fetch_assoc()) {
                            $id = $row1['id'];
                            $played_num = $row1['played_num'];

                            if($played_num != $prev_played_num)
                                $rank_num ++;

                                $mysqli_rms_cache->query("UPDATE $table_temp SET rank_num=$rank_num WHERE id=$id");
                                $prev_played_num = $played_num;
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {

                    //generate query to get all played numbers by hours
                    $query_start = "SELECT ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = "shenzhen_publisher_id," . $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query and store all played numbers and airtimes (by hours) in $played_nums_airtime_arr array
                    $played_nums_airtime_arr = array();
                    $r1 = $mysqli_rms->query($query);

                    if($r1 && $r1->num_rows) {
                        $played_nums_airtime_arr = $r1->fetch_assoc();
                        $shenzhen_publisher_id = $played_nums_airtime_arr['shenzhen_publisher_id'];
                    }

                    //calculate total played number and total airtime
                    $total_played_num = 0;
                    $total_airtime = 0;
                    for($i=0; $i<24; $i++) {
                        $index1 = "played_num_".$i;
                        $index2 = "airtime_".$i;

                        $total_played_num += !empty($played_nums_airtime_arr[$index1]) ? $played_nums_airtime_arr[$index1] : 0;
                        $total_airtime += (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2] : 0;
                    }

                    //generate insert query
                    if($total_played_num > 0) { //insert only such of publishers which are played at least once
                        $query_start = "INSERT INTO $table_temp ";
                        $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,";
                        $query_played_num = "";
                        $query_airtime = "";
                        $query_percent = "";
                        $query_end = "NOW())";

                        for($i=0; $i<24; $i++) {
                            $index1 = "played_num_".$i;
                            $index2 = "airtime_".$i;

                            $query_played_num .= $played_nums_airtime_arr[$index1].",";
                            $query_airtime .= (!empty($played_nums_airtime_arr[$index2]) && $played_nums_airtime_arr[$index2] > 0) ? $played_nums_airtime_arr[$index2]."," : "0,";
                            $query_percent .= "NULL,";
                        }

                        $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                        //-----------------------------------

                        //insert data in temp table
                        if($mysqli_rms_cache->query($query))
                            $result ++;
                    }
                }

            }
        }

        //calculate percetages by hours
        if($result > 0) {
            //calculate summarized played numbers for each hour
            $fields = "";
            for($i=0; $i<24; $i++) {
                $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
            }
            $fields = rtrim($fields, ",");

            $query = "SELECT $fields FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            $played_num_sum = array();
            if($r && $r->num_rows) {
                $played_num_sum = $r->fetch_array();
            }
            //-----------------------------

            //calculate percentages
            if(count($played_num_sum) > 0) {
                $query = "SELECT * FROM $table_temp ORDER BY id";
                $r = $mysqli_rms_cache->query($query);

                if($r && $r->num_rows) {
                    while($row = $r->fetch_assoc()) {
                        $id = $row['id'];
                        $percentages = array();

                        //calculate percentages and store in $percentages array
                        for($i=0; $i<24; $i++) {
                            $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                            $played_num = $row[$field];

                            $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                            $percentages[] = number_format($percentage, 4, '.', '');
                        }
                        //-----------------------

                        //generate update query
                        $query_start = "UPDATE $table_temp SET ";
                        $query_middle = "";
                        $query_end = " WHERE id=$id";

                        for($i=0; $i<24; $i++) {
                            $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                        }

                        $query_middle = rtrim($query_middle, ",");
                        $query = $query_start . $query_middle . $query_end;
                        //-------------------------

                        //update table
                        $mysqli_rms_cache->query($query);
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime_continent...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime_continent($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    //generate query to get all played numbers by hours grouped by continent_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_publisher_id, s.continent_code, s.continent_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.continent_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $continent_name = $row1['continent_name'];
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','".$mysqli_rms_cache->escape_str($continent_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }


                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(continent_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $continent_code = $row['continent_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE continent_code='$continent_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE continent_code='$continent_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime_country...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime_country($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.country_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $country_name = $row1['country_name'];
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','".$mysqli_rms_cache->escape_str($country_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }//while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(country_code) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE country_code='$country_code' ";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------
    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime_subdivision...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime_subdivision($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and subdivision_code
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.subdivision_name, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.subdivision_code";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $subdivision_name = $row1['subdivision_name'];
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($subdivision_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }// while
                    }
                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, subdivision_code FROM $table_temp GROUP BY country_code, subdivision_code";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $subdivision_code = $row['subdivision_code'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE subdivision_code='$subdivision_code' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime_city...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime_city($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    //generate query to get all played numbers by hours grouped by country_code and city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.city, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY s.country_code, s.city";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= ($row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT country_code, city FROM $table_temp GROUP BY country_code, city";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $country_code = $row['country_code'];
                    $city_name = $row['city'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE city='".$mysqli_rms_cache->escape_str($city_name)."' AND country_code='$country_code'";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}


/**
 * Update Tables started with 'publisher_played_by_daytime_station...'
 *
 * @param array $config_arr
 * @param object $mysqli_rms (stat db connection)
 * @param object $mysqli_rms_cache (cache db connection)
 * @param string $log_file
 * @param string $log_file_temp
 * @param bool $finalize_process (Optional, Default is 'true')
 * @param bool $update_track_charts (Optional, Default is 'true')
 * @return number
 */
function cug_rms_update_publisher_played_by_daytime_station($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, $finalize_process=true, $update_track_charts=true) {
    global $Tables, $ERRORS;
    $result = 0;

    $table      = $config_arr['tables']['table'];
    $table_temp = $config_arr['tables']['table_temp'];
    $stat_table = $config_arr['tables']['stat_table'];

    //check if it is time to calculate statistics data for current time period
    if(cug_rms_is_time_to_update_cache_table($mysqli_rms, $mysqli_rms_cache, $stat_table, $table, $config_arr['time_periods'])) {
        //select all unique publisher ids
        $query = cug_rms_query_get_distinct_publisher_ids($stat_table);
        $r = $mysqli_rms->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $cugate_publisher_id = $row['cugate_publisher_id'];


                //select data
                if($cugate_publisher_id > 0) {
                    //generate query to get all played numbers by hours grouped by city
                    $query_arr = array();

                    $query_start = "SELECT r.shenzhen_publisher_id, s.continent_code, s.country_code, s.subdivision_code, s.city, r.station_id, ";
                    $query_played_num = "";
                    $query_airtime = "";
                    $query_middle = "";
                    $query_end = " FROM $stat_table AS r ";
                    $query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
                    $query_end .= "WHERE r.cugate_publisher_id=$cugate_publisher_id ";
                    $query_end .= "AND r.duration>0 ";
                    $query_end .= "GROUP BY r.station_id";

                    for($i=0; $i<24; $i++) {
                        $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
                        $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

                        $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
                        $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
                    }

                    $query_airtime = rtrim($query_airtime, ",");
                    $query_middle = $query_played_num . $query_airtime;
                    $query = $query_start . $query_middle . $query_end;

                    //execute query
                    $played_nums_arr = array();
                    $r1 = $mysqli_rms->query($query);
                    if($r1 && $r1->num_rows) {
                        while($row1 = $r1->fetch_assoc()) {
                            $continent_code = $row1['continent_code'];
                            $country_code = $row1['country_code'];
                            $subdivision_code = $row1['subdivision_code'];
                            $city_name = $row1['city'];
                            $station_id = $row1['station_id'];
                            $shenzhen_publisher_id = $row1['shenzhen_publisher_id'];

                            //calculate total played number and total airtime
                            $total_played_num = 0;
                            $total_airtime = 0;
                            for($i=0; $i<24; $i++) {
                                $index1 = "played_num_".$i;
                                $index2 = "airtime_".$i;

                                $total_played_num += !empty($row1[$index1]) ? $row1[$index1] : 0;
                                $total_airtime += (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2] : 0;
                            }

                            //generate insert query
                            if($total_played_num > 0) { //insert only such of tracks which are played at least once
                                $query_start = "INSERT INTO $table_temp ";
                                $query_start .= "VALUES(NULL,$cugate_publisher_id,$shenzhen_publisher_id,'$continent_code','$country_code','$subdivision_code','".$mysqli_rms_cache->escape_str($city_name)."',$station_id,";
                                $query_played_num = "";
                                $query_airtime = "";
                                $query_percent = "";
                                $query_end = "NOW())";

                                for($i=0; $i<24; $i++) {
                                    $index1 = "played_num_".$i;
                                    $index2 = "airtime_".$i;

                                    $query_played_num .= $row1[$index1].",";
                                    $query_airtime .= (!empty($row1[$index2]) && $row1[$index2] > 0) ? $row1[$index2]."," : "0,";
                                    $query_percent .= "NULL,";
                                }

                                $query = $query_start . $query_played_num . $query_percent . $query_airtime . "$total_played_num," . "$total_airtime," . $query_end;
                                //-----------------------------------

                                //insert data in temp table
                                if($mysqli_rms_cache->query($query))
                                    $result ++;
                            }
                        }
                    }

                }

            }
        }


        //calculate percetages by hours
        if($result > 0) {
            $query = "SELECT DISTINCT(station_id) FROM $table_temp";
            $r = $mysqli_rms_cache->query($query);

            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $station_id = $row['station_id'];

                    //calculate summarized played numbers for each hour
                    $fields = "";
                    for($i=0; $i<24; $i++) {
                        $fields .= ($i < 10) ? "SUM(played_num_0$i)," : "SUM(played_num_$i),";
                    }
                    $fields = rtrim($fields, ",");

                    $query = "SELECT $fields FROM $table_temp ";
                    $query .= "WHERE station_id=$station_id";
                    $r1 = $mysqli_rms_cache->query($query);

                    $played_num_sum = array();
                    if($r1 && $r1->num_rows) {
                        $played_num_sum = $r1->fetch_array();
                    }
                    //-----------------------------

                    //calculate percentages
                    if(count($played_num_sum) > 0) {
                        $query = "SELECT * FROM $table_temp ";
                        $query .= "WHERE station_id=$station_id";
                        $r1 = $mysqli_rms_cache->query($query);

                        if($r1 && $r1->num_rows) {
                            while($row1 = $r1->fetch_assoc()) {
                                $id = $row1['id'];
                                $percentages = array();

                                //calculate percentages and store in $percentages array
                                for($i=0; $i<24; $i++) {
                                    $field = ($i < 10) ? "played_num_0$i" : "played_num_$i";
                                    $played_num = $row1[$field];

                                    $percentage = ($played_num_sum[$i] > 0) ? ($played_num * 100) / $played_num_sum[$i] : 0;
                                    $percentages[] = number_format($percentage, 4, '.', '');
                                }
                                //-----------------------

                                //generate update query
                                $query_start = "UPDATE $table_temp SET ";
                                $query_middle = "";
                                $query_end = " WHERE id=$id";

                                for($i=0; $i<24; $i++) {
                                    $query_middle .= ($i < 10) ? "percent_0$i=".$percentages[$i]."," : "percent_$i=".$percentages[$i].",";
                                }

                                $query_middle = rtrim($query_middle, ",");
                                $query = $query_start . $query_middle . $query_end;
                                //-------------------------

                                //update table
                                $mysqli_rms_cache->query($query);
                            }
                        }
                    }
                }
            }
        }
        //-------------------------------

    }
    else {
        $log_text = date("Y-m-d H:i:s")." - Table:".$table." - Status:".array_search($ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE'], $ERRORS).PHP_EOL;
        cug_rms_write_log($log_file_temp, $log_text);
    }

    //finalize cache table update
    if($finalize_process)
        cug_rms_finalize_cache_table_update($mysqli_rms_cache, $table, $table_temp, $new_status=1, $curr_process_id=2, $next_process_id=3, $result, $config_arr['time_periods'], $log_file, $log_file_temp);

    return $result;
}
?>