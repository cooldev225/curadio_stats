<?PHP

include_once "config.php";
include_once "globals.php";
include_once "actions.php";
include_once "error_codes.php";
include_once "output.php";
include_once "lib/db/mysqli.php";

include_once "$CuLib_PATH"."globals.php";
include_once "$CuLib_PATH"."core/track.php";
include_once "$CuLib_PATH"."core/member.php";
include_once "$CuLib_PATH"."core/album.php";
include_once "$CuLib_PATH"."core/client.php";

include_once "$CuLib_PATH"."core/cache/cache_track.php";
include_once "$CuLib_PATH"."core/cache/cache_album.php";
include_once "$CuLib_PATH"."core/cache/cache_member.php";

include_once "$CuLib_PATH"."core/country.php";
include_once "$CuLib_PATH"."core/object.php";
include_once "$CuLib_PATH"."core/fp.php";
include_once "$CuLib_PATH"."core/file_structure.php";
include_once "$CuLib_PATH"."lib/json.php";
include_once "$CuLib_PATH"."lib/time.php";
include_once "$CuLib_PATH"."lib/array.php";
include_once "$CuLib_PATH"."core/log.php";
include_once "$CuLib_PATH"."lib/url.php";
include_once "$CuLib_PATH"."lib/ftp.php";
include_once "$CuLib_PATH"."lib/ffmpeg.php";
include_once "$CuLib_PATH"."lib/request.php";
include_once "$CuLib_PATH"."core/wm.php";
include_once "$CuLib_PATH"."core/user.php";
include_once "$CuLib_PATH"."core/user_favorites.php";

include_once "lib/init.php";
include_once "lib/global-func.php";
include_once "lib/statistics.php";
include_once "lib/amounts.php";

global $ROOT_SUCCESS_NODE, $ROOT_ERROR_NODE, $ERRORS, $API_OUTPUT_FORMAT;

$ACTION = !empty($_POST['action']) ? $_POST['action'] : 0;
$response = array();


//connect to DB
$mysqli = new cug__mysqli($cug_db_host, $cug_db_user, $cug_db_password, $cug_db_name, $cug_db_server_port, $cug_db_ssl, $cug_db_server_key, $cug_db_server_cert, $cug_db_ca_cert);
$mysqli_rms_cache = new cug__mysqli($rms_cache_db_host, $rms_cache_db_user, $rms_cache_db_password, $rms_cache_db_name, $rms_cache_db_server_port, $rms_cache_db_ssl, $rms_cache_db_server_key, $rms_cache_db_server_cert, $rms_cache_db_ca_cert);


