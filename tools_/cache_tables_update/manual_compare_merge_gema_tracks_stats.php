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

//compare and merge 2 statistics results from 'temp_gema_tracks_stats' and 'temp_gema_imagem_311_tracks_2015' tables into 'temp_gema_tracks_stats_merged_2015' table

echo "START: ".date("Y-m-d H:i:s").PHP_EOL.PHP_EOL;

//connect to DB
$mysqli_rms = new cug__mysqli($rms_db_host, $rms_db_user, $rms_db_password, $rms_db_name, $rms_db_server_port, $rms_db_ssl, $rms_db_server_key, $rms_db_server_cert, $rms_db_ca_cert);
$mysqli_rms_cache = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user, $rms_cache_db_password, $rms_cache_db_name, $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_rms_cache_global = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user_global, $rms_cache_db_password_global, $db_name="", $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);
$mysqli_cug = new cug__mysqli($cug_db_host, $cug_db_user, $cug_db_password, $cug_db_name, $cug_db_server_port, $cug_db_ssl, $cug_db_server_key, $cug_db_server_cert, $cug_db_ca_cert);


//direction 1 - from 'temp_gema_tracks_stats' to 'temp_gema_imagem_311_tracks_2015'
$query = "SELECT * FROM temp_gema_tracks_stats";
$r = $mysqli_rms_cache->query($query);
while($row = $r->fetch_assoc()) {
    $work_id = $row['work_id'];
    $play_date_from = $row['play_date_from'];
    $station_code = !empty($row['station_code']) ? "'".$row['station_code']."'" : "NULL";
    $station_code_str = ($station_code == "NULL") ? " IS NULL" : "=".$station_code;
    $station_name = $row['station_name'];
    $cug_airtime = $row['airtime'];
    $cug_play_num = $row['play_num'];
    $cug_track_id = $row['cug_track_id'];
    $cug_station_id = $row['cug_station_id'];
    
    $query = "SELECT *, sum(airtime) AS total_airtime, sum(play_num) AS total_play_num FROM temp_gema_imagem_311_tracks_2015 WHERE work_id=$work_id AND play_date_from='$play_date_from' AND station_code$station_code_str";
    $r1 = $mysqli_rms_cache->query($query);
    $row1 = $r1->fetch_assoc();
    
    if(!empty($row1['id'])) {  
        $composer = $row1['composer'];
        $title = $row1['title'];
        $gema_airtime = $row1['total_airtime'];
        $gema_play_num = $row1['total_play_num'];
    }
    else {
        $arr = get_composer_and_title($work_id);        
        $composer = $arr['composer'];
        $title = $arr['title'];
        $gema_airtime = "NULL";
        $gema_play_num = "NULL";
    }
    
    //insert into merged table
    $query = "SELECT id FROM temp_gema_tracks_stats_merged_2015 WHERE work_id=$work_id AND play_date_from='$play_date_from' AND station_code$station_code_str";
    
    $r2 = $mysqli_rms_cache->query($query);
    if($r2->num_rows == 0) {
        $query = "INSERT INTO temp_gema_tracks_stats_merged_2015 VALUES(";
        $query .= "NULL,'".$mysqli_rms_cache->escape_str($composer)."','".$mysqli_rms_cache->escape_str($title)."',$work_id,'".$mysqli_rms_cache->escape_str($station_name)."',$station_code,'$play_date_from','$play_date_from',$gema_airtime,$gema_play_num,$cug_airtime,$cug_play_num,$cug_track_id,$cug_station_id,NULL,NULL,NULL,NULL,NOW()";
        $query .= ")";
        
        if(!$mysqli_rms_cache->query($query)) {
            echo "ERR - ".$query.PHP_EOL;
        }
    }

}


