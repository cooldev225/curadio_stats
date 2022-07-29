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

//mark rows in 'temp_gema_tracks_stats_merged_2015' if station was not monitored at all at specific date or track was not monitored at spesific date

echo "START: ".date("Y-m-d H:i:s").PHP_EOL.PHP_EOL;

//connect to DB
$mysqli_rms = new cug__mysqli($rms_db_host, $rms_db_user, $rms_db_password, $rms_db_name, $rms_db_server_port, $rms_db_ssl, $rms_db_server_key, $rms_db_server_cert, $rms_db_ca_cert);
$mysqli_rms_cache = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user, $rms_cache_db_password, $rms_cache_db_name, $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_rms_cache_global = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user_global, $rms_cache_db_password_global, $db_name="", $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_cug = new cug__mysqli($cug_db_host, $cug_db_user, $cug_db_password, $cug_db_name, $cug_db_server_port, $cug_db_ssl, $cug_db_server_key, $cug_db_server_cert, $cug_db_ca_cert);



$query = "SELECT id, cug_station_id, gema_airtime, cug_airtime FROM temp_gema_tracks_stats_merged_2015 ORDER BY id";
$r = $mysqli_rms_cache->query($query);
while($row = $r->fetch_assoc()) {
    $id = $row['id'];
    $cug_station_id = $row['cug_station_id'];
    $gema_airtime = !empty($row['gema_airtime']) ? $row['gema_airtime'] : 0;
    $cug_airtime = !empty($row['cug_airtime']) ? $row['cug_airtime'] : 0;
    
    if($gema_airtime > 0) {
        $gema_amount_composer = get_amount_composer($cug_station_id, $gema_airtime, $year=2015);
        
        $query = "UPDATE temp_gema_tracks_stats_merged_2015 SET gema_amount_composer='$gema_amount_composer' WHERE id=$id";   
        $mysqli_rms_cache->query($query);
    }
    //-------------------------
    if($cug_airtime > 0) {
        $cug_amount_composer = get_amount_composer($cug_station_id, $cug_airtime, $year=2015);
        
        $query = "UPDATE temp_gema_tracks_stats_merged_2015 SET cug_amount_composer='$cug_amount_composer' WHERE id=$id";
        $mysqli_rms_cache->query($query);
    }
    
}




//---------------------------
function get_composer_and_title($work_id) {
    global $mysqli_rms_cache;
    $result = array();
    $result['composer'] = "";
    $result['title'] = "";

    if($work_id > 0) {
        $r = $mysqli_rms_cache->query("SELECT composer,title FROM temp_gema_imagem_311_tracks_2015 WHERE work_id=$work_id LIMIT 1");
        if($r && $r->num_rows){
            $row = $r->fetch_assoc();
            $result['composer'] = $row['composer'];
            $result['title'] = $row['title'];
        }
    }

    return $result;
}


//---------------------------
function get_amount_composer($station_id, $airtime, $year) {
    global $mysqli_rms_cache;
    $result = 0;
    
    //get coefficients
    $start_date = $year."-01-01";
    $end_date = $year."-12-31";
    
    $r = $mysqli_rms_cache->query("SELECT * FROM amount_coefficient WHERE station_id=$station_id AND start_date='$start_date' AND end_date='$end_date'");
    if($r && $r->num_rows){
        $row = $r->fetch_assoc();
        $culture_factor = $row['culture_factor'];
        $broadcaster_coefficient = $row['broadcaster_coefficient'];
        
        if($culture_factor > 0 && $broadcaster_coefficient > 0) {
            //get amount for per minute
            $r1 = $mysqli_rms_cache->query("SELECT * FROM amount_price_composer WHERE start_date='$start_date' AND end_date='$end_date'");
            if($r1 && $r1->num_rows){
                $row1 = $r1->fetch_assoc();
                $time_interval_sec = $row1['time_interval_sec'];
                $amount = $row1['amount'];
                
                if($time_interval_sec > 0 && $amount > 0) {
                    $result = $culture_factor * $broadcaster_coefficient * ($airtime / $time_interval_sec) * $amount;
                }
            }
        }
    }
    
    return $result;
}


//close DB connections
$mysqli_rms->close();
$mysqli_rms_cache->close();
$mysqli_rms_cache_global->close();
$mysqli_cug->close();

echo PHP_EOL."END: ".date("Y-m-d H:i:s").PHP_EOL;
?>