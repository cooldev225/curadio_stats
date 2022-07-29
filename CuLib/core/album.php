<?PHP

/**
 * Get Album Data (Get all related data of the aAlbum)
 * 
 * @param int $album_id
 * @param bool $album_info (Optional, return album info as well, default: false)
 * @param bool $album_members (Optional, return album's members, default: true)
 * @param bool $album_clients (Optional, return album's clients, default: true)
 * @param bool $album_discs (Optional, return album's discs-tracks, default: true)
 * @return array
 */
function cug_get_album_data($album_id, $album_info=false, $album_members=true, $album_clients=true, $album_discs=true) {
    global $mysqli, $Tables;
    $result = array();
    
    if($album_id > 0) {
        //get album info
        if($album_info) {
            $result['album'] = (array)cug_get_album($album_id);
        }
        
        //get members
        if($album_members)
            $result['members'] = cug_get_album_members($album_id);
        
        //get clients
        if($album_clients)
            $result['clients'] = cug_get_album_clients($album_id);
        
        //get discs-tracks
        if($album_discs) {
            $arr = cug_get_album_tracks($album_id);
            
            foreach($arr as $disc_key=>$disc) {
                foreach($disc['tracks'] as $track_index=>$track) {
                    $track_id = $track['track_id'];
                    
                    //get genre title
                    if(!empty($track['genre_id'])) {
                        $temp_arr = $mysqli->get_field_val($Tables['genre'], "title", "id={$track['genre_id']}");
                        $arr[$disc_key]['tracks'][$track_index]['genre_title'] = $temp_arr[0]['title'];
                    }
                    else {
                        $arr[$disc_key]['tracks'][$track_index]['genre_title'] = "";
                    }
                        
                    //get fileunder title
                    if(!empty($track['fileunder_id'])) {
                        $temp_arr = $mysqli->get_field_val($Tables['genre'], "title", "id={$track['fileunder_id']}");
                        $arr[$disc_key]['tracks'][$track_index]['fileunder_title'] = $temp_arr[0]['title'];
                    }
                    else {
                        $arr[$disc_key]['tracks'][$track_index]['fileunder_title'] = "";
                    }
                        
                    //get track-copyright_c
                    if(!empty($track['copyright_c'])) {
                        $arr[$disc_key]['tracks'][$track_index]['copyright_c_title'] = cug_get_member_name($track['copyright_c']);
                    }
                    else {
                        $arr[$disc_key]['tracks'][$track_index]['copyright_c_title'] = "";
                    }
                        
                    //get language code
                    if(!empty($track['lang_id'])) {
                        $temp_arr = $mysqli->get_field_val($Tables['lang_details'], "code_represent", "id={$track['lang_id']}");
                        $arr[$disc_key]['tracks'][$track_index]['lang_code'] = $temp_arr[0]['title'];
                    }
                    else {
                        $arr[$disc_key]['tracks'][$track_index]['lang_code'] = "";
                    }
                    
                    //get track-members
                    $arr[$disc_key]['tracks'][$track_index]['members'] = cug_get_track_members($track_id);
                    
                    //get track-clients
                    $arr[$disc_key]['tracks'][$track_index]['clients'] = cug_get_track_clients($track_id);
                    
                    //get track-publishers
                    $arr[$disc_key]['tracks'][$track_index]['publishers'] = cug_get_track_publishers($track_id);
                }
            }
            
            $result['discs'] = $arr;
        }
        //------------------------
    }
    
    return $result;
}

/**
 * Get Album Clients (from 'album_client_rel' table)
 * 
 * @param int $album_id
 * @return array
 */
function cug_get_album_clients($album_id) {
    global $mysqli, $Tables;
    
    $result = array();
    $index = 0;
    
    $r = $mysqli->query("CALL get_album_clients($album_id)");
    
    if($r->num_rows) {
        while($arr = $r->fetch_assoc()) {
            $result[$index] = $arr;
            $index ++;
        }
    }
    
    if($mysqli->more_results())
        $mysqli->next_result();
    
    return 	$result;
}


/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Album
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Album Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	subtitle (STRING)
 * @param	title_version (STRING)
 * @param	genre_id (INT)
 * @param	fileunder_id (INT)
 * @param	genre_from_file (STRING)
 * @param	lang_id (INT)
 * @param	total_discs (INT)
 * @param	label_id (INT)
 * @param	label_code (STRING)
 * @param	catalogue_num (STRING)
 * @param	tag_status_ig (STRING)
 * @param	rec_date (STRING - MySQL DATE Format)
 * @param	rel_date (STRING - MySQL DATE Format)
 * @param	sale_start_date (STRING - MySQL DATE Format)
 * @param	ean_code (STRING)
 * @param	upc_code (STRING)
 * @param	type_id (INT)
 * @param	rel_format_id (INT)
 * @param	package_id (INT)
 * @param	asin_p (STRING)
 * @param	asin_d (STRING)
 * @param	img_path (STRING)
 * @param	img_34 (INT)
 * @param	img_64 (INT)
 * @param	img_174 (INT)
 * @param	img_300 (INT)
 * @param	img_600 (INT)
 * @param	img_orgn (INT - Size of the Original Image File)
 * @param	copyright_c (INT)
 * @param	copyright_c_date (STRING - MySQL DATE Format)
 * @param	copyright_p (INT)
 * @param	copyright_p_date (STRING - MySQL DATE Format)
 * @param	register_from (INTEGER)
 * @param	register_date (STRING - MySQL DATETIME Format)
 * @param	register_ip (STRING) 
 * @param	trash_status (INT)
 * @param	online (INT)
 * @param	uniqid - STRING
 * @param	country_allowed - STRING
 * @param	explicit_content - (INT)
 * @param	gapless_playing - (INT)
 * @param	external_id - STRING (Some identification for the Album, which comes from the Client)
 * @param	shenzhen_id - INT
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__album
{
	public 
	$id,
	$title,
	$subtitle,
	$title_version,
	$genre_id,
	$fileunder_id,
	$genre_from_file,
	$lang_id,
	$total_discs,
	$label_id,
	$label_code,
	$catalogue_num,
	$tag_status_id,
	$rec_date,
	$rel_date,
	$sale_start_date,
	$ean_code,
	$upc_code,
	$type_id,
	$rel_format_id,
	$package_id,
	$asin_p,
	$asin_d,
	$img_path,
	$img_34,
	$img_64,
	$img_174,
	$img_300,
	$img_600,
	$img_orgn,
	$copyright_c,
	$copyright_c_date,
	$copyright_p,
	$copyright_p_date,
	$register_from,
	$register_date,
	$register_ip,
	$trash_status,
	$online,
	$uniqid,
	$country_allowed,
	$explicit_content,
	$gapless_playing,
	$external_id,
	$shenzhen_id,
	$update_time;
}


/**
 * Album's Disc Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	disc_num (INT)
 * @param	total_tracks (INT)
 * @param	total_time (INT)
 * @param	album_id (INT)
 * @param	img_path (STRING)
 * @param	img_34 (INT)
 * @param	img_64 (INT)
 * @param	img_174 (INT)
 * @param	img_300 (INT)
 * @param	img_600 (INT)
 * @param	img_orgn (INT - Size of the Original Image File)
 * @param	uniqid - STRING
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__album_disc
{
	public 
	$id,
	$title,
	$disc_num,
	$total_tracks,
	$total_time,
	$album_id,
	$img_path,
	$img_34,
	$img_64,
	$img_174,
	$img_300,
	$img_600,
	$img_orgn,
	$uniqid,
	$update_time;
}


/**
 * Get Album Format List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_album_format_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['album_format']." ORDER BY ".$field." ".$sort_type);
	
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}	
		}

return $result;
}

/**
 * Get Album Format (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_album_format($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['album_format'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['album_format'], "title", "id=".$mysqli->escape_str($item));
		}
		else {
			return 0;
		}


		if(!empty($result[0][0])) {
			return $result[0][0];
		}
		else {
			return 0;
		}

	}
	else {
		return 0;
	}
}


/**
 * Register New Album Format
 *
 * @param string
 * @return integer
 */
function cug_reg_album_format($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['album_format'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['album_format']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
				return $mysqli->insert_id;
			}
			else {
				return -1; // Error
			}
		}
		else {
			return $result[0]['id'];
		}
	}
	else {
		return 0;
	}
}


/**
 * Edit Existing Album Format
 *
 * @param integer (ID of Existing Album Format)
 * @param string (New Album Format)
 * @return integer
 */
function cug_edit_album_format($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['album_format']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
			return 1;
		}
		else {
			return -1; // Error
		}
	}
	else {
		return 0;
	}
}


/**
 * Get Album Package List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_album_package_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['album_package']." ORDER BY ".$field." ".$sort_type);
	
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}	
		}

return $result;
}

/**
 * Get Album Package (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_album_package($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['album_package'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['album_package'], "title", "id=".$mysqli->escape_str($item));
		}
		else {
			return 0;
		}


		if(!empty($result[0][0])) {
			return $result[0][0];
		}
		else {
			return 0;
		}

	}
	else {
		return 0;
	}
}


/**
 * Get Album Type List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_album_type_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['album_type']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get Album Type (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_album_type($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['album_type'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['album_type'], "title", "id=".$mysqli->escape_str($item));
		}
		else {
			return 0;
		}


		if(!empty($result[0][0])) {
			return $result[0][0];
		}
		else {
			return 0;
		}

	}
	else {
		return 0;
	}
}


/**
 * Register New Album Package
 *
 * @param string
 * @return integer
 */
function cug_reg_album_package($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['album_package'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['album_package']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
				return $mysqli->insert_id; // OK
			}
			else {
				return -1; // Error
			}
		}
		else {
			return $result[0]['id']; // Already Exist
		}
	}
	else {
		return 0;
	}
}

/**
 * Edit Existing Album Package
 *
 * @param integer (ID of Existing Album Package)
 * @param string (New Album package)
 * @return integer
 */
function cug_edit_album_package($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['album_package']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
			return 1;
		}
		else {
			return -1; // Error
		}
	}
	else {
		return 0;
	}
}


/**
 * Register New Disc
 *
 * @param object of album_disc Class
 * @return integer
 */
function cug_reg_disc($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if($obj->disc_num != null && $obj->disc_num >= 0) {
			$fields .= "disc_num,";
			$values .= $mysqli->escape_str($obj->disc_num).",";
		}

		if(!empty($obj->total_tracks)) {
			$fields .= "total_tracks,";
			$values .= $mysqli->escape_str($obj->total_tracks).",";
		}

		if(!empty($obj->total_time)) {
			$fields .= "total_time,";
			$values .= $mysqli->escape_str($obj->total_time).",";
		}
		
		if(!empty($obj->album_id)) {
			$fields .= "album_id,";
			$values .= $mysqli->escape_str($obj->album_id).",";
		}
		
		if(!empty($obj->img_path)) {
			$fields .= "img_path,";
			$values .= "'".$mysqli->escape_str($obj->img_path)."',";
		}

		if($obj->img_34 != null && $obj->img_34 >= 0) {
			$fields .= "img_34,";
			$values .= $obj->img_34.",";
		}

		if($obj->img_64 != null && $obj->img_64 >= 0) {
			$fields .= "img_64,";
			$values .= $obj->img_64.",";
		}

		if($obj->img_174 != null && $obj->img_174 >= 0) {
			$fields .= "img_174,";
			$values .= $obj->img_174.",";
		}
		
		if($obj->img_300 != null && $obj->img_300 >= 0) {
			$fields .= "img_300,";
			$values .= $obj->img_300.",";
		}
		
		if($obj->img_600 != null && $obj->img_600 >= 0) {
			$fields .= "img_600,";
			$values .= $obj->img_600.",";
		}
		
		if($obj->img_orgn != null && $obj->img_orgn >= 0) {
			$fields .= "img_orgn,";
			$values .= $obj->img_orgn.",";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}

		$fields .= "uniqid)";
		if(!empty($obj->uniqid)) {
			$values .= "'".$mysqli->escape_str($obj->uniqid)."')";
		}
		else {
			$uniqid = uniqid();
			$values .= "'".$uniqid."')";
		}


		$query = "INSERT INTO ".$Tables['album_disc'].$fields.$values;

		if($mysqli->query($query)) {
			return $mysqli->insert_id;
		}
		else {
			return -1;
		}

	}
	else {
		return 0;
	}
}


/**
 * Get Disc Info
 *
 * @param object of album_disc Class
 * @param string -> 'UNIQID' or 'ID', default is 'ID'
 * @return object of album_disc Class
 */
function cug_get_disc($obj)
{
global $mysqli, $Tables, $FILE_SERVER_URL;
$fields = "";

	if($obj) {
		
		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND ";
		}
		elseif(!empty($obj->uniqid)) {
			$fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."' AND ";
		}
		else {
			if(!empty($obj->title)) {
				$fields .= "title='".$mysqli->escape_str($obj->title)."' AND ";
			}
			if($obj->disc_num != null && $obj->disc_num >= 0) {
				$fields .= "disc_num=".$obj->disc_num." AND ";
			}
			if($obj->total_tracks != null && $obj->total_tracks >= 0) {
				$fields .= "total_tracks=".$obj->total_tracks." AND ";
			}
			if($obj->total_time != null && $obj->total_time >= 0) {
				$fields .= "total_time=".$obj->total_time." AND ";
			}
			if(!empty($obj->album_id)) {
				$fields .= "album_id=".$mysqli->escape_str($obj->album_id)." AND ";
			}
			if(!empty($obj->img_path)) {
				$fields .= "img_path='".$mysqli->escape_str($obj->img_path)."' AND ";
			}
			if($obj->img_34 != null && $obj->img_34 >= 0) {
				$fields .= "img_34=".$obj->img_34." AND ";
			}
			if($obj->img_64 != null && $obj->img_64 >= 0) {
				$fields .= "img_64=".$obj->img_64." AND ";
			}
			if($obj->img_174 != null && $obj->img_174 >= 0) {
				$fields .= "img_174=".$obj->img_174." AND ";
			}
			if($obj->img_300 != null && $obj->img_300 >= 0) {
				$fields .= "img_300=".$obj->img_300." AND ";
			}
			if($obj->img_600 != null && $obj->img_600 >= 0) {
				$fields .= "img_600=".$obj->img_600." AND ";
			}
			if($obj->img_orgn != null && $obj->img_orgn >= 0) {
				$fields .= "img_orgn=".$obj->img_orgn." AND ";
			}
			if(!empty($obj->update_time)) {
				$fields .= "update_time='".$mysqli->escape_str($obj->update_time)."' AND ";
			}
		}
		
		$fields = substr($fields, 0, strlen($fields)-4);
		$query = "SELECT * FROM ".$Tables['album_disc']." WHERE ".$fields;
		$r = $mysqli->query($query);
		
			if($r) {
	
				$arr = $r->fetch_array();
				if($arr) {
						
					$obj = new cug__album_disc();
						
					$obj->id				= $arr['id'];
					$obj->title				= $arr['title'];
					$obj->disc_num			= $arr['disc_num'];
					$obj->total_tracks		= $arr['total_tracks'];
					$obj->total_time		= $arr['total_time'];
					$obj->album_id			= $arr['album_id'];
					$obj->img_path			= $arr['img_path'];
					$obj->uniqid			= $arr['uniqid'];
					$obj->update_time		= $arr['update_time'];
										
					
					$img_path = !empty($obj->img_path) ? $obj->img_path : $FILE_SERVER_URL;
					//$img_path = !empty($obj->img_path) ? cug_get_url_protocol()."://".$obj->img_path : $FILE_SERVER_URL;
					
					$obj->img_34 = $img_path."/?o=disc&i=".$obj->id."&s=34";
					$obj->img_64 = $img_path."/?o=disc&i=".$obj->id."&s=64";
					$obj->img_174 = $img_path."/?o=disc&i=".$obj->id."&s=174";
					$obj->img_300 = $img_path."/?o=disc&i=".$obj->id."&s=300";
					$obj->img_600 = $img_path."/?o=disc&i=".$obj->id."&s=600";
					$obj->img_orgn = $img_path."/?o=disc&i=".$obj->id."&s=mega";
					
					$obj->img_34_num 	= $arr['img_34'];
					$obj->img_64_num 	= $arr['img_64'];
					$obj->img_174_num 	= $arr['img_174'];
					$obj->img_300_num 	= $arr['img_300'];
					$obj->img_600_num 	= $arr['img_600'];
					$obj->img_orgn_num 	= $arr['img_orgn'];
					
					
					/*
					if($arr['img_34'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', '34', $img_path);
						$obj->img_34 = $file_info['url'];
					}
					else { 
						$obj->img_34 = "";
					}
					//-------
					if($arr['img_64'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', '64', $img_path);
						$obj->img_64 = $file_info['url'];
					}
					else {
						$obj->img_64 = "";
					}
					//-------
					if($arr['img_174'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', '174', $img_path);
						$obj->img_174 = $file_info['url'];
					}
					else {
						$obj->img_174 = "";
					}
					//-------
					if($arr['img_300'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', '300', $img_path);
						$obj->img_300 = $file_info['url'];
					}
					else {
						$obj->img_300 = "";
					}
					//-------
					if($arr['img_600'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', '600', $img_path);
						$obj->img_600 = $file_info['url'];
					}
					else {
						$obj->img_600 = "";
					}
					//-------
					if($arr['img_orgn'] > 0) {
						$file_info = cug_get_obj_file_info($arr['id'], 'DISC', 'mega', $img_path);
						$obj->img_orgn = $file_info['url'];
					}
					else {
						$obj->img_orgn = "";
					}
					//-------
					*/
					
					return $obj;
				}
				else return NULL;
			} else return NULL;

	}
	else {
		return NULL;
	}
}


