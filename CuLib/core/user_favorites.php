<?PHP

/**
 * Get Table Name and Field Name of the Favorite Object
 *
 * @param string $object_name
 * @return array
 */
function cug_get_fav_object_table_and_field($object_name) {
	global $Tables;
	$result = array();

	switch(strtoupper($object_name)) {
		case 'TRACK';
			$result['table'] = $Tables['user_fav_track'];
			$result['field'] = "track_id";
		break;
		//-------------------------
		case 'ARTIST':
			$result['table'] = $Tables['user_fav_artist'];
			$result['field'] = "artist_id";
		break;
		//-------------------------
		case 'RSTATION':
			$result['table'] = $Tables['user_fav_rstation'];
			$result['field'] = "rstation_id";
		break;
		//-------------------------
		default:
			$result['table'] = "";
			$result['field'] = "";
		break;
	}

	return $result;
}


/**
 * Register User's Favorite Track
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $track_id
 * @param int $album_id
 * @param int $artist_id
 * @param int $pos_num (Position Number in the List, default: 1)
 * @return int
 */
function cug_reg_user_fav_track($user_id, $module_id, $track_id, $album_id, $artist_id, $pos_num=1) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $track_id > 0 && $album_id > 0 && $artist_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['user_fav_track']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND track_id=".$mysqli->escape_str($track_id)." AND album_id=".$mysqli->escape_str($album_id)." AND artist_id=".$mysqli->escape_str($artist_id);
		$r = $mysqli->query($query);

		if(!$r->num_rows) {
			//add new entry
			$pos = ($pos_num == 0) ? 1 : $pos_num;
			if($pos > 1) {
				$max_pos_num = cug_get_fav_object_max_pos($user_id, $module_id, 'TRACK');
					if($pos > ($max_pos_num + 1))
						$pos = $max_pos_num + 1;
			}
			
			$query = "INSERT INTO {$Tables['user_fav_track']} (user_id, module_id, track_id, album_id, artist_id, pos) ";
			$query .= "VALUES($user_id, $module_id, $track_id, $album_id, $artist_id, $pos)";
			
				if($mysqli->query($query)) {
					$insert_id = $mysqli->insert_id;
					cug_reorder_fav_object_pos($user_id, $module_id, 'TRACK', $action='ADD', $curr_pos=0, $new_pos=$pos, $track_id);
					return $insert_id;
				}
				else 
					return -1;
		}
		else {
			$row = $r->fetch_array();
			return $row['id'];
		}
	}
	
	return 0;
}


/**
 * Register User's Favorite Artist
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $artist_id
 * @param int $pos_num (default: 1)
 * @return int
 */
function cug_reg_user_fav_artist($user_id, $module_id, $artist_id, $pos_num=1) {
	global $mysqli, $Tables;
	
	if($user_id > 0 && $module_id > 0 && $artist_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['user_fav_artist']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND artist_id=".$mysqli->escape_str($artist_id);
		$r = $mysqli->query($query);
	
		if(!$r->num_rows) {
			//add new entry
			$pos = ($pos_num == 0) ? 1 : $pos_num;
			if($pos > 1) {
				$max_pos_num = cug_get_fav_object_max_pos($user_id, $module_id, 'ARTIST');
					if($pos > ($max_pos_num + 1))
						$pos = $max_pos_num + 1;
			}			
			
			$query = "INSERT INTO {$Tables['user_fav_artist']} (user_id, module_id, artist_id, pos) ";
			$query .= "VALUES($user_id, $module_id, $artist_id, $pos)";
				
			if($mysqli->query($query)) {
				$insert_id = $mysqli->insert_id;
				cug_reorder_fav_object_pos($user_id, $module_id, 'ARTIST', $action='ADD', $curr_pos=0, $new_pos=$pos, $artist_id);
				return $insert_id;
			}
			else
				return -1;
		}
		else {
			$row = $r->fetch_array();
			return $row['id'];
		}
	}
	
	return 0;
}

/**
 * Register User's Favorite RadioStation 
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $station_id
 * @param int $pos_num (default: 1)
 * @return int
 */
