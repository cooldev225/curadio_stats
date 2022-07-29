<?PHP

/**
 * Register Error Occurred During Portal Fies Analyzing Process
 * 
 * @param int $module_id
 * @param int $portal_id
 * @param string $portal_item_id
 * @param int $error_id
 * @param string $error_txt (Optional)
 * @param string $error_time (Optional)
 * @param int $country_id (Optional)
 * @param string $ip (Optional)
 * @return number
 */
function cug_reg_err_portal_analyze($module_id, $portal_id, $portal_item_id, $error_id, $error_txt="", $error_time="", $country_id=0, $ip="") {
	global $mysqli, $Tables;
	
	if($module_id > 0 && $portal_id > 0 && $portal_item_id && $error_id > 0) {
		
		if(!$ip) { $ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ""; }
		if(!$country_id) $country_id = cug_get_country_id_by_ip($ip);
		
		$query = "INSERT INTO {$Tables['portal_files_analyze_error']} VALUES(NULL,";
		$query .= "$module_id,$portal_id,'".$mysqli->escape_str($portal_item_id)."',$error_id,";
		$query .= ($error_time) ? "'$error_time'," : "NOW(),";
		$query .= ($country_id) ? "$country_id," : "NULL,";
		$query .= ($ip) ? "'$ip'," : "NULL,";
		$query .= ($error_txt) ? "'".$mysqli->escape_str($error_txt)."'," : "NULL,";
		$query .= "NOW())";
		
		if($mysqli->query($query))
			return $mysqli->insert_id;
		else 
			return -1;
	}
	
	return 0;
}
?>