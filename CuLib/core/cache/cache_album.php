<?PHP
/**
 * Update Album Info in 'cache_albumss' table
 *
 * Delete existing album info and add new info if $force_update=true
 *
 * @param integer $album_id
 * @param bool $force_update (Optional, default: false)
 * @return integer
 */
function cug_cache_update_album($album_id, $force_update=false) {
	if($force_update || !cug_cache_check_album_id($album_id)) { //or if Album ID is not exists in cache table
		cug_cache_del_album($album_id);
		return cug_cache_add_album($album_id, $check_existing=false);
	}
	else { //if Album ID exists in cache table
		//get hash code
		$arr = cug_cache_collect_album_info($album_id);
		if(count($arr) > 0) {
			//compare hash code
			if(!cug_cache_check_album_hash($arr['hash_code'])) { //if hash codes was changed
				cug_cache_del_album($album_id);
				return cug_cache_add_album($album_id, $check_existing=false);
			}
			else {
				return -4; // Nothing to change
			}
		}
		else
			return -3; //Album ID was not found
	}
}


/**
 * Update Album Cover
 * 
 * @param int $album_id
 * @param int $has_cover (1-YES; 0-NO;)
 * @param string $cover_url (Optional)
 * @return boolean
 */
function cug_cache_edit_album_cover($album_id, $has_cover, $cover_url="") {
	global $mysqli_cache, $Tables;
	
		if($album_id > 0) {
			$query = "UPDATE {$Tables['cache_albums']} SET cover=";
			$query .= ($has_cover) ? "1" : "0";
			$query .= ($cover_url) ? ", cover_url='".$mysqli_cache->escape_str($cover_url)."'" : "";
			$query .= " WHERE album_id=$album_id";
			
			if($mysqli_cache->query($query))
				return true;
		}
		
	return false;	 
}


/**
 * Check if Album ID exists in 'cache_albums' table
 *
 * @param int $album_id
 * @return boolean
 */
function cug_cache_check_album_id($album_id) {
	global $mysqli_cache, $Tables;

	if($album_id > 0) {
		$r = $mysqli_cache->query("SELECT album_id FROM ".$Tables['cache_albums']." WHERE album_id=$album_id");
		if($r->num_rows)
			return true;
		else
			return false;
	}
	else
		return false;
}


/**
 * Check if Album Hash Code exists in 'cache_albums' table
 *
 * @param string $album_hash
 * @return boolean
 */
function cug_cache_check_album_hash($album_hash) {
	global $mysqli_cache, $Tables;

	if($album_hash) {
		$r = $mysqli_cache->query("SELECT album_id FROM ".$Tables['cache_albums']." WHERE album_hash='".$mysqli_cache->escape_str($album_hash)."'");
		if($r->num_rows)
			return true;
		else
			return false;
	}
	else
		return false;
}

/**
 * Add Album into 'cache_albums' table
 *
 * @param integer $album_id
 * @param bool $check_existing (Optional, default: true)
 * @return integer
 */
