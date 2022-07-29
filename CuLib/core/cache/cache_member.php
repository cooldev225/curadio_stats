<?PHP
/**
 * Update Member Info in 'cache_members' table
 *
 * Delete existing member info and add new info if $force_update=true
 *
 * @param integer $member_id
 * @param bool $force_update (Optional, default: false)
 * @return integer
 */
function cug_cache_update_member($member_id, $force_update=false) {
	if($force_update || !cug_cache_check_member_id($member_id)) { //or if Member ID is not exists in cache table
		cug_cache_del_member($member_id);
		return cug_cache_add_member($member_id, $check_existing=false);
	}
	else { //if Member ID exists in cache table
		//get hash code
		$arr = cug_cache_collect_member_info($member_id);
		if(count($arr) > 0) {
			//compare hash code
			if(!cug_cache_check_member_hash($arr['hash_code'], "", "")) { //if hash codes was changed
				cug_cache_del_member($member_id);
				return cug_cache_add_member($member_id, $check_existing=false);
			}
			else {
				return -4; // Nothing to change
			}
		}
		else
			return -3; //Member ID was not found
	}
}


/**
 * Update Member Image
 *
 * @param int $member_id
 * @param int $has_img (1-YES; 0-NO;)
 * @param string $img_url (Optional)
 * @return boolean
 */
function cug_cache_edit_member_img($member_id, $has_img, $img_url="") {
	global $mysqli_cache, $Tables;

	if($member_id > 0) {
		$query = "UPDATE {$Tables['cache_members']} SET img=";
		$query .= ($has_img) ? "1" : "0";
		$query .= ($img_url) ? ", img_url='".$mysqli_cache->escape_str($img_url)."'" : "";
		$query .= " WHERE member_id=$member_id";
			
		if($mysqli_cache->query($query))
			return true;
	}

	return false;
}


/**
 * Add Member into 'cache_members' table
 *
 * @param integer $member_id
 * @param bool $check_existing (Optional, default: true)
 * @return integer
 */
