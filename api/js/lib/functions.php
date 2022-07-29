<?PHP

function send_request($action, $params="") {
    global $API_OUTPUT_FORMAT, $OUTPUT_FORMAT;
    $result = "";
  
    if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['web'])
        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['json'];
    $TOKEN = get_token();
	
    if($TOKEN) {
		
        $SESSION_ID = get_session_id($TOKEN);
    
        if($SESSION_ID) {
		
            session_write_close();
          
            $result = send_action($TOKEN, $SESSION_ID, $action, $params);
			
        }
    }
    
    return trim($result);
}


//---------------------
function get_session_id($token)
{
global $API_ENDPOINT_URL, $API_VERSION, $API_BUILD, $API_OUTPUT_FORMAT, $ACTIONS;	

$url_params = "access_token=".urlencode($token)."&";
$url_params .= "action=".urlencode($ACTIONS['INIT_SESSION'])."&";
$url_params .= "v=".urlencode($API_VERSION)."&";
$url_params .= "b=".urlencode($API_BUILD)."&";
$url_params .= "f=".urlencode($API_OUTPUT_FORMAT)."&";
$url_params .= "device_os=LINUX&";
$url_params .= "device_id=1&";
$url_params .= "device_name=Server APP";

$response_body = http_request($API_ENDPOINT_URL, $url_params);

return parse_session_id($response_body);
}


//---------------------
function parse_session_id($response_body)
{
global $API_OUTPUT_FORMAT;

	if($API_OUTPUT_FORMAT == "xml") {	
		$arr = cug_xmlstr_to_array($response_body);
		return !empty($arr['session_id']) ? $arr['session_id'] : "";
	}
	else if($API_OUTPUT_FORMAT == "json") {
		$arr = json_decode($response_body, true);
		return !empty($arr['data']['session_id']) ? $arr['data']['session_id'] : "";
	}
	else {
		return "";
	}
}




//---------------------
function send_action($token, $session_id, $action, $additional_params="", $return_header=false)
{
global $API_ENDPOINT_URL, $API_OUTPUT_FORMAT;
	
$global_params = "access_token=".urlencode($token)."&";
$global_params .= "action=".urlencode($action)."&";

$output_format = !empty($_POST['f']) ? strtolower($_POST['f']) : $API_OUTPUT_FORMAT;
$global_params .= "f=".urlencode($output_format);

	if($additional_params)
		$url_params = $global_params."&".$additional_params;
	else 
		$url_params = $global_params;
	
return http_request($API_ENDPOINT_URL, $url_params, $session_id, $return_header);
}


//----------------------
function get_token()
{
global $CLIENT_ID, $CLIENT_SECRET, $TOKEN_ENDPOINT_URL;

$url_params = "grant_type="."client_credentials"."&";
$url_params .= "client_id=".urlencode($CLIENT_ID)."&";
$url_params .= "client_secret=".urlencode($CLIENT_SECRET);

$response_body = http_request($TOKEN_ENDPOINT_URL, $url_params);

return parse_token($response_body);
}



//----------------------
function parse_token($response_body)
{
$arr = json_decode($response_body, true);

	if(count($arr) > 0) {
		return !empty($arr['access_token']) ? $arr['access_token'] : false;
	}
	else {
		return false;
	}

}



//----------------------
function http_request($url, $url_params, $session_id="", $return_header=false, $ssl_verify=false, $accept_encoding="gzip", $post_method=true)
{
	if($ch = curl_init()) {
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $url_params);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
		curl_setopt($ch, CURLOPT_POST, $post_method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		//curl_setopt($ch, CURLOPT_CAINFO, "cugate.pem");
		
		if($session_id) curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $session_id . '; path=/');
		if($accept_encoding) curl_setopt($ch, CURLOPT_ENCODING, $accept_encoding);
		
		curl_setopt($ch, CURLOPT_HEADER, $return_header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
	
		$result = curl_exec($ch);
		

	    /*
		echo curl_error($ch);
		$info = curl_getinfo($ch);
		print_r($info);
		*/
		
		return $result;
	}
	else {
		return false;
	}	
}




//-----------------------
function write_log($file, $data)
{
	if($f = fopen($file, 'w+')) {
		fwrite($f, $data);
		fclose($f);
	}
}


//----------------------------
function is_host_allowed($url) {
    global $ALLOWED_HOSTS;
    $result = false;
    
    if($url) {
        $arr = parse_url($url);
        $host = !empty($arr['host']) ? $arr['host'] : "";
        
        if(array_search($host, $ALLOWED_HOSTS) !== false)
            $result = true;
    }
    
    return $result;
}

?>