switch($ACTION) {
	
	// =1=
	//********************************
	case $ACTIONS['INIT_SESSION'] : 
	//********************************
		$response[$ROOT_SUCCESS_NODE]['attributes']  = array('action' => (int)$ACTION, 'timestamp' => time());
		$response[$ROOT_SUCCESS_NODE]['session_id']  = cugapi_init_session();
		
		cugapi_output($ACTION, $response);
	break;

	
	// =2=
	//********************************
	case $ACTIONS['GET_STAT_DATA'] : 
	//********************************	    
	    //capture parameters
	    $track_ids = cug_rms_stat_capture_track_id($_POST); 
	    $track_id_info = cug_rms_stat_get_track_id_info($track_ids['cugate_track_id'], $track_ids['shenzhen_track_id']);
	    
	    $time_period = !empty($_POST['time_period']) ? $_POST['time_period'] : "";
	    
	    $output_format = !empty($_POST['f']) ? strtolower($_POST['f']) : "";
	    
	    //get data
	    $data = cug_rms_stat_get_data_by_track($track_ids['cugate_track_id'], $track_ids['shenzhen_track_id'], $time_period, $limit=10);
	    //print_r($data);
	    
	    if($data > 0) { // OK
	        $response[$ROOT_SUCCESS_NODE]['attributes'] = array('action' => (int)$ACTION, "{$track_id_info['track_id_field']}" => $track_id_info['track_id'], 'timestamp' => time());
	        $response[$ROOT_SUCCESS_NODE]['result']  = $data;
	    }
	    else { // Error
	        $response[$ROOT_ERROR_NODE]['code']  = (int)$data;
	        $response[$ROOT_ERROR_NODE]['msg']   = array_search($data, $ERRORS);
	    }
	    
	    //output
	    if($output_format == $OUTPUT_FORMAT['web'])
	        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['web'];
	    
	    cugapi_output($ACTION, $response);
	    
	
	break;
	
	
	// =3=
	//********************************
	case $ACTIONS['GET_AREA_LIST'] :
	//********************************
	    $output_format = !empty($_POST['f']) ? strtolower($_POST['f']) : "";
	    
	    //get data
	    $data = array();
	    $area_list = cug_rms_stat_get_area_list();
	    $station_list = cug_rms_stat_get_station_list();
	    
	    if(count($area_list) > 0 && count($station_list) > 0) {
	        $data['area_list'] = $area_list;
	        $data['station_list'] = $station_list;
	    }
	    else {
	        $data = $ERRORS['NO_AREA_DATA'];
	    }
	    
	    //print_r($data);
	    
	    if($data > 0) { // OK
	        $response[$ROOT_SUCCESS_NODE]['attributes'] = array('action' => (int)$ACTION, 'timestamp' => time());
	        $response[$ROOT_SUCCESS_NODE]['result']  = $data;
	    }
	    else { // Error
	        $response[$ROOT_ERROR_NODE]['code']  = (int)$data;
	        $response[$ROOT_ERROR_NODE]['msg']   = array_search($data, $ERRORS);
	    }
	     
	    //output
	    if($output_format == $OUTPUT_FORMAT['web'])
	        $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['web'];
	         
	    cugapi_output($ACTION, $response);
	         
	
	 break;		
	
	
	 
	 // =4=
	 //********************************
	 case $ACTIONS['CHECK_TRACK'] :
	 //********************************
	     //capture parameters
	     $track_ids = cug_rms_stat_capture_track_id($_POST);
	     $track_id_info = cug_rms_stat_get_track_id_info($track_ids['cugate_track_id'], $track_ids['shenzhen_track_id']);
	     
	     $data = cug_rms_stat_check_track($track_ids['cugate_track_id'], $track_ids['shenzhen_track_id']);
	     
	     if($data > 0) { // OK
	         $response[$ROOT_SUCCESS_NODE]['attributes'] = array('action' => (int)$ACTION, "{$track_id_info['track_id_field']}" => $track_id_info['track_id'], 'timestamp' => time());
	         $response[$ROOT_SUCCESS_NODE]['result']  = $data;
	     }
	     else { // Error
	         $response[$ROOT_ERROR_NODE]['code']  = (int)$data;
	         $response[$ROOT_ERROR_NODE]['msg']   = array_search($data, $ERRORS);
	     }
	           
	     cugapi_output($ACTION, $response);
	     
	 break;
	     
	 
	 
	 // =12345678= (test)
	 //********************************
	 case 12345678 :
	 //********************************
	   $data = cug_rms_stat_get_amounts($cugate_track_id=2874120, $shenzhen_track_id=0, "LAST_YEAR", $is_daytime=true, $amount_sum=true, $object="composer", $country_code="DE", $subdivision_code="", $city="", $station_id=0);
	   print_r($data);
	 break;

	 
	//********************************
	default:
	//********************************
	    $output_format = !empty($_POST['f']) ? strtolower($_POST['f']) : "";
	    
	    $response[$ROOT_ERROR_NODE]['code'] = (int)$ERRORS['UNKNOWN_ACTION_ID'];
		$response[$ROOT_ERROR_NODE]['msg']  = array_search($ERRORS['UNKNOWN_ACTION_ID'], $ERRORS);
		
		//output
		if($output_format == $OUTPUT_FORMAT['web'])
		    $API_OUTPUT_FORMAT = $OUTPUT_FORMAT['web'];
		
		echo cugapi_output(0, $response);
			
	break;	
	
}

$mysqli->close();
$mysqli_rms_cache->close();
?>