function cug_cache_add_member($member_id, $check_existing=true) {
	global $mysqli_cache, $Tables;

	if($member_id > 0) {
		if($check_existing) {
			if(cug_cache_check_member_id($member_id))
				return -2; //already exists
		}
	
		$arr = cug_cache_collect_member_info($member_id);
		if(!count($arr))
			return -3; //Member ID was not found
	
	
		//Add entry
		if($mysqli_cache->query($arr['insert_query'])) {
			return $member_id;
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
 * Collect Member Info
 *
 * @param int $member_id
 * @return array | Member Object, Hash Code, Insert Query
 */
function cug_cache_collect_member_info($member_id) {
	global $mysqli, $Tables;
	$result = array();

	$member = cug_get_member($member_id, "ID");

	if($member != null) {
		$result['obj'] = $member;
		
		//get member-albums
		$member_albums = cug_get_member_related_albums($member_id, 0, 100);
					
		//get member-tracks
		$member_tracks = cug_get_member_related_tracks($register_from=0, $tag_status=0, $trash_status=-1, $member_id, $role_id=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=1000);			
		
		
		//generate HASH codes
		//------------------------
		//MEMBER
		$member_used_name = cug_get_member_name_or_alias($member->title, $member->alias, 2);
		$img_url = $member->img_path."/?o=member&i=".$member->id."&s=300&mt=".$member->member_type_id."&mg=".$member->gender_id;				
		$has_img = ((int)$member->img_orgn_num > 0) ? "1" : "0";
		$member_str = $member->id.$member_used_name.$member->member_type_id.$member->standard_role_id.$member->gender_id.$member->tag_status_id.$has_img.$img_url;

		$member_hash_code = hash("sha256", $member_str, false);
		$result['hash_code'] = $member_hash_code;
		
		//ALBUMS
		$albums_str = cug_cache_get_member_albums_json($member_albums);
		$album_hash_code = hash("sha256", $albums_str, false);
		$result['album_hash_code'] = $album_hash_code;
		//------------------------			
		
		//TRACKS
		$tracks_str = cug_cache_get_member_tracks_json($member_tracks);
		$track_hash_code = hash("sha256", $tracks_str, false);
		$result['track_hash_code'] = $track_hash_code;
		
						
		
		//generate SQL query
		//------------------------
		$query = "INSERT INTO ".$Tables['cache_members']." VALUES($member->id,'$member_hash_code','$album_hash_code','$track_hash_code',";
		$query .= "'".$mysqli->escape_str($member_used_name)."',";
		$query .= ($member->member_type_id > 0) ? $member->member_type_id."," : "NULL,";
		$query .= ($member->standard_role_id > 0) ? $member->standard_role_id."," : "NULL,";
		$query .= ($member->gender_id > 0) ? $member->gender_id."," : "NULL,";
		$query .= (!empty($member->tag_status_id)) ? $member->tag_status_id."," : "NULL,";
		$member_url = "";
		$query .= ($member_url) ? "'".$mysqli->escape_str($member_url)."'," : "NULL,";
		$query .= $has_img.",";
		$query .= ($img_url) ? "'".$mysqli->escape_str($img_url)."'," : "NULL,";
		$query .= ($albums_str) ? "'".$mysqli->escape_str($albums_str)."'," : "NULL,";
		$query .= ($tracks_str) ? "'".$mysqli->escape_str($tracks_str)."'," : "NULL,";
		$query .= "NULL)";
		
		//echo $query;
		$result['insert_query'] = $query;
	}

	return $result;
}


/**
 * Get JSON string for Member's Albums
 *
 * @param array
 * @return string
 */
function cug_cache_get_member_albums_json($member_albums)
{
$str = "";	

	if(!empty($member_albums[1][0]['total']) && $member_albums[1][0]['total'] > 0) {
		if(!empty($member_albums[0]) && count($member_albums[0]) > 0) {
			$str .= "{";
			$str .= "\"total\":".$member_albums[1][0]['total'].",";
			$str .= "\"data\":[";
			
				foreach($member_albums[0] as $member_album) {
					$str .= "{";
					$str .= "\"id\":".$member_album['id'].",";
					$str .= "\"title\":\"".str_replace('"', '\"', $member_album['title'])."\",";
					/*$str .= "\"url\":"."\"\"".",";*/
					
					$has_cover = ((int)$member_album['img_orgn'] > 0) ? "1" : "0";
					$str .= "\"cover\":".$has_cover.",";
					
					$album_cover = $member_album['img_path']."/?o=album&i=".$member_album['id']."&s=300";
					$str .= "\"cover_url\":\"".$album_cover."\"";
					
					$str .= "},";
				}
			
			$str = substr($str, 0, strlen($str) - 1);
			$str .= "]";
			$str .= "}";
		}
	}

return $str;
}



/**
 * Get JSON string for Member's Tracks
 *
 * @param array
 * @return string
 */
function cug_cache_get_member_tracks_json($member_tracks)
{
$str = "";	

	if(!empty($member_tracks[1][0]['total']) && $member_tracks[1][0]['total'] > 0) {
		if(!empty($member_tracks[0]) && count($member_tracks[0]) > 0) {
			$str .= "{";
			$str .= "\"total\":".$member_tracks[1][0]['total'].",";
			$str .= "\"data\":[";
			
				foreach($member_tracks[0] as $member_track) {
					$str .= "{";
					$str .= "\"id\":".$member_track['id'].",";
					$str .= "\"title\":\"".str_replace('"', '\"', $member_track['title'])."\",";
					//$str .= "\"part\":\"".str_replace('"', '\"', $member_track['part'])."\",";
					//$str .= "\"version\":\"".str_replace('"', '\"', $member_track['version'])."\",";
					$track_time = ($member_track['track_time'] > 0) ? $member_track['track_time'] : 0;
					$str .= "\"time\":".$track_time.",";
					//$str .= "\"url\":"."\"\"".",";
					
					$file_url = !empty($member_track['f_prev_path']) ? $member_track['f_prev_path']."/?i=".$member_track['file_id'] : "";
					$str .= "\"file_url\":\"".$file_url."\"";
					
					$str .= "},";
				}
			
			$str = substr($str, 0, strlen($str) - 1);
			$str .= "]";
			$str .= "}";
		}
	}

return $str;
}




/**
 * Delete Member from 'cache_members' table
 *
 * @param integer
 * @param bool (default: false) 
 * @return bool
 */
function cug_cache_del_member($member_id) {
	global $mysqli, $Tables;
	
		if($member_id > 0) {
			if($mysqli->query("DELETE FROM ".$Tables['cache_members']." WHERE member_id=$member_id")) {			
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
 * Delete Culinks for Member
 *
 * @param int $member_id
 * @return boolean
 */
function cug_cache_del_member_culink($member_id) {
	global $mysqli_cache, $Tables;
	$result = false;

	if($member_id > 0) {
		$query = "DELETE FROM {$Tables['cache_culink_member']} WHERE member_id=$member_id";

		if($mysqli_cache->query($query)) {
			$result = true;
		}
	}

	return $result;
}


/**
 * Get Album from 'cache_members' table
 *
 * @param integer $member_id
 * @return array
 */
function cug_cache_get_member($member_id)
{
	global $mysqli_cache, $Tables;
	$empty = array();

	if($member_id > 0) {
		$query = "SELECT * FROM ".$Tables['cache_members']." WHERE member_id=$member_id";

		if($r = $mysqli_cache->query($query)) {
			return $r->fetch_array();
		}
	}

	return $empty;
}


/**
 * Check if Member ID exists in 'cache_members' table
 *
 * @param int $member_id
 * @return boolean
 */
function cug_cache_check_member_id($member_id) {
	global $mysqli_cache, $Tables;

	if($member_id > 0) {
		$r = $mysqli_cache->query("SELECT member_id FROM ".$Tables['cache_members']." WHERE member_id=$member_id");
		if($r->num_rows)
			return true;
		else
			return false;
	}
	else
		return false;
}


/**
 * Check if Member Hash Code exists in 'cache_members' table
 *
 * @param string $member_hash
 * @param string $album_hash (Optional)
 * @param string $track_hash (Optional)
 * @return boolean
 */
function cug_cache_check_member_hash($member_hash, $album_hash="", $track_hash="") {
	global $mysqli_cache, $Tables;

	if($member_hash) {
		$query = "SELECT member_id FROM ".$Tables['cache_members']." WHERE member_hash='".$mysqli_cache->escape_str($member_hash)."'";
		$query .= ($album_hash) ? " AND album_hash='".$mysqli_cache->escape_str($member_hash)."'" : "";
		$query .= ($track_hash) ? " AND track_hash='".$mysqli_cache->escape_str($track_hash)."'" : "";
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