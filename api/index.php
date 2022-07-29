<?PHP
session_start();
//error_reporting(E_ALL);

$OUTPUT_FORMAT['xml']	= 'xml';
$OUTPUT_FORMAT['json']	= 'json';
$OUTPUT_FORMAT['web']   = 'web'; //for website, used for $ACTIONS['GET_STAT_DATA']

$ROOT_SUCCESS_NODE 		= 'data';
$ROOT_ERROR_NODE 		= 'error';

	/*
	if(!empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
		if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
	}
	*/
$dev_f = fopen("log_dev.txt", "a");
fwrite($dev_f, "Starting action={$_POST['action']}".PHP_EOL);

//log post data
if($f = fopen("log_index.txt", "a")) {
    fwrite($f, @date("Y-m-d H:i:s").PHP_EOL);
    fwrite($f, $_SERVER['REMOTE_ADDR'].PHP_EOL);
    //fwrite($f, implode(PHP_EOL, $_POST));
    fwrite($f, serialize($_POST));
    fwrite($f, PHP_EOL.PHP_EOL);
    fclose($f);
}
$API_VERSION 		= !empty($_SESSION['API_VERSION']) ? $_SESSION['API_VERSION'] : (!empty($_POST['v']) ? $_POST['v'] : "");
$API_BUILD 			= !empty($_SESSION['API_BUILD'])   ? $_SESSION['API_BUILD']   : (!empty($_POST['b']) ? $_POST['b'] : "");
$API_OUTPUT_FORMAT 	= !empty($_POST['f']) ? strtolower($_POST['f']) : "";

include dirname(dirname(__FILE__))."/CuLib/lib/xml.php";
//include "functions.php";

//----------------------
// Define output format
//----------------------
switch($API_OUTPUT_FORMAT) {
	case $OUTPUT_FORMAT['xml']:  header("Content-Type: application/xml");  break;
	case $OUTPUT_FORMAT['json']: header("Content-Type: application/json"); break;
	default: 
		$API_OUTPUT_FORMAT = "xml";
		header("Content-Type: application/xml");
	break;
}
	


//----------------------
// Check Authorize
//----------------------
require_once 'server.php';

//Delete old (expired) tokens
$storage->deleteOldTokens($server->config['access_lifetime']);
$strong_pass = false;
if(isset($_POST['mode']) && $_POST['mode']=='developer!@#')$strong_pass = true;
if ((!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) && !$strong_pass) {	
	$response = array();
	$response[$ROOT_ERROR_NODE]['code'] = -1;
	$response[$ROOT_ERROR_NODE]['msg']  = "Request is not authorized";
	
		if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
		
			echo cug_xml_start_tag($ROOT_ERROR_NODE, array(), $prefix="", PHP_EOL);
		
			echo cug_xml_start_tag("code", array(), "\t");
			echo cug_xml_entities($response['error']['code']);
			echo cug_xml_end_tag("code", "", PHP_EOL);
			
			echo cug_xml_start_tag("msg", array(), "\t");
			echo cug_xml_entities($response['error']['msg']);
			echo cug_xml_end_tag("msg", "", PHP_EOL);		
		
			echo cug_xml_end_tag($ROOT_ERROR_NODE);
		}
		else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
			echo json_encode($response);
		}	
	
	
	//$server->getResponse()->send();
	exit();
}

//-------------------
// Check Session
//-------------------
fwrite($dev_f, '	session is empty? '.(empty($_SESSION['CLIENT_ID'])?"Yes":"No").PHP_EOL);
if(empty($_SESSION['CLIENT_ID'])) {
	if(empty($_POST['action']) || $_POST['action'] != 1) {
		$response = array();
		$response[$ROOT_ERROR_NODE]['code'] = -2;
		$response[$ROOT_ERROR_NODE]['msg']  = "Session has expired";

			if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
			
				echo cug_xml_start_tag($ROOT_ERROR_NODE, array(), $prefix="", PHP_EOL);
			
				echo cug_xml_start_tag("code", array(), "\t");
				echo cug_xml_entities($response['error']['code']);
				echo cug_xml_end_tag("code", "", PHP_EOL);
								
				echo cug_xml_start_tag("msg", array(), "\t");
				echo cug_xml_entities($response['error']['msg']);
				echo cug_xml_end_tag("msg", "", PHP_EOL);
			
				echo cug_xml_end_tag($ROOT_ERROR_NODE);
			}
			else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
				echo json_encode($response);
			}
					
		exit();			
	}
}


//-----------------------------
// Check API Version and Build
//-----------------------------
fwrite($dev_f,"	API_VERSION={$API_VERSION}, API_BUILD={$API_BUILD}".PHP_EOL);
if($API_VERSION && $API_BUILD) {
	$app_path = __DIR__ . DIRECTORY_SEPARATOR . "v" . $API_VERSION . DIRECTORY_SEPARATOR . $API_BUILD;
		if(!file_exists($app_path)) {
			$response = array();
			$response[$ROOT_ERROR_NODE]['code'] = -3;
			$response[$ROOT_ERROR_NODE]['msg']  = "Wrong API_VERSION or API_BUILD";
			
				if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
				
					echo cug_xml_start_tag($ROOT_ERROR_NODE, array(), $prefix="", PHP_EOL);
				
					echo cug_xml_start_tag("code", array(), "\t");
					echo cug_xml_entities($response['error']['code']);
					echo cug_xml_end_tag("code", "", PHP_EOL);
					echo cug_xml_start_tag("msg", array(), "\t");
					echo cug_xml_entities($response['error']['msg']);
					echo cug_xml_end_tag("msg", "", PHP_EOL);
				
					echo cug_xml_end_tag($ROOT_ERROR_NODE);
				}
				else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
					echo json_encode($response);
				}				
						
			exit();
		}
}
else {
	$response = array();
	$response[$ROOT_ERROR_NODE]['code'] = -4;
	$response[$ROOT_ERROR_NODE]['msg']  = "API_VERSION or API_BUILD was not provided";

		if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
		
			echo cug_xml_start_tag($ROOT_ERROR_NODE, array(), $prefix="", PHP_EOL);
		
			echo cug_xml_start_tag("code", array(), "\t");
			echo cug_xml_entities($response['error']['code']);
			echo cug_xml_end_tag("code", "", PHP_EOL);
			
			echo cug_xml_start_tag("msg", array(), "\t");
			echo cug_xml_entities($response['error']['msg']);
			echo cug_xml_end_tag("msg", "", PHP_EOL);
		
			echo cug_xml_end_tag($ROOT_ERROR_NODE);
		}
		else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
			echo json_encode($response);
		}		

	exit();		
}



//---------------------------	
// Start processing Request
//---------------------------
$include_path = "v".$API_VERSION."/".$API_BUILD."/api.php";

include $include_path;

fwrite($dev_f, PHP_EOL.PHP_EOL);
?>