function cug_reg_user_fav_radio_station($user_id, $module_id, $station_id, $pos_num=1) {
	global $mysqli, $Tables;
	
	if($user_id > 0 && $module_id > 0 && $station_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['user_fav_rstation']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND rstation_id=".$mysqli->escape_str($station_id);
		$r = $mysqli->query($query);
	
		if(!$r->num_rows) {
			//add new entry
			$pos = ($pos_num == 0) ? 1 : $pos_num;
			if($pos > 1) {
				$max_pos_num = cug_get_fav_object_max_pos($user_id, $module_id, 'RSTATION');
					if($pos > ($max_pos_num + 1))
						$pos = $max_pos_num + 1;
			}			
			
			$query = "INSERT INTO {$Tables['user_fav_rstation']} (user_id, module_id, rstation_id, pos) ";
			$query .= "VALUES($user_id, $module_id, $station_id, $pos)";
	
			if($mysqli->query($query)) {
				$insert_id = $mysqli->insert_id;
				cug_reorder_fav_object_pos($user_id, $module_id, 'RSTATION', $action='ADD', $curr_pos=0, $new_pos=$pos, $station_id);
				return $insert_id;
			}
			else
				return -1;
		}
		else {
			$row = $r->fetch_array();
			return $row['id'];
		}
	}
	
	return 0;
}


/**
 * Delete User's Favorite Objects
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $object_name ('TRACK' or 'ARTIST' or 'RSTATION')
 * @param int $object_id (optional)
 * @return boolean
 */
function cug_del_user_fav_objects($user_id, $module_id, $object_name, $object_id=0) {
	global $mysqli, $Tables;
	
	if($user_id > 0 && $module_id > 0 && $object_name) {
		$arr = cug_get_fav_object_table_and_field($object_name);	
		
			if($arr['table'] && $arr['field']) {
				$query = "DELETE FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id";
								
					if($object_id > 0) {
						$query .= " AND {$arr['field']}=$object_id";
						$curr_pos = cug_get_fav_object_pos($user_id, $module_id, $object_name, $object_id);
					}
				
				$mysqli->query($query);
				
				if($object_id > 0)
					cug_reorder_fav_object_pos($user_id, $module_id, $object_name, $action='REMOVE', $curr_pos, $new_pos=0, $object_id=0);
				
				
				return true;
			}
	}
	
	return false;
}


/**
 * Get User's Favorite Objects
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $object_name ('TRACK' or 'ARTIST' or 'RSTATION')
 * @return array
 */
function cug_get_user_fav_objects($user_id, $module_id, $object_name) {
	global $mysqli, $Tables;
	$result = array();
	
	if($user_id > 0 && $module_id > 0 && $object_name) {
		$arr = cug_get_fav_object_table_and_field($object_name);		
		
			if($arr['table']) {
				$query = "SELECT * FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id ORDER BY pos";	
				$r = $mysqli->query($query);
				
				if($r) {
					while($row = $r->fetch_array(MYSQL_ASSOC)) {
						$result[] = $row;
					}
				}
				
				$result = cug_get_user_fav_objects_info($result);
			}
	}
	
	return $result;
}


/**
 * Get User's Favorite Objects Additional Info
 * 
 * @param array $arr (Array returned by cug_get_user_fav_objects() function)
 * @return array
 */
function cug_get_user_fav_objects_info($arr) {
	
	foreach($arr as $key=>$val) {
		// TRACK
		//-------------------------
		if(!empty($val['track_id'])) {
			$obj_arr = cug_cache_get_track($val['track_id']);
			
			$full_title = "";
			
			if(!empty($obj_arr['track_title'])) {
				$arr[$key]['track_title'] = $obj_arr['track_title'];
				$full_title = $obj_arr['track_title'];
			}
			//--------------
			if(!empty($obj_arr['track_part'])) {
				$arr[$key]['track_part'] = $obj_arr['track_part'];
				$full_title .= "/".$obj_arr['track_part'];
			}
			//-------------
			if(!empty($obj_arr['track_version'])) {
				$arr[$key]['track_version'] = $obj_arr['track_version'];
				$full_title .= "/".$obj_arr['track_version'];
			}
			//-------------
			if(!empty($obj_arr['track_time'])) {
				$arr[$key]['track_time'] = $obj_arr['track_time'];
			}
			//-------------
			if(!empty($obj_arr['file_url'])) {
				$arr[$key]['file_url'] = $obj_arr['file_url'];
			}
			//-------------
			$arr[$key]['track_full_title'] = $full_title;
		}
		
		// ALBUM
		//-------------------------
		if(!empty($val['album_id'])) {
			$obj_arr = cug_cache_get_album($val['album_id']);
			
			$full_title = "";
			
			if(!empty($obj_arr['album_title'])) {
				$arr[$key]['album_title'] = $obj_arr['album_title'];
				$full_title = $obj_arr['album_title'];
			}
			//-----------
			if(!empty($obj_arr['album_version'])) {
				$arr[$key]['album_version'] = $obj_arr['album_version'];
				$full_title = "/".$obj_arr['album_version'];
			}
			//-----------
			$arr[$key]['album_cover'] = !empty($obj_arr['cover_url']) ? $obj_arr['cover_url'] : "";
			//-------------
			$arr[$key]['album_full_title'] = $full_title;
		}
		
		// ARTIST
		//-------------------------
		if(!empty($val['artist_id'])) {
			$obj_arr = cug_cache_get_member($val['artist_id']);
								
			if(!empty($obj_arr['member_name'])) {
				$arr[$key]['artist_name'] = $obj_arr['member_name'];
			}
			//-----------
			$arr[$key]['artist_img'] = !empty($obj_arr['img_url']) ? $obj_arr['img_url'] : "";
		}		
	}
		
	return $arr;
}

