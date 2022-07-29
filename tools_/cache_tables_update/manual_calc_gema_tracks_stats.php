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


$START_DATE_TIMESTAMP   = strtotime('2015-01-01 00:00:00') * 1000;
$END_DATE_TIMESTAMP     = strtotime('2015-12-31 23:59:59') * 1000;

//get track ids
$query = "SELECT gema.cug_track_id, gema.gema_work_id, r.id AS rms_id FROM temp_gema_imagem_matched_tracks AS gema ";
$query .= "LEFT JOIN cr_detect_results AS r ON gema.cug_track_id=r.cugate_track_id ";
$query .= "WHERE r.id IS NOT NULL AND r.played_date >= $START_DATE_TIMESTAMP AND r.played_date <= $END_DATE_TIMESTAMP ";
$query .= "GROUP BY gema.cug_track_id";

$r = $mysqli_rms->query($query);
while($row = $r->fetch_assoc()) {
    $cug_track_id = $row['cug_track_id'];
    $gema_work_id = $row['gema_work_id'];
    
    //get stats
    $query = "SELECT r.cugate_track_id, r.station_id, gs.gema_station_code, s.station_name, FROM_UNIXTIME(r.played_date / 1000, '%Y-%m-%d') AS played_date, r.duration, SUM(r.duration) AS airtime, COUNT(r.id) AS played_num ";
    $query .= "FROM cr_detect_results AS r ";
    $query .= "INNER JOIN cr_stations AS s ON r.station_id=s.id AND s.country_code='DE' ";
    $query .= "LEFT JOIN temp_gema_stations AS gs ON r.station_id=gs.station_id ";
    $query .= "WHERE r.cugate_track_id=$cug_track_id AND r.played_date >= $START_DATE_TIMESTAMP AND r.played_date <= $END_DATE_TIMESTAMP ";
    $query .= "GROUP BY r.station_id, FROM_UNIXTIME(r.played_date / 1000, '%Y-%m-%d')";
    
    //echo $query.PHP_EOL;
    
    $r1 = $mysqli_rms->query($query);
    if($r1 && $r1->num_rows) {
        while($row1 = $r1->fetch_assoc()) {
            $gema_station_code = !empty($row1['gema_station_code']) ? "'".$row1['gema_station_code']."'" : "NULL";
            
            //get gema composer(s)
            $composers = "";
            $query = "SELECT composer FROM temp_gema_imagem WHERE work_id=$gema_work_id";
            $r2 = $mysqli_rms->query($query);
            if($r2 && $r2->num_rows) {
                while($row2 = $r2->fetch_assoc()) {
                    $composers .= $row2['composer'].", ";
                }
            }
            
            $composers = rtrim($composers, ", ");
            
            if($composers) { 
                $composers = "'".$mysqli_rms->escape_str($composers)."'"; 
            }
            else {
                $composers = "NULL";
            }
            //------------------------------------
            
            //get gema track title
            $query = "SELECT track_title FROM temp_gema_imagem WHERE work_id=$gema_work_id";
            $r2 = $mysqli_rms->query($query);
            $row2 = $r2->fetch_assoc();
            $gema_track_title = "'".$mysqli_rms->escape_str($row2['track_title'])."'";
            
            //insert data in table
            $query = "INSERT INTO temp_gema_tracks_stats VALUES(";
            $query .= "NULL,$composers,$gema_track_title,$gema_work_id,'".$mysqli_rms->escape_str($row1['station_name'])."',".$gema_station_code.",'{$row1['played_date']}','{$row1['played_date']}',{$row1['airtime']},{$row1['played_num']},$cug_track_id,{$row1['duration']},{$row1['station_id']},NOW())";
            
            //echo $query.PHP_EOL;
            $mysqli_rms->query($query);
        }
    }
    
    //break;
}

//close DB connections
$mysqli_rms->close();
$mysqli_rms_cache->close();
$mysqli_rms_cache_global->close();
$mysqli_cug->close();
?>