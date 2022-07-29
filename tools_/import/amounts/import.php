<?PHP
// Import-Update Amount Coefficients for Radio Stations

error_reporting(E_ALL);

include "config.php";
include "lib/db/mysqli.php";

//connect to db
$mysqli = new cug__mysqli($db_host, $db_user, $db_password, $db_name, $db_server_port, $db_ssl, $db_server_key, $db_server_cert, $db_ca_cert);


$FILE = "files/2015/extracted/GEMA -20170323.csv";
$START_DATE = '2017-01-01';
$END_DATE = '2017-12-31';

if($f = fopen($FILE, 'r')) {
    $counter = 1;
    
    while (($buffer = fgets($f)) !== false) {
        $row = substr($buffer, 0, strlen($buffer)-2);
        $arr = str_getcsv($row, ",");
        $arr = clean_array($arr);
        
        $culture_factor = !empty($arr[0]) ? $arr[0] : 0;
        $broadcaster_coefficient = !empty($arr[1]) ? $arr[1] : 0;
        $station_id = !empty($arr[2]) ? $arr[2] : 0;
        
        echo $counter."\t".$culture_factor."\t".$broadcaster_coefficient."\t".$station_id."\t";
        
        if($station_id > 0 && $culture_factor > 0 && $broadcaster_coefficient > 0) {
            //check station id
            $query = "SELECT id FROM cr_stations WHERE id=$station_id";
            $row = $mysqli->get_field_val("cr_stations","id", "id=$station_id");
            
            if(!empty($row[0]['id'])) {
                //check for existing entry
                $query = "SELECT id FROM amount_coefficient WHERE station_id=$station_id AND start_date='$START_DATE' AND end_date='$END_DATE'";
                $r = $mysqli->query($query);
                
                if($r && $r->num_rows) { //exists
                    $row = $r->fetch_assoc();
                    $coefficient_id = $row['id'];
                    
                    //update existing entry
                    $query = "UPDATE amount_coefficient SET culture_factor=$culture_factor, broadcaster_coefficient=$broadcaster_coefficient WHERE id=$coefficient_id";
                    $mysqli->query($query);
                    echo "OK_UPDATE";
                }
                else { //insert new entry
                    $query = "INSERT INTO amount_coefficient (station_id,start_date,end_date,culture_factor,broadcaster_coefficient) ";
                    $query .= "VALUES($station_id,'$START_DATE','$END_DATE',$culture_factor,$broadcaster_coefficient)";
                    
                    if($mysqli->query($query))
                        echo "OK_INSERT";
                    else
                        echo "ERR_INSERT";
                }
            }
            else {
                echo "ERR_WRONG_STATION_ID";
            }
        }
        else {
            echo "ERR_NOT_ENOUGH_FIELDS";
        }
        
        echo PHP_EOL;
        $counter ++;
    }
    
    fclose($f);
}




//-------------------------
function clean_array($arr)
{
    $result = array();

    foreach($arr as $key => $value) {
        $result[$key] = str_replace(array("\r\n", "\n", "\r"), '', trim($value));
    }

    return 	$result;
}


//close DB connection
$mysqli->close();
?>