/**
 * Get Last Listened Radio Stations List
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $sort_by (Optional, Structure: 'Field1|ASC;Field2|DESC,...' )
 * @param number $limit_from (Optional)
 * @param number $limit_quant (Optional)
 * @return array
 */
function cug_get_listened_rstations_list($user_id, $module_id, $sort_by="", $limit_from=0, $limit_quant=0) {
	global $mysqli, $Tables;
	$result = array();

	if($user_id > 0 && $module_id > 0) {
		$query = "SELECT * FROM {$Tables['listened_rstation']} WHERE user_id=$user_id AND module_id=$module_id";
		
		//parse 'sort_by'
		$sort = "";
		if($sort_by) {
			$arr = explode(";", $sort_by);
			foreach($arr as $val) {
				$arr2 = explode("|", trim($val));
		
				if(strtoupper(trim($arr2[0])) == "TIMESTAMP")
					$sort_field = "update_time";
				else
					$sort_field = trim($arr2[0]);
				//-------------------------
				if(!empty($arr2[1]))
					$sort_method = trim($arr2[1]);
				else
					$sort_method = (strtoupper(trim($arr2[0])) == "TIMESTAMP") ? "DESC" : "ASC";
				//-------------------------
		
				$sort .= " ".$sort_field." ".$sort_method.",";
			}
				
			if($sort) $sort = rtrim($sort, ",");
		}
		
		if(!$sort) $sort = " update_time DESC"; //by default
		//-----------------------------------
		
		$query .= " ORDER BY ".$sort;
		$query .= ($limit_quant > 0) ? " LIMIT $limit_from, $limit_quant" : "";
		$r = $mysqli->query($query);

			if($r) {
				while($row = $r->fetch_array(MYSQL_ASSOC)) {
					$result[] = $row;
				}
			}
	}

	return $result;
}


/**
 * Get Last Listened Tracks List
 *
 * @param int $user_id
 * @param int $module_id
 * @param int $rstation_id (Ooptional)
 * @param string $sort_by (Optional, Structure: 'Field1|ASC;Field2|DESC,...' )
 * @param number $limit_from (Optional)
 * @param number $limit_quant (Optional)
 * @return array
 */
function cug_get_listened_tracks_list($user_id, $module_id, $rstation_id=0, $sort_by="", $limit_from=0, $limit_quant=0) {
	global $mysqli, $Tables;
	$result = array();

	if($user_id > 0 && $module_id > 0) {
		$query = "SELECT * FROM {$Tables['listened_track']} WHERE user_id=$user_id AND module_id=$module_id";
		$query .= ($rstation_id > 0) ? " AND rstation_id=".$mysqli->escape_str($rstation_id) : "";
		
		//parse 'sort_by'
		$sort = "";
		if($sort_by) {
			$arr = explode(";", $sort_by);
			foreach($arr as $val) {
				$arr2 = explode("|", trim($val));
		
				if(strtoupper(trim($arr2[0])) == "TIMESTAMP")
					$sort_field = "update_time";
				else
					$sort_field = trim($arr2[0]);
				//-------------------------
				if(!empty($arr2[1]))
					$sort_method = trim($arr2[1]);
				else
					$sort_method = (strtoupper(trim($arr2[0])) == "TIMESTAMP") ? "DESC" : "ASC";
				//-------------------------
		
				$sort .= " ".$sort_field." ".$sort_method.",";
			}
				
			if($sort) $sort = rtrim($sort, ",");
		}
		
		if(!$sort) $sort = " update_time DESC"; //by default
		//-----------------------------------
		
		$query .= " ORDER BY ".$sort;		
		$query .= ($limit_quant > 0) ? " LIMIT $limit_from, $limit_quant" : "";
		$r = $mysqli->query($query);

		if($r) {
			while($row = $r->fetch_array(MYSQL_ASSOC)) {
				$result[] = $row;
			}
			
			$result = cug_get_user_fav_objects_info($result);
		}
	}

	return $result;
}


