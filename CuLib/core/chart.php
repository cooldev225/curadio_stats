<?PHP

/**
 * Register New Track's Chart
 * 
 * @param int $chart_type_id
 * @param int $pos
 * @param int $track_id
 * @param int $album_id
 * @param int $member_id
 * @param number $status (Optional, default: 1)
 * @param string $comments (Optional)
 * @param string $register_ip (Optional)
 * @return number
 */
function cug_reg_chart_track($chart_type_id, $pos, $track_id, $album_id, $member_id, $status=1, $comments="", $register_ip="") {
	global $mysqli, $Tables;
	
	if($chart_type_id > 0 && $pos > 0 && $track_id > 0 && $album_id > 0 && $member_id > 0) {
		//check for existing entry
		$arr = cug_get_chart_track($chart_type_id, $track_id);
		
		if(count($arr) == 0) {
			if(!$register_ip) $register_ip = $_SERVER['REMOTE_ADDR'];
			
			$query = "INSERT INTO {$Tables['chart_tracks']} (chart_type_id,track_id,album_id,member_id,pos,status";
			$query .= ($comments) ? ",comments" : "";
			$query .= ($register_ip) ? ",register_ip)" : ")";
			
			$query .= " VALUES($chart_type_id,$track_id,$album_id,$member_id,$pos,$status";
			$query .= ($comments) ? ",'".$mysqli->escape_str($comments)."'" : "";
			$query .= ($register_ip) ? ",'".$mysqli->escape_str($register_ip)."')" : ")";
			
				if($mysqli->query($query)) {
					return $mysqli->insert_id;
				}
				else {
					return -2; //unable to insert
				}
		}
		else {
			return -1; //entry already exists
		}
	}
	else {
		return 0; //not enough fields
	}
}


/**
 * Get Tracks Charts
 * 
 * @param int $chart_type_id
 * @param int $track_id (Optional, default: 0)
 * @param int $album_id (Optional, default: 0)
 * @param int $member_id (Optional, default: 0)
 * @param int $pos (Optional, default: -1)
 * @param int $status (Optional, default: -1)
 * @return array
 */
