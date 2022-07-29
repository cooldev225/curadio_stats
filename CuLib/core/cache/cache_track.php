<?PHP

/**
 * Update Track Info in 'cache_tracks' table
 *
 * Delete existing track info and add new info if $force_update=true
 *
 * @param integer $track_id
 * @param bool $force_update (Optional, default: false)
 * @return integer
 */
function cug_cache_update_track($track_id, $force_update=false) {
	if($force_update || !cug_cache_check_track_id($track_id)) { //or if Track ID is not exists in cache table
		cug_cache_del_track($track_id);
		return cug_cache_add_track($track_id, $check_existing=false);
	}
	else { //if Track ID exists in cache table
		//get hash code
		$arr = cug_cache_collect_track_info($track_id);
		if(count($arr) > 0) {
			//compare hash code
			if(!cug_cache_check_track_hash($arr['hash_code'], $arr['album_hash_code'], $arr['keyboard_hash_code'], $arr['cube_hash_code'])) { //if hash codes was changed
				cug_cache_del_track($track_id);
				return cug_cache_add_track($track_id, $check_existing=false);
			}
			else {
				return -4; // Nothing to change
			}
		}
		else
			return -3; //Track ID was not found
	}
}


/**
 * Add Track into 'cache_tracks' table
 *
 * @param integer $track_id
 * @param bool $check_existing (Optional, default: true)
 * @return integer
 */
function cug_cache_add_track($track_id, $check_existing=true) {
global $mysqli_cache, $Tables;

	if($track_id > 0) {
		if($check_existing) {
			if(cug_cache_check_track_id($track_id))
				return -2; //already exists
		}
	
		$arr = cug_cache_collect_track_info($track_id);
		if(!count($arr))
			return -3; //Track ID was not found
	
	
		//Add entry
		if($mysqli_cache->query($arr['insert_query'])) {
			return $track_id;
		}
		else {
			return -1; // Error
		}
		//-----------------------
	}
	else {
		return 0;
	}
}


/**
 * Collect Track Info
 *
 * @param int $track_id
 * @return array | Track Object, Hash Code, Insert Query
 */
function cug_cache_collect_track_info($track_id) {
	global $mysqli, $Tables;
	$result = array();

	$track = cug_get_track($track_id, "ID");

		if($track != null) {
			$result['obj'] = $track;
			
			//get track-albums
			$track_albums = cug_get_track_albums($track_id);
			
			//get track-members
			$track_members = cug_get_track_members($track_id);
			
			//get footprint data
			//$footprint = cug_fp_get_footprint_by_track($track->f_id, 0, 0,   0, 1);
			$footprint = array();
				
			$tempo_id = 0;
			$mood_id = 0;
				if(count($footprint) > 0) {
					//get TEMPO id
					$tempo_id = 0;
	
					//get MOOD id
					$mood_id = 0;
					
					//get KEY id
					$key_arr = cug_fp_get_key($footprint[0]['key_val']);
	
				}
			
				
			//get CUBE data
			$cube_data = "";
			
	
			//generate HASH codes
			//------------------------
			//TRACK
			$file_url = !empty($track->f_prev_path) ? $track->f_prev_path."/?i=".$track->f_id : "";
			$track_str = $track->id . $track->title . $track->part . $track->version . $track->f_track_time . $file_url . $track->genre_id . $track->fileunder_id . $tempo_id . $mood_id . $track->tag_status_id;
			$track_str .= (!empty($key_arr['key_id'])) ? $key_arr['key_id'] : "";
			$members_str = cug_cache_get_track_members_json($track_members);
			$track_str .= $members_str;
			$track_hash_code = hash("sha256", $track_str, false);
			$result['hash_code'] = $track_hash_code;
			
			//ALBUMS
			$albums_str = "";
				if(count($track_albums) > 0) {
					$albums_str = cug_cache_get_track_albums_json($track_albums);
				}
			$album_hash_code = hash("sha256", $albums_str, false);
			$result['album_hash_code'] = $album_hash_code;
	
			//KEYBOARD
			$keyboard_str = "";
				if(count($footprint) > 0) {
					$keyboard_str = cug_cache_get_track_keyboard_json($footprint);
				}
			$keyboard_hash_code = hash("sha256", $keyboard_str, false);
			$result['keyboard_hash_code'] = $keyboard_hash_code;
			
			//CUBE
			$cube_str = "";
			$cube_hash_code = hash("sha256", $cube_str, false);
			$result['cube_hash_code'] = $cube_hash_code;
			//------------------------
			
			
			
			//generate SQL query
			//------------------------
			$query = "INSERT INTO ".$Tables['cache_tracks']." VALUES($track->id,'$track_hash_code','$album_hash_code','$keyboard_hash_code','$cube_hash_code',";
			$query .= "'".$mysqli->escape_str($track->title)."','".$mysqli->escape_str($track->part)."','".$mysqli->escape_str($track->version)."',";
			$query .= (!empty($track->f_track_time)) ? $track->f_track_time."," : "NULL,";
			$query .= "'".$mysqli->escape_str($file_url)."',";
			$query .= ($track->genre_id > 0) ? $track->genre_id."," : "NULL,";
			$query .= ($track->fileunder_id > 0) ? $track->fileunder_id."," : "NULL,";
			$query .= ($tempo_id > 0) ? $tempo_id."," : "NULL,";
			$query .= ($mood_id > 0) ? $mood_id."," : "NULL,";
			$query .= (!empty($track->tag_status_id)) ? $track->tag_status_id."," : "NULL,";
			$query .= (!empty($key_arr['key_id'])) ? $key_arr['key_id']."," : "NULL,";
			$query .= ($members_str) ? "'".$mysqli->escape_str($members_str)."'," : "NULL,";
			$query .= ($albums_str) ? "'".$mysqli->escape_str($albums_str)."'," : "NULL,";
			$query .= ($keyboard_str) ? "'".$mysqli->escape_str($keyboard_str)."'," : "NULL,";
			$query .= ($cube_str) ? "'".$mysqli->escape_str($cube_str)."'," : "NULL,";
			$query .= "NULL)";
			
			//echo $query;
			
			$result['insert_query'] = $query;
		}

	return $result;
}