/**
 * Add Listened Radio Station
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $rstation_id
 * @return number
 */
function cug_reg_listened_rstation($user_id, $module_id, $rstation_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $rstation_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['listened_rstation']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND rstation_id=".$mysqli->escape_str($rstation_id);
		$r = $mysqli->query($query);

		if(!$r->num_rows) {
			//add new entry
			$query = "INSERT INTO {$Tables['listened_rstation']} (user_id, module_id, rstation_id) ";
			$query .= "VALUES($user_id, $module_id, $rstation_id)";

				if($mysqli->query($query))
					return $mysqli->insert_id;
				else
					return -1;
		}
		else { //update existing
			$row = $r->fetch_array();
			$id = $row['id'];
			
			$query = "UPDATE {$Tables['listened_rstation']} SET update_time=NOW() WHERE id=$id";
			
				if($mysqli->query($query))
					return $id;
				else
					return -2;
		}
	}

	return 0;
}


/**
 * Add Listened Track
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $track_id
 * @param int $album_id
 * @param int $artist_id
 * @param int $rstation_id
 * @return number
 */
function cug_reg_listened_track($user_id, $module_id, $track_id, $album_id, $artist_id, $rstation_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $track_id > 0 && $album_id > 0 && $artist_id > 0 && $rstation_id > 0) {

		//add new entry
		$query = "INSERT INTO {$Tables['listened_track']} (user_id, module_id, track_id, album_id, artist_id, rstation_id) ";
		$query .= "VALUES($user_id, $module_id, $track_id, $album_id, $artist_id, $rstation_id)";

		if($mysqli->query($query))
			return $mysqli->insert_id;
		else
			return -1;
	}

	return 0;
}


/**
 * Delete Listened Radio Station
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $rstation_id
 * @return number
 */
function cug_del_listened_rstation($user_id, $module_id, $rstation_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $rstation_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['listened_rstation']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND rstation_id=".$mysqli->escape_str($rstation_id);
		$r = $mysqli->query($query);

		if($r->num_rows) {
			//delete entry
			$row = $r->fetch_array();
			$id = $row['id'];
			
			$query = "DELETE FROM {$Tables['listened_rstation']} WHERE id=$id";

				if($mysqli->query($query))
					return 1;
				else
					return -1;
		}
		else {
			return -2; // $rstation_id does not exists
		}
	}

	return 0;
}


/**
 * Delete Listened Track
 *
 * @param int $user_id
 * @param int $module_id
 * @param int $track_id
 * @return number
 */
function cug_del_listened_track($user_id, $module_id, $track_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $track_id > 0) {
		//check for existing entry
		$query = "SELECT id FROM {$Tables['listened_track']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id)." AND track_id=".$mysqli->escape_str($track_id);
		$r = $mysqli->query($query);

		if($r->num_rows) {
			//delete entry			
			$query = "DELETE FROM {$Tables['listened_track']} WHERE track_id=$track_id";

			if($mysqli->query($query))
				return 1;
			else
				return -1;
		}
		else {
			return -2; // $track_id does not exists
		}
	}

	return 0;
}

/**
 * Empty Listened Radio Stations
 *
 * @param int $user_id
 * @param int $module_id
 * @return number
 */
function cug_empty_listened_rstations($user_id, $module_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0) {
		$query = "DELETE FROM {$Tables['listened_rstation']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id);

			if($mysqli->query($query))
				return 1;
			else
				return -1;
	}

	return 0;
}


/**
 * Empty Listened Tracks
 *
 * @param int $user_id
 * @param int $module_id
 * @return number
 */
