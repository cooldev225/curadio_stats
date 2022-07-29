<?PHP
//URLS
#$TOKEN_ENDPOINT_URL = "https://stats.cugate.com/token.php";
#$API_ENDPOINT_URL 	= "https://stats.cugate.com/index.php";
$TOKEN_ENDPOINT_URL = "http://192.168.2.22/stats/token.php";
$API_ENDPOINT_URL = "http://192.168.2.22/stats/index.php";
//CREDENTIALS
$CLIENT_ID = "1449760511624622080";  //Javascript Access from Websites
$CLIENT_SECRET = "881ee3d3a947849ac5621c5c39f11591";

//API CONFIGURATION
$API_VERSION = 1;
//$API_BUILD = '20160725';
$API_BUILD = '20161010';
//$API_BUILD = 'test';

//OUTPUT FORMAT
$OUTPUT_FORMAT['xml']	= 'xml';
$OUTPUT_FORMAT['json']	= 'json';
$OUTPUT_FORMAT['web']	= 'web'; //website
$API_OUTPUT_FORMAT = 'json'; //by default

//ALLOWED HOSTS
$ALLOWED_HOSTS[0] = "cumarket.net";
$ALLOWED_HOSTS[1] = "www.cumarket.net";
$ALLOWED_HOSTS[2] = "cartulia.cumarket.com";
$ALLOWED_HOSTS[3] = "cugate.cumarket.com";
$ALLOWED_HOSTS[4] = "memomedia.cumarket.com";
$ALLOWED_HOSTS[5] = "stats.cugate.com";


//ACTIONS
$ACTIONS = array();
$ACTIONS['TEST'] 					        = 1000;

$ACTIONS['INIT_SESSION'] 					= 1;

?>
