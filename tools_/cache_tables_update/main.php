<?PHP
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


//connect to DB
$mysqli_rms = new cug__mysqli($rms_db_host, $rms_db_user, $rms_db_password, $rms_db_name, $rms_db_server_port, $rms_db_ssl, $rms_db_server_key, $rms_db_server_cert, $rms_db_ca_cert);
$mysqli_rms_cache = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user, $rms_cache_db_password, $rms_cache_db_name, $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_rms_cache_global = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user_global, $rms_cache_db_password_global, $db_name="", $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_cug = new cug__mysqli($cug_db_host, $cug_db_user, $cug_db_password, $cug_db_name, $cug_db_server_port, $cug_db_ssl, $cug_db_server_key, $cug_db_server_cert, $cug_db_ca_cert);



//capture parameters
$ACTION = !empty($argv[1]) ? strtoupper($argv[1]) : ""; //example: 'CALC_STAT_DATA'
$FILTER = !empty($argv[2]) ? strtoupper($argv[2]) : ""; //example: 'TRACK_PLAYED_BY_COUNTRY'
$TIME_PERIOD = !empty($argv[3]) ? strtoupper($argv[3]) : ""; //example: 'LAST_7_DAYS'


switch($ACTION) {
    //----------------------------
    case 'EXTRACT_STAT_DATA':
    //----------------------------
        $log_file =  cug_rms_get_log_file_name($ACTION);
        $log_file_temp =  cug_rms_get_log_file_name($ACTION, $temp_log_file=1);
        cug_rms_extract_stat_data($log_file, $log_file_temp);       
    break;
    
    
    //----------------------------
    case 'CALC_STAT_DATA':
    //----------------------------
        $log_file =  cug_rms_get_log_file_name($ACTION, $temp_log_file=0, $FILTER, $TIME_PERIOD);
        $log_file_temp =  cug_rms_get_log_file_name($ACTION, $temp_log_file=1, $FILTER, $TIME_PERIOD);
        
        //prepare cache tables
        $config_arr = cug_rms_prepare_cache_tables($FILTER, $TIME_PERIOD, $log_file, $log_file_temp);
    
        //update cache tables
        if($config_arr > 0) {
            $function_name = "cug_rms_update_".strtolower($FILTER);
            call_user_func($function_name, $config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file, $log_file_temp, true, true);
        }
    
    break;    
    
    //----------------------------
    case 'RENAME_CACHE_TEMP_TABLES':
    //----------------------------
        $log_file =  cug_rms_get_log_file_name($ACTION);
        $log_file_temp =  cug_rms_get_log_file_name($ACTION, $temp_log_file=1);
        cug_rms_rename_cache_temp_tables($log_file, $log_file_temp);
    break;
    
    //----------------------------
    case 'TEST':
    //----------------------------
        cug_rms_update_track_charts($cache_table="track_played_total__last_7_days", $time_period="last_7_days");
    break;    
    
    
    //----------------------------
    default:
    //----------------------------    
        //****************   
    break;       
}


//close DB connections
$mysqli_rms->close();
$mysqli_rms_cache->close();
$mysqli_rms_cache_global->close();
$mysqli_cug->close();
?>