function cug_get_chart_track($chart_type_id, $track_id=0, $album_id=0, $member_id=0, $pos=-1, $status=-1) {
	global $mysqli, $Tables;
	$result = array();
	
	if($chart_type_id > 0) {
		$query = "SELECT * FROM {$Tables['chart_tracks']} WHERE chart_type_id=$chart_type_id";
		$query .= ($track_id > 0) ? " AND track_id=$track_id" : "";
		$query .= ($album_id > 0) ? " AND album_id=$album_id" : "";
		$query .= ($member_id > 0) ? " AND member_id=$member_id" : "";
		$query .= ($pos >= 0) ? " AND pos=$pos" : "";
		$query .= ($status >= 0) ? " AND status=$status" : "";
		$query .= " ORDER BY pos";
		
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
 * Get Tracks Charts (Alternative function, used for get data filled by statistics system)
 *
 * @param int $chart_type_id
 * @param int $track_id (Optional, default: 0)
 * @param int $album_id (Optional, default: 0)
 * @param int $member_id (Optional, default: 0)
 * @param int $pos (Optional, default: -1)
 * @param int $status (Optional, default: -1)
 * @return array
 */
function cug_get_chart_track_alt($chart_type_id, $track_id=0, $album_id=0, $member_id=0, $pos=-1, $status=-1) {
    global $mysqli, $Tables;
    $result = array();

    if($chart_type_id > 0) {
        $query = "SELECT * FROM {$Tables['chart_tracks_alt']} WHERE chart_type_id=$chart_type_id";
        $query .= ($track_id > 0) ? " AND track_id=$track_id" : "";
        $query .= ($album_id > 0) ? " AND album_id=$album_id" : "";
        $query .= ($member_id > 0) ? " AND member_id=$member_id" : "";
        $query .= ($pos >= 0) ? " AND pos=$pos" : "";
        $query .= ($status >= 0) ? " AND status=$status" : "";
        $query .= " ORDER BY pos";

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
 * Get Albums Charts
 * 
 * @param int $chart_type_id
 * @param int $album_id (Optional, default: 0)
 * @param int $member_id (Optional, default: 0)
 * @param int $pos (Optional, default: -1)
 * @param int $status (Optional, default: -1)
 * @return array
 */
function cug_get_chart_album($chart_type_id, $album_id=0, $member_id=0, $pos=-1, $status=-1) {
	global $mysqli, $Tables;
	$result = array();

	if($chart_type_id > 0) {
		$query = "SELECT * FROM {$Tables['chart_albums']} WHERE chart_type_id=$chart_type_id";
		$query .= ($album_id > 0) ? " AND album_id=$album_id" : "";
		$query .= ($member_id > 0) ? " AND member_id=$member_id" : "";
		$query .= ($pos >= 0) ? " AND pos=$pos" : "";
		$query .= ($status >= 0) ? " AND status=$status" : "";
		$query .= " ORDER BY pos";

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
 * Register New Album's Chart
 * 
 * @param int $pos
 * @param int $album_id
 * @param int $member_id
 * @param number $status (Optional, default: 1)
 * @param string $comments (Optional)
 * @param string $register_ip (Optional)
 * @return number
 */
function cug_reg_chart_album($chart_type_id, $pos, $album_id, $member_id, $status=1, $comments="", $register_ip="") {
	global $mysqli, $Tables;

	if($chart_type_id > 0 && $pos > 0 && $album_id > 0 && $member_id > 0) {
		//check for existing entry
		$arr = cug_get_chart_album($chart_type_id, $album_id);

		if(count($arr) == 0) {
			if(!$register_ip) $register_ip = $_SERVER['REMOTE_ADDR'];
			
			$query = "INSERT INTO {$Tables['chart_albums']} (chart_type_id,album_id,member_id,pos,status";
			$query .= ($comments) ? ",comments" : "";
			$query .= ($register_ip) ? ",register_ip)" : ")";

						
			$query .= " VALUES($chart_type_id,$album_id,$member_id,$pos,$status";
			$query .= ($comments) ? ",'".$mysqli->escape_str($comments)."'" : "";
			$query .= ($register_ip) ? ",'".$mysqli->escape_str($register_ip)."')" : ")";
			$query;
				
			if($mysqli->query($query)) {
				return $mysqli->insert_id;
			}
			else {
				return -2; //unable to insert
			}
		}
		else {
			return -1; //entry already exists
		}
	}
	else {
		return 0; //not enough fields
	}
}


/**
 * Get Members Charts
 *
 * @param int $chart_type_id
 * @param int $member_id (Optional, default: 0)
 * @param int $pos (Optional, default: -1)
 * @param int $status (Optional, default: -1)
 * @return array
 */
function cug_get_chart_member($chart_type_id, $member_id=0, $pos=-1, $status=-1) {
	global $mysqli, $Tables;
	$result = array();

	if($chart_type_id > 0) {
		$query = "SELECT * FROM {$Tables['chart_members']} WHERE chart_type_id=$chart_type_id";
		$query .= ($member_id > 0) ? " AND member_id=$member_id" : "";
		$query .= ($pos >= 0) ? " AND pos=$pos" : "";
		$query .= ($status >= 0) ? " AND status=$status" : "";
		$query .= " ORDER BY pos";

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
 * Register New Member's Chart
 *
 * @param int $pos
 * @param int $member_id
 * @param number $status (Optional, default: 1)
 * @param string $comments (Optional)
 * @param string $register_ip (Optional)
 * @return number
 */
function cug_reg_chart_member($chart_type_id, $pos, $member_id, $status=1, $comments="", $register_ip="") {
	global $mysqli, $Tables;

	if($chart_type_id > 0 && $pos > 0 && $member_id > 0) {
		//check for existing entry
		$arr = cug_get_chart_member($chart_type_id, $member_id);

		if(count($arr) == 0) {
			if(!$register_ip) $register_ip = $_SERVER['REMOTE_ADDR'];
			
			$query = "INSERT INTO {$Tables['chart_members']} (chart_type_id,member_id,pos,status";
			$query .= ($comments) ? ",comments" : "";
			$query .= ($register_ip) ? ",register_ip)" : ")";

			$query .= " VALUES($chart_type_id,$member_id,$pos,$status";
			$query .= ($comments) ? ",'".$mysqli->escape_str($comments)."'" : "";
			$query .= ($register_ip) ? ",'".$mysqli->escape_str($register_ip)."')" : ")";

			if($mysqli->query($query)) {
				return $mysqli->insert_id;
			}
			else {
				return -2; //unable to insert
			}
		}
		else {
			return -1; //entry already exists
		}
	}
	else {
		return 0; //not enough fields
	}
}


/**
 * Empty Charts Table
 * 
 * @param string $object_name ('ALBUM', 'TRACK', 'MEMBER')
 * @return number
 */
function cug_empty_chart_table($object_name) {
	global $mysqli, $Tables;
	
	switch(strtoupper($object_name)) {
		case 'ALBUM':
			$table = $Tables['chart_albums'];
		break;
		//---------------------
		case 'TRACK':
			$table = $Tables['chart_tracks'];
		break;
		//---------------------
		case 'MEMBER':
			$table = $Tables['chart_members'];
		break;
		//---------------------
		default:
			$table = "";
		break;	
	}
	
	if($table) {
		$query = "DELETE FROM $table;";
			if($mysqli->query($query)) {
				$mysqli->query("ALTER TABLE $table AUTO_INCREMENT=1");
				return 1;
			}	
			else 
				return -1;
	}
	else 
		return 0;
}
?>