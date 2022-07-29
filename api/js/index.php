<?PHP
//error_reporting(E_ALL);

include "globals.php";
include "lib/functions.php";
include "lib/xml.php";
ini_set('memory_limit', '-1');

//----------------------
// Capture Parameters
//----------------------
$FORMAT 	= !empty($_REQUEST['f']) ? strtolower($_REQUEST['f']) : "";
$ACTION     = !empty($_REQUEST['a']) ? $_REQUEST['a'] : "";


//capture parameters
$PARAMS = "";

foreach($_REQUEST as $key=>$val) {
    if($key)
        $PARAMS .= $key."=".$val."&";
}

$PARAMS = rtrim($PARAMS, "&");


//log post data
/*
if($f = fopen("log_js_index.txt", "a")) {
    fwrite($f, @date("Y-m-d H:i:s").PHP_EOL);
    fwrite($f, $_SERVER['REMOTE_ADDR'].PHP_EOL);
    //fwrite($f, implode(PHP_EOL, $_POST));
    fwrite($f, serialize($_POST));
    fwrite($f, PHP_EOL.PHP_EOL);

    fclose($f);
}
 */

// this is needed to allow access from other websites which are on different domains than 'stats.cugate.com'
// without this header javascript call (from other website) to API url will not be allowed
header('Access-Control-Allow-Origin: *'); 


//----------------------
// Define output format
//----------------------
switch($FORMAT) {
    case $OUTPUT_FORMAT['xml']:
        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['xml'];
        header("Content-Type: application/xml");  
    break;
    //--------------------
    case $OUTPUT_FORMAT['json']: 
        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['json'];
        header("Content-Type: application/json"); 
    break;
    //--------------------
    case $OUTPUT_FORMAT['web']:
        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['web'];
        header("Content-Type: application/text");
    break;  
    //--------------------
    default:
        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['json'];
        header("Content-Type: application/json");
    break;    
}

echo send_request($ACTION, $PARAMS);

/*
//----------------------
// Check Client's Website (Host) if it is allowed to use statistical data
//----------------------
$http_origin = !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";

if(is_host_allowed($http_origin)) {
    echo send_request($ACTION, $PARAMS);
}
else {
    if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml'])
        echo "<error><code>-999</code><msg>NOT ALLOWED</msg></error>";
    elseif($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json'])
        echo "{\"error\":{\"code\":-999,\"msg\":\"NOT ALLOWED\"}}";
    elseif($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['web'])
        echo "[[\"error\", \"-999\", \"NOT ALLOWED\"]]";
}
*/
?>
