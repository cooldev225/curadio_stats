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

//echo date("Y-m-d H:i:s").PHP_EOL;
//echo date("Y-m-d", strtotime('last week monday')).PHP_EOL;
//cug_rms_send_notification_email("1", "AAAAAA");

/*
foreach($TIME_PERIODS as $time_period) {
    $time_periods = cug_rms_get_time_periods($time_period);
    print_r($time_periods);
}
*/

//echo PHP_EOL."OK".PHP_EOL;

//cug_rms_update_track_charts("track_played_total__last_7_days", "LAST_7_DAYS");
//cug_rms_copy_stations_table();


// Manual Calculation
/*
// - disable calculation time condition in the function
// - create relevant cache temp table
// - disable crontabs
$config_arr = array();
$config_arr['tables']['table'] = "";
$config_arr['tables']['table_temp'] = "track_played_total__this_month_tmp";
$config_arr['tables']['stat_table'] = "cr_detect_results__this_month";
cug_rms_update_track_played_total($config_arr, $mysqli_rms, $mysqli_rms_cache, $log_file="", $log_file_temp="", $finalize_process=false, $update_track_charts=false);
*/

/*
$query_start = "SELECT ";
$query_played_num = "";
$query_airtime = "";
$query_middle = "";
$query_end = " FROM cr_detect_results__last_30_days AS r ";
$query_end .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
$query_end .= "WHERE cugate_member_id=33862";

for($i=0; $i<24; $i++) {
    $start_time = ($i < 10) ? "0$i:00:00" : "$i:00:00";
    $end_time = (($i + 1) < 10) ? "0".($i + 1).":00:00" : ($i + 1).":00:00";

    $query_played_num .= "COUNT(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN 1 END) AS played_num_$i,";
    $query_airtime .= "SUM(CASE WHEN played_time >= '$start_time' AND played_time < '$end_time' THEN duration END) AS airtime_$i,";
}

$query_airtime = rtrim($query_airtime, ",");
$query_middle = "shenzhen_member_id," . $query_played_num . $query_airtime;
$query = $query_start . $query_middle . $query_end;

echo PHP_EOL.$query.PHP_EOL;
*/

//cug_rms_archive_subtable($time_period="last_year");
//cug_rms_rename_cache_temp_tables($log_file="", $log_file_temp="");
//cug_rms_archive_cache_table($filter="track_played_total", $time_period="last_month", $temp_table_rows_count=1, $delimiter="__");

//$status = cug_rms_archive_year();
//echo PHP_EOL.$status.PHP_EOL;
//array_search($status, $ERRORS);
//cug_rms_collect_archive_tables($last_year=2015);

//cug_rms_update_track_played_by_artist_test();
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

function cug_rms_update_track_played_by_artist_test() {
    global $mysqli_rms, $mysqli_rms_cache, $Tables, $ERRORS;
    $result = 0;

    $table      = 'track_played_by_artist__last_7_days';
    $table_temp = 'track_played_by_artist__last_7_days_tmp';
    $stat_table = 'cr_detect_results__last_7_days';


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
                    $query .= "GROUP BY cugate_track_id ";
                    $query .= "ORDER BY played_num DESC";
                    
                    //echo $query.PHP_EOL;

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

    
//close DB connections
$mysqli_rms->close();
$mysqli_rms_cache->close();
$mysqli_rms_cache_global->close();
$mysqli_cug->close();
?>