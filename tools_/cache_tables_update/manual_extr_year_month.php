<?PHP
/**
 * Extract statistical data into sub tables manually for specific time periods
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
$MONTHS = !empty($argv[2]) ? strtoupper($argv[2]) : 0; //MONTH Numbers, from 1 to 12, should be provided like: 1-2-3-4-5-6-7-8-9-10-11-12, or just 0 if you don't want to extract data for specific month

//connect to DB
$mysqli_rms = new cug__mysqli($rms_db_host, $rms_db_user, $rms_db_password, $rms_db_name, $rms_db_server_port, $rms_db_ssl, $rms_db_server_key, $rms_db_server_cert, $rms_db_ca_cert);



if($YEAR > 0) {
    //parth MONTHS
    $arr = explode("-", $MONTHS);
    $month_arr = array();
    foreach($arr as $val) {
        if($val > 0)
            $month_arr[] = $val;
    }
    
    //$month_arr = array(1,2,3,4,5,6,7,8,9,10,11,12);
    cug_rms_extract_year_month($mysqli_rms, $YEAR, $month_arr);
}
else {
    echo "'YEAR' was not provided !".PHP_EOL;
}


echo PHP_EOL."END: ".date("Y-m-d H:i:s").PHP_EOL;

/**
 * Extract data into subtables by YEAR and MONTH (All existing tables will be skipped)
 * 
 * @param object $db_conn
 * @param int $year
 * @param array $month_arr
 * @return void
 */
function cug_rms_extract_year_month($db_conn, $year, $month_arr) {
    global $Tables, $stat_table_index, $RMS_QUERY_MAKE_TABLE;
    
    if($year > 0) {
        $source_table_main = $Tables[$stat_table_index];
        $source_table_with_year = $source_table_main."__year_".$year;
        
        $source_table_exists = false;
        
        if(!$db_conn->table_exists($source_table_with_year)) {
            //create subtable by year
            $query = "CREATE TABLE $source_table_with_year ".$RMS_QUERY_MAKE_TABLE[$stat_table_index];
            $db_conn->query($query);
            
            //extract data and store in new subtable
            $time_periods = cug_rms_get_time_periods_by_year_month($year, $month=0);           
            if(cug_rms_extract_data($db_conn, $source_table_main, $source_table_with_year, $time_periods['start_timestamp'], $time_periods['end_timestamp'])) {
                $source_table_exists = true;
            }
        }
        else {
            $source_table_exists = true;
        }
        //-------------------
        
        if($source_table_exists) {
            echo $source_table_with_year." - OK".PHP_EOL;
           
            //create sub tables by month
            foreach($month_arr as $month) {
                $month_num = ($month<10) ? "0".$month : $month;
                $sub_table_month = $source_table_main."__year_".$year."__month_".$month_num;
                echo $sub_table_month." - OK".PHP_EOL;
                
                $sub_table_exists = false;
                
                if(!$db_conn->table_exists($sub_table_month)) {
                    //create subtable
                    $query = "CREATE TABLE $sub_table_month ".$RMS_QUERY_MAKE_TABLE[$stat_table_index];
                    $db_conn->query($query);
                    
                    //extract data and store in new subtable
                    $time_periods = cug_rms_get_time_periods_by_year_month($year, $month);

                    if(cug_rms_extract_data($db_conn, $source_table_with_year, $sub_table_month, $time_periods['start_timestamp'], $time_periods['end_timestamp'])) {
                        $sub_table_exists = true;
                    }
                }
                else {
                    $sub_table_exists = true;
                }
                //----------------------
                
                if($sub_table_exists)
                    echo $sub_table_month." - OK".PHP_EOL;
                else 
                    echo $sub_table_month." - ERROR".PHP_EOL;
                  
            }
        }
        else {
            echo $source_table_with_year." - ERROR".PHP_EOL;
        }
        
    }
}


/**
 * Extract Data into subtable
 * 
 * @param object $db_conn
 * @param string $main_stat_table
 * @param string $sub_table
 * @param int $start_timestamp
 * @param int $end_timestamp
 * @return boolean
 */
function cug_rms_extract_data($db_conn, $main_stat_table, $sub_table, $start_timestamp, $end_timestamp) {
    $result = false;
    
    $query = "INSERT INTO $sub_table ";
    $query .= "SELECT * FROM $main_stat_table WHERE played_date >= $start_timestamp and played_date <= $end_timestamp";
    echo $query;
    if($db_conn->query($query)) {
        $result = true;
    }
    
    return $result;
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




    
//close DB connections
$mysqli_rms->close();
?>