/**
 * Edit Existing Disc
 *
 * @param integer
 * @param object of album_disc Class
 * @return integer
 */
function cug_edit_disc($disc_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $disc_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->disc_num)) $fields .= "disc_num=".$mysqli->escape_str($obj->disc_num).",";
		if(!empty($obj->total_tracks)) $fields .= "total_tracks=".$mysqli->escape_str($obj->total_tracks).",";
		if(!empty($obj->total_time)) $fields .= "total_time=".$mysqli->escape_str($obj->total_time).",";
		if(!empty($obj->album_id)) $fields .= "album_id=".$mysqli->escape_str($obj->album_id).",";
		if(!empty($obj->img_path)) $fields .= "img_path='".$mysqli->escape_str($obj->img_path)."',";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";
		
		if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= "img_34=".$mysqli->escape_str($obj->img_34).",";
		if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= "img_64=".$mysqli->escape_str($obj->img_64).",";
		if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= "img_174=".$mysqli->escape_str($obj->img_174).",";
		if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= "img_300=".$mysqli->escape_str($obj->img_300).",";
		if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= "img_600=".$mysqli->escape_str($obj->img_600).",";
		if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= "img_orgn=".$mysqli->escape_str($obj->img_orgn).",";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['album_disc']." SET ".$fields." WHERE id=".$disc_id;

			if($mysqli->query($query))
				return TRUE;
			else
				return FALSE;
		}
		else
			return FALSE;
	}
	else
		return FALSE;
}


/**
 * Register New Album
 *
 * @param object of album Class
 * @return integer
 */
function cug_reg_album($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->subtitle)) {
			$fields .= "subtitle,";
			$values .= "'".$mysqli->escape_str($obj->subtitle)."',";
		}

		if(!empty($obj->title_version)) {
			$fields .= "title_version,";
			$values .= "'".$mysqli->escape_str($obj->title_version)."',";
		}
		
		if(!empty($obj->genre_id)) {
			$fields .= "genre_id,";
			$values .= $mysqli->escape_str($obj->genre_id).",";
		}

		if(!empty($obj->fileunder_id)) {
			$fields .= "fileunder_id,";
			$values .= $mysqli->escape_str($obj->fileunder_id).",";
		}
		
		if(!empty($obj->genre_from_file)) {
			$fields .= "genre_from_file,";
			$values .= "'".$mysqli->escape_str($obj->genre_from_file)."',";
		}

		if(!empty($obj->lang_id)) {
			$fields .= "lang_id,";
			$values .= $mysqli->escape_str($obj->lang_id).",";
		}

		if(!empty($obj->total_discs)) {
			$fields .= "total_discs,";
			$values .= $mysqli->escape_str($obj->total_discs).",";
		}
		
		if(!empty($obj->label_id)) {
			$fields .= "label_id,";
			$values .= $mysqli->escape_str($obj->label_id).",";
		}		

		if(!empty($obj->label_code)) {
			$fields .= "label_code,";
			$values .= "'".$mysqli->escape_str($obj->label_code)."',";
		}

		if(!empty($obj->catalogue_num)) {
			$fields .= "catalogue_num,";
			$values .= "'".$mysqli->escape_str($obj->catalogue_num)."',";
		}

		if(!empty($obj->rec_date)) {
			$fields .= "rec_date,";
			$values .= "'".$mysqli->escape_str($obj->rec_date)."',";
		}

		if(!empty($obj->rel_date)) {
			$fields .= "rel_date,";
			$values .= "'".$mysqli->escape_str($obj->rel_date)."',";
		}
		
		if(!empty($obj->sale_start_date)) {
			$fields .= "sale_start_date,";
			$values .= "'".$mysqli->escape_str($obj->sale_start_date)."',";
		}

		if(!empty($obj->ean_code)) {
			$fields .= "ean_code,";
			$values .= "'".$mysqli->escape_str($obj->ean_code)."',";
		}
		
		if(!empty($obj->upc_code)) {
			$fields .= "upc_code,";
			$values .= "'".$mysqli->escape_str($obj->upc_code)."',";
		}

		if(!empty($obj->type_id)) {
			$fields .= "type_id,";
			$values .= $mysqli->escape_str($obj->type_id).",";
		}
		
		if(!empty($obj->rel_format_id)) {
			$fields .= "rel_format_id,";
			$values .= $mysqli->escape_str($obj->rel_format_id).",";
		}

		if(!empty($obj->package_id)) {
			$fields .= "package_id,";
			$values .= $mysqli->escape_str($obj->package_id).",";
		}
		
		if(!empty($obj->asin_p)) {
			$fields .= "asin_p,";
			$values .= "'".$mysqli->escape_str($obj->asin_p)."',";
		}

		if(!empty($obj->asin_d)) {
			$fields .= "asin_d,";
			$values .= "'".$mysqli->escape_str($obj->asin_d)."',";
		}
		
		if(!empty($obj->img_path)) {
			$fields .= "img_path,";
			$values .= "'".$mysqli->escape_str($obj->img_path)."',";
		}
		
		if(!empty($obj->copyright_c)) {
			$fields .= "copyright_c,";
			$values .= $mysqli->escape_str($obj->copyright_c).",";
		}
		
		if(!empty($obj->copyright_c_date)) {
			$fields .= "copyright_c_date,";
			$values .= "'".$mysqli->escape_str($obj->copyright_c_date)."',";
		}
		
		if(!empty($obj->copyright_p)) {
			$fields .= "copyright_p,";
			$values .= $mysqli->escape_str($obj->copyright_p).",";
		}
		
		if(!empty($obj->copyright_p_date)) {
			$fields .= "copyright_p_date,";
			$values .= "'".$mysqli->escape_str($obj->copyright_p_date)."',";
		}
		
		
		if(!empty($obj->register_from)) {
			$fields .= "register_from,";
			$values .= $mysqli->escape_str($obj->register_from).",";
		}
		
		if(!empty($obj->register_date)) {
			$fields .= "register_date,";
			$values .= "'".$mysqli->escape_str($obj->register_date)."',";
		}
		
		if(!empty($obj->register_ip)) {
			$fields .= "register_ip,";
			$values .= "'".$mysqli->escape_str($obj->register_ip)."',";
		}
		
		if($obj->trash_status != null && $obj->trash_status >= 0) {
			$fields .= "trash_status,";
			$values .= $obj->trash_status.",";
		}
		
		if(!empty($obj->online)) {
			$fields .= "online,";
			$values .= $mysqli->escape_str($obj->online).",";
		}
		
		if(!empty($obj->country_allowed)) {
			$fields .= "country_allowed,";
			$values .= "'".$mysqli->escape_str($obj->country_allowed)."',";
		}
		
		if(!empty($obj->explicit_content)) {
			$fields .= "explicit_content,";
			$values .= $mysqli->escape_str($obj->explicit_content).",";
		}
		
		if(!empty($obj->gapless_playing)) {
			$fields .= "gapless_playing,";
			$values .= $mysqli->escape_str($obj->gapless_playing).",";
		}
		
		if(!empty($obj->external_id)) {
			$fields .= "external_id,";
			$values .= "'".$mysqli->escape_str($obj->external_id)."',";
		}
		
		if(!empty($obj->shenzhen_id)) {
		    $fields .= "shenzhen_id,";
		    $values .= $mysqli->escape_str($obj->shenzhen_id).",";
		}
		
		$fields .= "tag_status_id,";
		if(!empty($obj->tag_status_id)) {
			$values .= $mysqli->escape_str($obj->tag_status_id).",";
		}
		else {
			$values .= "1,"; // Unchecked
		}

		if($obj->img_34 != null && $obj->img_34 >= 0) {
			$fields .= "img_34,";
			$values .= $obj->img_34.",";
		}

		if($obj->img_64 != null && $obj->img_64 >= 0) {
			$fields .= "img_64,";
			$values .= $obj->img_64.",";
		}

		if($obj->img_174 != null && $obj->img_174 >= 0) {
			$fields .= "img_174,";
			$values .= $obj->img_174.",";
		}
		
		if($obj->img_300 != null && $obj->img_300 >= 0) {
			$fields .= "img_300,";
			$values .= $obj->img_300.",";
		}
		
		if($obj->img_600 != null && $obj->img_600 >= 0) {
			$fields .= "img_600,";
			$values .= $obj->img_600.",";
		}
		
		if($obj->img_orgn != null && $obj->img_orgn >= 0) {
			$fields .= "img_orgn,";
			$values .= $obj->img_orgn.",";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}

		$fields .= "uniqid)";
		if(!empty($obj->uniqid)) {
			$values .= "'".$mysqli->escape_str($obj->uniqid)."')";
		}
		else {
			$uniqid = uniqid();
			$values .= "'".$uniqid."')";
		}


		$query = "INSERT INTO ".$Tables['album'].$fields.$values;

		if($mysqli->query($query)) {
			return $mysqli->insert_id;
		}
		else {
			return -1;
		}

	}
	else {
		return 0;
	}
}


/**
 * Get Album Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID' or 'ID', default is 'ID'
 * @return object of album Class
 */
function cug_get_album($item, $item_type="ID")
{
global $mysqli, $Tables, $FILE_SERVER_URL;

	if(!empty($item)) {

		if($item_type == "ID") {
			$query = "SELECT * FROM ".$Tables['album']." WHERE id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$query = "SELECT * FROM ".$Tables['album']." WHERE uniqid='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}

		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__album();
					
				$obj->id					= $arr['id'];
				$obj->title					= $arr['title'];
				$obj->subtitle				= $arr['subtitle'];
				$obj->title_version			= $arr['title_version'];
				$obj->genre_id				= $arr['genre_id'];
				$obj->fileunder_id			= $arr['fileunder_id'];
				$obj->genre_from_file		= $arr['genre_from_file'];
				$obj->lang_id				= $arr['lang_id'];
				$obj->total_discs			= $arr['total_discs'];
				$obj->label_id				= $arr['label_id'];
				$obj->label_code			= $arr['label_code'];
				$obj->catalogue_num			= $arr['catalogue_num'];
				$obj->tag_status_id			= $arr['tag_status_id'];
				$obj->rec_date				= $arr['rec_date'];
				$obj->rel_date				= $arr['rel_date'];
				$obj->sale_start_date		= $arr['sale_start_date'];
				$obj->ean_code				= $arr['ean_code'];
				$obj->upc_code				= $arr['upc_code'];
				$obj->type_id				= $arr['type_id'];
				$obj->rel_format_id			= $arr['rel_format_id'];
				$obj->package_id			= $arr['package_id'];
				$obj->asin_p				= $arr['asin_p'];
				$obj->asin_d				= $arr['asin_d'];
				$obj->img_path				= $arr['img_path'];
				$obj->copyright_c			= $arr['copyright_c'];
				$obj->copyright_c_date		= $arr['copyright_c_date'];
				$obj->copyright_p			= $arr['copyright_p'];
				$obj->copyright_p_date		= $arr['copyright_p_date'];
				$obj->register_from			= $arr['register_from'];
				$obj->register_date			= $arr['register_date'];
				$obj->register_ip			= $arr['register_ip'];
				$obj->trash_status			= $arr['trash_status'];
				$obj->online				= $arr['online'];
				$obj->uniqid				= $arr['uniqid'];
				$obj->country_allowed		= $arr['country_allowed'];
				$obj->explicit_content		= $arr['explicit_content'];
				$obj->gapless_playing		= $arr['gapless_playing'];				
				$obj->external_id			= $arr['external_id'];
				$obj->shenzhen_id			= $arr['shenzhen_id'];
				$obj->update_time			= $arr['update_time'];

				$img_path = !empty($obj->img_path) ? $obj->img_path : $FILE_SERVER_URL;
				//$img_path = !empty($obj->img_path) ? cug_get_url_protocol()."://".$obj->img_path : $FILE_SERVER_URL;
				
				$obj->img_34 = $img_path."/?o=album&i=".$obj->id."&s=34";
				$obj->img_64 = $img_path."/?o=album&i=".$obj->id."&s=64";
				$obj->img_174 = $img_path."/?o=album&i=".$obj->id."&s=174";
				$obj->img_300 = $img_path."/?o=album&i=".$obj->id."&s=300";
				$obj->img_600 = $img_path."/?o=album&i=".$obj->id."&s=600";
				$obj->img_orgn = $img_path."/?o=album&i=".$obj->id."&s=mega";
				
				$obj->img_34_num = $arr['img_34'];
				$obj->img_64_num = $arr['img_64'];
				$obj->img_174_num = $arr['img_174'];
				$obj->img_300_num = $arr['img_300'];
				$obj->img_600_num = $arr['img_600'];
				$obj->img_orgn_num = $arr['img_orgn'];
				
				/*
				if($arr['img_34'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', '34', $img_path);
					$obj->img_34 = $file_info['url'];
				}
				else { 
					$obj->img_34 = "";
				}
				$obj->img_34_num = $arr['img_34'];
				//-------
				
				if($arr['img_64'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', '64', $img_path);
					$obj->img_64 = $file_info['url'];
				}
				else {
					$obj->img_64 = "";
				}
				$obj->img_64_num = $arr['img_64'];
				//-------
				
				if($arr['img_174'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', '174', $img_path);
					$obj->img_174 = $file_info['url'];
				}
				else {
					$obj->img_174 = "";
				}
				$obj->img_174_num = $arr['img_174'];
				//-------
				
				if($arr['img_300'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', '300', $img_path);
					$obj->img_300 = $file_info['url'];
				}
				else {
					$obj->img_300 = "";
				}
				$obj->img_300_num = $arr['img_300'];
				//-------
				
				if($arr['img_600'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', '600', $img_path);
					$obj->img_600 = $file_info['url'];
				}
				else {
					$obj->img_600 = "";
				}
				$obj->img_600_num = $arr['img_600'];
				//-------
				
				if($arr['img_orgn'] > 0) {
					$file_info = cug_get_obj_file_info($arr['id'], 'ALBUM', 'mega', $img_path);
					$obj->img_orgn = $file_info['url'];
				}
				else {
					$obj->img_orgn = "";
				}
				$obj->img_orgn_num = $arr['img_orgn'];
				//-------
				*/
					
				return $obj;
			}
			else return NULL;
		} else return NULL;

	}
	else {
		return NULL;
	}
}


/**
 * Get Albums
 *
 * @param string
 * @param integer (default is 1)
 * @param string ('ID', 'TITLE' default is 'TITLE')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @return array
 */