/**
 * Get JSON string for Track's Albums
 *
 * @param array
 * @return string
 */
function cug_cache_get_track_albums_json($track_albums)
{
$str = "";	

	if(count($track_albums) > 0) {
		$str .= "{";
		$str .= "\"total\":".count($track_albums).",";
		$str .= "\"data\":[";
		
		foreach($track_albums as $track_album) {
			$str .= "{";
			$str .= "\"id\":".$track_album['album_id'].",";
			$str .= "\"title\":\"".str_replace('"', '\"', $track_album['title'])."\",";
			$str .= "\"disc_num\":".$track_album['disc_num'].",";
			$str .= "\"track_num\":".$track_album['track_num'].",";
			$str .= "\"ean\":\"".str_replace('"', '\"', $track_album['ean_code'])."\",";
			$str .= "\"cat_num\":\"".str_replace('"', '\"', $track_album['catalogue_num'])."\",";
			/*$str .= "\"url\":"."\"\"".",";*/
			
			$has_cover = ((int)$track_album['img_orgn'] > 0) ? "1" : "0";
			$str .= "\"cover\":".$has_cover.",";
			
			$album_cover = $track_album['img_path']."/?o=album&i=".$track_album['album_id']."&s=300";
			$str .= "\"cover_url\":\"".$album_cover."\"";
			
			$str .= "},";
		}
		
		$str = substr($str, 0, strlen($str) - 1);
		$str .= "]";
		$str .= "}";
	}

return $str;
}

/**
 * Get JSON string for Track's Members
 *
 * @param array
 * @return string
 */
function cug_cache_get_track_members_json($track_members)
{
$str = "";

	if(count($track_members) > 0) {
		$str .= "{";
		$str .= "\"total\":".count($track_members).",";
		$str .= "\"data\":[";

		foreach($track_members as $track_member) {
			$str .= "{";
			$str .= "\"id\":".$track_member['member_id'].",";
			
			$member_title = cug_get_member_name_or_alias($track_member['title'], $track_member['alias'], $track_member['used_name']);
			$str .= "\"title\":\"".str_replace('"', '\"', $member_title)."\",";
			
			$str .= "\"role_id\":".$track_member['role_id'].",";
			
			$isprimary = ($track_member['isprimary']) ? "true" : "false";
			$str .= "\"primary\":".$isprimary.",";
			
			/*$str .= "\"url\":"."\"\"".",";*/
				
			$has_img = ((int)$track_member['img_orgn'] > 0) ? "1" : "0";
			$str .= "\"img\":".$has_img.",";
			
			$img_url = $track_member['img_path']."/?o=member&i=".$track_member['member_id']."&s=300&mt=".$track_member['member_type_id']."&mg=".$track_member['gender_id'];
			$str .= "\"img_url\":\"".$img_url."\"";
				
			$str .= "},";
		}

		$str = substr($str, 0, strlen($str) - 1);
		$str .= "]";
		$str .= "}";
	}

return $str;
}


/**
 * Get JSON string for Track's Keyboard Data
 *
 * @param array
 * @return string
 */