//direction 2 - from 'temp_gema_imagem_311_tracks_2015' to 'temp_gema_tracks_stats'
$query = "SELECT *, sum(airtime) AS total_airtime, sum(play_num) AS total_play_num FROM temp_gema_imagem_311_tracks_2015 GROUP BY work_id, station_code, play_date_from";
$r = $mysqli_rms_cache->query($query);
while($row = $r->fetch_assoc()) {
    $work_id = $row['work_id'];
    $play_date_from = $row['play_date_from'];
    $station_code = !empty($row['station_code']) ? "'".$row['station_code']."'" : "NULL";
    $station_code_str = ($station_code == "NULL") ? " IS NULL" : "=".$station_code;
    $station_name = $row['cug_station_name'];
    $gema_airtime = $row['total_airtime'];
    $gema_play_num = $row['total_play_num'];
    $composer = $row['composer'];
    $title = $row['title'];

    $query = "SELECT * FROM temp_gema_tracks_stats WHERE work_id=$work_id AND play_date_from='$play_date_from' AND station_code$station_code_str";
    $r1 = $mysqli_rms_cache->query($query);
    $row1 = $r1->fetch_assoc();
    
    if(!empty($row1['id'])) {  
        $row1 = $r1->fetch_assoc();

        $cug_airtime = $row1['airtime'];
        $cug_play_num = $row1['play_num'];
        $cug_track_id = $row1['cug_track_id'];
        $cug_station_id = $row1['cug_station_id'];
    }
    else {
        $cug_airtime = "NULL";
        $cug_play_num = "NULL";
        $cug_track_id = get_cug_track_id($work_id);
        $cug_station_id = get_cug_station_id($station_code);
    }

    //insert into merged table
    $query = "SELECT id FROM temp_gema_tracks_stats_merged_2015 WHERE work_id=$work_id AND play_date_from='$play_date_from' AND station_code$station_code_str";
    $r2 = $mysqli_rms_cache->query($query);
    
    if($r2->num_rows == 0) {
        $query = "INSERT INTO temp_gema_tracks_stats_merged_2015 VALUES(";
        $query .= "NULL,'".$mysqli_rms_cache->escape_str($composer)."','".$mysqli_rms_cache->escape_str($title)."',$work_id,'".$mysqli_rms_cache->escape_str($station_name)."',$station_code,'$play_date_from','$play_date_from',$gema_airtime,$gema_play_num,$cug_airtime,$cug_play_num,$cug_track_id,$cug_station_id,NULL,NULL,NULL,NULL,NOW()";
        $query .= ")";
        
        if(!$mysqli_rms_cache->query($query)) {
            echo "ERR - ".$query.PHP_EOL;
        }
    }
}



//---------------------------
function get_composer_and_title($work_id) {
    global $mysqli_rms_cache;
    $result = array();
    $result['composer'] = "";
    $result['title'] = "";

    if($work_id > 0) {
        $r = $mysqli_rms_cache->query("SELECT composer,title FROM temp_gema_imagem_311_tracks_2015 WHERE work_id=$work_id");
        if($r && $r->num_rows){
            $row = $r->fetch_assoc();
            $result['composer'] = $row['composer'];
            $result['title'] = $row['title'];
        }
    }

    return $result;
}


//---------------------------
function get_cug_track_id($work_id) {
    global $mysqli_rms_cache;
    $result = "NULL";
    
    if($work_id > 0) {
        $r = $mysqli_rms_cache->query("SELECT cug_track_id FROM temp_gema_tracks_stats WHERE work_id=$work_id LIMIT 1");
        if($r && $r->num_rows){
            $row = $r->fetch_assoc();
            $result = $row['cug_track_id'];
        }
    }
    
    return $result;
}


//---------------------------
function get_cug_station_id($station_code) {
    global $mysqli_rms_cache;
    $result = "NULL";

    if($station_code && $station_code != "NULL") {
        $r = $mysqli_rms_cache->query("SELECT station_id FROM temp_gema_stations WHERE gema_station_code=$station_code LIMIT 1");
        if($r && $r->num_rows){
            $row = $r->fetch_assoc();
            $result = $row['station_id'];
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