function cug_empty_listened_tracks($user_id, $module_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0) {
		$query = "DELETE FROM {$Tables['listened_track']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id);

		if($mysqli->query($query))
			return 1;
		else
			return -1;
	}

	return 0;
}


/**
 * Get Position (in the list) of the Favorite Object
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $object_name
 * @param int $object_id
 * @return int
 */
function cug_get_fav_object_pos($user_id, $module_id, $object_name, $object_id) {
	global $mysqli, $Tables;
	$result = 0;
	
	if($user_id > 0 && $module_id > 0 && $object_name && $object_id > 0) {	
		$arr = cug_get_fav_object_table_and_field($object_name);
		
			if($arr['table'] && $arr['field']) {
				$query = "SELECT pos FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id AND {$arr['field']}=$object_id";
				$r = $mysqli->query($query);
				
					if($r) {
						if($r->num_rows) {
							$row = $r->fetch_array(MYSQL_ASSOC);
							$result = $row['pos'];
						}
					}
			}
	}

	return $result;
}


/**
 * Reorder Favorite Objects Positions in the List
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $object_name
 * @param string $action
 * @param int $curr_pos
 * @param int $new_pos
 * @param int $object_id (optional, default value: 0)
 * @return bool
 */
function cug_reorder_fav_object_pos($user_id, $module_id, $object_name, $action, $curr_pos, $new_pos, $object_id=0) {
	global $mysqli, $Tables;
	$result = false;
	
	if($user_id > 0 && $module_id > 0 && $object_name && $action) {
		$arr = cug_get_fav_object_table_and_field($object_name);
		
		if($arr['table'] && $arr['field']) {
			switch(strtoupper($action)) {
				//New Object was added
				//-----------------
				case 'ADD':
					if($new_pos > 0 && $object_id > 0) {
						$query = "SELECT id FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id AND pos>=$new_pos AND {$arr['field']}<>$object_id ORDER BY pos";
						$r = $mysqli->query($query);
						
						if($r) {
							if($r->num_rows) {
								$counter = 0;
								while($row = $r->fetch_array(MYSQL_ASSOC)) {
									$id = $row['id'];
									
									$counter ++;
									$new_pos_num = $new_pos + $counter;
									
									$query = "UPDATE {$arr['table']} SET pos=$new_pos_num WHERE id=$id";
									$mysqli->query($query);
								}
							}
						}
						
						$result = true;
					}
				break;
				
				//Object was removed
				//-----------------
				case 'REMOVE':
					if($curr_pos > 0) {
						$query = "SELECT id FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id AND pos>=$curr_pos ORDER BY pos";
						$r = $mysqli->query($query);
						
						if($r) {
							if($r->num_rows) {
								$counter = 0;
								while($row = $r->fetch_array(MYSQL_ASSOC)) {
									$id = $row['id'];									
									$counter ++;
									
										if($counter == 1)
											$new_pos_num = $curr_pos;
										else 
											$new_pos_num = $curr_pos + $counter - 1;
										
									$query = "UPDATE {$arr['table']} SET pos=$new_pos_num WHERE id=$id";
									$mysqli->query($query);
								}
							}
						}	

						$result = true;
					}
				break;
				
				//Object's position was changed
				//-----------------
				case 'CHANGE':
					if($curr_pos > 0 && $new_pos > 0 && $object_id > 0) {
						//check new position
						$max_pos_num = cug_get_fav_object_max_pos($user_id, $module_id, $object_name);
							
							if($new_pos > $max_pos_num)
								$new_pos = $max_pos_num;
						
							
						//set new position
						$query = "UPDATE {$arr['table']} SET pos=$new_pos WHERE user_id=$user_id AND module_id=$module_id AND {$arr['field']}=$object_id";
						if($mysqli->query($query)) {
							//---------------------------
							if($new_pos > $curr_pos) {//moved down
								$count = $new_pos - $curr_pos;
								$query = "SELECT id, pos FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id AND pos>$curr_pos AND {$arr['field']}<>$object_id ORDER BY pos LIMIT 0, $count";
								$r = $mysqli->query($query);
								
								if($r) {
									if($r->num_rows) {
										while($row = $r->fetch_array(MYSQL_ASSOC)) {
											$id = $row['id'];
											$curr_pos_num = $row['pos'];
											
											$new_pos_num = $curr_pos_num - 1;
											$query = "UPDATE {$arr['table']} SET pos=$new_pos_num WHERE id=$id";
											$mysqli->query($query);
										}
										
										$result = true;
									}
								}
							}
							//---------------------------
							elseif($new_pos < $curr_pos) {//moved up
								$count = $curr_pos - $new_pos;
								$query = "SELECT id, pos FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id AND pos<$curr_pos AND pos>=$new_pos AND {$arr['field']}<>$object_id ORDER BY pos LIMIT 0, $count";
								$r = $mysqli->query($query);
								
								if($r) {
									if($r->num_rows) {
										while($row = $r->fetch_array(MYSQL_ASSOC)) {
											$id = $row['id'];
											$curr_pos_num = $row['pos'];
												
											$new_pos_num = $curr_pos_num + 1;
											$query = "UPDATE {$arr['table']} SET pos=$new_pos_num WHERE id=$id";
											$mysqli->query($query);
										}
								
										$result = true;
									}
								}								
							}
							//----------------------------
						}
					}
				break;
			}
		}
	}
	
	return $result;
}


