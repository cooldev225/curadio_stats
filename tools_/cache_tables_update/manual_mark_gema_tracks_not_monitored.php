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



$query = "SELECT id, cug_track_id, cug_station_id, play_date_from FROM temp_gema_tracks_stats_merged_2015 WHERE cug_airtime IS NULL";
$r = $mysqli_rms_cache->query($query);
while($row = $r->fetch_assoc()) {
    $id = $row['id'];
    $cug_station_id = $row['cug_station_id'];
    $cug_track_id = $row['cug_track_id'];
    $play_date = $row['play_date_from'];
    
    $station_was_monitored = check_station_if_monitored($cug_station_id, $play_date);
    $track_was_monitored = check_track_if_monitored($cug_track_id, $play_date);
    
    $query = "UPDATE temp_gema_tracks_stats_merged_2015 SET cug_station_monitored=$station_was_monitored, cug_track_monitored=$track_was_monitored WHERE id=$id";
    
    $mysqli_rms_cache->query($query);
}




//---------------------------
function check_station_if_monitored($cug_station_id, $play_date) {
    global $mysqli_rms;
    $result = 0;

    if($cug_station_id > 0) {
        $START_TIMESTAMP   = strtotime("$play_date 00:00:00") * 1000;
        $END_TIMESTAMP     = strtotime("$play_date 23:59:59") * 1000;
        
        $r = $mysqli_rms->query("SELECT id FROM cr_detect_results WHERE station_id=$cug_station_id AND played_date>=$START_TIMESTAMP AND played_date<=$END_TIMESTAMP");
        if($r->num_rows > 0){
            $result = 1;
        }
    }

    return $result;
}

//---------------------------
function check_track_if_monitored($cug_track_id, $play_date) {
    global $mysqli_rms;
    $result = 0;

    if($cug_track_id > 0) {
        $START_TIMESTAMP   = strtotime("$play_date 00:00:00") * 1000;
        $END_TIMESTAMP     = strtotime("$play_date 23:59:59") * 1000;
        
        $r = $mysqli_rms->query("SELECT id FROM cr_detect_results WHERE cugate_track_id=$cug_track_id AND played_date>=$START_TIMESTAMP AND played_date<=$END_TIMESTAMP");
        if($r->num_rows > 0){
            $result = 1;
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