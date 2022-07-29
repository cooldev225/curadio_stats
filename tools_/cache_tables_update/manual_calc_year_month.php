<?PHP
/**
 * Calculate statistical data manually for specific time periods
 * 
 */
error_reporting(E_ALL);

include "config.php";
include "error_codes.php";

include "lib/db/mysqli.php";
include "lib/queries.php";
include "lib/global_func.php";
include "lib/rms_stat_cache.php";
include "lib/rms_stat_data.php";

include "lib/mail/PHPMailerAutoload.php";
include "globals.php";


echo "START: ".date("Y-m-d H:i:s").PHP_EOL.PHP_EOL;

//capture parameters
$YEAR = !empty($argv[1]) ? strtoupper($argv[1]) : 0; //YEAR, like 2015
$MONTH = !empty($argv[2]) ? strtoupper($argv[2]) : 0; //MONTH Number, from 1 to 12 or 0 if CALC_MOD is 'YEAR'
$CALC_MODE = !empty($argv[3]) ? strtoupper($argv[3]) : ""; //'YEAR' - calculate stat data only for whole year; 'MONTH' - calculate stat data only for month; 'BOTH' - calculate data for whole year and month;

//connect to DB
$mysqli_rms = new cug__mysqli($rms_db_host, $rms_db_user, $rms_db_password, $rms_db_name, $rms_db_server_port, $rms_db_ssl, $rms_db_server_key, $rms_db_server_cert, $rms_db_ca_cert);
$mysqli_rms_cache = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user, $rms_cache_db_password, $rms_cache_db_name, $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_rms_cache_global = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user_global, $rms_cache_db_password_global, $db_name="", $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_cug = new cug__mysqli($cug_db_host, $cug_db_user, $cug_db_password, $cug_db_name, $cug_db_server_port, $cug_db_ssl, $cug_db_server_key, $cug_db_server_cert, $cug_db_ca_cert);


if($YEAR > 0) {
    if(strtoupper($CALC_MODE) == 'YEAR' || strtoupper($CALC_MODE) == 'MONTH' || strtoupper($CALC_MODE) == 'BOTH') {
        //$month_arr = array(1,2,3,4,5,6,7,8,9,10,11,12);
        $month_arr = ($MONTH > 0) ? array($MONTH) : array();
        cug_rms_calc_year_month($source_db_conn=$mysqli_rms, $dest_db_conn=$mysqli_rms_cache_global, $YEAR, $month_arr, $CALC_MODE);
    }
    else {
        echo "Unknown CALC_MODE !".PHP_EOL;
    }
}
else {
    echo "'YEAR' was not provided !".PHP_EOL;
}


echo PHP_EOL."END: ".date("Y-m-d H:i:s").PHP_EOL;


/**
 * Calculate Statistical Data for specific Year or/and Months
 * (Archive DB will be created if it is not exists, function will skip calculation for already existing archive tables even if they are empty)
 * 
 * @param object $source_db_conn
 * @param object $dest_db_conn
 * @param int $year
 * @param array $month_arr (Array of month numbers from 1 to 12, for which months you want to calculate stat data)
 * @param bool $calc_mode (Calculation Mode, if 'YEAR' calculates stat data only for whole year, like: 'track_played_total__year_2015'; if 'MONTH' calculates data only for months, like: 'track_played_total__month_01'; if 'BOTH' calcaulates data for whole year and months; Default is 'BOTH')
 */