function cug_get_albums($album_title, $limit=1, $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	//-------------------------
	if(strlen($album_title) == 1)
		$search_criteria = $mysqli->escape_str($album_title)."%";
	elseif(strlen($album_title) > 1)
		$search_criteria = "%".$mysqli->escape_str($album_title)."%";
	else
		return $result;
	//-------------------------
	if($sort_by == "ID")
		$field = "id";
	else
		$field = "title";
	//-------------------------


	$r = $mysqli->query("SELECT * FROM ".$Tables['album']." WHERE title LIKE '$search_criteria' ORDER BY ".$field." ".$sort_type." LIMIT $limit");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get Disc ID by AlbmuID and DiscNum
 * 
 * @param int $album_id
 * @param int $disc_num
 * @return int
 */
function cug_get_album_disc_id($album_id, $disc_num) {
	global $mysqli, $Tables;
	$result = 0;
	
	if($album_id > 0 && $disc_num >= 0) {
		$query = "SELECT disc_id FROM {$Tables['track_album_rel']} WHERE album_id=".$mysqli->escape_str($album_id)." AND disc_num=".$mysqli->escape_str($disc_num);
		$r = $mysqli->query($query);
			if($r->num_rows) {
				$row = $r->fetch_array();
				$result = $row[0];
			}
	}
	
	return $result;
}

/**
 * Edit Existing Album
 *
 * @param array od Album IDs
 * @param object of Album Class
 * @param bool (Update empty fields or not, default is false)
 * @return bool
 */
function cug_edit_album($album_ids_arr, $obj, $update_empty_fields=false)
{
global $mysqli, $Tables;
$fields = "";
$where = "";

	if(count($album_ids_arr) > 0 && !empty($album_ids_arr[0])) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		
		if(isset($obj->subtitle)) {
			if($update_empty_fields && empty($obj->subtitle)) $fields .= "subtitle='',";
			elseif(!empty($obj->subtitle)) $fields .= "subtitle='".$mysqli->escape_str($obj->subtitle)."',";
		}
		//------------------------
		if(isset($obj->title_version)) {
			if($update_empty_fields && empty($obj->title_version)) $fields .= "title_version='',";
			elseif(!empty($obj->title_version)) $fields .= "title_version='".$mysqli->escape_str($obj->title_version)."',";
		}
		//------------------------
		if(isset($obj->genre_id)) {
			if($update_empty_fields && empty($obj->genre_id)) $fields .= "genre_id=0,";
			elseif($obj->genre_id != null && $obj->genre_id >= 0) $fields .= "genre_id=".$mysqli->escape_str($obj->genre_id).",";
		}
		//------------------------
		if(isset($obj->fileunder_id)) {
			if($update_empty_fields && empty($obj->fileunder_id)) $fields .= "fileunder_id=0,";
			elseif($obj->fileunder_id != null && $obj->fileunder_id >= 0) $fields .= "fileunder_id=".$mysqli->escape_str($obj->fileunder_id).",";
		}
		//------------------------
		if(isset($obj->genre_from_file)) {
			if($update_empty_fields && empty($obj->genre_from_file)) $fields .= "genre_from_file='',";
			elseif(!empty($obj->genre_from_file)) $fields .= "genre_from_file='".$mysqli->escape_str($obj->genre_from_file)."',";
		}
		//------------------------
		if(isset($obj->lang_id)) {
			if($update_empty_fields && empty($obj->lang_id)) $fields .= "lang_id=0,";
			elseif($obj->lang_id != null && $obj->lang_id >= 0) $fields .= "lang_id=".$mysqli->escape_str($obj->lang_id).",";				
		}
		//------------------------
		if(isset($obj->label_id)) {
			if($update_empty_fields && empty($obj->label_id)) $fields .= "label_id=null,";
			elseif($obj->label_id != null && $obj->label_id >= 0) $fields .= "label_id=".$mysqli->escape_str($obj->label_id).",";
		}
		//------------------------
		if(isset($obj->label_code)) {
			if($update_empty_fields && empty($obj->label_code)) $fields .= "label_code='',";
			elseif(!empty($obj->label_code)) $fields .= "label_code='".$mysqli->escape_str($obj->label_code)."',";
		}
		//------------------------
		if(isset($obj->catalogue_num)) {
			if($update_empty_fields && empty($obj->catalogue_num)) $fields .= "catalogue_num='',";
			elseif(!empty($obj->catalogue_num)) $fields .= "catalogue_num='".$mysqli->escape_str($obj->catalogue_num)."',";
		}
		//------------------------
		if(isset($obj->rec_date)) {
			if($update_empty_fields && empty($obj->rec_date)) $fields .= "rec_date=null,";
			elseif(!empty($obj->rec_date)) $fields .= "rec_date='".$mysqli->escape_str($obj->rec_date)."',";
		}
		//------------------------
		if(isset($obj->rel_date)) {
			if($update_empty_fields && empty($obj->rel_date)) $fields .= "rel_date=null,";
			elseif(!empty($obj->rel_date)) $fields .= "rel_date='".$mysqli->escape_str($obj->rel_date)."',";
		}
		//------------------------
		if(isset($obj->sale_start_date)) {
			if($update_empty_fields && empty($obj->sale_start_date)) $fields .= "sale_start_date=null,";
			elseif(!empty($obj->sale_start_date)) $fields .= "sale_start_date='".$mysqli->escape_str($obj->sale_start_date)."',";
		}
		//------------------------
		if(isset($obj->ean_code)) {
			if($update_empty_fields && empty($obj->ean_code)) $fields .= "ean_code='',";
			elseif(!empty($obj->ean_code)) $fields .= "ean_code='".$mysqli->escape_str($obj->ean_code)."',";
		}
		//------------------------
		if(isset($obj->upc_code)) {
			if($update_empty_fields && empty($obj->upc_code)) $fields .= "upc_code='',";
			elseif(!empty($obj->upc_code)) $fields .= "upc_code='".$mysqli->escape_str($obj->upc_code)."',";
		}
		//------------------------
		if(isset($obj->type_id)) {
			if($update_empty_fields && empty($obj->type_id)) $fields .= "type_id=0,";
			elseif(!empty($obj->type_id)) $fields .= "type_id=".$mysqli->escape_str($obj->type_id).",";
		}
		//------------------------
		if(isset($obj->rel_format_id)) {
			if($update_empty_fields && empty($obj->rel_format_id)) $fields .= "rel_format_id=0,";
			elseif(!empty($obj->rel_format_id)) $fields .= "rel_format_id=".$mysqli->escape_str($obj->rel_format_id).",";
		}
		//------------------------
		if(isset($obj->package_id)) {
			if($update_empty_fields && empty($obj->package_id)) $fields .= "package_id=0,";
			elseif(!empty($obj->package_id)) $fields .= "package_id=".$mysqli->escape_str($obj->package_id).",";
		}
		//------------------------
		if(isset($obj->asin_p)) {
			if($update_empty_fields && empty($obj->asin_p)) $fields .= "asin_p='',";
			elseif(!empty($obj->asin_p)) $fields .= "asin_p='".$mysqli->escape_str($obj->asin_p)."',";
		}
		//------------------------
		if(isset($obj->asin_d)) {
			if($update_empty_fields && empty($obj->asin_d)) $fields .= "asin_d='',";
			elseif(!empty($obj->asin_d)) $fields .= "asin_d='".$mysqli->escape_str($obj->asin_d)."',";
		}
		//------------------------
		if(isset($obj->copyright_c)) {
			if($update_empty_fields && empty($obj->copyright_c)) $fields .= "copyright_c=0,";
			elseif(!empty($obj->copyright_c)) $fields .= "copyright_c=".$mysqli->escape_str($obj->copyright_c).",";
		}
		//------------------------
		if(isset($obj->copyright_c_date)) {
			if($update_empty_fields && empty($obj->copyright_c_date)) $fields .= "copyright_c_date=null,";
			elseif(!empty($obj->copyright_c_date)) $fields .= "copyright_c_date='".$mysqli->escape_str($obj->copyright_c_date)."',";
		}
		//------------------------
		if(isset($obj->copyright_p)) {
			if($update_empty_fields && empty($obj->copyright_p)) $fields .= "copyright_p=0,";
			elseif(!empty($obj->copyright_p)) $fields .= "copyright_p=".$mysqli->escape_str($obj->copyright_p).",";
		}
		//------------------------
		if(isset($obj->copyright_p_date)) {
			if($update_empty_fields && empty($obj->copyright_p_date)) $fields .= "copyright_p_date=null,";
			elseif(!empty($obj->copyright_p_date)) $fields .= "copyright_p_date='".$mysqli->escape_str($obj->copyright_p_date)."',";
		}
		//------------------------
		if(isset($obj->country_allowed)) {
			if($update_empty_fields && empty($obj->country_allowed)) $fields .= "country_allowed=null,";
			elseif(!empty($obj->country_allowed)) $fields .= "country_allowed='".$mysqli->escape_str($obj->country_allowed)."',";
		}
		//------------------------
		if(isset($obj->explicit_content)) {
			if($update_empty_fields && empty($obj->explicit_content)) $fields .= "explicit_content=null,";
			elseif(!empty($obj->explicit_content)) $fields .= "explicit_content=".$mysqli->escape_str($obj->explicit_content).",";
		}
		//------------------------
		if(isset($obj->gapless_playing)) {
			if($update_empty_fields && empty($obj->gapless_playing)) $fields .= "gapless_playing=null,";
			elseif(!empty($obj->gapless_playing)) $fields .= "gapless_playing=".$mysqli->escape_str($obj->gapless_playing).",";
		}
		//------------------------		
		
		if(!empty($obj->img_path)) $fields .= "img_path='".$mysqli->escape_str($obj->img_path)."',";
		if(!empty($obj->total_discs)) $fields .= "total_discs=".$mysqli->escape_str($obj->total_discs).",";
		if(!empty($obj->tag_status_id)) $fields .= "tag_status_id=".$mysqli->escape_str($obj->tag_status_id).",";
		if(!empty($obj->register_from)) $fields .= "register_from=".$mysqli->escape_str($obj->register_from).",";
		if($obj->trash_status != null && $obj->trash_status >= 0) $fields .= "trash_status=".$mysqli->escape_str($obj->trash_status).",";
		if(!empty($obj->online)) $fields .= "online=".$mysqli->escape_str($obj->online).",";
		if(!empty($obj->external_id)) $fields .= "external_id='".$mysqli->escape_str($obj->external_id)."',";
		if(!empty($obj->shenzhen_id)) $fields .= "shenzhen_id=".$mysqli->escape_str($obj->shenzhen_id).",";
		//if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		//if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";		
		
		if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= "img_34=".$mysqli->escape_str($obj->img_34).",";
		if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= "img_64=".$mysqli->escape_str($obj->img_64).",";
		if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= "img_174=".$mysqli->escape_str($obj->img_174).",";
		if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= "img_300=".$mysqli->escape_str($obj->img_300).",";
		if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= "img_600=".$mysqli->escape_str($obj->img_600).",";
		if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= "img_orgn=".$mysqli->escape_str($obj->img_orgn).",";
		
	
		
		if(strlen($fields) > 0) {
			
			$fields = substr($fields, 0, strlen($fields)-1);
			
				for($i=0; $i<count($album_ids_arr); $i++) {
					if($album_ids_arr[$i] > 0) {
						$where .= "id=".$album_ids_arr[$i]." OR ";
					}	
				}
			
			if(strlen($where) > 0) {
				$where = substr($where, 0, strlen($where)-3);
				$query = "UPDATE ".$Tables['album']." SET ".$fields." WHERE $where";
					
					if($mysqli->query($query))
						return TRUE;
					else
						return FALSE;
			}
			else {
				return FALSE;
			}		
		}
		else {
			return FALSE;
		}
	}
	else {
		return FALSE;
	}
}


/**
 * Register Album-Client Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_album_client_rel($album_id, $client_id, $licensee_id, $uniqid="")
{
global $mysqli, $Tables;

	if($album_id>0 && $client_id>0 && $licensee_id>0) {
		
		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['album_client']." WHERE album_id=$album_id AND client_id=$client_id AND licensee_id=$licensee_id");
		
			if( !$r->num_rows ) {
				
				if(!$uniqid)
					$uniq_id = uniqid();
				else
					$uniq_id = $mysqli->escape_str($uniqid);
				
				
				$query = "INSERT INTO ".$Tables['album_client']." VALUES(NULL, $album_id, $client_id, $licensee_id, '$uniq_id', NULL)";
					
					if($mysqli->query($query))
						return $mysqli->insert_id;
					else
						return -1;				
			}
			else {
				$arr = $r->fetch_array();
				return $arr['id'];
			}
	}
	else 
		return 0;
}


/**
 * Get Album-Client Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'ALBUM', 'CLIENT', 'LICENSEE', 'UNIQID'; default is 'UNIQID')
 * @param integer (default is 0)
 * @param integer (default is 100)
 * @return array
 */
function cug_get_album_client_rel($item, $item_type="UNIQID", $limit_from=0, $limit_quant=100)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($item) && !empty($item_type)) {
		
		switch($item_type) {
			
			case 'UNIQID':
				$field = "uniqid";
				$value = "'".$mysqli->escape_str($item)."'";
				break;
				
			case 'ID':
				$field = "id";
				$value = $mysqli->escape_str($item);
				break;

			case 'ALBUM':
				$field = "album_id";
				$value = $mysqli->escape_str($item);
				break;
				
			case 'CLIENT':
				$field = "client_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'LICENSEE':
				$field = "licensee_id";
				$value = $mysqli->escape_str($item);
				break;
	
			default:
				$field = "";
				break;	
		}

		
			if(!empty($field)) {
				
				$r = $mysqli->query("SELECT * FROM ".$Tables['album_client']." WHERE ".$field."=".$value." LIMIT $limit_from, $limit_quant");
				
				if($r->num_rows) {
					while($arr = $r->fetch_array()) {
						$result[$index] = $arr;
						$index ++;
					}	
				}				
			}
	}
	
return $result;
}


/**
 * Register Album-Member Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_album_member_rel($album_id, $member_id, $role_id, $uniqid="")
{
global $mysqli, $Tables;


	if($album_id>0 && $member_id>0 && $role_id>0) {
		
		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['album_member']." WHERE album_id=$album_id AND member_id=$member_id AND role_id=$role_id");

			if( !$r->num_rows ) {
				
				if(!$uniqid)
					$uniq_id = uniqid();
				else
					$uniq_id = $mysqli->escape_str($uniqid);
				
				$query = "INSERT INTO ".$Tables['album_member']." VALUES(NULL, $album_id, $member_id, $role_id, '$uniq_id', NULL)";
				
				if($mysqli->query($query))
					return $mysqli->insert_id;
				else
					return -1;
			}
			else {
				$arr = $r->fetch_array();
				return $arr['id'];
			}


	}
	else
		return 0;
}


/**
 * Get Album-Member Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'ALBUM', 'MEMBER', 'ROLE', 'UNIQID'; default is 'UNIQID')
 * @return array
 */
function cug_get_album_member_rel($item, $item_type="UNIQID")
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($item) && !empty($item_type)) {

		switch($item_type) {
				
			case 'UNIQID':
				$field = "uniqid";
				$value = "'".$mysqli->escape_str($item)."'";
				break;

			case 'ID':
				$field = "id";
				$value = $mysqli->escape_str($item);
				break;

			case 'ALBUM':
				$field = "album_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'MEMBER':
				$field = "member_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'ROLE':
				$field = "role_id";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {
			$r = $mysqli->query("SELECT * FROM ".$Tables['album_member']." WHERE ".$field."=".$value);

			if($r) {
				while($arr = $r->fetch_array()) {
					$result[$index] = $arr;
					$index ++;
				}	
			}
		}
	}

return $result;
}


/**
 * Get Albums of Track
 *
 * @param int
 * @param int
 * @param int
 * @return array
 */
function cug_get_track_albums($track_id, $register_from=0, $online=-1)
{
global $mysqli, $Tables;
$result = array();
$index = 0;


	if(!empty($track_id)) {
		$r = $mysqli->query("CALL get_track_albums($track_id, $register_from, $online)");

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
		
		if($mysqli->more_results())
			$mysqli->next_result();
	}

return $result;
}



/**
 * Get Member's related Albums
 *
 * @param integer
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @param integer
 * @return array
 */
function cug_get_member_related_albums($member_id, $limit_from=0, $limit_quant=30, $online=-1)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;


	// First Query
	$query  = "CALL get_member_related_albums($member_id, $limit_from, $limit_quant, $online);";


	// Second Query
	$query .= "SELECT FOUND_ROWS() AS total;";


	if($mysqli->multi_query($query)) {
		do {
			if($result = $mysqli->store_result()) {
				while($row = $result->fetch_array()) {
					$result_arr[$result_index][$index] = $row;
					$index ++;
				}
				$result_index ++;
				$index = 0;
				$result->free();

				if(!$mysqli->more_results())
					break;
			}
		} while ($mysqli->next_result());
	}