/**
 * Get Max Position of the Favorite Object
 * 
 * @param int $user_id
 * @param int $module_id
 * @param string $object_name
 * @return int
 */
function cug_get_fav_object_max_pos($user_id, $module_id, $object_name) {
	global $mysqli, $Tables;
	$result = -1;
	
	if($user_id > 0 && $module_id > 0 && $object_name) {
		$arr = cug_get_fav_object_table_and_field($object_name);
	
		if($arr['table'] && $arr['field']) {
			$query = "SELECT MAX(pos) FROM {$arr['table']} WHERE user_id=$user_id AND module_id=$module_id";
			$r = $mysqli->query($query);
			
				if($r) {
					if($r->num_rows) {
						$row = $r->fetch_array();
						$result = $row[0];
					}
					else {
						$result = 0;
					}
				}
		}
	}
	
	return $result;
}



/**
 * Add (incriment) most listened Radio Station
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $rstation_id
 * @return boolean
 */
function cug_add_most_listened_rstation($user_id, $module_id, $rstation_id) {
	global $mysqli, $Tables;
	
	if($user_id > 0 && $module_id > 0 && $rstation_id > 0) {
		$arr = cug_get_most_listened_rstations_list($user_id, $module_id, $top_num=1, $rstation_id);
		
		if(!empty($arr[0]['id'])) {
			$id = $arr[0]['id'];
			$query = "UPDATE {$Tables['most_listened_rstation']} SET listened=listened+1 WHERE id=$id";
			
				if($mysqli->query($query))
					return true; 
		}
		else {
			if(cug_reg_most_listened_rstation($user_id, $module_id, $rstation_id))
				return true;
		}
	}
	
	return false;
	
}


/**
 * Get Most Listened Radio Stations
 * 
 * @param int $user_id
 * @param int $module_id
 * @param number $top_num (Optional, default: 10)
 * @param int $rstation_id (Optional, default: 0)
 * @return array
 */
function cug_get_most_listened_rstations_list($user_id, $module_id, $top_num=10, $rstation_id=0) {
	global $mysqli, $Tables;
	$result = array();
	
	if($user_id > 0 && $module_id > 0) {
		$query = "SELECT * FROM {$Tables['most_listened_rstation']} WHERE user_id=$user_id AND module_id=$module_id";
		$query .= ($rstation_id > 0) ? " AND rstation_id=$rstation_id" : "";
		$query .= " ORDER BY listened DESC";
		$query .= ($top_num > 0) ? " LIMIT 0, $top_num" : " LIMIT 0, 10";
		
		$r = $mysqli->query($query);
		
		if($r) {
			while($row = $r->fetch_array(MYSQL_ASSOC)) {
				$result[] = $row;
			}
		}		
	}
	
	return $result;
}


/**
 * Register New Entry in Most Listened Radio Stations List
 * 
 * @param int $user_id
 * @param int $module_id
 * @param int $rstation_id
 * @return int
 */
function cug_reg_most_listened_rstation($user_id, $module_id, $rstation_id) {
	global $mysqli, $Tables;

	if($user_id > 0 && $module_id > 0 && $rstation_id > 0) {
		$query = "INSERT INTO {$Tables['most_listened_rstation']} (user_id, module_id, rstation_id, listened) VALUES($user_id, $module_id, $rstation_id, 1)";

		if($mysqli->query($query)) {
			return $mysqli->insert_id;
		}
	}

	return 0;
}
?>