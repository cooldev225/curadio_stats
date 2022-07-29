<?PHP

/**
 * Get Downloaded Files Folder Path 
 * 
 * @param int $id
 * @return string
 */
function cug_portal_get_files_folder_path($id) {
	global $mysqli, $Tables;
	
	if($id > 0) {
		$query = "SELECT * FROM {$Tables['portal_folders']} WHERE id=$id";
		
		$r = $mysqli->query($query);
			if($r && $r->num_rows) {
				$row = $r->fetch_array(MYSQLI_ASSOC);
				return $row['folder_path'];
			}
	}
	
	return "";
}

/**
 * Get file address
 * 
 * @param array $file_info (returned by cug_portal_get_file_info() function)
 * @return string
 */
function cug_portal_get_file_addr($file_info) {
	$file = "";
	
	if(count($file_info) > 0) {
		switch($file_info['portal_id']) {
			//YOUTUBE
			case 3:
			//--------------
				$file = $file_info['folder_path'].$file_info['file_id']."_".$file_info['file_duration'];
			break;
		}
	}
	
	return $file;
}

/**
 * Delete Portal's File and/or File Info
 * 
 * @param int $id
 * @param boolean $delete_info (Optional, default: true)
 * @param boolean $delete_file (Optional, default: false)
 * @return boolean
 */
function cug_portal_del_file_info($id, $delete_info=true, $delete_file=false) {
	global $mysqli, $Tables;
	$result = false;
	
	if($id > 0 && ($delete_info || $delete_file)) {
		//-------------------
		if($delete_file) {
			$file_info = cug_portal_get_file_info($id);
			
			if(count($file_info) > 0) {
				$file = cug_portal_get_file_addr($file_info);
				@unlink($file);
				$result = true;
			}
		}
		//-----------------
		if($delete_info) {
			$query = "DELETE FROM {$Tables['portal_files']} WHERE id=$id";
			
			if($mysqli->query($query))
				$result = true;	
			else 
				$result = false;
		}
	}

	return $result;
}


/**
 * Get Portal's File Info
 * 
 * @param int $id
 * @param number $portal_id (Optional)
 * @param string $portal_file_id (Optional)
 * @return array
 */
function cug_portal_get_file_info($id, $portal_id=0, $portal_file_id="") {
	global $mysqli, $Tables;
	$result = array();

	if($id > 0 || $portal_id > 0 || $portal_file_id) {
		//generate query
		$query = "SELECT fl.*, fld.folder_path FROM {$Tables['portal_files']} AS fl INNER JOIN {$Tables['portal_folders']} AS fld ON fl.folder_id=fld.id WHERE";

		$query .= ($id > 0) ? " fl.id=$id AND" : "";
		$query .= ($portal_file_id) ? " fl.file_id='".$mysqli->escape_str($portal_file_id)."' AND" : "";
		$query .= ($portal_id > 0) ? " fl.portal_id=$portal_id AND" : "";

		$query = rtrim($query, "AND");
		//----------------------

		$r = $mysqli->query($query);
			if($r && $r->num_rows) {
				$result = $r->fetch_array(MYSQLI_ASSOC);
			}
	}

	return $result;
}


/**
 * Register New File Info
 * 
 * @param int $portal_id
 * @param int $folder_id
 * @param array $file_info
 * @param int $user_id (Optional)
 * @param int $country_id (Optional)
 * @param string $user_ip (Optional)
 * @return number
 */
function cug_portal_reg_file_info($portal_id, $folder_id, $file_info, $user_id=0, $country_id=0, $user_ip="") {
	global $mysqli, $Tables;

	if($portal_id > 0 && $folder_id > 0 && count($file_info) > 0) {
		$file_id = $file_info['file_id'];

		//check for existing entry
		$arr = cug_portal_get_file_info($id=0, $portal_id, $file_id);

		if(!count($arr)) {
			$fields = " (portal_id,folder_id,";
			$values = " VALUES($portal_id,$folder_id,";
				
			if(!empty($file_info['file_id'])) {
				$fields .= "file_id,";
				$values .= "'".$mysqli->escape_str($file_info['file_id'])."',";
			}
				
			if(!empty($file_info['file_duration'])) {
				$fields .= "file_duration,";
				$values .= $mysqli->escape_str($file_info['file_duration']).",";
			}
				
			if(!empty($file_info['file_type'])) {
				$fields .= "file_type,";
				$values .= $mysqli->escape_str($file_info['file_type']).",";
			}
				
			if(!empty($file_info['file_format'])) {
				$fields .= "file_format,";
				$values .= "'".$mysqli->escape_str($file_info['file_format'])."',";
			}
				
			if(!empty($file_info['file_ext'])) {
				$fields .= "file_ext,";
				$values .= "'".$mysqli->escape_str($file_info['file_ext'])."',";
			}
				
			if(!empty($file_info['file_srate'])) {
				$fields .= "file_srate,";
				$values .= "'".$mysqli->escape_str($file_info['file_srate'])."',";
			}
				
			if(!empty($file_info['file_brate'])) {
				$fields .= "file_brate,";
				$values .= "'".$mysqli->escape_str($file_info['file_brate'])."',";
			}
				
			if(!empty($file_info['file_size'])) {
				$fields .= "file_size,";
				$values .= $mysqli->escape_str($file_info['file_size']).",";
			}
				
			if(!empty($file_info['file_tag'])) {
				$fields .= "file_tag,";
				$values .= "'".$mysqli->escape_str($file_info['file_tag'])."',";
			}
				
			if($user_id) {
				$fields .= "user_id,";
				$values .= $user_id.",";
			}
			
			//----------------------
			if(!$user_ip) { $user_ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ""; }
			if(!$country_id) $country_id = cug_get_country_id_by_ip($user_ip);
			
			if($user_ip) {
				$fields .= "user_ip,";
				$values .= "'".$mysqli->escape_str($user_ip)."',";
			}

			if($country_id) {
				$fields .= "country_id,";
				$values .= $country_id.",";
			}
			//----------------------
				
			if(!empty($file_info['download_time'])) {
				$fields .= "download_time)";
				$values .= "'".$mysqli->escape_str($file_info['download_time'])."')";
			}
			else {
				$fields .= "download_time)";
				$values .= "NOW())";
			}

			$query = "INSERT INTO ".$Tables['portal_files'].$fields.$values;
				
			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else
			return $arr['id'];
	}

	return 0;
}
?>