return $result_arr;
}


/**
 * Get Album list for TE
 *
 * @param string (default is '0')
 * @param integer (default is 0)
 * @param string (default is "null")
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
*  @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0) 
 * @param string ('TITLE', 'GENRE', 'EAN', 'LABEL', 'DISCS')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @param integer (default is -1)
 * @return array
 */
function cug_get_album_list_te($register_from='0', $tag_status=0, $search_term="null", $is_album_title=0, $is_genre_title=0, $is_ean_code=0, $is_cat_num=0, $is_album_artist=0, $is_track_artist=0, $is_track_composer=0, $is_album_empty=0, $user_id=0, $action_id=0, $object_id=0, $trash_status=0, $type_id=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=30, $online=-1, $is_album_id=0, $duplicate_group_id=0)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;


	// First Query
	$query  = "CALL get_album_list_te('$register_from',$tag_status,";
	$query .= ($search_term=="null" || !$search_term) ? "null," : ((strlen($search_term) > 3) ? "'%".$mysqli->escape_str($search_term)."%'," : "'".$mysqli->escape_str($search_term)."%',");
	$query .= "$is_album_title,$is_genre_title,$is_ean_code,$is_cat_num,$is_album_artist,$is_track_artist,$is_track_composer,";
	$query .= "$is_album_empty,$user_id,$action_id,$object_id,$trash_status,$type_id,'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant,$online,$is_album_id,$duplicate_group_id);";

	//echo $query; echo "</br>";
	
	// Second Query
	$query .= "SELECT FOUND_ROWS() AS total;";


	if($mysqli->multi_query($query)) {
		do {
			if($result = $mysqli->store_result()) {
				while($row = $result->fetch_array()) {
					$result_arr[$result_index][$index] = $row;
					$index ++;
				}
				$result_index ++;
				$index = 0;
				$result->free();

				if(!$mysqli->more_results())
					break;
			}
		} while ($mysqli->next_result());
	}

return $result_arr;
}


/**
 * Get Number of Tracks from Album
 *
 * @param integer
 * @return array
 */
function cug_get_album_tracks_num($album_id)
{
global $mysqli, $Tables;
$result = 0;

	if($album_id > 0) {
		$r = $mysqli->query("SELECT COUNT(track_id) FROM ".$Tables['track_album_rel']." WHERE album_id=$album_id");

		if($r->num_rows) {
			if($arr = $r->fetch_array()) {
				$result = $arr[0];
			}
		}
	}

	
return $result;
}


/**
 * Get List of Tracks from Album
 *
 * @param integer
 * @return array
 */
function cug_get_album_tracks($album_id)
{
global $mysqli, $Tables;
$result = array();
$discs_index = 0;
$tracks_index = 0;

	if($album_id > 0) {
		//get discs
		$discs_arr = cug_get_album_discs($album_id);
		
		foreach($discs_arr as $disc_arr) {
			$result[$discs_index]['disc'] = $disc_arr;
			
			//get tracks
			$tracks_arr = cug_get_disc_tracks($disc_arr['id']);
				foreach($tracks_arr as $track_arr) {
					$result[$discs_index]['tracks'][$tracks_index] = $track_arr;
					$tracks_index ++;
				}
				
			$discs_index ++;
			$tracks_index = 0;
		}
	}

return $result;
}


/**
 * Delete Album-Disc-Track Relations
 *
 * @param integer
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @return bool
 */
function cug_del_album_track_rel($album_id, $disc_id=0)
{
global $mysqli, $Tables;

	if($album_id > 0) {
		$where = " album_id=$album_id AND";
			
		if($disc_id > 0) $where .= " disc_id=$disc_id AND";
			
		$where = substr($where, 0, strlen($where) - 4);

		$query = "DELETE FROM ".$Tables['track_album_rel']." WHERE $where";
		if($mysqli->query($query)) {
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
 * Get List of Discs from Album
 *
 * @param integer
 * @return array
 */
function cug_get_album_discs($album_id)
{
global $mysqli, $Tables, $FILE_SERVER_URL;
$result = array();
$index = 0;

	if($album_id > 0) {
		if($r = $mysqli->query("SELECT id FROM ".$Tables['album_disc']." WHERE album_id=$album_id ORDER BY disc_num")) {		
			if($r->num_rows) {
				while($arr = $r->fetch_array()) {
					//get disc info including image covers
					$disc_obj = new cug__album_disc();
					$disc_obj->id = $arr['id'];
					
					$disc_obj = cug_get_disc($disc_obj);					
					
					$result[$index]['id'] 			= $disc_obj->id;
					$result[$index]['title'] 		= $disc_obj->title;
					$result[$index]['disc_num'] 	= $disc_obj->disc_num;
					$result[$index]['total_tracks'] = $disc_obj->total_tracks;
					$result[$index]['total_time'] 	= $disc_obj->total_time;
					$result[$index]['img_path'] 	= $disc_obj->img_path;
					
					$result[$index]['img_34'] 	= $disc_obj->img_34;
					$result[$index]['img_64'] 	= $disc_obj->img_64;
					$result[$index]['img_174'] 	= $disc_obj->img_174;
					$result[$index]['img_300'] 	= $disc_obj->img_300;
					$result[$index]['img_600'] 	= $disc_obj->img_600;
					$result[$index]['img_orgn'] = $disc_obj->img_orgn;

					$result[$index]['img_34_num'] 	= $disc_obj->img_34_num;
					$result[$index]['img_64_num'] 	= $disc_obj->img_64_num;
					$result[$index]['img_174_num'] 	= $disc_obj->img_174_num;
					$result[$index]['img_300_num'] 	= $disc_obj->img_300_num;
					$result[$index]['img_600_num'] 	= $disc_obj->img_600_num;
					$result[$index]['img_orgn_num'] = $disc_obj->img_orgn_num;
					
					$index ++;
				}
			}
		}	
	}

return $result;
}


/**
 * Get List of Tracks from Disc
 *
 * @param integer
 * @return array
 */
function cug_get_disc_tracks($disc_id)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if($disc_id > 0) {	
		$query = "SELECT tar.track_id, tar.file_id AS file_id, t.title, t.part, t.version, tar.track_num, f.track_time, t.has_file, t.tag_status_id, f.fp_status, f.wm_status, fs.f_prev_path, fs.f_prev_path2, f.track_type_id, f.f_format_id, f.f_size, f.f_brate, f.f_srate, tar.hidden, t.register_from, t.genre_id, t.fileunder_id, t.rec_date, t.lang_id, t.copyright_c FROM ".$Tables['track_album_rel']." AS tar LEFT JOIN ".$Tables['track']." AS t ON tar.track_id=t.id LEFT JOIN ".$Tables['track_file']." AS f ON tar.file_id=f.id LEFT JOIN {$Tables['track_file_server']} AS fs ON f.f_prev_path=fs.id WHERE tar.disc_id=$disc_id ORDER BY tar.track_num";

		if($r = $mysqli->query($query)) {
			if($r->num_rows) {
				while($arr = $r->fetch_assoc()) {
					$result[$index] = $arr;
					$index ++;
				}
			}
		}	
	}

return $result;
}


/**
 * Get list of all related object ids for Albums - used in cug_del_albums() function
 * @param array
 * @param array
 */
function cug_get_album_related_object_ids($album_ids)
{
global $mysqli, $Tables;
$result = array();



	if(count($album_ids) > 0) {
		$index = 0;
		foreach($album_ids as $album_id) {
				
			$result[$index]['album_id'] = $album_id;
				
			//get members
			$members = cug_get_album_member_rel($album_id, "ALBUM");
			$subindex = 0;
				
				foreach($members as $member) {
						
					if(!isset($result[$index]['members']))
						$result[$index]['members'] = array();
						
					if(!in_array($member['member_id'], $result[$index]['members'])) {
						$result[$index]['members'][$subindex] = $member['member_id'];
						$subindex ++;
					}
				}
			unset($members);
			//-------------------------------
			
			//get clients
			$clients = cug_get_album_client_rel($album_id, "ALBUM");
			$subindex = 0;

				foreach($clients as $client) {
	
					if(!isset($result[$index]['clients']))
						$result[$index]['clients'] = array();
	
					if(!in_array($client['client_id'], $result[$index]['clients'])) {
						$result[$index]['clients'][$subindex] = $client['client_id'];
						$subindex ++;
					}
					//-------------------
					if(!in_array($client['licensee_id'], $result[$index]['clients'])) {
						$result[$index]['clients'][$subindex] = $client['licensee_id'];
						$subindex ++;
					}
				}
				
			unset($clients);
			//-------------------------------

			//get discs
			$discs = cug_get_album_discs($album_id);
			$subindex = 0;
			
				foreach($discs as $disc) {
				
					if(!isset($result[$index]['discs']))
						$result[$index]['discs'] = array();
				
					if(!in_array($disc['id'], $result[$index]['discs'])) {
						$result[$index]['discs'][$subindex] = $disc['id'];
						$subindex ++;
					}
				}
			unset($discs);
			//-------------------------------			
			
			//get tracks
			$discs = cug_get_album_tracks($album_id);
			$subindex = 0;
				
			foreach($discs as $disc) {			
				foreach($disc['tracks'] as $track) {
					if(!isset($result[$index]['tracks']))
						$result[$index]['tracks'] = array();
				
					if(!in_array($track['track_id'], $result[$index]['tracks'])) {
						$result[$index]['tracks'][$subindex] = $track['track_id'];
						$subindex ++;
					}
				}
			}
			unset($discs);
			//-------------------------------			
			
			$index ++;
		}
	}

return $result;
}


/**
 * Delete Related Objects to Album
 * @param array
 * @param integer (Default is 0)
 * @return void
 */
function cug_del_album_related_objects($related_objects, $user_id=0)
{
global $mysqli, $Tables;
$result = array();

	if(count($related_objects) > 0) {
		foreach($related_objects as $related_object) {
				
			//members
			if(count($related_object['members']) > 0) {
				foreach($related_object['members'] as $member_id) {
					cug_del_member($member_id, $user_id, false);
				}
			}
				

		}//end of foreach
	}//end of if

}


/**
 * Delete Album-Member Relations
 *
 * @param array of Album IDs
 * @param array of member ids and their role ids, if this array is empty then all linked members will be deleted
 * @return bool
 */
function cug_del_album_member_rel($album_ids, $members_roles = array())
{
global $mysqli, $Tables;
$where_id = "(";
$where_member = "(";
$where_role = "(";

	if(count($album_ids) > 0) {
		//-------------
		foreach($album_ids as $album_id) {
			$where_id .= ($album_id > 0) ? "album_id=$album_id OR " : "";
		}
		//-------------
		if(count($members_roles)) {
			foreach($members_roles as $member_role) {
				$where_member .= (!empty($member_role['member_id']) && $member_role['member_id'] > 0) ? "member_id=".$member_role['member_id']." OR " : "";
				$where_role .= (!empty($member_role['role_id']) && $member_role['role_id'] > 0) ? "role_id=".$member_role['role_id']." OR " : "";
			}
		}
		//-------------

		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";

			if(strlen($where_member) > 1) {
				$where_member = substr($where_member, 0, strlen($where_member)-3).")";
			}
			else {
				$where_member = "";
			}
			//------------
			if(strlen($where_role) > 1) {
				$where_role = substr($where_role, 0, strlen($where_role)-3).")";
			}
			else {
				$where_role = "";
			}

			$query = "DELETE FROM ".$Tables['album_member']." WHERE $where_id";
			$query .= (strlen($where_member)) ? " AND $where_member" : "";
			$query .= (strlen($where_role)) ? " AND $where_role" : "";

				if($mysqli->query($query)) {
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
	else {
		return false;
	}
}


/**
 * Delete Album-Client Relation
 *
 * @param array
 * @return bool
 */
function cug_del_album_client_rel($album_ids)
{
global $mysqli, $Tables;
$where_id = "(";

	if(count($album_ids) > 0) {
		//-------------
		foreach($album_ids as $album_id) {
			$where_id .= ($album_id > 0) ? "album_id=$album_id OR " : "";
		}
		//-------------


		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";
			$query = "DELETE FROM ".$Tables['album_client']." WHERE $where_id";

			if($mysqli->query($query)) {
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
	else {
		return false;
	}
}


/**
 * Delete Albums 
 * @param array $album_ids
 * @param bool $delete_related_objects Default: false
 * @param integer $user_id Default: 0
 * @param bool $delete_culinks Default: true
 * @param bool $delete_cache Default: true
 * @param bool $update_cache Default: true
 * @param void
 */
function cug_del_albums($album_ids, $delete_related_objects=false, $user_id=0,  $delete_culinks=true, $delete_cache=true, $update_cache=true)
{
global $mysqli, $Tables, $ftp_server;

	if(count($album_ids) > 0) {

		//get related objects
		$related_objects = cug_get_album_related_object_ids($album_ids);
		
		//delete relations
		cug_del_album_member_rel($album_ids);
		cug_del_album_client_rel($album_ids);

		foreach($related_objects as $related_object) {//$related_objects always contains at least passed album_id even if this album is not linked to any other object				
			// delete tracks
			if(isset($related_object['tracks'])) {
				foreach($related_object['tracks'] as $track_id) {
					
					//check tracks if it is in other albums
					$track_album_rel = cug_get_track_album_rel($track_id, $item_type="TRACK");
					$track_is_in_other_albums = false;
					
						foreach($track_album_rel as $rel) {
							if(!in_array($rel['album_id'], $album_ids)) {
								$track_is_in_other_albums = true;
								break;
							}
						}
					//------------------
					
					if(!$track_is_in_other_albums) {//delete track					
						cug_del_tracks(array($track_id), $delete_related_objects, $user_id, $delete_culinks, $delete_cache, $update_cache);
					}
					else {// delete only track-album relations
						foreach($album_ids as $album_id) {
							cug_del_track_album_rel(array($track_id), $album_id);
								
								//delete culinks
								if($delete_culinks)
									cug_cache_del_track_culink($track_id, $album_id); 
								//-------------------------
								
								//cache tables
								if($delete_cache) {
									//delete from 'cache_tracks' table
									cug_cache_del_track($track_id);
									
									//update 'cache_tracks' table
									if($update_cache) {
									    cug_cache_add_track($track_id);
									}
								}
								//-------------------------
								
						}
					}
				}
			}
				
			// delete discs
			if(isset($related_object['discs'])) {
				foreach($related_object['discs'] as $disc_id) {
					cug_del_disc($disc_id, $user_id);
				}
			}

			
			//delete album
			$mysqli->query("DELETE FROM ".$Tables['album'] ." WHERE id=".$related_object['album_id']);
			cug_delete_obj_img($related_object['album_id'], "ALBUM", $ftp_server);
			
			//delete album_more_info
			$mysqli->query("DELETE FROM {$Tables['album_more_info']} WHERE album_id=".$related_object['album_id']);
			
			//delete cumarket_albums
			$mysqli->query("DELETE FROM {$Tables['cumarket_albums']} WHERE album_id=".$related_object['album_id']);
			
			//delete album-release
			cug_del_album_release($related_object['album_id']);
			
			
			//delete culinks
			if($delete_culinks)
				cug_cache_del_album_culink($related_object['album_id']);
			//-------------------------
			
			
			//update cache tables
			if($delete_cache) {
				//update 'cache_albums' table
				cug_cache_del_album($related_object['album_id']);	
			
				//update 'cache_members' table
				if(!empty($related_object['members'])) {
					foreach($related_object['members'] as $member_id) {
						cug_cache_del_member($member_id);
						
						if($update_cache) {
						  cug_cache_add_member($member_id);
						}
					}
				}
			}
			//-------------------------
			
			
			//register log
			$obj = new cug__log_te();
			$obj->action_id = 4; //Delete
			$obj->subaction_id = 0;
			$obj->object_id = 2; //Album
			$obj->object_item_id = $related_object['album_id'];
			$obj->subitem_id = 0;
			$obj->user_id = $user_id;
			
			$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1";
			$obj->country_id = cug_get_country_id_by_ip($ip);
			
			$obj->ip = $ip;
			$obj->session_id = session_id();
			$obj->start_time = @date("Y-m-d H:i:s");
			$obj->end_time = @date("Y-m-d H:i:s");
			cug_reg_log_te($obj);
			unset($obj);

		}//end of foreach

		if($delete_related_objects)
			cug_del_album_related_objects($related_objects, $user_id=0);

	}//end of if(count($track_ids) > 0)
}


/**
 * Delete Disc
 * @param integer
 * @param bool
 */
function cug_del_disc($disc_id, $user_id=0)
{
global $mysqli, $Tables;	
	
	if($mysqli->query("DELETE FROM ".$Tables['album_disc'] ." WHERE id=$disc_id")) {
		
		//register log
		$obj = new cug__log_te();
		$obj->action_id = 4; //Delete
		$obj->subaction_id = 0;
		$obj->object_id = 3; //Disc
		$obj->object_item_id = $disc_id;
		$obj->subitem_id = 0;
		$obj->user_id = $user_id;
		
		$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1";
		$obj->country_id = cug_get_country_id_by_ip($ip);
		$obj->ip = $ip;
		
		$obj->session_id = session_id();
		$obj->start_time = @date("Y-m-d H:i:s");
		$obj->end_time = @date("Y-m-d H:i:s");
		cug_reg_log_te($obj);
		unset($obj);
		
		return true;
	}
	else {
		return false;
	}
}


/**
 * Get current Tag Status of the Album
 *
 * @param integer (id of the Album)
 * @return integer
 */
function cug_get_album_tag_status($album_id)
{
global $mysqli, $Tables;

	if($album_id > 0) {
		$query = "SELECT tag_status_id FROM ".$Tables['album']." WHERE id=$album_id";
		$r = $mysqli->query($query);
		if($r) {
			$arr = $r->fetch_array();
			if($arr) {
				return $arr['tag_status_id'];
			}
		}
	}

return 0;
}


/**
 * Get Album's Owner (register_from) ID
 *
 * @param integer (id of the Album)
 * @return integer
 */
function cug_get_album_owner_id($album_id) {
    global $mysqli, $Tables;

    if($album_id > 0) {
        $query = "SELECT register_from FROM ".$Tables['album']." WHERE id=$album_id";
        $r = $mysqli->query($query);
        
        if($r && $r->num_rows) {
            $arr = $r->fetch_assoc();
            return $arr['register_from'];
        }
    }

    return 0;
}


/**
 * Get Album IDs by Genre IDs
 *
 * @param integer $limit
 * @param array $genres
 * @param integer $register_from (Optional)
 * @param array $default_albums_by_genres (Optional)
 * @param int $label_category_id (Optional)
 * @return array
 */
function cug_get_album_ids_by_genres($limit=30, $genres, $register_from=0, $default_albums_by_genres=array(), $label_category_id=0)
{
global $mysqli, $Tables, $FILE_SERVER_URL;
$result = array();

	if(count($genres) > 0) {		
			foreach($genres as $key => $val) {
				$genre_id = (is_array($val)) ? $val['id'] : ((is_int($key)) ? $val : 0);
											
				if($genre_id > 0) {
					$query = "SELECT a.* FROM {$Tables['album']} AS a ";
					
					//get default album ids
					if(!empty($default_albums_by_genres[$genre_id]) && is_array($default_albums_by_genres[$genre_id]) && count($default_albums_by_genres[$genre_id]) > 0) {
					    $query .= "WHERE ";
						foreach($default_albums_by_genres[$genre_id] as $album_id) {
							$query .= "a.id=".$album_id." OR ";
						}
						
						$query = rtrim($query, "OR ");
						
						//to get result in same order
						$query .= " ORDER BY CASE a.id ";
						$counter = 1;
						foreach($default_albums_by_genres[$genre_id] as $album_id) {
							$query .= " WHEN ".$album_id." THEN ".$counter;
							$counter ++;
						}
						$query .= " END";
						//echo $query;
					} 
					else {//get albums from DB by genre_id
					    if($label_category_id > 0) {
					        $query .= "INNER JOIN {$Tables['album_label_cat']} AS alcr ON a.id=alcr.album_id ";
					    }
					    
					    $query .= "WHERE ";
						$query .= ($register_from > 0) ? "a.register_from=$register_from AND " : "";
						$query .= ($label_category_id > 0) ? "alcr.label_cat_id=$label_category_id AND " : "";
						$query .= " a.genre_id=$genre_id AND a.img_174 IS NOT NULL";
						$query .= " ORDER BY rand()"; 
						$query .= " LIMIT 0, $limit";
					}
					
					//echo PHP_EOL.$query.PHP_EOL;
					$r = $mysqli->query($query);
						if($r->num_rows) {
							$index = 0;
							while($arr = $r->fetch_assoc()) {
								$result[$genre_id][$index]['id'] = $arr['id'];
								$result[$genre_id][$index]['title'] = $arr['title'];
								$result[$genre_id][$index]['genre_id'] = $arr['genre_id'];
								
								if(!empty($arr['img_174'])) {
									$result[$genre_id][$index]['cover_174'] = $arr['img_path']."/?o=album&i=".$arr['id']."&s=174";
								}
								else {
									$result[$genre_id][$index]['cover_174'] = $FILE_SERVER_URL."/?o=album&i=-1&s=174";
								}
									
								$index ++;
							}
						}

						
						//check if for some Genre we have number of Albums less then number provided in $limit variable, if so then fullfill it
						if(count($default_albums_by_genres) > 0) {
    						$albums_in_current_genre = count($result[$genre_id]);
    						
    						if($albums_in_current_genre < $limit) {
    							$albums_needed = $limit - $albums_in_current_genre;
    							
    							//generate query
    							$query = "SELECT * FROM {$Tables['album']} WHERE genre_id=$genre_id AND img_174 IS NOT NULL";
    							$album_ids = "";
    							foreach($result[$genre_id] as $key => $val) {
    								$album_ids .= " id<>{$val['id']} AND";
    							}
    							
    							if($album_ids) {
    								$album_ids = rtrim($album_ids, "AND");
    								$query .= " AND ".$album_ids;
    							}
    							
    							$query .= " ORDER BY rand() LIMIT $albums_needed";
    							
    							//execute query
    							$r = $mysqli->query($query);
    							if($r->num_rows) {
    								while($arr = $r->fetch_array()) {
    									$result[$genre_id][$index]['id'] = $arr['id'];
    									$result[$genre_id][$index]['title'] = $arr['title'];
    									$result[$genre_id][$index]['genre_id'] = $arr['genre_id'];
    								
    									if(!empty($arr['img_174'])) {
    										$result[$genre_id][$index]['cover_174'] = $arr['img_path']."/?o=album&i=".$arr['id']."&s=174";
    									}
    									else {
    										$result[$genre_id][$index]['cover_174'] = $FILE_SERVER_URL."/?o=album&i=-1&s=174";
    									}
    										
    									$index ++;
    								}								
    							}
    						}
						}
						//-------------------------------
				}
				
	
			}
	}

return $result;
}


/**
 * Copy Album
 * 
 * @param int $source_album_id
 * @param boolean $copy_discs_tracks_rel (true)
 * @param boolean $copy_clients_rel (true)
 * @param boolean $copy_members_rel (true)
 * @return int
 */
function cug_copy_album($source_album_id, $copy_discs_tracks_rel=true, $copy_clients_rel=true, $copy_members_rel=true) {
	global $mysqli, $Tables, $slash;

	$source_album = cug_get_album($source_album_id);
	
	if($source_album != null) {
		unset($source_album->id);
		unset($source_album->uniqid);
		unset($source_album->update_time);
		
		$source_album->img_34 = $source_album->img_34_num;
		$source_album->img_64 = $source_album->img_64_num;
		$source_album->img_174 = $source_album->img_174_num;
		$source_album->img_300 = $source_album->img_300_num;
		$source_album->img_600 = $source_album->img_600_num;
		$source_album->img_orgn = $source_album->img_orgn_num;
		
		$new_album_id = cug_reg_album($source_album); //make copy, register source album as a new album
		
			if($new_album_id > 0) {
				
				//copy album covers
				if($source_album->img_34 > 0) 
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "34");
				if($source_album->img_64 > 0) 
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "64");
				if($source_album->img_174 > 0)
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "174");
				if($source_album->img_300 > 0)
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "300");
				if($source_album->img_600 > 0)
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "600");
				if($source_album->img_orgn > 0)
					cug_copy_obj_img($source_album_id, $new_album_id, "ALBUM", "mega");
				//--------------------
				
				//copy discs, album-tracks-relations
				if($copy_discs_tracks_rel) {
					//get source album discs/tracks
					$discs_tracks = cug_get_album_tracks($source_album_id);			
					
					foreach($discs_tracks as $disc_track) {
						$source_disc_id = $disc_track['disc']['id'];
						
						//register new disc
						$source_disc = new cug__album_disc();
						$source_disc->album_id		= $new_album_id;
						$source_disc->title 		= $disc_track['disc']['title'];
						$source_disc->disc_num 		= $disc_track['disc']['disc_num'];
						$source_disc->total_tracks 	= $disc_track['disc']['total_tracks'];
						$source_disc->total_time 	= $disc_track['disc']['total_time'];
						$source_disc->img_path 		= $disc_track['disc']['img_path'];
						$source_disc->img_34 		= $disc_track['disc']['img_34_num'];
						$source_disc->img_64 		= $disc_track['disc']['img_64_num'];
						$source_disc->img_174 		= $disc_track['disc']['img_174_num'];
						$source_disc->img_300 		= $disc_track['disc']['img_300_num'];
						$source_disc->img_600 		= $disc_track['disc']['img_600_num'];
						$source_disc->img_orgn 		= $disc_track['disc']['img_orgn_num'];
						$source_disc->img_path 		= $disc_track['disc']['img_path'];
						
						$new_disc_id = cug_reg_disc($source_disc);
						
							if($new_disc_id > 0) {
								//copy cover image of the source disc
								if(!empty($source_disc->img_orgn)) {
									
									if($source_disc->img_34 > 0)
										cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "34");
									if($source_disc->img_64 > 0)
										cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "64");
									if($source_disc->img_174 > 0)
										cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "174");
									if($source_disc->img_300 > 0)
										cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "300");
									if($source_disc->img_600 > 0)
										cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "600");

									cug_copy_obj_img($source_disc_id, $new_disc_id, "DISC", "mega");								
								}
								//--------------------
								
								//register new track-album relations
								foreach($disc_track['tracks'] as $track) {
									cug_reg_track_album_rel($track['track_id'], $new_album_id, $new_disc_id, $source_disc->disc_num, $track['track_num'], $track['hidden'], $track['file_id']);
								}
							}
					}
				}
				//----------------------
				
				
				//copy album-client-relations
				if($copy_clients_rel) {
					$album_client_rel = cug_get_album_client_rel($source_album_id, $item_type="ALBUM");
						foreach($album_client_rel as $album_client) {
							cug_reg_album_client_rel($new_album_id, $album_client['client_id'], $album_client['licensee_id']);
						}
				}
					
				//copy album-member-relations
				if($copy_members_rel) {
					$album_member_rel = cug_get_album_member_rel($source_album_id, $item_type="ALBUM");
						foreach($album_member_rel as $album_member) {
							cug_reg_album_member_rel($new_album_id, $album_member['member_id'], $album_member['role_id']);
						}	
				}
				
				return $new_album_id; //OK
			}
			else {
				return -1; //unable to register new album
			}
	}
	else {
		return 0; //unknown source album
	}
	
}