function cug_cache_get_track_keyboard_json($footprint)
{
$str = "";

	if(count($footprint) > 0) {
		$str .= "{";
			
			for($i=1; $i<=12; $i++) {
				$str .= "\"n$i\":\"".$footprint[0]['n'.$i]."\",";
			}

		$str = substr($str, 0, strlen($str) - 1);
		$str .= "}";
	}

return $str;
}

/**
 * Delete Track from 'cache_tracks' table
 *
 * @param integer
 * @param bool (default: false) 
 * @return bool
 */
function cug_cache_del_track($track_id)
{
global $mysqli, $Tables;

	if($track_id > 0) {
		if($mysqli->query("DELETE FROM ".$Tables['cache_tracks']." WHERE track_id=$track_id")) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}


/**
 * Get Track from 'cache_tracks' table
 *
 * @param integer $track_id
 * @return array
 */
function cug_cache_get_track($track_id)
{
global $mysqli_cache, $Tables;
$empty = array();
	
	if($track_id > 0) {
		$query = "SELECT * FROM ".$Tables['cache_tracks']." WHERE track_id=$track_id";
	
			if($r = $mysqli_cache->query($query)) {
				return $r->fetch_array(MYSQLI_ASSOC);
			}
	}
	
return $empty;	
}



/**
 * Delete Culinks for Track
 * 
 * @param number $track_id (either track_id or album_id must be provided)
 * @param number $album_id (either track_id or album_id must be provided)
 * @param number $portal_id (optional)
 * @param number $link_cat_id (optional)
 * @param number $product_id (optional)
 * @param number $product_type_id (optional)
 * @param number $query_type_id (optional)
 * @param number $country_id (optional)
 * @return boolean
 */
function cug_cache_del_track_culink($track_id=0, $album_id=0, $portal_id=0, $link_cat_id=0, $product_id=0, $product_type_id=0, $query_type_id=0, $country_id=0) {
	global $mysqli_cache, $Tables;
	$result = false;
	
	if($track_id > 0 || $album_id > 0) {
		$query = "DELETE FROM {$Tables['cache_culink_track']} WHERE";
		$query .= ($track_id > 0) ? " track_id=$track_id AND" : "";
		$query .= ($album_id > 0) ? " album_id=$album_id AND" : "";
		
		$query = rtrim($query, "AND");
		
		$query .= ($portal_id > 0) ? " AND portal_id=$portal_id" : "";
		$query .= ($link_cat_id > 0) ? " AND link_cat_id=$link_cat_id" : "";
		$query .= ($product_id > 0) ? " AND product_id=$product_id" : "";
		$query .= ($product_type_id > 0) ? " AND product_type_id=$product_type_id" : "";
		$query .= ($query_type_id > 0) ? " AND query_type_id=$query_type_id" : "";
		$query .= ($country_id > 0) ? " AND country_id=$country_id" : "";
		
		
		if($mysqli_cache->query($query)) {
			$result = true;
		}
	}
	
	return $result;
}


/**
 * Check if Track ID exists in 'cache_tracks' table
 * 
 * @param int $track_id
 * @return boolean
 */
function cug_cache_check_track_id($track_id) {
	global $mysqli_cache, $Tables; 
	
	if($track_id > 0) {
		$r = $mysqli_cache->query("SELECT track_id FROM ".$Tables['cache_tracks']." WHERE track_id=$track_id");
		if($r->num_rows)
			return true;
		else 
			return false;
	}
	else 
		return false;
}


/**
 * Check if Track Hash Code exists in 'cache_tracks' table
 *
 * @param string $track_hash
 * @param string $album_hash (Optional)
 * @param string $keyboard_hash (Optional)
 * @param string $cube_hash (Optional)
 * @return boolean
 */
function cug_cache_check_track_hash($track_hash, $album_hash="", $keyboard_hash="", $cube_hash="") {
	global $mysqli_cache, $Tables;

	if($track_hash) {
		$query = "SELECT track_id FROM ".$Tables['cache_tracks']." WHERE track_hash='".$mysqli_cache->escape_str($track_hash)."'";
		$query .= ($album_hash) ? " AND album_hash='".$mysqli_cache->escape_str($album_hash)."'" : "";
		$query .= ($keyboard_hash) ? " AND keyboard_hash='".$mysqli_cache->escape_str($keyboard_hash)."'" : "";
		$query .= ($cube_hash) ? " AND cube_hash='".$mysqli_cache->escape_str($cube_hash)."'" : "";
		
		$r = $mysqli_cache->query($query);
		
			if($r->num_rows)
				return true;
			else
				return false;
	}
	else
		return false;
}
?>