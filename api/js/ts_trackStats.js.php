<?PHP
error_reporting(E_ALL);

include "globals.php";
include "lib/functions.php";
include "lib/xml.php";

ini_set('memory_limit', '-1');


$ACTION     = "3";
$PARAMS = "f=json";


// this is needed to allow access from other websites which are on different domains than 'stats.cugate.com'
// without this header javascript call (from other website) to API url will not be allowed
header('Access-Control-Allow-Origin: *');


$data = send_request($ACTION, $PARAMS);

$arr = json_decode($data, true);


//generate output
if(!count($arr)) { //no result
    echo "var ts_areaList = [];".PHP_EOL;
    echo "var ts_stationList = [];".PHP_EOL;
}
else { //OK
    $area_list = $arr['data']['result']['area_list'];
    $station_list = $arr['data']['result']['station_list'];
    
    //area list
    $area_text = "var ts_areaList = [";
    
    foreach($area_list as $continent) {//continents
        $continent_code = $continent[0];
        $continent_name = $continent[1];
        
        $area_text .= "[";
        $area_text .= "\"".$continent_code."\"".","."\"".$continent_name."\"".",";
        $area_text .= "[";
        
        foreach($continent[2] as $country) {//countries
            $country_code = $country[0];
            $country_name = $country[1];
            
            $area_text .= "[";
            $area_text .= "\"".$country_code."\"".","."\"".$country_name."\"".",";
            $area_text .= "[";
            
            foreach($country[2] as $subdivision) {//subdivisions
                $subdivision_code = $subdivision[0];
                $subdivision_name = $subdivision[1];
                
                $area_text .= "[";
                $area_text .= "\"".$subdivision_code."\"".","."\"".$subdivision_name."\"";
                $area_text .= "],";
                
            }//subdivision
            $area_text = rtrim($area_text, ",");
            $area_text .= "]],";
        }//country
        $area_text = rtrim($area_text, ",");
        $area_text .= "]],";
    }//continent
    
    $area_text = rtrim($area_text, ",");
    $area_text .= "];";
    
    echo $area_text.PHP_EOL.PHP_EOL;
    
    
    //station list
    //---------------------------
    $station_text = "var ts_stationList = [";
    
    foreach($station_list as $station) {
        $station_id = $station[0];
        $station_name = $station[1];
        
        $station_text .= "[";
        $station_text .= "\"".$station_id."\"".","."\"".$station_name."\"";
        $station_text .= "],";
    }
    
    $station_text = rtrim($station_text, ",");
    $station_text .= "];";
    
    echo $station_text.PHP_EOL;
}

?>