/**
 * Check Disc for existing
 * Return 0 if Disc does not exists, or ID of the Disc
 * 
 * @param number $album_id
 * @paramnumber $disc_num
 * @param number $total_tracks (default: 0)
 * @param number $total_time (default: 0)
 * @return number
 */
function cug_check_album_disc($album_id, $disc_num, $total_tracks=0, $total_time=0) {
	global $mysqli, $Tables;
	
	if($album_id > 0 && $disc_num > 0) {
		$query = "SELECT id FROM {$Tables['album_disc']} WHERE album_id=$album_id AND disc_num=$disc_num";
		$query .= ($total_tracks > 0) ? " AND total_tracks=$total_tracks" : "";
		$query .= ($total_time > 0) ? " AND total_time=$total_time" : "";
		//echo $query;
		
		$r = $mysqli->query($query);
			if($r->num_rows) {
				$row = $r->fetch_array();
				return $row['id'];
			}
	}
	
	return 0;
}


/**
 * Register Album's Additional Data 
 * 
 * Used in API
 * 
 * @param int $album_id
 * @param string $info
 * @param string $ext_img_url (External Image Cover URL)
 * @return number
 */
function cug_reg_album_more_info($album_id, $info, $ext_img_url="") {
	global $mysqli, $Tables;

	if($album_id > 0 && ($info || $ext_img_url)) {

		//check for existing record
		$query = "SELECT id FROM {$Tables['album_more_info']} WHERE album_id=$album_id";
		$r = $mysqli->query($query);
		if($r->num_rows) {
			$arr = $r->fetch_array();
			return $arr['id'];
		}

		//register new record
		$fields = "album_id,";
		$values = $album_id.",";

		if($info) {
			$fields .= "info,";
			$values .= "'".$mysqli->escape_str($info)."',";
		}
		//------------------
		if($ext_img_url) {
			$fields .= "ext_img_url,";
			$values .= "'".$mysqli->escape_str($ext_img_url)."',";
		}


		$reg_date = date("Y-m-d H:i:s");


		$fields .= "register_date";
		$values .= "'".$reg_date."'";

		$query = "INSERT INTO {$Tables['album_more_info']} (".$fields.") VALUES(".$values.")";

		if($mysqli->query($query))
			return $mysqli->insert_id;
		else
			return -1;
	}
	else
		return 0;
}


/**
 * Edit Album's Additional Data
 *
 * Used in API
 *
 * @param int $album_id
 * @param bool $update_empty_fields
 * @param string $info
 * @param string $ext_img_url (External Image URL)
 * @param bool $register_as_new default: false
 * @return number
 */
function cug_edit_album_more_info($album_id, $update_empty_fields, $info, $ext_img_url, $register_as_new=false) {
	global $mysqli, $Tables;

	if($album_id > 0) {

		//check for existing record
		$query = "SELECT id, ext_img_url FROM {$Tables['album_more_info']} WHERE album_id=$album_id";
		$r = $mysqli->query($query);
		//--------------------------
		if($r->num_rows) {
			$row = $r->fetch_array();
			$ext_img_url_old = $row['ext_img_url'];

			$values = "";

			//check fields
			if(isset($info)) {
				if(!empty($info))
					$values .= "info='".$mysqli->escape_str($info)."',";
				elseif($update_empty_fields)
				    $values .= "info=null,";
			}
			//---------
			if(isset($ext_img_url)) {
				if(!empty($ext_img_url))
					$values .= "ext_img_url='".$mysqli->escape_str($ext_img_url)."',";
				elseif($update_empty_fields)
				    $values .= "ext_img_url=null,";
				
				$values .= "ext_img_downloaded=null,";
			}
			//-----------------------

			if($values) {
				$values = substr($values, 0, strlen($values)-1);

				$query = "UPDATE {$Tables['album_more_info']} SET $values WHERE album_id=$album_id";
				if($mysqli->query($query)) {

					//update image statuse
				    if(isset($ext_img_url)) {
                        if(empty($ext_img_url)) {
                            if($update_empty_fields) {
						      $status_arr = array(0,0,0,0,0,0,0);
						      cug_update_obj_img_status("ALBUM", $album_id, $status_arr);
                            }
                        }
                        else {
                          $status_arr = array(0,0,0,0,0,0,0);
                          cug_update_obj_img_status("ALBUM", $album_id, $status_arr);
                        }
					}
					

					return true;
				}
			}
		}
		else {
			if($register_as_new) {
				if(cug_reg_album_more_info($album_id, $info, $ext_img_url) > 0)
					return true;
				else 
					return false;
			}
			else {
				return true;
			}
		}

	}

	return false;
}


/**
 * Update Status of External Image for the Member
 *
 * Used in API
 *
 * @param int $album_id
 * @param int $status (1 - was Downloaded;  0 - Was not downloaded;)
 * @return boolean
 */
function cug_edit_album_more_info_img_status($album_id, $status) {
	global $mysqli, $Tables;

	if($album_id > 0) {
		$query = "UPDATE {$Tables['album_more_info']} SET ext_img_downloaded=".$mysqli->escape_str($status)." WHERE album_id=$album_id";
		if($mysqli->query($query)) {
			return true;
		}
	}

	return false;
}