function cug_rms_calc_year_month($source_db_conn, $dest_db_conn, $year, $month_arr=array(), $calc_mode='BOTH') {
    global $Tables, $stat_table_index, $DB, $RMS_QUERY_MAKE_TABLE;
    
    if($year > 0) {
        $archive_db = $DB['archive_db_prefix'].$year;
        
        //attempt to create archive database
        $archive_db_exists = cug_rms_create_archive_db($dest_db_conn, $archive_db);
        
        if($archive_db_exists) {
            //get unique filter names
            $filters_arr = cug_rms_get_unique_filters();
            
            
            //collect all archive tables
            //--------------------------------
            $archive_tables = array();
            $index = 0;
            
            //tables by years
            if(strtoupper($calc_mode) == 'YEAR' || strtoupper($calc_mode) == 'BOTH') {
                foreach($filters_arr as $filter) {
                    $archive_tables[$index]['table_name'] = strtolower($filter)."__year_".$year;
                    $archive_tables[$index]['filter'] = $filter;
                    $archive_tables[$index]['month'] = 0;
                    $archive_tables[$index]['month_num'] = "";
                    $index ++;
                }
            }
            
            //tables by months
            if(strtoupper($calc_mode) == 'MONTH' || strtoupper($calc_mode) == 'BOTH') {
                if(count($month_arr) > 0) {
                    foreach($filters_arr as $filter) {
                        foreach($month_arr as $month) {
                            $month_num = ($month<10) ? "0".$month : $month;
                            
                            $archive_tables[$index]['table_name'] = strtolower($filter)."__month_".$month_num;
                            $archive_tables[$index]['filter'] = $filter;
                            $archive_tables[$index]['month'] = $month;
                            $archive_tables[$index]['month_num'] = $month_num;
                            $index ++;
                        }
                    }
                }
            }
            //----------------------------
            
            
            //start archive process
            foreach($archive_tables as $archive_table) {
                $archive_table_name = $archive_table['table_name'];

                if(!$dest_db_conn->table_exists_in_db($archive_db, $archive_table_name)) {//if archive table does not exists
                    //create archive table
                    $archive_table_name_with_db = $archive_db.".".$archive_table_name;
                    $query = "CREATE TABLE $archive_table_name_with_db ".$RMS_QUERY_MAKE_TABLE[strtolower($archive_table['filter'])];
                    
                    if($dest_db_conn->query($query)) { 
                        //define configuration info
                        $config_arr = array();
                        
                        $time_periods = cug_rms_get_time_periods_by_year_month($year, $archive_table['month']);                           
                        $config_arr['time_periods'] = $time_periods;
                        
                        $config_arr['tables']['table'] = $archive_table_name_with_db;
                        $config_arr['tables']['table_temp'] = $archive_table_name_with_db;
                        
                        $source_stat_table = $Tables[$stat_table_index]."__year_".$year;
                        $source_stat_table.= ($archive_table['month_num']) ? "__month_".$archive_table['month_num'] : "";
                        $config_arr['tables']['stat_table'] = $source_stat_table;

                        
                        //calculate stat data
                        if($source_db_conn->table_exists($source_stat_table)) {
                            $function_name = "cug_rms_update_".strtolower($archive_table['filter']);
                            echo "call >> " . $function_name . PHP_EOL;
                            call_user_func($function_name, $config_arr, $source_db_conn, $dest_db_conn,  "", "", false, false);
                            
                            echo $archive_table_name_with_db.PHP_EOL;
                        }
  
                    }
                    
                }
                
            }
            
        }
    
    }
}


/**
 * Get time periods by year or/and month
 * 
 * @param int $year
 * @param int $month
 * @return array
 */
function cug_rms_get_time_periods_by_year_month($year, $month) {
    $result = array();
    
    if($year) {
        if($month > 0) {
            $month_num = ($month < 10) ? "0".$month : $month;
            
            $result['start_date'] = $year."-".$month_num."-01";
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
            $d = new DateTime($result['start_date']);
            $total_days_in_month = $d->format('t');
            
            $result['end_date'] = $year."-".$month_num."-".$total_days_in_month;
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        }
        else { //whole year
            $result['start_date'] = $year."-01-01";
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
            $result['end_date'] = $year."-12-31";
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
        }
    }
    
    return $result;
}


/**
 * Get Unique Filter Names
 * 
 * @param bool $all (if 'false' then returns unique filters only for enabled tables, if 'true' then returns all unique filters, default is 'false')
 * @return array
 */
function cug_rms_get_unique_filters($all=false) {
    global $mysqli_rms_cache, $Tables;
    $result = array();
    $index = 0;
    
    $query = "SELECT table_name FROM {$Tables['cache_table']}";
    $query .= (!$all) ? " WHERE enabled=1" : "";
    $r = $mysqli_rms_cache->query($query);
    
    if($r && $r->num_rows) {
        while($row = $r->fetch_assoc()) {
            $cache_table = $row['table_name'];            
            $arr = cug_rms_split_cache_table_name($cache_table, $delimiter="__");
            $filter = $arr['filter'];
            
            if(array_search($filter, $result) === false) {
                $result[$index] = $filter;
                $index ++;
            }
        }
    }
    
    return $result;
}


/**
 * Create Archive DB
 * 
 * @param object $db_conn
 * @param string $arcive_db
 * @return boolean
 */
function cug_rms_create_archive_db($db_conn, $arcive_db) {  
    $result = false;
    
    if(!$db_conn->db_exists($arcive_db)) {
        if($db_conn->create_db($arcive_db, $char_set="utf8", $collation="utf8_general_ci")) {
            $result = true;
        }
    }
    else{
        $result = true;
    }
    
    return $result;
}

    
//close DB connections
$mysqli_rms->close();
$mysqli_rms_cache->close();
$mysqli_rms_cache_global->close();
$mysqli_cug->close();
?>