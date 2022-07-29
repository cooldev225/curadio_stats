<?PHP

/**
 * Get Playlist-Track Relations
 * 
 * @param int $portal_id
 * @param int $playlist_id
 * @param string $playlist_track_id
 * @param int $track_id
 * @param bool $get_playlist_info (Optional, default: false)
 * @param int $playlist_status (Optional, default: -1)
 * @return array
 */
function cug_get_portal_playlist_track_rel($portal_id, $playlist_id, $playlist_track_id, $track_id, $get_playlist_info=false, $playlist_status=-1) {
	global $mysqli, $Tables;
	$result = array();
	
	if($portal_id > 0 || $playlist_id > 0 || $playlist_track_id || $track_id > 0) {
		$query = "SELECT pt.id, pt.portal_id, pt.playlist_id, pt.playlist_track_id, pt.track_id, pt.playlist_track_num";
		$query .= ($get_playlist_info) ? ", p.playlist_id AS portal_playlist_id, p.playlist_title, p.playlist_url, p.status" : "";
		$query .= " FROM {$Tables['portal_playlist_track']} AS pt";
		$query .= ($get_playlist_info) ? " LEFT JOIN {$Tables['portal_playlist']} AS p ON pt.playlist_id=p.id" : "";
		
		$query .= " WHERE";
		$query .= ($portal_id > 0) ? " pt.portal_id=$portal_id AND" : "";
		$query .= ($playlist_id > 0) ? " pt.playlist_id=$playlist_id AND" : "";
		$query .= ($playlist_track_id) ? " pt.playlist_track_id='".$mysqli->escape_str($playlist_track_id)."' AND" : ""; 
		$query .= ($track_id > 0) ? " pt.track_id=$track_id AND" : "";
		$query .= ($get_playlist_info && $playlist_status >= 0) ? (($playlist_status > 0) ? " p.status=1 AND" : " p.status=0 AND") : "";

		$query = rtrim($query, "AND");
		$r = $mysqli->query($query);
		
		if($r && $r->num_rows) {
			while($row = $r->fetch_array(MYSQL_ASSOC)) {
				$result[] = $row;
			}
		}
		
	}
	
	return $result;
}


/**
 * Get Playlist-Genre Relations
 * 
 * @param int $portal_id
 * @param int $playlist_id
 * @param int $genre_id
 * @param int $fileunder_id
 * @param bool $get_playlist_info (Optional, default: false)
 * @param int $playlist_status (Optional, default: -1)
 * @return array
 */
function cug_get_portal_playlist_genre_rel($portal_id, $playlist_id, $genre_id, $fileunder_id, $get_playlist_info=false, $playlist_status=-1) {
	global $mysqli, $Tables;
	$result = array();

	if($portal_id > 0 || $playlist_id > 0 || $genre_id || $fileunder_id > 0) {
		$query = "SELECT pg.id, pg.portal_id, pg.playlist_id, pg.genre_id, pg.fileunder_id";
		$query .= ($get_playlist_info) ? ", p.playlist_id AS portal_playlist_id, p.playlist_title, p.playlist_url, p.status" : "";
		$query .= " FROM {$Tables['portal_playlist_genre']} AS pg";
		$query .= ($get_playlist_info) ? " LEFT JOIN {$Tables['portal_playlist']} AS p ON pg.playlist_id=p.id" : "";

		$query .= " WHERE";
		$query .= ($portal_id > 0) ? " pg.portal_id=$portal_id AND" : "";
		$query .= ($playlist_id > 0) ? " pg.playlist_id=$playlist_id AND" : "";
		$query .= ($genre_id > 0) ? " pg.genre_id=$genre_id AND" : "";
		$query .= ($fileunder_id > 0) ? " pg.fileunder_id=$fileunder_id AND" : "";
		$query .= ($get_playlist_info && $playlist_status >= 0) ? (($playlist_status > 0) ? " p.status=1 AND" : " p.status=0 AND") : "";

		$query = rtrim($query, "AND");
		$r = $mysqli->query($query);

		if($r && $r->num_rows) {
			while($row = $r->fetch_array(MYSQL_ASSOC)) {
				$result[] = $row;
			}
		}

	}

	return $result;
}


/**
 * Get list of Portal's Playlists
 * 
 * @param int $id
 * @param int $portal_id
 * @param string $playlist_id
 * @param string $playlist_title
 * @param int $status (Optional, default: -1)
 * @return array
 */