function cug_cache_add_album($album_id, $check_existing=true) {
	global $mysqli_cache, $Tables;

	if($album_id > 0) {		
		if($check_existing) {
			if(cug_cache_check_album_id($album_id))
				return -2; //already exists 
		}
		
		$arr = cug_cache_collect_album_info($album_id);
		if(!count($arr))
			return -3; //Album ID was not found


		//Add entry
		if($mysqli_cache->query($arr['insert_query'])) {
			return $album_id;
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
 * Collect Album Info
 * 
 * @param int $album_id
 * @return array | Album Object, Hash Code, Insert Query
 */
function cug_cache_collect_album_info($album_id) {
	global $mysqli, $Tables;
	$result = array();
	
	$album = cug_get_album($album_id, "ID");
		
	if($album != null) {	
		$result['obj'] = $album;
		
		//get album-tracks
		$album_tracks = cug_get_album_tracks($album_id);
			
		//get album-members
		$album_members = cug_get_album_members($album_id);
	
	
		//generate HASH codes
		//------------------------
		//TRACKS
		$tracks_str = cug_cache_get_album_tracks_json($album_tracks);
	
		//MEMBERS
		$members_str = cug_cache_get_album_members_json($album_members);
	
		//ALBUM
		$has_cover = ((int)$album->img_orgn_num > 0) ? "1" : "0";
		$cover_url = $album->img_path."/?o=album&i=".$album->id."&s=300";
	
		$catalogue_num = ($album->catalogue_num) ? $album->catalogue_num : "";
		$rel_date 	= ($album->rel_date) ? $album->rel_date : "";
	
		$genre_id 	= ($album->genre_id) ? $album->genre_id : 0;
		$genre_str = cug_get_object_title($genre_id, "GENRE");
	
		$type_id 	= ($album->type_id) ? $album->type_id : 0;
		$type_str = cug_get_object_title($type_id, "ALBUM_TYPE");
	
		$format_id 	= ($album->rel_format_id) ? $album->rel_format_id : 0;
		$format_str = cug_get_object_title($format_id, "ALBUM_FORMAT");
	
		$album_url = "";
		$album_str = $album->id.$album->title.$album->title_version.$album->total_discs.$album->ean_code.$album->tag_status_id.$album->catalogue_num.$rel_date.$genre_str.$type_str.$format_str.$album_url.$has_cover.$cover_url;
		$album_str .= $tracks_str . $members_str;
	
		$album_hash_code = hash("sha256", $album_str, false);
		$result['hash_code'] = $album_hash_code;
	
	
		//generate SQL query
		//------------------------
		$query = "INSERT INTO ".$Tables['cache_albums']." VALUES($album->id,'$album_hash_code',";
		$query .= "'".$mysqli->escape_str($album->title)."',";
		$query .= (!empty($album->title_version)) ? "'".$album->title_version."'," : "NULL,";
		$query .= ($album->total_discs > 0) ? $album->total_discs."," : "NULL,";
		$query .= (!empty($album->ean_code)) ? "'".$album->ean_code."'," : "NULL,";
		$query .= (!empty($album->tag_status_id)) ? $album->tag_status_id."," : "NULL,";
	
		$query .= ($album->catalogue_num) ? "'".$album->catalogue_num."'," : "NULL,";
		$query .= ($rel_date) ? "'".$mysqli->escape_str($rel_date)."'," : "NULL,";
		$query .= ($genre_str) ? "'".$mysqli->escape_str($genre_str)."'," : "NULL,";
		$query .= ($type_str) ? "'".$mysqli->escape_str($type_str)."'," : "NULL,";
		$query .= ($format_str) ? "'".$mysqli->escape_str($format_str)."'," : "NULL,";
	
		$query .= ($album_url) ? "'".$mysqli->escape_str($album_url)."'," : "NULL,";
		$query .= $has_cover.",";
		$query .= ($cover_url) ? "'".$mysqli->escape_str($cover_url)."'," : "NULL,";
		$query .= ($tracks_str) ? "'".$mysqli->escape_str($tracks_str)."'," : "NULL,";
		$query .= ($members_str) ? "'".$mysqli->escape_str($members_str)."'," : "NULL,";
		$query .= "NULL)";
	
		//echo $query;
		$result['insert_query'] = $query;
	}

	return $result;
}


/**
 * Get JSON string for Album's Members
 *
 * @param array
 * @return string
 */
function cug_cache_get_album_members_json($album_members)
{
$str = "";

	if(count($album_members) > 0) {
		$str .= "{";
		$str .= "\"total\":".count($album_members).",";
		$str .= "\"data\":[";

		foreach($album_members as $album_member) {
			$str .= "{";
			$str .= "\"id\":".$album_member['member_id'].",";
			
			$member_title = cug_get_member_name_or_alias($album_member['title'], $album_member['alias'], $album_member['used_name']);
			$str .= "\"title\":\"".str_replace('"', '\"', $member_title)."\",";
	
			$str .= "\"role_id\":".$album_member['role_id'].",";

			$has_img = ((int)$album_member['img_orgn'] > 0) ? "1" : "0";
			$str .= "\"img\":".$has_img.",";
			
			$img_url = $album_member['img_path']."/?o=member&i=".$album_member['member_id']."&s=300&mt=".$album_member['member_type_id']."&mg=".$album_member['gender_id'];
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
 * Get JSON string for Album's Tracks
 *
 * @param array
 * @return string
 */
function cug_cache_get_album_tracks_json($album_tracks)
{
$str = "";	
$substr = "";

$total_discs = 0;
$total_tracks = 0;
$disc_num = 0;

	if(count($album_tracks) > 0) {
		
		foreach($album_tracks as $disc_tracks) {
			$total_discs ++;
			$disc_num = $disc_tracks['disc']['disc_num'];
			
			if(!empty($disc_tracks['tracks'])) {
				foreach($disc_tracks['tracks'] as $track) {
					$total_tracks ++;
	
					$substr .= "{";
					$substr .= "\"id\":".$track['track_id'].",";
					$substr .= "\"title\":\"".str_replace('"', '\"', $track['title'])."\",";
					//$substr .= "\"part\":\"".str_replace('"', '\"', $track['part'])."\",";
					//$substr .= "\"version\":\"".str_replace('"', '\"', $track['version'])."\",";
					
					$track_time = ($track['track_time'] > 0) ? $track['track_time'] : 0;
					$substr .= "\"time\":".$track_time.",";
					
					$track_num = ($track['track_num'] > 0) ? $track['track_num'] : 0;
					$substr .= "\"track_num\":".$track_num.",";				
					
					$substr .= "\"disc_num\":".$disc_num.",";
					
					//$substr .= "\"url\":"."\"\"".",";
					
					$track_url = "";
					$file_url = !empty($track['f_prev_path']) ? $track['f_prev_path']."/?i=".$track['file_id'] : "";
					//$substr .= "\"url\":\"".$track_url."\",";
					$substr .= "\"file_url\":\"".$file_url."\"";
					
					$substr .= "},";
				}
			}
		}
		//--------------------
		
			if(strlen($substr) > 0) {
				$substr = substr($substr, 0, strlen($substr) - 1);
				$substr .= "]";
				$substr .= "}";
				
				$str = "{";
				$str .= "\"total_discs\":".$total_discs.",";
				$str .= "\"total_tracks\":".$total_tracks.",";
				$str .= "\"data\":[";
				$str .= $substr;
			}
	}

return $str;
}




/**
 * Delete Album from 'cache_albums' table
 *
 * @param integer
 * @param bool (default: false) 
 * @return bool
 */
function cug_cache_del_album($album_id)
{
global $mysqli_cache, $Tables;

	if($album_id > 0) {
		if($mysqli_cache->query("DELETE FROM ".$Tables['cache_albums']." WHERE album_id=$album_id")) {					
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
 * Delete Culinks for Album
 * 
 * @param number $album_id
 * @param number $portal_id (optional)
 * @param number $link_cat_id (optional)
 * @param number $product_id (optional)
 * @param number $product_type_id (optional)
 * @param number $query_type_id (optional)
 * @param number $country_id (optional)
 * @return boolean
 */
function cug_cache_del_album_culink($album_id, $portal_id=0, $link_cat_id=0, $product_id=0, $product_type_id=0, $query_type_id=0, $country_id=0) {
	global $mysqli_cache, $Tables;
	$result = false;

	if($album_id > 0) {
		$query = "DELETE FROM {$Tables['cache_culink_album']} WHERE album_id=$album_id";

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
 * Get Album from 'cache_albums' table
 *
 * @param integer $album_id
 * @return array
 */
function cug_cache_get_album($album_id)
{
	global $mysqli_cache, $Tables;
	$empty = array();

	if($album_id > 0) {
		$query = "SELECT * FROM ".$Tables['cache_albums']." WHERE album_id=$album_id";

		if($r = $mysqli_cache->query($query)) {
			return $r->fetch_array(MYSQLI_ASSOC);
		}
	}

	return $empty;
}
?>