/**
 * Add New Track to Album
 *
 * Used in API
 *
 * @param int $client_id
 * @param string $user_ip
 * @param array $arr
 * @param bool $online (optional, default: false)
 * @return number|array
 */
function cug_add_new_tracks_to_album($client_id, $user_ip, $arr, $online=false) {
	global $mysqli, $Tables, $ERRORS, $FILE_SERVER_URL;
	$result = array();
	
	
	if( (!empty($arr['album_ext_id']) || !empty($arr['album_id'])) && !empty($arr['tracks']) && count($arr['tracks']) > 0 ) {
		
		//check mandatory fields
		$album_id_is_valid = true;
		$track_mandatory_fields = true;
		$file_mandatory_fields 	= true;
		
		
		//check album id
		$album_id = 0;
		$album_title = "";
		
		if(!empty($arr['album_id'])) {
			$album_id = $arr['album_id'];
			$album_obj = cug_get_album($album_id);
			
				if($album_obj == null)
					$album_id_is_valid = false;
				else 
					$album_title = $album_obj->title;
		}
		else {
			if($client_id == 3066) //SHenzhen (Tony)
		        $query = "SELECT id, title FROM {$Tables['album']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str(trim($arr['album_ext_id']));
			else
			    $query = "SELECT id, title FROM {$Tables['album']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str(trim($arr['album_ext_id']))."'";
			
			
			$r = $mysqli->query($query);				
			if($r->num_rows) {
				$row = $r->fetch_array();
				$album_id = $row['id'];
				$album_title = $row['title'];
			}
			
			if(!$album_id)
				$album_id_is_valid = false;
		}
		
		
		//check tracks fields
		if($album_id_is_valid) {
			foreach($arr['tracks'] as $track) {
				if(empty($track['track_ext_id']) || empty($track['track_num']) || empty($track['disc_num']) || empty($track['track_title'])) {
					$track_mandatory_fields = false;
					break;
				}
				
				if(empty($track['file']['file_ext_name']) || empty($track['file']['file_type_id']) || empty($track['file']['file_format_id']) || empty($track['file']['time'])) {
					$file_mandatory_fields = false;
					break;
				}			
			}
		}
		
		//---------------------------------
		if(!$album_id_is_valid) {
			return $ERRORS['INVALID_ALBUM_ID'];
		}
		//---------------------------------
		if(!$track_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_TRACK_FIELDS'];
		}
		//---------------------------------
		if(!$file_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_FILE_FIELDS'];
		}
		
		
		//START REGISTRATIONS
		$reg_date = @date("Y-m-d H:i:s");
		$total_tracks = count($arr['tracks']);
		
		$result['album_id'] = $album_id;
		
		$result['total_discs'] = $total_tracks;
		$result['discs_success'] = 0;
		$result['discs_failed'] = 0;
		$result['disc_ids'] = "";
		
		$result['total_tracks'] = $total_tracks;
		$result['tracks_success'] = 0;
		$result['tracks_failed'] = 0;
		$result['track_ids'] = "";
		
		$result['total_files'] = $total_tracks;
		$result['files_success'] = 0;
		$result['files_failed'] = 0;
		$result['file_ids'] = "";
		
		foreach($arr['tracks'] as $track) {
			//get disc id
			$disc_num = trim($track['disc_num']);
			$disc_id = cug_get_album_disc_id($album_id, $disc_num);
			
			if(!$disc_id) {//register new disc
				$new_disc = new cug__album_disc();
				$new_disc->album_id 	= $album_id;
				$new_disc->disc_num 	= $disc_num;
				$new_disc->total_tracks = 0;
				$new_disc->total_time 	= 0;
				$new_disc->title 		= $album_title;
				$new_disc->img_path 	= $FILE_SERVER_URL;
					
				$disc_id = cug_reg_disc($new_disc);				
			}
			//-------------------------------
			
			if($disc_id > 0) {
				$result['discs_success'] += 1;
				$result['disc_ids'] .= $disc_id.",";
				
				//check for existing track-album relation
				$track_num = trim($track['track_num']);
				$query = "SELECT * FROM {$Tables['track_album_rel']} WHERE album_id=$album_id AND disc_id=$disc_id AND track_num=$track_num";
				$r = $mysqli->query($query);
				
				if(!$r->num_rows) {
					//check for existing TRACK
				    if($client_id == 3066) //Shenzhen (Tony)
					    $query = "SELECT id FROM {$Tables['track']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str(trim($track['track_ext_id']));					
					else 
					    $query = "SELECT id FROM {$Tables['track']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str(trim($track['track_ext_id']))."'";
					
					$r = $mysqli->query($query);
					if($r->num_rows) {
						$row = $r->fetch_array();
						$track_id = $row['id'];
					}
					else {//register new TRACK
						$new_track = new cug__track();
						
						if($client_id == 3066) //Shenzhen (Tony)
						    $new_track->shenzhen_id = trim($track['track_ext_id']);
						else 
						    $new_track->external_id = trim($track['track_ext_id']);
						
						$new_track->unconfirmed	    = !empty($track['unconfirmed']) ? trim($track['unconfirmed']) : 0;
						$new_track->title 		    = trim($track['track_title']);
						$new_track->part			= !empty($track['track_part']) ? trim($track['track_part']) : "";
						$new_track->version			= !empty($track['track_version']) ? trim($track['track_version']) : "";
						$new_track->lang_id			= !empty($track['lang_id']) ? trim($track['lang_id']) : 0;
						$new_track->genre_id		= !empty($track['genre_id']) ? trim($track['genre_id']) : 0;
						$new_track->fileunder_id	= !empty($track['fileunder_id']) ? trim($track['fileunder_id']) : 0;
						$new_track->genre_from_file = !empty($track['genre_name']) ? trim($track['genre_name']) : "";
						$new_track->rec_date		= !empty($track['rec_date']) ? cug_parse_date_for_mysql(trim($track['rec_date'])) : "";
							
						$new_track->tag_status_id = 1; //not checked
						$new_track->has_file = 0;
							
						//parse 'copyright_c'
						$copyright_c = !empty($track['copyright_c']) ? trim($track['copyright_c']) : "";
						$copyright_c_id = 0;
							
						if($copyright_c) {
							$query = "SELECT id FROM {$Tables['member']} WHERE title='".$mysqli->escape_str($copyright_c)."' OR alias='".$mysqli->escape_str($copyright_c)."'";
							$r = $mysqli->query($query);
							if($r->num_rows) {
								$row = $r->fetch_array();
								$copyright_c_id = $row['id'];
							}
							else {
								$new_member = new cug__member();
								$new_member->title = $copyright_c;
					
								$new_member->register_from 	= $client_id;
								$new_member->register_date 	= $reg_date;
								$new_member->register_ip 	= $user_ip;
					
								$copyright_c_id = cug_reg_member($new_member);
							}
					
						}
						//-----------------
					
						$new_track->copyright_c = ($copyright_c_id > 0) ? $copyright_c_id : 0;
							
						$new_track->register_from 	= $client_id;
						$new_track->register_date 	= $reg_date;
						$new_track->register_ip 	= $user_ip;
						if($online) $new_track->online = 1;
							
						$track_id = cug_reg_track($new_track);
					}

					if($track_id > 0) {
						$result['tracks_success'] += 1;
						$result['track_ids'] .= $track_id.",";
						
						//check for existing FILE
						$file = $track['file'];
						$query = "SELECT id FROM {$Tables['track_file']} WHERE f_register_from=$client_id AND file_ext_name='".$mysqli->escape_str(trim($file['file_ext_name']))."'";
						$r = $mysqli->query($query);
						
						if($r->num_rows) {
							$row = $r->fetch_array();
							$file_id = $row['id'];
						}
						else { //register new FILE
							$new_file = new cug__track_file();
							$new_file->file_ext_name 	= trim($file['file_ext_name']);
							$new_file->f_track_type_id 	= trim($file['file_type_id']);
							$new_file->f_format_id 		= trim($file['file_format_id']);
							$new_file->f_track_time	 	= trim($file['time'])*1000;
							$new_file->f_size	= !empty($file['file_size']) ? trim($file['file_size']) : 0;
							$new_file->f_brate	= !empty($file['file_brate']) ? trim($file['file_brate']) : 0;
							$new_file->f_srate	= !empty($file['file_srate']) ? trim($file['file_srate']) : 0;
							$new_file->f_fp_status = 0;
							$new_file->f_wm_status = 0;
						
							$new_file->f_register_from 	= $client_id;
							$new_file->f_register_date 	= $reg_date;
							$new_file->f_register_ip 	= $user_ip;
						
							$file_id = cug_reg_track_file($new_file);
						}

						if($file_id > 0) {
							$result['files_success'] += 1;
							$result['file_ids'] .= $file_id.",";
							
							$track_is_hidden = !empty($track['hidden']) ? 1 : 0;
							cug_reg_track_album_rel($track_id, $album_id, $disc_id, $disc_num, $track_num, $track_is_hidden, $file_id);
							
							//TRACK-MEMBER relations
							if(!empty($track['members'])) {
								foreach($track['members'] as $member) {
									$member_id 			= !empty($member['member_id']) ? trim($member['member_id']) : "";
									$member_ext_id 		= !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
									$member_role_id 	= !empty($member['role_id']) ? trim($member['role_id']) : 0;
									$member_is_primary 	= !empty($member['isprimary']) ? trim($member['isprimary']) : 0;
							
									if($member_id > 0 && $member_role_id > 0) {
										//check MEMBER ID
										$query = "SELECT id FROM {$Tables['member']} WHERE id=".$mysqli->escape_str($member_id);
										$r = $mysqli->query($query);
										if(!$r->num_rows) {
											$member_id = 0;
										}
									}
									else {
										if($member_ext_id && $member_role_id > 0) {
											//check if we have this MEMBER already
											if($client_id == 3066) //Shenzhen (Tony)
										        $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str($member_ext_id);
											else 
											    $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str($member_ext_id)."'";
											
											
											$r = $mysqli->query($query);
											if($r->num_rows) {
												$row = $r->fetch_array();
												$member_id = $row['id'];
											}
												
										}
									}
							
									if($member_id > 0) {
										cug_reg_track_member_rel($track_id, $member_id, $member_role_id, $member_is_primary);
									}
							
								}
							}
							//---------------------	
							
							//TRACK-PUBLISHERS relations
							if(!empty($track['publishers'])) {
								foreach($track['publishers'] as $publisher) {
									$publisher_id = cug_get_client_id_by_title(trim($publisher['publisher_name']), $insert_new_client=true);
									if($publisher_id > 0) {
										cug_reg_track_publisher_rel($track_id, $publisher_id);
									}
								}
							}
							//--------------------							

							//TRACK-CLIENT relations
							if(!empty($track['isrc'])) {
								cug_reg_track_client_rel($track_id, $client_id, $masterright_id="", $licensor_id="", $licensee_id="", $copyright_p="", $our_masterright_id="", trim($track['isrc']));
							}
							//---------------------	

						}
						else {
							$result['files_failed'] += 1;
							$result['file_ids'] .= $file_id.","; //error registering file
						}
					}
					else {
						$result['tracks_failed'] += 1;
						$result['files_failed'] += 1;
						$result['track_ids'] .= $track_id.","; //error registering new track
						$result['file_ids'] .= "0,";
					}
				}
				else {
					$result['tracks_failed'] += 1;
					$result['files_failed'] += 1;
					$result['track_ids'] .= "-2,"; // track-album relation already exists
					$result['file_ids'] .= "0,";
				}
								
			}
			else {
				$result['discs_failed'] += 1;
				$result['disc_ids'] .= $disc_id.","; //error registering disc
			}
		}
	}
	else {
		return $ERRORS['INVALID_DATA_STRUCTURE'];
	}
	
	
	if($result['disc_ids']) 	$result['disc_ids'] 	= rtrim($result['disc_ids'], ",");
	if($result['track_ids']) 	$result['track_ids'] 	= rtrim($result['track_ids'], ",");
	if($result['file_ids']) 	$result['file_ids'] 	= rtrim($result['file_ids'], ",");
	
	return $result;
}


/**
 * Import Albums
 * 
 * Used in API
 * 
 * @param int $client_id
 * @param string $user_ip
 * @param array $arr
 * @param bool $online (optional, default: false)
 * @return number|array
 */