function cug_get_portal_playlists($id, $portal_id, $playlist_id, $playlist_title, $status=-1) {
	global $mysqli, $Tables;
	$result = array();

	if($id > 0 || $portal_id > 0 || $playlist_id || $playlist_title || $status >= 0) {
		$query = "SELECT * FROM {$Tables['portal_playlist']} WHERE";
		
		if($id > 0) {
			$query .= " id=$id AND";
		}
		else {
			$query .= ($portal_id > 0) ? " portal_id=$portal_id AND" : "";
			$query .= ($playlist_id) ? " playlist_id='".$mysqli->escape_str($playlist_id)."' AND" : "";
			$query .= ($playlist_title) ? " playlist_title='".$mysqli->escape_str($playlist_title)."' AND" : "";
			
			if($status >= 0) {
				$query .= ($status > 0) ? " status=1 AND" : " (status IS NULL OR status=0) AND";
			}
		}
		
		$query = rtrim($query, "AND");
		$r = $mysqli->query($query);

		if($r && $r->num_rows) {
			while($row = $r->fetch_array(MYSQL_ASSOC)) {
				$result[] = $row;
			}
		}

	}

	return $result;
}


/**
 * Register Playlist-Track Relation
 * 
 * @param int $portal_id
 * @param int $playlist_id
 * @param string $playlist_track_id
 * @param int $track_id
 * @param int $playlist_track_num
 * @param string $reg_time (Optional, default: '')
 * @return number
 */
function cug_reg_portal_playlist_track_rel($portal_id, $playlist_id, $playlist_track_id, $track_id, $playlist_track_num, $reg_time='') {
	global $mysqli, $Tables;
	
	if($portal_id > 0 && $playlist_id > 0 && $playlist_track_id && $track_id > 0 && $playlist_track_num > 0) {
		//check for existing entry
		$data = cug_get_portal_playlist_track_rel($portal_id, $playlist_id, $playlist_track_id, $track_id);
		
		if(!count($data)) {
			$query = "INSERT INTO {$Tables['portal_playlist_track']} (portal_id,playlist_id,playlist_track_id,track_id,playlist_track_num,reg_time)";
			$query .= " VALUES($portal_id,$playlist_id,'".$mysqli->escape_str($playlist_track_id)."',$track_id,$playlist_track_num";
			$query .= ($reg_time) ? ",'".$mysqli->escape_str($reg_time)."')" : ",NOW())";
			
			if($mysqli->query($query))
				return $mysqli->insert_id;
			else 
				return -1;
		}
		else
			return 0; //already exists
	}
	
	return -2;
}


/**
 * Register Playlist-Genre Relation
 * 
 * @param int $portal_id
 * @param int $playlist_id
 * @param int $genre_id
 * @param int $fileunder_id (Optional, default: 0)
 * @param string $reg_time (Optional, default: '')
 * @return number (0 - Already exists; -1 - insert error; -2 - not enough fields; positive number [insert_id] - OK;)
 */
function cug_reg_portal_playlist_genre_rel($portal_id, $playlist_id, $genre_id, $fileunder_id=0, $reg_time='') {
	global $mysqli, $Tables;

	if($portal_id > 0 && $playlist_id > 0 && $genre_id > 0) {
		//check for existing entry
		$data = cug_get_portal_playlist_genre_rel($portal_id, $playlist_id, $genre_id, $fileunder_id);

		if(!count($data)) {
			$query = "INSERT INTO {$Tables['portal_playlist_genre']} (portal_id,playlist_id,genre_id,fileunder_id,reg_time)";
			$query .= " VALUES($portal_id,$playlist_id,$genre_id";
			$query .= ($fileunder_id > 0) ? ",$fileunder_id" : ",NULL";
			$query .= ($reg_time) ? ",'".$mysqli->escape_str($reg_time)."')" : ",NOW())";
				
			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else
			return 0; //already exists
	}

	return -2;
}


/**
 * Register New Portal's Playlist
 * 
 * @param int $portal_id
 * @param string $playlist_id
 * @param string $playlist_title
 * @param string $playlist_url
 * @param int $status (Optional, default: 1)
 * @param string $comments (Optional, default: '')
 * @param string $reg_time (Optional, default: '')
 * @return number (0 - Already exists; -1 - insert error; -2 - not enough fields; positive number [insert_id] - OK;)
 */
function cug_reg_portal_playlist($portal_id, $playlist_id, $playlist_title, $playlist_url, $status=1, $comments="", $reg_time="") {
	global $mysqli, $Tables;

	if($portal_id > 0 && $playlist_id && $playlist_title && $playlist_url) {
		//check for existing entry
		$data = cug_get_portal_playlists(0, $portal_id, $playlist_id, "");
		$id = !empty($data[0]['id']) ? $data[0]['id'] : 0;
		
		if(!$id) {
			$query = "INSERT INTO {$Tables['portal_playlist']} (portal_id,playlist_id,playlist_title,playlist_url,status,comments,reg_time)";
			$query .= " VALUES($portal_id,'".$mysqli->escape_str($playlist_id)."','".$mysqli->escape_str($playlist_title)."','".$mysqli->escape_str($playlist_url)."'";
			$query .= ($status > 0) ? ",$status" : ",0";
			$query .= ($comments) ? ",'".$mysqli->escape_str($comments)."'" : ",NULL";
			$query .= ($reg_time) ? ",'".$mysqli->escape_str($reg_time)."')" : ",NOW())";

			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else
			return 0; //already exists
	}

	return -2;
}
?>