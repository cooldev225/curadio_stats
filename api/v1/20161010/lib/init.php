<?PHP
/**
 * Initialize Session
 *
 * @return INT
 */
function cugapi_init_session() {
    global $server, $storage, $API_VERSION, $API_BUILD;
    
    $client_id = $server->resourceController->token['client_id'];
    $client_data = $storage->getClientDetails($client_id);
    
    $_SESSION['CLIENT_ID'] 		= $client_id;
    $_SESSION['APP_ID'] 		= $client_data['app_id']; //Application ID
    $_SESSION['APP_DET_ID'] 	= $client_data['app_details_id']; //Application Details ID
    $_SESSION['API_VERSION'] 	= $API_VERSION;
    $_SESSION['API_BUILD'] 		= $API_BUILD;
    $_SESSION['DEVICE_OS'] 		= !empty($_POST['device_os']) ? $_POST['device_os'] : "";
    $_SESSION['DEVICE_ID'] 		= !empty($_POST['device_id']) ? $_POST['device_id'] : "";
    $_SESSION['DEVICE_NAME'] 	= !empty($_POST['device_name']) ? $_POST['device_name'] : "";
    $_SESSION['USER_IP'] 		= $_SERVER['REMOTE_ADDR'];
    $_SESSION['USER_ID'] 		= 0;
    $_SESSION['COUNTRY_ID'] 	= cug_get_country_id_by_ip($_SERVER['REMOTE_ADDR']);
    
    $_SESSION['CUGATE_CLIENT_ID'] = $client_data['cugate_client_id'];
    $dev_f = fopen("log_dev.txt", "a");
    fwrite($dev_f,"	_SESSION=".(json_encode($_SESSION)).PHP_EOL);
    return session_id();
}


?>