function cug_import_albums($client_id, $user_ip, $arr, $online=false) {
	global $mysqli, $Tables, $ERRORS, $FILE_SERVER_URL;
	$result = array();
	
	$total_albums = !empty($arr['albums']) ? count($arr['albums']) : 0;

	if($total_albums > 0) {
		//check data
		$album_mandatory_fields = true;
		$disc_mandatory_fields 	= true;
		$track_mandatory_fields = true;
		$file_mandatory_fields 	= true;
		$total_discs_num_is_ok 	= true;
		$total_tracks_num_is_ok = true;
		$discs_total_time_is_ok	= true;
			
		foreach($arr['albums'] as $album) {
			//check album's mandatory fields
			if(empty($album['album_ext_id']) || empty($album['album_title']) || empty($album['total_discs'])) {
				$album_mandatory_fields = false;
				break;
			}

			//check total discs number
			$total_discs = count($album['discs']);
			if((int)$album['total_discs'] != $total_discs) {
				$total_discs_num_is_ok = false;
				break;
			}

				
			//-----------------------
			foreach($album['discs'] as $disc) {
				//check disc's mandatory fields
				if(empty($disc['disc_num']) || empty($disc['total_tracks']) || empty($disc['total_time'])) {
					$disc_mandatory_fields = false;
					break 2;
				}

				$discs_total_time = trim($disc['total_time']);

				//check total tracks number
				$total_tracks = count($disc['tracks']);
				if((int)$disc['total_tracks'] != $total_tracks) {
					$total_tracks_num_is_ok = false;
					break 2;
				}

				//-----------------
				$tracks_total_time = 0;
				foreach($disc['tracks'] as $track) {
					//check track's mandatory fields
					if(empty($track['track_ext_id']) || empty($track['track_num']) || empty($track['track_title'])) {
						$track_mandatory_fields = false;
						break 3;
					}

					//check file's mandatory fields
					if(empty($track['file']['file_ext_name']) || empty($track['file']['file_type_id']) || empty($track['file']['file_format_id']) || empty($track['file']['time'])) {
						$file_mandatory_fields = false;
						break 3;
					}
						
					$tracks_total_time += trim($track['file']['time']);
				}

				//check tracks total time
				if($discs_total_time != $tracks_total_time) {
					$discs_total_time_is_ok = false;
					break 2;
				}
			}
		}
			
		if(!$album_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_ALBUM_FIELDS'];
		}
		//------------------------------
		if(!$total_discs_num_is_ok) {
			return $ERRORS['DISCS_NUM_NOT_MATCHED'];
		}
		//------------------------------
		if(!$disc_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_DISC_FIELDS'];
		}
		//------------------------------
		if(!$total_tracks_num_is_ok) {
			return $ERRORS['TRACKS_NUM_NOT_MATCHED'];
		}
		//------------------------------
		if(!$track_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_TRACK_FIELDS'];
		}
		//---------------------------------
		if(!$file_mandatory_fields) {
			return $ERRORS['NOT_ENOUGH_FILE_FIELDS'];
		}
		//---------------------------------
		if(!$discs_total_time_is_ok) {
			return $ERRORS['DISCS_TOTAL_TIME_NOT_MATCHED'];
		}
		//---------------------------------
		//---------------------------------

		$reg_date = @date("Y-m-d H:i:s");
			
		$result['total_albums'] = $total_albums;
		$result['albums_success'] 		= 0;
		$result['albums_failed'] 		= 0;
		$result['album_ids'] 			= "";
		$result['albums']				= array();
		$album_index = 0;
			
		//START REGISTRATIONS
		foreach($arr['albums'] as $album) {
			//check for existing ALBUM
			if($client_id == 3066) //Shenzhen (Tony)
			    $query = "SELECT id FROM {$Tables['album']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str(trim($album['album_ext_id']));
			else 
			 $query = "SELECT id FROM {$Tables['album']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str(trim($album['album_ext_id']))."'";
			
			
			$r = $mysqli->query($query);

			if($r->num_rows) {
				$row = $r->fetch_array();
				$album_id = $row['id'];
			}
			else {//register new ALBUM
				$new_album = new cug__album();
				
				if($client_id == 3066) //Shenzhen (Tony)
				    $new_album->shenzhen_id = trim($album['album_ext_id']);
				else 
				    $new_album->external_id = trim($album['album_ext_id']);
				
				$new_album->title 		= trim($album['album_title']);
				$new_album->subtitle 		= !empty($album['album_subtitle']) ? trim($album['album_subtitle']) : "";
				$new_album->title_version 	= !empty($album['title_version']) ? trim($album['title_version']) : "";
				$new_album->genre_id 		= !empty($album['genre_id']) ? trim($album['genre_id']) : 0;
				$new_album->fileunder_id 	= !empty($album['fileunder_id']) ? trim($album['fileunder_id']) : 0;
				$new_album->genre_from_file = !empty($album['genre_name']) ? trim($album['genre_name']) : "";
				$new_album->lang_id 		= !empty($album['lang_id']) ? trim($album['lang_id']) : 0;
				$new_album->total_discs 	= !empty($album['total_discs']) ? trim($album['total_discs']) : 0;
				$new_album->label_code 		= !empty($album['label_code']) ? trim($album['label_code']) : "";
				$new_album->catalogue_num 	= !empty($album['catalogue_num']) ? trim($album['catalogue_num']) : "";
				$new_album->rec_date 		= !empty($album['rec_date']) ? cug_parse_date_for_mysql(trim($album['rec_date'])) : "";
				$new_album->rel_date 		= !empty($album['rel_date']) ? cug_parse_date_for_mysql(trim($album['rel_date'])) : "";
				$new_album->ean_code 		= !empty($album['ean_code']) ? trim($album['ean_code']) : "";
				$new_album->upc_code 		= !empty($album['upc_code']) ? trim($album['upc_code']) : "";
				$new_album->type_id 		= !empty($album['album_type_id']) ? trim($album['album_type_id']) : 0;
				$new_album->rel_format_id 	= !empty($album['release_format_id']) ? trim($album['release_format_id']) : 0;
				$new_album->copyright_c_date= !empty($album['copyright_c_date']) ? cug_parse_date_for_mysql(trim($album['copyright_c_date'])) : "";
				$new_album->copyright_p_date= !empty($album['copyright_p_date']) ? cug_parse_date_for_mysql(trim($album['copyright_p_date'])) : "";
					
				$copyright_p 	= !empty($album['copyright_p']) ? trim($album['copyright_p']) : "";
				$copyright_c 	= !empty($album['copyright_c']) ? trim($album['copyright_c']) : "";
					
				$copyright_p_id = cug_get_client_id_by_title($copyright_p, $insert_new_client=true);
				$copyright_c_id = cug_get_client_id_by_title($copyright_c, $insert_new_client=true);
					
				$new_album->copyright_p = ($copyright_p_id > 0) ? $copyright_p_id : 0;
				$new_album->copyright_c = ($copyright_c_id > 0) ? $copyright_c_id : 0;
					
				$new_album->register_from 	= $client_id;
				$new_album->register_date 	= $reg_date;
				$new_album->register_ip 	= $user_ip;
				$new_album->img_path		= $FILE_SERVER_URL;
				if($online) $new_album->online = 1;
				
				$album_id = cug_reg_album($new_album);
			}

			if($album_id > 0) {
				$result['albums_success'] += 1;
				$result['albums'][$album_index]['album_id'] = $album_id;
					
				//register additional info
				$cover_url 			= !empty($album['cover_url']) ? trim($album['cover_url']) : "";
				$additional_info 	= !empty($album['additional_info']) ? trim($album['additional_info']) : "";
					
				if($cover_url || $additional_info) {
					cug_reg_album_more_info($album_id, $additional_info, $cover_url);
				}
					
				//register ALBUM-MEMBER relations
				if(!empty($album['members'])) {
					foreach($album['members'] as $member) {
						$member_id = !empty($member['member_id']) ? trim($member['member_id']) : 0;
						$member_ext_id = !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
						$member_role_id = !empty($member['role_id']) ? trim($member['role_id']) : 0;
							
						if($member_id > 0 && $member_role_id > 0) {
							//check MEMBER ID
							$query = "SELECT id FROM {$Tables['member']} WHERE id=".$mysqli->escape_str($member_id);
							$r = $mysqli->query($query);
								if(!$r->num_rows) {
									$member_id = 0;
								}
						}
						else {
							if($member_ext_id && $member_role_id > 0) {
								//check if we have this MEMBER already
							    if($client_id == 3066) //Shenzhen (Tony)
								    $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str($member_ext_id);
							    else 
							        $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str($member_ext_id)."'";
								
								$r = $mysqli->query($query);
								if($r->num_rows) {
									$row = $r->fetch_array();
									$member_id = $row['id'];
								}
								//---------------------------

							}
						}
						
						
							if($member_id > 0) {
								cug_reg_album_member_rel($album_id, $member_id, $member_role_id);
							}
					}
				}
				//---------------------
					
				//register ALBUM-CLIENT relations
				if(!empty($album['licensee'])) {
					foreach($album['licensee'] as $licensee) {
						$licensee_id = cug_get_client_id_by_title(trim($licensee['licensee_name']), $insert_new_client=true);
						if($licensee_id > 0) {
							cug_reg_album_client_rel($album_id, $client_id, $licensee_id);
						}
					}
				}
				//--------------------
					
				//DISCS
				$result['albums'][$album_index]['total_discs'] 		= count($album['discs']);
				$result['albums'][$album_index]['discs_success'] 	= 0;
				$result['albums'][$album_index]['discs_failed'] 	= 0;
				$result['albums'][$album_index]['disc_ids'] 		= "";
				$result['albums'][$album_index]['discs']			= array();
				$disc_index = 0;
					
				foreach($album['discs'] as $disc) {
					$disc_total_time = trim($disc['total_time'])*1000;
					
					//check for existing DISC
					$disc_id = cug_check_album_disc($album_id, trim($disc['disc_num']), 0, 0);
					//$disc_id = cug_check_album_disc($album_id, trim($disc['disc_num']), trim($disc['total_tracks']), $disc_total_time);
					$disc_num = trim($disc['disc_num']);

					if($disc_id == 0) {//register new DISC
						$new_disc = new cug__album_disc();
						$new_disc->album_id 	= $album_id;
						$new_disc->disc_num 	= $disc_num = trim($disc['disc_num']);
						$new_disc->total_tracks = trim($disc['total_tracks']);
						$new_disc->total_time 	= $disc_total_time;
						$new_disc->title 		= !empty($disc['disc_title']) ? trim($disc['disc_title']) : trim($album['album_title']);
						$new_disc->img_path 	= $FILE_SERVER_URL;
							
						$disc_id = cug_reg_disc($new_disc);
					}

					if($disc_id > 0) {
						$result['albums'][$album_index]['discs_success'] += 1;
						$result['albums'][$album_index]['discs'][$disc_index]['disc_id'] = $disc_id;
							
						//TRACKS
						$result['albums'][$album_index]['discs'][$disc_index]['total_tracks'] 	= count($disc['tracks']);
						$result['albums'][$album_index]['discs'][$disc_index]['tracks_success'] = 0;
						$result['albums'][$album_index]['discs'][$disc_index]['tracks_failed'] 	= 0;
						$result['albums'][$album_index]['discs'][$disc_index]['track_ids'] 		= "";
						
						
						$total_file_ids = 0;
						$files_success = 0;
						$files_failed = 0;
						$files_ids = "";
						
						$track_index = 0;
							
						foreach($disc['tracks'] as $track) {
							//check for existing TRACK
						    if($client_id == 3066) //Shenzhen (Tony)
							     $query = "SELECT id FROM {$Tables['track']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str(trim($track['track_ext_id']));
						    else 
						        $query = "SELECT id FROM {$Tables['track']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str(trim($track['track_ext_id']))."'";

							$r = $mysqli->query($query);

							if($r->num_rows) {
								$row = $r->fetch_array();
								$track_id = $row['id'];
							}
							else {//register new TRACK
								$new_track = new cug__track();
								
								if($client_id == 3066) //Shenzhen (Tony)
								    $new_track->shenzhen_id = trim($track['track_ext_id']);
								else 
								    $new_track->external_id = trim($track['track_ext_id']);
								
								$new_track->unconfirmed     = !empty($track['unconfirmed']) ? trim($track['unconfirmed']) : 0;
								$new_track->title 		    = trim($track['track_title']);
								$new_track->part			= !empty($track['track_part']) ? trim($track['track_part']) : "";
								$new_track->version			= !empty($track['track_version']) ? trim($track['track_version']) : "";
								$new_track->lang_id			= !empty($track['lang_id']) ? trim($track['lang_id']) : 0;
								$new_track->genre_id		= !empty($track['genre_id']) ? trim($track['genre_id']) : 0;
								$new_track->fileunder_id	= !empty($track['fileunder_id']) ? trim($track['fileunder_id']) : 0;
								$new_track->genre_from_file = !empty($track['genre_name']) ? trim($track['genre_name']) : "";
								$new_track->rec_date		= !empty($track['rec_date']) ? cug_parse_date_for_mysql(trim($track['rec_date'])) : "";
									
								$new_track->tag_status_id = 1; //not checked
								$new_track->has_file = 0;
									
								//parse 'copyright_c'
								$copyright_c = !empty($track['copyright_c']) ? trim($track['copyright_c']) : "";
								$copyright_c_id = 0;
									
								if($copyright_c) {
									$query = "SELECT id FROM {$Tables['member']} WHERE title='".$mysqli->escape_str($copyright_c)."' OR alias='".$mysqli->escape_str($copyright_c)."'";
									$r = $mysqli->query($query);
									if($r->num_rows) {
										$row = $r->fetch_array();
										$copyright_c_id = $row['id'];
									}
									else {
										$new_member = new cug__member();
										$new_member->title = $copyright_c;

										$new_member->register_from 	= $client_id;
										$new_member->register_date 	= $reg_date;
										$new_member->register_ip 	= $user_ip;

										$copyright_c_id = cug_reg_member($new_member);
									}
										
								}
								//-----------------

								$new_track->copyright_c = ($copyright_c_id > 0) ? $copyright_c_id : 0;
									
								$new_track->register_from 	= $client_id;
								$new_track->register_date 	= $reg_date;
								$new_track->register_ip 	= $user_ip;
								if($online) $new_track->online = 1;
									
								$track_id = cug_reg_track($new_track);
							}


							if($track_id > 0) {
								$track_num = trim($track['track_num']);
								$track_is_hidden = !empty($track['hidden']) ? trim($track['hidden']) : 0;
									
								$result['albums'][$album_index]['discs'][$disc_index]['tracks_success'] += 1;
									
								//FILE
								$file = $track['file'];
									
								//check for existing FILE
								$query = "SELECT id FROM {$Tables['track_file']} WHERE f_register_from=$client_id AND file_ext_name='".$mysqli->escape_str(trim($file['file_ext_name']))."'";
									
								$r = $mysqli->query($query);

								if($r->num_rows) {
									$row = $r->fetch_array();
									$file_id = $row['id'];
								}
								else { //register new FILE
									$new_file = new cug__track_file();
									$new_file->file_ext_name 	= trim($file['file_ext_name']);
									$new_file->f_track_type_id 	= trim($file['file_type_id']);
									$new_file->f_format_id 		= trim($file['file_format_id']);
									$new_file->f_track_time	 	= trim($file['time'])*1000;
									$new_file->f_size	= !empty($file['file_size']) ? trim($file['file_size']) : 0;
									$new_file->f_brate	= !empty($file['file_brate']) ? trim($file['file_brate']) : 0;
									$new_file->f_srate	= !empty($file['file_srate']) ? trim($file['file_srate']) : 0;
									$new_file->f_fp_status = 0;
									$new_file->f_wm_status = 0;
										
									$new_file->f_register_from 	= $client_id;
									$new_file->f_register_date 	= $reg_date;
									$new_file->f_register_ip 	= $user_ip;
										
									$file_id = cug_reg_track_file($new_file);
								}
								

								$total_file_ids += 1;
								$files_ids .= $file_id.",";
								
									if($file_id > 0) {
										$files_success += 1;
										
										//TRACK-ALBUM-DISC relations
										cug_reg_track_album_rel($track_id, $album_id, $disc_id, $disc_num, $track_num, $track_is_hidden, $file_id);
									}
									else {
										$files_failed += 1;
									}


								//TRACK-MEMBER relations
								if(!empty($track['members'])) {
									foreach($track['members'] as $member) {
										$member_id 			= !empty($member['member_id']) ? trim($member['member_id']) : "";
										$member_ext_id 		= !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
										$member_role_id 	= !empty($member['role_id']) ? trim($member['role_id']) : 0;
										$member_is_primary 	= !empty($member['isprimary']) ? trim($member['isprimary']) : 0;

										if($member_id > 0 && $member_role_id > 0) {
											//check MEMBER ID
											$query = "SELECT id FROM {$Tables['member']} WHERE id=".$mysqli->escape_str($member_id);
											$r = $mysqli->query($query);
												if(!$r->num_rows) {
													$member_id = 0;
												}
										}
										else {
											if($member_ext_id && $member_role_id > 0) {
												//check if we have this MEMBER already
											    if($client_id == 3066) //Shenzhen (Tony)
												    $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str($member_ext_id);
											    else 
											        $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str($member_ext_id)."'";
												
												
												$r = $mysqli->query($query);
												if($r->num_rows) {
													$row = $r->fetch_array();
													$member_id = $row['id'];
												}
													
											}
										}
										
											if($member_id > 0) {
												cug_reg_track_member_rel($track_id, $member_id, $member_role_id, $member_is_primary);
											}
										
									}
								}
								//---------------------

								//TRACK-PUBLISHERS relations
								if(!empty($track['publishers'])) {
									foreach($track['publishers'] as $publisher) {
										$publisher_id = cug_get_client_id_by_title(trim($publisher['publisher_name']), $insert_new_client=true);
										if($publisher_id > 0) {
											cug_reg_track_publisher_rel($track_id, $publisher_id);
										}
									}
								}
								//--------------------
								
								
								//TRACK-CLIENT relations
								if(!empty($track['isrc'])) {
									cug_reg_track_client_rel($track_id, $client_id, $masterright_id="", $licensor_id="", $licensee_id="", $copyright_p="", $our_masterright_id="", trim($track['isrc']));
								}
								//---------------------
								
									
								$track_index ++;
							}
							else {
								$result['albums'][$album_index]['discs'][$disc_index]['tracks_failed'] += 1;
							}

							$result['albums'][$album_index]['discs'][$disc_index]['track_ids'] .= $track_id.",";
						}
							
						$result['albums'][$album_index]['discs'][$disc_index]['track_ids'] = !empty($result['albums'][$album_index]['discs'][$disc_index]['track_ids']) ? substr($result['albums'][$album_index]['discs'][$disc_index]['track_ids'], 0, strlen($result['albums'][$album_index]['discs'][$disc_index]['track_ids']) - 1) : "";
						
						$result['albums'][$album_index]['discs'][$disc_index]['total_files'] = $total_file_ids;
						$result['albums'][$album_index]['discs'][$disc_index]['files_success'] = $files_success;
						$result['albums'][$album_index]['discs'][$disc_index]['files_failed'] = $files_failed;
						$result['albums'][$album_index]['discs'][$disc_index]['file_ids'] = !empty($files_ids) ? substr($files_ids, 0, strlen($files_ids) - 1) : "";
						
						$disc_index ++;
					}
					else {
						$result['albums'][$album_index]['discs_failed'] += 1;
					}

					$result['albums'][$album_index]['disc_ids'] .= $disc_id.",";
				}
					
				$result['albums'][$album_index]['disc_ids'] = !empty($result['albums'][$album_index]['disc_ids']) ? substr($result['albums'][$album_index]['disc_ids'], 0, strlen($result['albums'][$album_index]['disc_ids']) - 1) : "";
					
				$album_index ++;
			}
			else {
				$result['albums_failed'] += 1;
			}

			$result['album_ids'] .= $album_id.",";
		}
			
		$result['album_ids'] = !empty($result['album_ids']) ? substr($result['album_ids'], 0, strlen($result['album_ids']) - 1) : "";
		return $result;
	}
	else {
		return $ERRORS['INVALID_ALBUM_DATA_STRUCTURE'];
	}
}


/**
 * Update Albums
 *
 * Used in API
 *
 * @param int $client_id
 * @param string $user_ip
 * @param string $albums_data
 * @return number|array
 */
function cug_update_albums($client_id, $user_ip, $albums_data) {
	global $mysqli, $Tables, $ERRORS;
	$result = array();

	if(!$client_id) {
		return $ERRORS['INVALID_CLIENT_ID'];
	}
	//-----------------
	if(!$user_ip) {
		$user_ip = $_SERVER['REMOTE_ADDR'];
	}
	//-----------------
	
	if(!empty($albums_data)) {
		$arr = json_decode($albums_data, true);
		
		//check JSON Data on errors
		$error_code = cug_json_last_error();
		if($error_code < 0) {
			return $error_code;
		}
		//-------------------------
		
		$total_albums = !empty($arr['albums']) ? count($arr['albums']) : 0;
		
		if($total_albums > 0) {
			//check mandatory fields
			$mandatory_fields = true;
			foreach($arr['albums'] as $album) {
				if(empty($album['album_ext_id']) && empty($album['album_id'])) {
					$mandatory_fields = false;
					break;
				}
			}
			
			if(!$mandatory_fields) {
				return $ERRORS['NOT_ENOUGH_ALBUM_FIELDS'];
			}
			//------------------------------

			//update albums
			$result['total_albums'] = $total_albums;
			$result['albums_success'] 		= 0;
			$result['albums_failed'] 		= 0;
			$result['album_ids'] 	= "";

			foreach($arr['albums'] as $album) {
				$status = 0; //no action
				
				//check albums id
				$album_id_to_be_checked = !empty($album['album_id']) ? trim($album['album_id']) : 0;
				$album_ext_id_to_be_checked = !empty($album['album_ext_id']) ? trim($album['album_ext_id']) : 0;
					
				$album_id = cug_check_album_id($album_id_to_be_checked, $album_ext_id_to_be_checked, $client_id);
				$incoming_album_id = ($album_id_to_be_checked > 0) ? $album_id_to_be_checked : $album_ext_id_to_be_checked;
				//---------------------------
				
				
				if($album_id > 0) {
					$fields = 0;
					
					$album_obj = new cug__album();
					if(isset($album['album_title'])) 		{ $album_obj->title 			= trim($album['album_title']); $fields ++; }
					if(isset($album['title_version'])) 		{ $album_obj->title_version 	= trim($album['title_version']); $fields ++; }
					if(isset($album['genre_id'])) 			{ $album_obj->genre_id 			= trim($album['genre_id']); $fields ++; }
					if(isset($album['fileunder_id'])) 		{ $album_obj->fileunder_id 		= trim($album['fileunder_id']); $fields ++; }
					if(isset($album['lang_id'])) 			{ $album_obj->lang_id 			= trim($album['lang_id']); $fields ++; }
					if(isset($album['label_code'])) 		{ $album_obj->label_code 		= trim($album['label_code']); $fields ++; }
					if(isset($album['catalogue_num'])) 		{ $album_obj->catalogue_num 	= trim($album['catalogue_num']); $fields ++; }
					if(isset($album['rec_date'])) 			{ $album_obj->rec_date 			= cug_parse_date_for_mysql(trim($album['rec_date'])); $fields ++; }
					if(isset($album['rel_date'])) 			{ $album_obj->rel_date 			= cug_parse_date_for_mysql(trim($album['rel_date'])); $fields ++; }
					if(isset($album['ean_code'])) 			{ $album_obj->ean_code 			= trim($album['ean_code']); $fields ++; }
					if(isset($album['upc_code'])) 			{ $album_obj->upc_code 			= trim($album['upc_code']); $fields ++; }
					if(isset($album['album_type_id'])) 		{ $album_obj->type_id 			= trim($album['album_type_id']); $fields ++; }
					if(isset($album['release_format_id'])) 	{ $album_obj->rel_format_id 	= trim($album['release_format_id']); $fields ++; }					
					if(isset($album['copyright_c_date'])) 	{ $album_obj->copyright_c_date 	= cug_parse_date_for_mysql(trim($album['copyright_c_date'])); $fields ++; }					
					if(isset($album['copyright_p_date'])) 	{ $album_obj->copyright_p_date 	= cug_parse_date_for_mysql(trim($album['copyright_p_date'])); $fields ++; }
					
					if(isset($album['copyright_c'])) {
						$copyright_c = !empty($album['copyright_c']) ? trim($album['copyright_c']) : "";
						$copyright_c_id = cug_get_client_id_by_title($copyright_c, $insert_new_client=true);
						$album_obj->copyright_c = $copyright_c_id;  
						$fields ++;
					}
					
					if(isset($album['copyright_p'])) {
						$copyright_p = !empty($album['copyright_p']) ? trim($album['copyright_p']) : "";
						$copyright_p_id = cug_get_client_id_by_title($copyright_p, $insert_new_client=true);
						$album_obj->copyright_p = $copyright_p_id;
						$fields ++;
					}

					
					
					if($fields > 0) {
						$album_was_updadted = cug_edit_album(array($album_id), $album_obj, true);
					}
					
					//update additional info
					$cover_url 			= isset($album['cover_url']) ? trim($album['cover_url']) : null;
					$additional_info 	= isset($album['additional_info']) ? trim($album['additional_info']) : null;
					
					if(isset($cover_url) || isset($additional_info)) {
						$album_moreinfo_was_updadted = cug_edit_album_more_info($album_id, $update_empty_fields=true, $additional_info, $cover_url, $register_as_new=true);
					}
					
					
					//update ALBUM-MEMBER relations
					if(!empty($album['members']) && count($album['members']) > 0) {
						$updated_album_member_rel = 0;						
						cug_del_album_member_rel(array($album_id)); //delete all existing relations
						
						if(count($album['members']) == 1 && count($album['members'][0]) == 0) {
							$updated_album_member_rel = 1;
						}
						else {						
							foreach($album['members'] as $member) {
								$member_id_to_be_checked = !empty($member['member_id']) ? trim($member['member_id']) : 0;
								$member_ext_id_to_be_checked = !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
								$member_role_id = !empty($member['role_id']) ? trim($member['role_id']) : 0;
	
								$member_id = cug_check_member_id($member_id_to_be_checked, $member_ext_id_to_be_checked, $client_id);			
						
									if($member_id > 0) {
										if(cug_reg_album_member_rel($album_id, $member_id, $member_role_id) > 0)
											$updated_album_member_rel += 1;
									}
								
							}
						}
						
						//define status
						if($updated_album_member_rel == count($album['members']))
							$album_member_rel_status = true;
						else 
							$album_member_rel_status = false;
					}
					//---------------------
					
					
					//update ALBUM-CLIENT relations
					if(!empty($album['licensee']) && count($album['licensee']) > 0) {
						$updated_album_client_rel = 0;
						cug_del_album_client_rel(array($album_id)); //delete all existing relations
						
						if(count($album['licensee']) == 1 && count($album['licensee'][0]) == 0) {
							$updated_album_client_rel = 1;
						}
						else {						
							foreach($album['licensee'] as $licensee) {
								if(!empty($licensee['licensee_name'])) {
									$licensee_id = cug_get_client_id_by_title(trim($licensee['licensee_name']), $insert_new_client=true);
									if($licensee_id > 0) {
										if(cug_reg_album_client_rel($album_id, $client_id, $licensee_id) > 0)
											$updated_album_client_rel += 1;
									}
								}
							}
						}
						
						//define status
						if($updated_album_client_rel == count($album['licensee']))
							$album_client_rel_status = true;
						else
							$album_client_rel_status = false;						
					}
					//--------------------					
					
					
						//define final status
						//---------------------
						if(isset($album_was_updadted) || isset($album_moreinfo_was_updadted)) {
							if((isset($album_was_updadted) && $album_was_updadted) || (isset($album_moreinfo_was_updadted) && $album_moreinfo_was_updadted)) {
								$status = 1; // OK
							}
							else {
								$status = -4; //internal error
							}
						}
						//----------------------
						if($status >= 0) {
							if(isset($album_member_rel_status)) {
								if($album_member_rel_status)
									$status = 1; // OK
								else 
									$status = -2; // Some Members were not updated
							}
						}
						//----------------------
						if($status >= 0) {
							if(isset($album_client_rel_status)) {
								if($album_client_rel_status)
									$status = 1; // OK
								else
									$status = -3; // Some Licensee were not updated
							}
						}
						//----------------------
						
				}
				else {
					$status = -1; //unknown album id
				}
				
					//check status
					if($status == 1) {
						$result['albums_success'] += 1;
					}
					else {
						$result['albums_failed'] += 1;
					}
					//-------------------	
				
				$result['album_ids'] .= $incoming_album_id.":".$status.",";
			}
			
			$result['album_ids'] = !empty($result['album_ids']) ? substr($result['album_ids'], 0, strlen($result['album_ids']) - 1) : "";
			return $result;
		}
		else 
			return $ERRORS['INVALID_ALBUM_DATA_STRUCTURE'];
	}
	else 
		echo $ERRORS['NO_ALBUM_DATA'];
}


/**
 * Check Album ID
 *
 * @param int $album_id
 * @param int $album_ext_id
 * @param int $client_id (default: 0)
 * @return int
 */
function cug_check_album_id($album_id, $album_ext_id, $client_id=0) {
	global $mysqli, $Tables;
	$field = "";
	$result = 0;

	if($album_id) {
		$album_id_str = $mysqli->escape_str($album_id);
		$field = "id";
	}
	elseif($album_ext_id) {
	    if($client_id == 3066){ //Shenzhen (Tony)
	       $album_id_str = $mysqli->escape_str($album_ext_id);
		   $field = "shenzhen_id";
	    }
	    else {
	        $album_id_str = "'".$mysqli->escape_str($album_ext_id)."'";
	        $field = "external_id";	        
	    }
	}
	//------------------------

	if($field) {
		$query = "SELECT id FROM {$Tables['album']} WHERE $field=".$album_id_str;
		$query .= ($client_id > 0) ? " AND register_from=$client_id" : "";
		$r = $mysqli->query($query);
			
		if($r->num_rows) {
			$row = $r->fetch_array();
			$result = $row['id'];
		}
		else {
			$result = -1;
		}
	}

	return $result;
}



/**
 * Register Album-Release Relation
 * 
 * @param int $album_id
 * @param string $country_allowed
 * @param string $digital_release_date
 * @param int $price_code_id
 * @return boolean
 */
function cug_reg_album_release($album_id, $country_allowed, $digital_release_date, $price_code_id) {
    global $mysqli, $Tables;
    $result = false;
    
    if($album_id > 0 && $country_allowed && $digital_release_date && $price_code_id > 0) {
        $query = "INSERT INTO {$Tables['album_release']} (album_id, country_allowed, dig_rel_date, price_code_id) VALUES(";
        $query .= "$album_id, '".$mysqli->escape_str($country_allowed)."', '".$mysqli->escape_str($digital_release_date)."', $price_code_id";
        $query .= ")";
        
        if($mysqli->query($query))
            $result = true;
    }
    
    return $result;
}


/**
 * Get Album Releases
 * 
 * @param int $album_id
 * @return array
 */
function cug_get_album_release($album_id) {
    global $mysqli, $Tables;
    $result = array();

    if($album_id) {
        $query = "SELECT * FROM {$Tables['album_release']} WHERE album_id=$album_id";
        $r = $mysqli->query($query);

        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
        }
    }

    return $result;
}


/**
 * Delete Album-Release Relation
 * 
 * @param int $album_id
 * @return boolean
 */
function cug_del_album_release($album_id) {
    global $mysqli, $Tables;
    $result = false;

    if($album_id > 0) {
        $query = "DELETE FROM {$Tables['album_release']} WHERE album_id=$album_id";

        if($mysqli->query($query))
            $result = true;
    }

    return $result;
}


/**
 * Register Album-LabelCategory Relation
 * 
 * @param int $album_id
 * @param int $label_cat_id
 * @param string $catalogue_num (Optional)
 * @return int
 */
function cug_reg_album_label_category_rel($album_id, $label_cat_id, $catalogue_num="") {
    global $mysqli, $Tables;

    if($album_id > 0 && $label_cat_id > 0) {
        // Check for existing record
        $r = $mysqli->query("SELECT id FROM ".$Tables['album_label_cat']." WHERE album_id=$album_id AND label_cat_id=$label_cat_id");

        if(!$r->num_rows) {
            $catalogue_num = ($catalogue_num) ? "'".$catalogue_num."'" : "NULL";
            $query = "INSERT INTO ".$Tables['album_label_cat']." VALUES(NULL, $album_id, $label_cat_id, $catalogue_num, NOW())";

            if($mysqli->query($query))
                return $mysqli->insert_id;
            else
                return -1;
        }
        else {
            $arr = $r->fetch_assoc();
            return $arr['id'];
        }
    }
    else
        return 0;
}


/**
 * Check if Album belongs to Label Category
 * 
 * @param int $album_id
 * @param int $label_cat_id
 * @return boolean
 */
function cug_is_album_from_label_category($album_id, $label_cat_id) {
    global $mysqli, $Tables;
    $result = false;
    
    if($album_id > 0 && $label_cat_id > 0) {
        $query = "SELECT id FROM {$Tables['album_label_cat']} WHERE album_id=$album_id AND label_cat_id=$label_cat_id";
        $r = $mysqli->query($query);
        
        if($r->num_rows)
            $result = true;
    }
    
    return $result;
}


/**
 * Change Client for Album and it's related objects
 * 
 * @param array $album_ids_arr
 * @param int $new_client_id
 * @param bool $update_album_members (Optional, Change Client for Album's Members too, default: true)
 * @param bool $update_track_members (Optional, default: true)
 * @param bool $update_tracks (Optional, default: true)
 * @param bool $update_files (Optional, default: true)
 * @return void
 */
function cug_change_album_client($album_ids_arr, $new_client_id, $update_album_members=true, $update_track_members=true, $update_tracks=true, $update_files=true){
    global $mysqli, $Tables;
    
    if($new_client_id > 0) {
        foreach($album_ids_arr as $album_id) {
            //change Client for Album
            $query = "UPDATE {$Tables['album']} SET register_from=$new_client_id WHERE id=$album_id";
            $mysqli->query($query);
            
            //change Client for Album Members
            if($update_album_members) {
                $arr = cug_get_album_member_rel($album_id, "ALBUM");
                foreach($arr as $val) {
                    $query = "UPDATE {$Tables['member']} SET register_from=$new_client_id WHERE id={$val['member_id']}";
                    $mysqli->query($query);
                }
            }
            
            //change Client for Tracks, Files
            if($update_tracks || $update_files) {
                $tracks = cug_get_album_tracks($album_id);
                foreach($tracks as $val) {
                    foreach($val['tracks'] as $track) {
                        $track_id = $track['track_id'];
                        $file_id = $track['file_id'];
                        
                        //change Clent for Tracks
                        if($update_tracks) {
                            $query = "UPDATE {$Tables['track']} SET register_from=$new_client_id WHERE id=$track_id";
                            $mysqli->query($query);
                        }
                        
                        //change Clent for Files
                        if($update_files) {
                            $query = "UPDATE {$Tables['track_file']} SET f_register_from=$new_client_id WHERE id=$file_id";
                            $mysqli->query($query);
                        }
                        
                    }
                }
            }
            //------------------------
            
            //change Client for Track Members
            if($update_track_members) {
                $members = cug_get_album_tracks_members($album_id);
            
                foreach($members as $member) {
                    $query = "UPDATE {$Tables['member']} SET register_from=$new_client_id WHERE id={$member['member_id']}";
                    $mysqli->query($query);
                }
            }
            //------------------------            
               
        }
    }
}


/**
 * Get total time of the Album
 * 
 * @param int $album_id
 * @param int $format (Optional, 1 - hh:mm:ss.ms; 2 - mm:ss, 3 - hh:mm:ss)
 * @return number|string
 */
function cug_get_album_total_time($album_id, $format=0) {
    global $mysqli, $Tables;
    $result = 0;
    
    if($album_id > 0) {
        $query = "SELECT SUM(total_time) AS time FROM {$Tables['album_disc']} WHERE album_id=$album_id";
        $r = $mysqli->query($query);
        
        if($r && $r->num_rows) {
            $row = $r->fetch_assoc();
            $result = $row['time'];
            
            if($format > 0) {
                $result = cug_mseconds_to_time($result, $format);
            }
        }
    }
    
    return $result;
}
?>