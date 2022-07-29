<?PHP 
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Track
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Track Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	part (STRING)
 * @param	version (STRING)
 * @param	arrangement_id (INT)
 * @param	lang_id (INT)
 * @param	tag_lang_id (INT)
 * @param	genre_id (INT)
 * @param	fileunder_id (INT)
 * @param	tag_status_id (INT)
 * @param	has_file (INT)
 * @param	lineup (STRING)
 * @param	characters (STRING)
 * @param	from_movie (STRING)
 * @param	colour_id (INT)
 * @param	rec_country_id (INT)
 * @param	rec_city (STRING)
 * @param	rec_date (STRING - MySQL DATE Format)
 * @param	rec_company_id (INT)
 * @param	copyright_c (STRING)
 * @param	register_from (INTEGER)
 * @param	register_date (STRING - MySQL DATETIME Format)
 * @param	register_ip (STRING)
 * @param	trash_status (INTEGER)
 * @param	online (INTEGER)
 * @param	comments (STRING)
 * @param	uniqid - STRING
 * @param	explicit_content - (INT)
 * @param	prelistening_index - (INT)
 * @param	prelistening_duration - (INT)
 * @param	external_id - STRING (Some identification for the Track, which comes from the Client)
 * @param	shenzhen_id - (INT)
 * @param	unconfirmed - (INT)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__track
{
	public
	$id,
	$title,
	$part,
	$version,
	$arrangement_id,
	$lang_id,
	$tag_lang_id,
	$genre_id,
	$fileunder_id,
	$genre_from_file,
	$tag_status_id,
	$has_file,
	$lineup,
	$characters,
	$from_movie,
	$score_url,
	$colour_id,
	$rec_country_id,
	$rec_city,
	$rec_date,
	$rec_company_id,
	$copyright_c,
	$register_from,
	$register_date,
	$register_ip,
	$trash_status,
	$online,
	$comments,
	$uniqid,
	$explicit_content,
	$prelistening_index,
	$prelistening_duration,
	$external_id,
	$shenzhen_id,
	$unconfirmed,
	$update_time;
}


/**
 * Track's File Class
 *
 * @param	f_id (INT)
 * @param	f_track_type_id (INT)
 * @param	f_track_time (INT)
 * @param	f_fp_status (INT)
 * @param	f_wm_status (INT)
 * @param	f_prev_path (int)
 * @param	f_format_id (INT)
 * @param	f_size (INT)
 * @param	f_brate (INT)
 * @param	f_srate (INT)
 * @param	f_register_from (INTEGER)
 * @param	f_register_date (STRING - MySQL DATETIME Format)
 * @param	f_register_ip (STRING)
 * @param	file_ext_name (STRING)
 * @param	f_uniqid (STRING)
 * @param	f_update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__track_file extends cug__track
{
	public
	$f_id,
	$f_track_type_id,
	$f_track_time,
	$f_fp_status,
	$f_wm_status,
	$f_prev_path,
	$f_format_id,
	$f_size,
	$f_brate,
	$f_srate,
	$f_register_from,
	$f_register_date,
	$f_register_ip,
	$file_ext_name,
	$f_uniqid,
	$f_update_time;
}

/**
 * Track-Client Relation Class
 *
 * @param	id (INT)
 * @param	track_id (INT)
 * @param	client_id (INT)
 * @param	masterright_id (INT)
 * @param	licensor_id (INT)
 * @param	licensee_id (INT)
 * @param	copyright_p (INT)
 * @param	our_masterright_id (INT)
 * @param	isrc (STRING)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__track_client_rel
{
	public
	$id,
	$track_id,
	$client_id,
	$masterright_id,
	$licensor_id,
	$licensee_id,
	$copyright_p,
	$our_masterright_id,
	$isrc,
	$uniq_id,
	$update_time;
}

/**
 * Track-Member Relation Class
 *
 * @param	id (INT)
 * @param	track_id (INT)
 * @param	member_id (INT)
 * @param	role_id (INT)
 * @param	isprimary (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__track_member_rel
{
	public
	$id,
	$track_id,
	$member_id,
	$role_id,
	$isprimary,
	$uniq_id,
	$update_time;
}

/**
 * Track-WM1 Class
 *
 * @param	wm1_code (INT)
 * @param	track_id (INT)
 * @param	file_id (INT)
 * @param	track_album_rel_id (INT)
 * @param	client_id (INT)
 * @param	licensee_id (INT)
 * @param	wm_status (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__track_wm1
{
	public
	$wm1_code,
	$track_id,
	$file_id,
	$track_album_rel_id,
	$client_id,
	$licensee_id,
	$wm_status,
	$uniq_id,
	$update_time;
}

/**
 * Arrangement Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	publisher_id (INT)
 * @param	music_score_url (STRING)
 * @param	creation_date (STRING - MySQL DATE Format)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__arrangement
{
	public
	$id,
	$title,
	$publisher_id,
	$music_score_url,
	$creation_date,
	$uniq_id,
	$update_time;
}

/**
 * Composition Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	part (STRING)
 * @param	genre_id (INT)
 * @param	music_period_id (STRING)
 * @param	music_date (STRING - MySQL DATE Format)
 * @param	tempo_id (INT)
 * @param	music_score_url (STRING)
 * @param	iswc (STRING)
 * @param	publisher_id (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__composition
{
	public
	$id,
	$title,
	$part,
	$genre_id,
	$music_period_id,
	$music_date,
	$tempo_id,
	$music_score_url,
	$iswc,
	$publisher_id,
	$uniqid,
	$update_time;
}

/**
 * Composition-Member Relation Class
 *
 * @param	id (INT)
 * @param	comp_id (INT)
 * @param	member_id (INT)
 * @param	role_id (INT)
 * @param	isprimary (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__composition_member_rel
{
	public
	$id,
	$comp_id,
	$member_id,
	$role_id,
	$isprimary,
	$uniq_id,
	$update_time;
}


/**
 * Check Track ID
 *
 * @param int $track_id
 * @param int $track_ext_id
 * @param int $client_id (default: 0)
 * @return int
 */
function cug_check_track_id($track_id, $track_ext_id, $client_id=0) {
	global $mysqli, $Tables;
	$field = "";
	$result = 0;

	if($track_id) {
		$track_id_str = $mysqli->escape_str($track_id);
		$field = "id";
	}
	elseif($track_ext_id) {
	    if($client_id == 3066) {//Shenzhen (Tony)
		    $track_id_str = $mysqli->escape_str($track_ext_id);
		    $field = "shenzhen_id";
	    }
	    else {
	        $track_id_str = "'".$mysqli->escape_str($track_ext_id)."'";
	        $field = "external_id";	        
	    }
	}
	//------------------------

	if($field) {
		$query = "SELECT id FROM {$Tables['track']} WHERE $field=".$track_id_str;
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
 * Register New Track
 *
 * @param object of Track Class
 * @return integer
 */
function cug_reg_track($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {
		

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->part)) {
			$fields .= "part,";
			$values .= "'".$mysqli->escape_str($obj->part)."',";
		}

		if(!empty($obj->version)) {
			$fields .= "version,";
			$values .= "'".$mysqli->escape_str($obj->version)."',";
		}

		if(!empty($obj->arrangement_id)) {
			$fields .= "arrangement_id,";
			$values .= $mysqli->escape_str($obj->arrangement_id).",";
		}

		if(!empty($obj->lang_id)) {
			$fields .= "lang_id,";
			$values .= $mysqli->escape_str($obj->lang_id).",";
		}
		
		if(!empty($obj->tag_lang_id)) {
			$fields .= "tag_lang_id,";
			$values .= $mysqli->escape_str($obj->tag_lang_id).",";
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

		if($obj->tag_status_id >= 0) {
			$fields .= "tag_status_id,";
			$values .= $mysqli->escape_str($obj->tag_status_id).",";
		}
		
		if($obj->has_file >= 0) {
			$fields .= "has_file,";
			$values .= $mysqli->escape_str($obj->has_file).",";
		}

		if(!empty($obj->lineup)) {
			$fields .= "lineup,";
			$values .= "'".$mysqli->escape_str($obj->lineup)."',";
		}

		if(!empty($obj->characters)) {
			$fields .= "characters,";
			$values .= "'".$mysqli->escape_str($obj->characters)."',";
		}

		if(!empty($obj->from_movie)) {
			$fields .= "from_movie,";
			$values .= "'".$mysqli->escape_str($obj->from_movie)."',";
		}
		
		if(!empty($obj->score_url)) {
			$fields .= "score_url,";
			$values .= "'".$mysqli->escape_str($obj->score_url)."',";
		}

		if(!empty($obj->colour_id)) {
			$fields .= "colour_id,";
			$values .= $mysqli->escape_str($obj->colour_id).",";
		}

		if(!empty($obj->rec_country_id)) {
			$fields .= "rec_country_id,";
			$values .= $mysqli->escape_str($obj->rec_country_id).",";
		}

		if(!empty($obj->rec_city)) {
			$fields .= "rec_city,";
			$values .= "'".$mysqli->escape_str($obj->rec_city)."',";
		}

		if(!empty($obj->rec_date)) {
			$fields .= "rec_date,";
			$values .= "'".$mysqli->escape_str($obj->rec_date)."',";
		}


		if(!empty($obj->rec_company_id)) {
			$fields .= "rec_company_id,";
			$values .= $mysqli->escape_str($obj->rec_company_id).",";
		}


		if(!empty($obj->copyright_c)) {
			$fields .= "copyright_c,";
			$values .= "'".$mysqli->escape_str($obj->copyright_c)."',";
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
		
		if($obj->online != null && $obj->online >= 0) {
			$fields .= "online,";
			$values .= $obj->online.",";
		}
		
		if(!empty($obj->comments)) {
			$fields .= "comments,";
			$values .= "'".$mysqli->escape_str($obj->comments)."',";
		}
		
		if(!empty($obj->update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}
		
		if(!empty($obj->explicit_content)) {
			$fields .= "explicit_content,";
			$values .= $mysqli->escape_str($obj->explicit_content).",";
		}
		
		if(!empty($obj->prelistening_index)) {
		    $fields .= "prelistening_index,";
		    $values .= $mysqli->escape_str($obj->prelistening_index).",";
		}
		
		if(!empty($obj->prelistening_duration)) {
		    $fields .= "prelistening_duration,";
		    $values .= $mysqli->escape_str($obj->prelistening_duration).",";
		}

		if(!empty($obj->external_id)) {
			$fields .= "external_id,";
			$values .= "'".$mysqli->escape_str($obj->external_id)."',";
		}
		
		if(!empty($obj->shenzhen_id)) {
		    $fields .= "shenzhen_id,";
		    $values .= $mysqli->escape_str($obj->shenzhen_id).",";
		}
		
		if(!empty($obj->unconfirmed)) {
		    $fields .= "unconfirmed,";
		    $values .= $mysqli->escape_str($obj->unconfirmed).",";
		}
		
		$fields .= "uniqid)";
		if(!empty($obj->uniqid)) {
			$values .= "'".$mysqli->escape_str($obj->uniqid)."')";
		}
		else {
			$uniqid = uniqid();
			$values .= "'".$uniqid."')";
		}


		$query = "INSERT INTO ".$Tables['track'].$fields.$values;
		
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
 * Register New Track's File
 *
 * @param object of Track File Class
 * @return integer
 */
function cug_reg_track_file($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->f_track_type_id)) {

		$fields = " (track_type_id,";
		$values = " VALUES(".$mysqli->escape_str($obj->f_track_type_id).",";

		if(!empty($obj->f_track_time)) {
			$fields .= "track_time,";
			$values .= $mysqli->escape_str($obj->f_track_time).",";
		}

		if($obj->f_fp_status >= 0) {
			$fields .= "fp_status,";
			$values .= $mysqli->escape_str($obj->f_fp_status).",";
		}
		
		if($obj->f_wm_status >= 0) {
			$fields .= "wm_status,";
			$values .= $mysqli->escape_str($obj->f_wm_status).",";
		}

		if(!empty($obj->f_prev_path)) {
			$fields .= "f_prev_path,";
			$values .= $mysqli->escape_str($obj->f_prev_path).",";
		}

		if(!empty($obj->f_format_id)) {
			$fields .= "f_format_id,";
			$values .= $mysqli->escape_str($obj->f_format_id).",";
		}

		if(!empty($obj->f_size)) {
			$fields .= "f_size,";
			$values .= $mysqli->escape_str($obj->f_size).",";
		}

		if(!empty($obj->f_brate)) {
			$fields .= "f_brate,";
			$values .= $mysqli->escape_str($obj->f_brate).",";
		}

		if(!empty($obj->f_srate)) {
			$fields .= "f_srate,";
			$values .= $mysqli->escape_str($obj->f_srate).",";
		}
		
		if(!empty($obj->f_register_from)) {
			$fields .= "f_register_from,";
			$values .= $mysqli->escape_str($obj->f_register_from).",";
		}
		
		if(!empty($obj->f_register_date)) {
			$fields .= "f_register_date,";
			$values .= "'".$mysqli->escape_str($obj->f_register_date)."',";
		}
		
		if(!empty($obj->f_register_ip)) {
			$fields .= "f_register_ip,";
			$values .= "'".$mysqli->escape_str($obj->f_register_ip)."',";
		}
		
		if(!empty($obj->file_ext_name)) {
			$fields .= "file_ext_name,";
			$values .= "'".$mysqli->escape_str($obj->file_ext_name)."',";
		}

		if(!empty($obj->f_update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->f_update_time)."',";
		}

		$fields .= "uniqid)";
		if(!empty($obj->f_uniqid)) {
			$values .= "'".$mysqli->escape_str($obj->f_uniqid)."')";
		}
		else {
			$uniqid = uniqid();
			$values .= "'".$uniqid."')";
		}


		$query = "INSERT INTO ".$Tables['track_file'].$fields.$values;

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
 * Get Track Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID' or 'ID', default is 'ID'
 * @return object of Trak_File Class
 */
function cug_get_track($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item)) {

		if($item_type == "ID") {
			$where = " WHERE t.id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$where = " WHERE t.uniqid='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}

		$query = "SELECT t.*, tfr.file_id AS f_id, f.track_type_id AS f_track_type_id, f.track_time AS f_track_time, f.fp_status AS f_fp_status, f.wm_status AS f_wm_status, fs.f_prev_path, fs.f_prev_path2, f.f_format_id, f.f_size, f.f_brate, f.f_srate, f.f_register_from, f.f_register_date, f.f_register_ip, f.uniqid AS f_uniqid, f.update_time AS f_update_time  FROM ".$Tables['track']." AS t LEFT JOIN {$Tables['track_file_rel']} AS tfr ON t.id=tfr.track_id LEFT JOIN ".$Tables['track_file']." AS f ON tfr.file_id=f.id LEFT JOIN {$Tables['track_file_server']} AS fs ON f.f_prev_path=fs.id".$where;

		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__track_file();
					
				$obj->id				= $arr['id'];
				$obj->title				= $arr['title'];
				$obj->part				= $arr['part'];
				$obj->version			= $arr['version'];
				$obj->arrangement_id	= $arr['arrangement_id'];
				$obj->lang_id			= $arr['lang_id'];
				$obj->tag_lang_id		= $arr['tag_lang_id'];
				$obj->genre_id			= $arr['genre_id'];
				$obj->fileunder_id		= $arr['fileunder_id'];
				$obj->genre_from_file	= $arr['genre_from_file'];
				$obj->tag_status_id		= $arr['tag_status_id'];
				$obj->has_file			= $arr['has_file'];
				$obj->lineup			= $arr['lineup'];
				$obj->characters		= $arr['characters'];
				$obj->from_movie		= $arr['from_movie'];
				$obj->score_url			= $arr['score_url'];
				$obj->colour_id			= $arr['colour_id'];
				$obj->rec_country_id	= $arr['rec_country_id'];
				$obj->rec_city			= $arr['rec_city'];
				$obj->rec_date			= $arr['rec_date'];
				$obj->rec_company_id	= $arr['rec_company_id'];
				$obj->copyright_c		= $arr['copyright_c'];
				$obj->register_from		= $arr['register_from'];
				$obj->online			= $arr['online'];
				$obj->register_date		= $arr['register_date'];
				$obj->register_ip		= $arr['register_ip'];
				$obj->trash_status		= $arr['trash_status'];
				$obj->comments			= $arr['comments'];
				$obj->uniqid			= $arr['uniqid'];
				$obj->explicit_content	= $arr['explicit_content'];
				$obj->prelistening_index	= $arr['prelistening_index'];
				$obj->prelistening_duration	= $arr['prelistening_duration'];
				$obj->external_id		= $arr['external_id'];
				$obj->shenzhen_id		= $arr['shenzhen_id'];
				$obj->update_time		= $arr['update_time'];
				$obj->f_id				= $arr['f_id'];
				$obj->f_track_id		= $arr['id'];
				$obj->f_track_type_id	= $arr['f_track_type_id'];
				$obj->f_track_time		= $arr['f_track_time'];
				$obj->f_fp_status		= $arr['f_fp_status'];
				$obj->f_wm_status		= $arr['f_wm_status'];
				$obj->f_prev_path		= ($arr['f_prev_path2']) ? $arr['f_prev_path']."/".$arr['f_prev_path2'] : $arr['f_prev_path'];
				$obj->f_format_id		= $arr['f_format_id'];
				$obj->f_size			= $arr['f_size'];
				$obj->f_brate			= $arr['f_brate'];
				$obj->f_srate			= $arr['f_srate'];
				$obj->f_register_from	= $arr['f_register_from'];
				$obj->f_register_date	= $arr['f_register_date'];
				$obj->f_register_ip		= $arr['f_register_ip'];
				$obj->f_uniqid			= $arr['f_uniqid'];
				$obj->f_update_time		= $arr['f_update_time'];
					
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
 * Get current Tag Status of the Track
 *
 * @param integer (id of the Track)
 * @return integer
 */
function cug_get_track_tag_status($track_id)
{
global $mysqli, $Tables;

	if($track_id > 0) {
		$query = "SELECT tag_status_id FROM ".$Tables['track']." WHERE id=$track_id";
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
 * Get Track File Format List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_file_format_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['track_file_format']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

	return $result;
}


/**
 * Get Track File Format (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_file_format($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['track_file_format'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['track_file_format'], "title", "id=".$mysqli->escape_str($item));
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
 * Edit Existing Track File Format
 *
 * @param integer (ID of Existing Track File Format)
 * @param string (New Track File Format)
 * @return integer
 */
function cug_edit_file_format($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['track_file_format']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New Track File Format
 *
 * @param string
 * @return integer
 */
function cug_reg_file_format($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['track_file_format'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['track_file_format']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Get Track File Type List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_file_type_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['track_file_type']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

	return $result;
}


/**
 * Get File ID
 * 
 * @param int $track_id
 * @param int $album_id
 * @return int
 */
function cug_get_file_id($track_id, $album_id) {
	global $mysqli, $Tables;
	$result = 0;
	
	if($track_id > 0 && $album_id > 0) {
		$query = "SELECT file_id FROM {$Tables['track_album_rel']} WHERE album_id=".$mysqli->escape_str($album_id)." AND track_id=".$mysqli->escape_str($track_id);
		$r = $mysqli->query($query);
			if($r->num_rows) {
				$arr = $r->fetch_array();
				$result = $arr[0];
			}
			else 
				$result = -1;
	}
	
	return $result;
}


/**
 * Get FileID by TrackID
 * 
 * @param int $track_id
 * @return int
 */
function cug_get_fileid_by_trackid($track_id) {
	global $mysqli, $Tables;
	$result = 0;

	if($track_id > 0) {
		$query = "SELECT file_id FROM {$Tables['track_file_rel']} WHERE track_id=".$mysqli->escape_str($track_id);
		$r = $mysqli->query($query);
		
			if($r->num_rows) {
				$arr = $r->fetch_array();
				$result = $arr[0];
			}
			else
				$result = -1;
	}

	return $result;
}


/**
 * Get TrackID by FileID
 *
 * @param int $file_id
 * @return int
 */
function cug_get_trackid_by_fileid($file_id) {
	global $mysqli, $Tables;
	$result = 0;

	if($file_id > 0) {
		$query = "SELECT track_id FROM {$Tables['track_file_rel']} WHERE file_id=".$mysqli->escape_str($file_id);
		$r = $mysqli->query($query);

		if($r->num_rows) {
			$arr = $r->fetch_array();
			$result = $arr[0];
		}
		else
			$result = -1;
	}

	return $result;
}


/**
 * Get TrackID by ExternalID
 *
 * @param int $external_id
 * @return int
 */
function cug_get_trackid_by_external_id($external_id) {
	global $mysqli, $Tables;
	$track_id = 0;

	if($external_id) {
		$query = "SELECT id FROM {$Tables['track']} WHERE external_id='".$mysqli->escape_str($external_id)."'";
		$r = $mysqli->query($query);

		if($r->num_rows) {
			$arr = $r->fetch_array();
			$track_id = $arr[0];
		}
	}

	return $track_id;
}


/**
 * Get TrackID by ShenzhenID
 *
 * @param int $shenzhen_id
 * @return int
 */
function cug_get_trackid_by_shenzhen_id($shenzhen_id) {
    global $mysqli, $Tables;
    $track_id = 0;

    if($shenzhen_id) {
        $query = "SELECT id FROM {$Tables['track']} WHERE shenzhen_id=".$mysqli->escape_str($shenzhen_id);
        $r = $mysqli->query($query);

        if($r->num_rows) {
            $arr = $r->fetch_array();
            $track_id = $arr[0];
        }
    }

    return $track_id;
}

/**
 * Register Track-File Relation
 * 
 * @param int $track_id
 * @param int $file_id
 * @return int
 */
function cug_reg_track_file_rel($track_id, $file_id) {
	global $mysqli, $Tables;
	$result = 0;
	
	if($track_id > 0 && $file_id > 0) {
		//check for existing
		$r = $mysqli->query("SELECT id FROM {$Tables['track_file_rel']} WHERE track_id=".$mysqli->escape_str($track_id)." AND file_id=".$mysqli->escape_str($file_id));
		
		if(!$r->num_rows) {
			$query = "INSERT INTO {$Tables['track_file_rel']} (track_id, file_id) VALUES(".$mysqli->escape_str($track_id).", ".$mysqli->escape_str($file_id).")";
			if($mysqli->query($query)) {
				$result = $mysqli->insert_id;
			}
		}
		else {
			$arr = $r->fetch_array();
			$result = $arr['id'];
		}
	}
	
	return $result;
}


/**
 * Delete Track-File Relation
 * 
 * @param int $track_id
 * @param int $file_id
 * @return boolean
 */
function cug_del_track_file_rel($track_id, $file_id) {
	global $mysqli, $Tables;
	$result = false;
	
	if($track_id > 0 || $file_id > 0) {
		$query = "DELETE FROM {$Tables['track_file_rel']} WHERE ";
		$query .= ($track_id > 0) ? "track_id=".$mysqli->escape_str($track_id)." AND " : "";
		$query .= ($file_id > 0) ? "file_id=".$mysqli->escape_str($file_id)." AND " : "";
		
		$query = substr($query, 0, strlen($query) - 4);
		
			if($mysqli->query($query))
				$result = true;
	}
	
	return $result;
}



/**
 * Get Track File Type (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_file_type($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['track_file_type'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['track_file_type'], "title", "id=".$mysqli->escape_str($item));
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
 * Edit Existing Track File Type
 *
 * @param integer (ID of Existing Track File Type)
 * @param string (New Track File Type)
 * @return integer
 */
function cug_edit_file_type($id, $new_title)
{
	global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['track_file_type']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Edit File Info
 * 
 * @param int $file_id
 * @param object $obj (instance of cug__track_file class)
 * @param boolean $update_empty_fields (default=false)
 * @return boolean
 */
function cug_edit_file_info($file_id, $obj, $update_empty_fields=false) {
	global $mysqli, $Tables;
	$fields = "";
	$result = false;
	
	if($file_id > 0) {
		if(!empty($obj->f_track_time)) $fields .= "track_time=".$mysqli->escape_str($obj->f_track_time).",";
		
		if(isset($obj->f_track_type_id)) {
			if($update_empty_fields && empty($obj->f_track_type_id)) $fields .= "track_type_id=0,";
			elseif(!empty($obj->f_track_type_id)) $fields .= "track_type_id=".$mysqli->escape_str($obj->f_track_type_id).",";
		}
		//------------------------
		
		if(isset($obj->f_fp_status)) {
			if($update_empty_fields && empty($obj->f_fp_status)) $fields .= "fp_status=0,";
			elseif(!empty($obj->f_fp_status)) $fields .= "fp_status=".$mysqli->escape_str($obj->f_fp_status).",";
		}
		//------------------------
		
		if(isset($obj->f_wm_status)) {
			if($update_empty_fields && empty($obj->f_wm_status)) $fields .= "wm_status=0,";
			elseif(!empty($obj->f_wm_status)) $fields .= "wm_status=".$mysqli->escape_str($obj->f_wm_status).",";
		}
		//------------------------
		
		if(isset($obj->f_prev_path)) {
			if($update_empty_fields && empty($obj->f_prev_path)) $fields .= "f_prev_path=null,";
			elseif(!empty($obj->f_prev_path)) $fields .= "f_prev_path=".$mysqli->escape_str($obj->f_prev_path).",";
		}
		//------------------------
		
		if(isset($obj->f_format_id)) {
			if($update_empty_fields && empty($obj->f_format_id)) $fields .= "f_format_id=0,";
			elseif(!empty($obj->f_format_id)) $fields .= "f_format_id=".$mysqli->escape_str($obj->f_format_id).",";
		}
		//------------------------
		
		if(isset($obj->f_size)) {
			if($update_empty_fields && empty($obj->f_size)) $fields .= "f_size=0,";
			elseif(!empty($obj->f_size)) $fields .= "f_size=".$mysqli->escape_str($obj->f_size).",";
		}
		//------------------------
		
		if(isset($obj->f_brate)) {
			if($update_empty_fields && empty($obj->f_brate)) $fields .= "f_brate=0,";
			elseif(!empty($obj->f_brate)) $fields .= "f_brate=".$mysqli->escape_str($obj->f_brate).",";
		}
		//------------------------
		
		if(isset($obj->f_srate)) {
			if($update_empty_fields && empty($obj->f_srate)) $fields .= "f_srate=0,";
			elseif(!empty($obj->f_srate)) $fields .= "f_srate=".$mysqli->escape_str($obj->f_srate).",";
		}
		//------------------------
		
		if(!empty($obj->f_register_from)) $fields .= "f_register_from=".$mysqli->escape_str($obj->f_register_from).",";
		if(!empty($obj->f_register_date)) $fields .= "f_register_date='".$mysqli->escape_str($obj->f_register_date)."',";
		if(!empty($obj->f_register_ip)) $fields .= "f_register_ip='".$mysqli->escape_str($obj->f_register_ip)."',";
		if(!empty($obj->file_ext_name)) $fields .= "file_ext_name='".$mysqli->escape_str($obj->file_ext_name)."',";
		
		
		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['track_file']." SET ".$fields." WHERE id=".$mysqli->escape_str($file_id);
			
				if($mysqli->query($query))
					$result = true;
		}
	}

	return $result;
}


/**
 * Register New Track File Type
 *
 * @param string
 * @return integer
 */
function cug_reg_file_type($title)
{
	global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['track_file_type'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['track_file_type']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Get Track Masterright List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_track_masterright_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['track_masterright']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

	return $result;
}


/**
 * Get Track Masterright (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_track_masterright($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['track_masterright'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['track_masterright'], "title", "id=".$mysqli->escape_str($item));
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
 * Edit Existing Track Masterright
 *
 * @param integer (ID of Existing Track Masterright)
 * @param string (New Track Masterright)
 * @return integer
 */
function cug_edit_track_masterright($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['track_masterright']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New Track Masterright
 *
 * @param string
 * @return integer
 */
function cug_reg_track_masterright($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['track_masterright'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['track_masterright']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Get Track-Album Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'TRACK', 'ALBUM', 'UNIQID'; default is 'ALBUM')
 * @return array
 */
function cug_get_track_album_rel($item, $item_type="ALBUM")
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

			case 'TRACK':
				$field = "track_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'ALBUM':
				$field = "album_id";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['track_album_rel']." WHERE ".$field."=".$value);

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
 * Register Track-Album Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param string
 * @return integer
 */
function cug_reg_track_album_rel($track_id, $album_id, $disc_id, $disc_num, $track_num, $hidden=0, $file_id=0, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $album_id>0 && $disc_id>0 && $disc_num>=0 && $track_num>=0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['track_album_rel']." WHERE album_id=$album_id AND disc_id=$disc_id AND track_id=$track_id AND track_num=$track_num");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);


			$query = "INSERT INTO ".$Tables['track_album_rel']." VALUES(NULL, $track_id, $album_id, $disc_id, $disc_num, $track_num, $hidden, $file_id, '$uniq_id', NULL)";

			if($mysqli->query($query)) {
				//register Track-File relation
				if($file_id > 0)
					cug_reg_track_file_rel($track_id, $file_id);
				//----------------------------------
				
				return $mysqli->insert_id;
			}
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
 * Get Track-Composition Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'TRACK', 'COMPOSITION', 'UNIQID'; default is 'UNIQID')
 * @return array
 */
function cug_get_track_composition_rel($item, $item_type="UNIQID")
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

			case 'TRACK':
				$field = "track_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'COMPOSITION':
				$field = "comp_id";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['track_composition_rel']." WHERE ".$field."=".$value);

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
 * Register Track-Composition Relation
 *
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_track_composition_rel($track_id, $comp_id, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $comp_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['track_composition_rel']." WHERE track_id=$track_id AND comp_id=$comp_id");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);


			$query = "INSERT INTO ".$Tables['track_composition_rel']." VALUES(NULL, $track_id, $comp_id, '$uniq_id', NULL)";

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
 * Get Track-Client Relations
 *
 * @param object of Track-Client Relation Class
 * @param bool (if TRUE then all Client fields will be checked with 'OR' operator, if FALSE then - with 'AND' operator; Default is FALSE)
 * @param integer (default is 0)
 * @param integer (default is 100)
 * @return array
 */
function cug_get_track_client_rel($obj, $check_in_any_clients=false, $limit_from=0, $limit_quant=100)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	if($check_in_any_clients) 
		$operator = "OR";
	else
		$operator = "AND";
	//----------------------------

	if(!empty($obj->id)) {
		$fields .= "id=".$obj->id." AND";
	}

	if(!empty($obj->track_id)) {
		$fields .= " track_id=".$obj->track_id." AND";
	}

	if(!empty($obj->client_id)) {
		$fields .= " client_id=".$obj->client_id." $operator";
	}

	if(!empty($obj->masterright_id)) {
		$fields .= " masterright_id=".$obj->masterright_id." AND";
	}

	if(!empty($obj->licensor_id)) {
		$fields .= " licensor_id=".$obj->licensor_id." $operator";
	}
	
	if(!empty($obj->licensee_id)) {
		$fields .= " licensee_id=".$obj->licensee_id." $operator";
	}
	
	if(!empty($obj->copyright_p)) {
		$fields .= " copyright_p=".$obj->copyright_p." $operator";
	}
	
	if(!empty($obj->our_masterright_id)) {
		$fields .= " our_masterright_id=".$obj->our_masterright_id." AND";
	}

	if(isset($obj->isrc)) {
		if(!empty($obj->isrc))
			$fields .= " isrc='".$mysqli->escape_str($obj->isrc)."' AND";
		else 
			$fields .= " (isrc='' OR isrc=null) AND";
	}	

	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}



		if(!empty($fields)) {

			$fields = substr($fields, 0, strlen($fields)-3);
			$r = $mysqli->query("SELECT * FROM ".$Tables['track_client_rel']." WHERE ".$fields." LIMIT $limit_from, $limit_quant");

			if($r->num_rows) {
				while($arr = $r->fetch_array()) {
					$result[$index] = $arr;
					$index ++;
				}
			}
		}


return $result;
}



/**
 * Register Track-Client Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @param string
 * @return integer
 */
function cug_reg_track_client_rel($track_id, $client_id, $masterright_id, $licensor_id, $licensee_id, $copyright_p, $our_masterright_id, $isrc, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $client_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['track_client_rel']." WHERE track_id=$track_id AND client_id=$client_id");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);
			
			$values = "(NULL, $track_id, $client_id,";
			$values .= ($masterright_id>0) ? $masterright_id."," : "0,";
			$values .= ($licensor_id>0) ? $licensor_id."," : "0,";
			$values .= ($licensee_id>0) ? $licensee_id."," : "0,";
			$values .= ($copyright_p>0) ? $copyright_p."," : "0,";
			$values .= ($our_masterright_id>0) ? $our_masterright_id."," : "0,";
			$values .= (!empty($isrc)) ? "'".$mysqli->escape_str($isrc)."'," : "'',";
			$values .= "'".$uniq_id."', NULL)";
			
			$query = "INSERT INTO ".$Tables['track_client_rel']." VALUES".$values;

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
 * Get Track-Member Relations
 *
 * @param object of Track-Member Relation Class
 * @return array
 */
function cug_get_track_member_rel($obj)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj->id)) {
		$fields .= "id=".$obj->id." AND";
	}

	if(!empty($obj->track_id)) {
		$fields .= " track_id=".$obj->track_id." AND";
	}

	if(!empty($obj->member_id)) {
		$fields .= " member_id=".$obj->member_id." AND";
	}

	if(!empty($obj->role_id)) {
		$fields .= " role_id=".$obj->role_id." AND";
	}

	if(!empty($obj->isprimary)) {
		$fields .= " isprimary=".$obj->isprimary." AND";
	}

	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}



	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$r = $mysqli->query("SELECT * FROM ".$Tables['track_member_rel']." WHERE ".$fields);

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	}


return $result;
}



/**
 * Register Track-Member Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param int $hidden (1 - hidden relation;  0 - not hidden relation;  default: 0)
 * @param string
 * @return integer
 */
function cug_reg_track_member_rel($track_id, $member_id, $role_id, $isprimary, $hidden=0, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $member_id>0 && $role_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['track_member_rel']." WHERE track_id=$track_id AND member_id=$member_id AND role_id=$role_id");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);
				
			$values = "(NULL, $track_id, $member_id, $role_id,";
			$values .= ($isprimary>=0) ? $isprimary."," : "0,";
			$values .= ($hidden > 0) ? "1," : "NULL,";
			$values .= "'".$uniq_id."', NULL)";
				
			$query = "INSERT INTO ".$Tables['track_member_rel']." VALUES".$values;
            
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
 * Get Track Wm1
 *
 * @param object of Track-WM1 Class
 * @return array
 */
function cug_get_track_wm1($obj)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj->wm1_code)) {
		$fields .= "wm1_code=".$obj->wm1_code." AND";
	}
	
	if(!empty($obj->track_id)) {
		$fields .= " track_id=".$obj->track_id." AND";
	}
	
	if(!empty($obj->file_id)) {
		$fields .= " file_id=".$obj->file_id." AND";
	}

	if(!empty($obj->track_album_rel_id)) {
		$fields .= " track_album_rel_id=".$obj->track_album_rel_id." AND";
	}

	if(!empty($obj->client_id)) {
		$fields .= " client_id=".$obj->client_id." AND";
	}

	if(!empty($obj->licensee_id)) {
		$fields .= " licensee_id=".$obj->licensee_id." AND";
	}
	
	if(!empty($obj->wm_status)) {
		$fields .= " wm_status=".$obj->wm_status." AND";
	}	

	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}


	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$r = $mysqli->query("SELECT * FROM ".$Tables['track_wm1']." WHERE ".$fields);

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	}


return $result;
}


/**
 * Register Track's Wm1
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_track_wm1($track_id, $file_id, $track_album_rel_id, $client_id, $licensee_id, $wm1_code, $wm_status, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $file_id>0 && $track_album_rel_id>0 && $client_id>0 && $licensee_id>0 && $wm1_code>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT wm1_code FROM ".$Tables['track_wm1']." WHERE track_album_rel_id=$track_album_rel_id AND wm1_code=$wm1_code AND client_id=$client_id  AND licensee_id=$licensee_id");

		if($r) {
			if( !$r->num_rows ) {
	
				if(!$uniqid)
					$uniq_id = uniqid();
				else
					$uniq_id = $mysqli->escape_str($uniqid);
	
				$values = "($wm1_code, $track_id, $file_id, $track_album_rel_id, $client_id, $licensee_id,";
				$values .= ($wm_status>=0) ? $wm_status."," : "0,";
				$values .= "'".$uniq_id."', NULL)";
	
				$query = "INSERT INTO ".$Tables['track_wm1']." VALUES".$values;
	
				if($mysqli->query($query))
					return $mysqli->insert_id;
				else
					return -1;
			}
			else {
				$arr = $r->fetch_array();
				return $arr[0];
			}
		}
		else {
			return 0;
		}
	}
	else
		return 0;
}



/**
 * Register New Composition
 *
 * @param object of Composition Class
 * @return integer
 */
function cug_reg_composition($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->part)) {
			$fields .= "part,";
			$values .= "'".$mysqli->escape_str($obj->part)."',";
		}

		if(!empty($obj->genre_id)) {
			$fields .= "genre_id,";
			$values .= $mysqli->escape_str($obj->genre_id).",";
		}

		if(!empty($obj->music_period_id)) {
			$fields .= "music_period_id,";
			$values .= $mysqli->escape_str($obj->music_period_id).",";
		}
		
		if(!empty($obj->music_date)) {
			$fields .= "music_date,";
			$values .= "'".$mysqli->escape_str($obj->music_date)."',";
		}

		if(!empty($obj->tempo_id)) {
			$fields .= "tempo_id,";
			$values .= $mysqli->escape_str($obj->tempo_id).",";
		}
		
		if(!empty($obj->music_score_url)) {
			$fields .= "music_score_url,";
			$values .= "'".$mysqli->escape_str($obj->music_score_url)."',";
		}
		
		if(!empty($obj->iswc)) {
			$fields .= "iswc,";
			$values .= "'".$mysqli->escape_str($obj->iswc)."',";
		}
		
		if(!empty($obj->publisher_id)) {
			$fields .= "publisher_id,";
			$values .= $mysqli->escape_str($obj->publisher_id).",";
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


		$query = "INSERT INTO ".$Tables['composition'].$fields.$values;

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
 * Get Composition Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID' or 'ID', default is 'ID'
 * @return object of Composition Class
 */
function cug_get_composition($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item)) {

		if($item_type == "ID") {
			$query = "SELECT * FROM ".$Tables['composition']." WHERE id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$query = "SELECT * FROM ".$Tables['composition']." WHERE uniqid='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}

		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__composition();
					
				$obj->id				= $arr['id'];
				$obj->title				= $arr['title'];
				$obj->part				= $arr['part'];
				$obj->genre_id			= $arr['genre_id'];
				$obj->music_period_id	= $arr['music_period_id'];
				$obj->music_date		= $arr['music_date'];
				$obj->tempo_id			= $arr['tempo_id'];
				$obj->music_score_url	= $arr['music_score_url'];
				$obj->iswc				= $arr['iswc'];
				$obj->publisher_id		= $arr['publisher_id'];
				$obj->uniqid			= $arr['uniqid'];
				$obj->update_time		= $arr['update_time'];
					
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
 * Get Compositions
 *
 * @param object of Composition Class
 * @param integer (default is 1)
 * @param string ('ID', 'TITLE' default is 'TITLE')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @return array
 */
function cug_get_compositions($obj, $limit=1, $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj->id)) {
		$fields .= "id=".$obj->id." AND";
	}
	//------------------------
	if(!empty($obj->title)) {
		
		if(strlen($obj->title) == 1)
			$fields .= " title LIKE '".$mysqli->escape_str($obj->title)."%' AND";
		elseif(strlen($obj->title) > 1)
			$fields .= " title LIKE '%".$mysqli->escape_str($obj->title)."%' AND";
	}
	//------------------------
	if(!empty($obj->part)) {
		$fields .= " part LIKE '%".$mysqli->escape_str($obj->part)."%' AND";
	}
	//------------------------
	if(!empty($obj->genre_id)) {
		$fields .= " genre_id=".$mysqli->escape_str($obj->genre_id)." AND";
	}
	//------------------------
	if(!empty($obj->music_period_id)) {
		$fields .= " music_period_id=".$mysqli->escape_str($obj->music_period_id)." AND";
	}
	//------------------------
	if(!empty($obj->music_date)) {
		$fields .= " music_date='".$mysqli->escape_str($obj->music_date)."' AND";
	}
	//------------------------
	if(!empty($obj->tempo_id)) {
		$fields .= " tempo_id=".$mysqli->escape_str($obj->tempo_id)." AND";
	}
	//------------------------
	if(!empty($obj->music_score_url)) {
		$fields .= " music_score_url='".$mysqli->escape_str($obj->music_score_url)."' AND";
	}
	//------------------------
	if(!empty($obj->iswc)) {
		$fields .= " iswc='".$mysqli->escape_str($obj->iswc)."' AND";
	}
	//------------------------
	if(!empty($obj->publisher_id)) {
		$fields .= " publisher_id=".$mysqli->escape_str($obj->publisher_id)." AND";
	}
	//------------------------
	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}


	//----------------
	if($sort_by == "TITLE")
		$sort_field = "title";
	else
		$sort_field = "id";
	

	//-------------------
	if(!empty($fields)) {
		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['composition']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['composition'];
	}
	//--------------------
	
	$query .= " ORDER BY $sort_field $sort_type LIMIT $limit";
	$r = $mysqli->query($query);
	
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}	
	


return $result;
}


/**
 * Edit Existing Composition
 *
 * @param integer
 * @param object of Composition Class
 * @return bool
 */
function cug_edit_composition($composition_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $composition_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->part)) $fields .= "part='".$mysqli->escape_str($obj->part)."',";
		if(!empty($obj->genre_id)) $fields .= "genre_id=".$mysqli->escape_str($obj->genre_id).",";
		if(!empty($obj->music_period_id)) $fields .= "music_period_id=".$mysqli->escape_str($obj->music_period_id).",";
		if(!empty($obj->music_date)) $fields .= "music_date='".$mysqli->escape_str($obj->music_date)."',";
		if(!empty($obj->tempo_id)) $fields .= "tempo_id=".$mysqli->escape_str($obj->tempo_id).",";
		if(!empty($obj->music_score_url)) $fields .= "music_score_url='".$mysqli->escape_str($obj->music_score_url)."',";
		if(!empty($obj->iswc)) $fields .= "iswc='".$mysqli->escape_str($obj->iswc)."',";
		if(!empty($obj->publisher_id)) $fields .= "publisher_id=".$mysqli->escape_str($obj->publisher_id).",";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['composition']." SET ".$fields." WHERE id=".$composition_id;

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
 * Register New Arrangement
 *
 * @param object of Arrangement Class
 * @return integer
 */
function cug_reg_arrangement($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->publisher_id)) {
			$fields .= "publisher_id,";
			$values .= $mysqli->escape_str($obj->publisher_id).",";
		}

		if(!empty($obj->music_score_url)) {
			$fields .= "music_score_url,";
			$values .= "'".$mysqli->escape_str($obj->music_score_url)."',";
		}

		if(!empty($obj->creation_date)) {
			$fields .= "creation_date,";
			$values .= "'".$mysqli->escape_str($obj->creation_date)."',";
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


		$query = "INSERT INTO ".$Tables['arrangement'].$fields.$values;

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
 * Get Arrangement Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID' or 'ID', default is 'ID'
 * @return object of Arrangement Class
 */
function cug_get_arrangement($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item)) {

		if($item_type == "ID") {
			$query = "SELECT * FROM ".$Tables['arrangement']." WHERE id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$query = "SELECT * FROM ".$Tables['arrangement']." WHERE uniqid='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}

		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__arrangement();
					
				$obj->id				= $arr['id'];
				$obj->title				= $arr['title'];
				$obj->publisher_id		= $arr['publisher_id'];
				$obj->music_score_url	= $arr['music_score_url'];
				$obj->creation_date		= $arr['creation_date'];
				$obj->uniqid			= $arr['uniqid'];
				$obj->update_time		= $arr['update_time'];
					
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
 * Get Arrangement List
 *
 * @param object of Arrangement Class
 * @return array
 */
function cug_get_arrangement_list($obj)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj->id)) {
		$fields .= "id=".$obj->id." AND";
	}

	if(!empty($obj->title)) {
		$fields .= " title='".$mysqli->escape_str($obj->title)."' AND";
	}

	if(!empty($obj->publisher_id)) {
		$fields .= " publisher_id=".$mysqli->escape_str($obj->publisher_id)." AND";
	}

	if(!empty($obj->music_score_url)) {
		$fields .= " music_score_url='".$mysqli->escape_str($obj->music_score_url)."' AND";
	}

	if(!empty($obj->creation_date)) {
		$fields .= " creation_date='".$mysqli->escape_str($obj->creation_date)."' AND";
	}

	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}



	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$r = $mysqli->query("SELECT * FROM ".$Tables['arrangement']." WHERE ".$fields);

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	}


return $result;
}


/**
 * Edit Existing Arrangement
 *
 * @param integer
 * @param object of Arrangement Class
 * @return integer
 */
function cug_edit_arrangement($arrangement_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $arrangement_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->publisher_id)) $fields .= "publisher_id=".$mysqli->escape_str($obj->publisher_id).",";
		if(!empty($obj->music_score_url)) $fields .= "music_score_url='".$mysqli->escape_str($obj->music_score_url)."',";
		if(!empty($obj->creation_date)) $fields .= "creation_date='".$mysqli->escape_str($obj->creation_date)."',";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['arrangement']." SET ".$fields." WHERE id=".$arrangement_id;

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
 * Get Composition-Member Relations
 *
 * @param object of Composition-Member Relation Class
 * @return array
 */
function cug_get_composition_member_rel($obj)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj->id)) {
		$fields .= "id=".$obj->id." AND";
	}

	if(!empty($obj->comp_id)) {
		$fields .= " comp_id=".$obj->comp_id." AND";
	}

	if(!empty($obj->member_id)) {
		$fields .= " member_id=".$obj->member_id." AND";
	}

	if(!empty($obj->role_id)) {
		$fields .= " role_id=".$obj->role_id." AND";
	}

	if(!empty($obj->isprimary)) {
		$fields .= " isprimary=".$obj->isprimary." AND";
	}

	if(!empty($obj->uniqid)) {
		$fields .= " uniqid='".$obj->uniqid."' AND";
	}



	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$r = $mysqli->query("SELECT * FROM ".$Tables['composition_member_rel']." WHERE ".$fields);

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	}


	return $result;
}



/**
 * Register Composition-Member Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_composition_member_rel($comp_id, $member_id, $role_id, $isprimary, $uniqid="")
{
global $mysqli, $Tables;

	if($comp_id>0 && $member_id>0 && $role_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['composition_member_rel']." WHERE comp_id=$comp_id AND member_id=$member_id AND role_id=$role_id");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, $comp_id, $member_id, $role_id,";
			$values .= ($isprimary>=0) ? $isprimary."," : "0,";
			$values .= "'".$uniq_id."', NULL)";

			$query = "INSERT INTO ".$Tables['composition_member_rel']." VALUES".$values;

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
 * Get Track-Publisher Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'TRACK', 'PUBLISHER', 'UNIQID'; default is 'TRACK')
 * @param integer (default is 0)
 * @param integer (default is 100)
 * @return array
 */
function cug_get_track_publisher_rel($item, $item_type="TRACK", $limit_from=0, $limit_quant=100)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($item)) {

		switch($item_type) {

			case 'UNIQID':
				$field = "uniqid";
				$value = "'".$mysqli->escape_str($item)."'";
				break;

			case 'ID':
				$field = "id";
				$value = $mysqli->escape_str($item);
				break;

			case 'TRACK':
				$field = "track_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'PUBLISHER':
				$field = "publisher_id";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['track_publisher_rel']." WHERE ".$field."=".$value." LIMIT $limit_from, $limit_quant");

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
 * Register Track-Publisher Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_track_publisher_rel($track_id, $publisher_id, $uniqid="")
{
global $mysqli, $Tables;

	if($track_id>0 && $publisher_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['track_publisher_rel']." WHERE track_id=$track_id AND publisher_id=$publisher_id");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);


			$query = "INSERT INTO ".$Tables['track_publisher_rel']." VALUES(NULL, $track_id, $publisher_id, '$uniq_id', NULL)";

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
 * Upload Track's File
 *
 * @param integer
 * @param string
 * @param string
 * @param bool (default is TRUE)
 * @return array
 */
function cug_upload_track_file($obj_id, $audio_file, $subfolder, $delete_original_file=TRUE)
{
global $ftp_server, $ftp_user, $ftp_password, $ftp_port;
$result = FALSE;

	if(!empty($audio_file) && $obj_id > 0) {
		$file_info = cug_get_obj_file_info($obj_id, 'TRACK', $subfolder);
		$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);
		
			if($ftp_conn) {
				$ftp_conn->make_dir($file_info['dir_tree_u']);
				$ftp_conn->dir_change($file_info['dir_tree_u']);
				
					if($ftp_conn->send_file($file_info['basename'], $audio_file)) {
						$result = TRUE;
						
							if($delete_original_file) {
								@unlink($audio_file);
							}
					}

			}
	}

return FALSE;	
}


/**
 * Get Track list for TE
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
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param string ('TITLE', 'GENRE', 'ALBUM', 'EAN', 'TRACKNUM', 'TRACKTIME', 'DISCNUM' - default is 'TITLE')
 * @param string ('ASC' or 'DESC')
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @param integer
 * @return array
 */
function cug_get_track_list_te($register_from='0', $tag_status=0, $search_term="null", $is_track_title=0, $is_album_title=0, $is_genre_title=0, $is_ean_code=0, $is_catalogue_num=0, $is_artist=0, $is_composer=0, $user_id=0, $action_id=0, $object_id=0, $trash_status=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=30, $online=-1, $is_track_id=0, $duplicate_group_id=0)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;	

	
	// First Query
	$query  = "CALL get_track_list_te('$register_from',$tag_status,";
	$query .= ($search_term=="null" || !$search_term) ? "null," : ((strlen($search_term) > 3) ? "'%".$mysqli->escape_str($search_term)."%'," : "'".$mysqli->escape_str($search_term)."%',");
	$query .= "$is_track_title,$is_album_title,$is_genre_title,$is_ean_code,$is_catalogue_num,$is_artist,$is_composer,";
	$query .= "$user_id,$action_id,$object_id,$trash_status,'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant, $online,$is_track_id,$duplicate_group_id);";
	
	//echo $query."</br>";
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
 * Get Track list for TE (Advanced Search)
 *
 * @param string (default is '0')
 * @param integer (default is 0)
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 * @param string
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param string ('TITLE', 'GENRE', 'ALBUM', 'EAN', 'TRACKNUM', 'TRACKTIME', 'DISCNUM' - default is 'TITLE')
 * @param string ('ASC' or 'DESC')
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @return array
 */
function cug_get_track_adv_list_te($register_from='0', $tag_status=0, $track_title, $track_part, $track_ver, $track_artist, $track_composer, $track_genre, $album_title, $album_ean, $album_cat, $user_id=0, $action_id=0, $object_id=0, $trash_status=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=30)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;



	// First Query
	$query  = "CALL get_track_list_adv_te('$register_from',$tag_status,";
	
	if(strlen($track_title) > 3) { $query .= ($track_title=="null") ? "null," : "'%".$mysqli->escape_str($track_title)."%',"; }
	elseif($track_title != "") { $query .= "'".$mysqli->escape_str($track_title)."%',"; } else { $query .= "'',"; }
	
	if(strlen($track_part) > 3) { $query .= ($track_part=="null") ? "null," : "'%".$mysqli->escape_str($track_part)."%',"; }
	elseif($track_part != "") { $query .= "'".$mysqli->escape_str($track_part)."%',"; } else { $query .= "'',"; }
	
	if(strlen($track_ver) > 3) { $query .= ($track_ver=="null") ? "null," : "'%".$mysqli->escape_str($track_ver)."%',"; }
	elseif($track_ver != "") { $query .= "'".$mysqli->escape_str($track_ver)."%',"; } else { $query .= "'',"; }
	
	if(strlen($track_artist) > 3) { $query .= ($track_artist=="null") ? "null," : "'%".$mysqli->escape_str($track_artist)."%',"; }
	elseif($track_artist != "") { $query .= "'".$mysqli->escape_str($track_artist)."%',"; } else { $query .= "'',"; }
	
	if(strlen($track_composer) > 3) { $query .= ($track_composer=="null") ? "null," : "'%".$mysqli->escape_str($track_composer)."%',"; }
	elseif($track_composer != "") { $query .= "'".$mysqli->escape_str($track_composer)."%',"; } else { $query .= "'',"; }
	
	if(strlen($track_genre) > 3) { $query .= ($track_genre=="null") ? "null," : "'%".$mysqli->escape_str($track_genre)."%',"; }
	elseif($track_genre != "") { $query .= "'".$mysqli->escape_str($track_genre)."%',"; } else { $query .= "'',"; }
	
	if(strlen($album_title) > 3) { $query .= ($album_title=="null") ? "null," : "'%".$mysqli->escape_str($album_title)."%',"; }
	elseif($album_title != "") { $query .= "'".$mysqli->escape_str($album_title)."%',"; } else { $query .= "'',"; }
	
	if(strlen($album_ean) > 3) { $query .= ($album_ean=="null") ? "null," : "'%".$mysqli->escape_str($album_ean)."%',"; }
	elseif($album_ean != "") { $query .= "'".$mysqli->escape_str($album_ean)."%',"; } else { $query .= "'',"; }
	
	if(strlen($album_cat) > 3) { $query .= ($album_cat=="null") ? "null," : "'%".$mysqli->escape_str($album_cat)."%',"; }
	elseif($album_cat != "") { $query .= "'".$mysqli->escape_str($album_cat)."%',"; } else { $query .= "'',"; }
	
	$query .= "$user_id,$action_id,$object_id,$trash_status,'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant);";

	
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
 * Get Compositions of Track
 *
 * @param integer
 * @param integer
 * @param integer (default is 0)
 * @return array
 */
function cug_get_track_compositions($track_id)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($track_id)) {

		$r = $mysqli->query("CALL get_track_compositions($track_id)");

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
 * Edit Track
 *
 * @param array od track IDs
 * @param object of Track Class
 * @param bool (Update empty fields or not, default is false)
 * @return bool
 */
function cug_edit_track($track_ids_arr, $obj, $update_empty_fields=false)
{
global $mysqli, $Tables;
$fields = "";
$where = "";


	if(count($track_ids_arr) > 0 && !empty($track_ids_arr[0])) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";

		if(isset($obj->part)) {
			if($update_empty_fields && empty($obj->part)) $fields .= "part='',";
			elseif(!empty($obj->part)) $fields .= "part='".$mysqli->escape_str($obj->part)."',";
		}

		if(isset($obj->version)) {
			if($update_empty_fields && empty($obj->version)) $fields .= "version='',";
			elseif(!empty($obj->version)) $fields .= "version='".$mysqli->escape_str($obj->version)."',";
		}
		//------------------------
		
		if(isset($obj->arrangement_id)) {
			if($update_empty_fields && empty($obj->arrangement_id)) $fields .= "arrangement_id=0,";
			elseif($obj->arrangement_id != null && $obj->arrangement_id >= 0) $fields .= "arrangement_id=".$mysqli->escape_str($obj->arrangement_id).",";
		}
		//------------------------
		
		if(isset($obj->lang_id)) {
			if($update_empty_fields && empty($obj->lang_id)) $fields .= "lang_id=0,";
			elseif($obj->lang_id != null && $obj->lang_id >= 0) $fields .= "lang_id=".$mysqli->escape_str($obj->lang_id).",";
		}
		//------------------------
		
		if(isset($obj->tag_lang_id)) {
			if($update_empty_fields && empty($obj->tag_lang_id)) $fields .= "tag_lang_id=0,";
			elseif($obj->tag_lang_id != null && $obj->tag_lang_id >= 0) $fields .= "tag_lang_id=".$mysqli->escape_str($obj->tag_lang_id).",";
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
		
		if(isset($obj->has_file)) {
			if($update_empty_fields && empty($obj->has_file)) $fields .= "has_file=0,";
			elseif($obj->has_file != null && $obj->has_file >= 0) $fields .= "has_file=".$mysqli->escape_str($obj->has_file).",";
		}
		//------------------------
		
		if(isset($obj->lineup)) {
			if($update_empty_fields && empty($obj->lineup)) $fields .= "lineup='',";
			elseif(!empty($obj->lineup)) $fields .= "lineup='".$mysqli->escape_str($obj->lineup)."',";
		}
		//------------------------
		
		if(isset($obj->characters)) {
			if($update_empty_fields && empty($obj->characters)) $fields .= "characters='',";
			elseif(!empty($obj->characters)) $fields .= "characters='".$mysqli->escape_str($obj->characters)."',";
		}
		//------------------------
		
		if(isset($obj->from_movie)) {
			if($update_empty_fields && empty($obj->from_movie)) $fields .= "from_movie='',";
			elseif(!empty($obj->from_movie)) $fields .= "from_movie='".$mysqli->escape_str($obj->from_movie)."',";
		}
		//------------------------
		
		if(isset($obj->score_url)) {
			if($update_empty_fields && empty($obj->score_url)) $fields .= "score_url='',";
			elseif(!empty($obj->score_url)) $fields .= "score_url='".$mysqli->escape_str($obj->score_url)."',";
		}
		//------------------------
		
		if(isset($obj->colour_id)) {
			if($update_empty_fields && empty($obj->colour_id)) $fields .= "colour_id=0,";
			elseif($obj->colour_id != null && $obj->colour_id >= 0) $fields .= "colour_id=".$mysqli->escape_str($obj->colour_id).",";
		}
		//------------------------
		
		if(isset($obj->rec_country_id)) {
			if($update_empty_fields && empty($obj->rec_country_id)) $fields .= "rec_country_id=0,";
			elseif($obj->rec_country_id != null && $obj->rec_country_id >= 0) $fields .= "rec_country_id=".$mysqli->escape_str($obj->rec_country_id).",";
		}
		//------------------------
		
		if(isset($obj->rec_city)) {
			if($update_empty_fields && empty($obj->rec_city)) $fields .= "rec_city='',";
			elseif(!empty($obj->rec_city)) $fields .= "rec_city='".$mysqli->escape_str($obj->rec_city)."',";
		}
		//------------------------
		
		if(isset($obj->rec_date)) {
			if($update_empty_fields && empty($obj->rec_date)) $fields .= "rec_date=null,";
			elseif(!empty($obj->rec_date)) $fields .= "rec_date='".$mysqli->escape_str($obj->rec_date)."',";
		}
		//------------------------
		
		if(isset($obj->rec_company_id)) {
			if($update_empty_fields && empty($obj->rec_company_id)) $fields .= "rec_company_id=0,";
			elseif($obj->rec_company_id != null && $obj->rec_company_id >= 0) $fields .= "rec_company_id=".$mysqli->escape_str($obj->rec_company_id).",";
		}
		//------------------------
		
		if(isset($obj->copyright_c)) {
			if($update_empty_fields && empty($obj->copyright_c)) $fields .= "copyright_c=0,";
			elseif(!empty($obj->copyright_c)) $fields .= "copyright_c=".$mysqli->escape_str($obj->copyright_c).",";
		}
		//------------------------
		
		if(isset($obj->comments)) {
			if($update_empty_fields && empty($obj->comments)) $fields .= "comments='',";
			elseif(!empty($obj->comments)) $fields .= "comments='".$mysqli->escape_str($obj->comments)."',";
		}
		//------------------------
		
		if(!empty($obj->tag_status_id)) $fields .= "tag_status_id=".$mysqli->escape_str($obj->tag_status_id).",";
		if(!empty($obj->register_from)) $fields .= "register_from=".$mysqli->escape_str($obj->register_from).",";
		if(!empty($obj->online)) $fields .= "online=".$mysqli->escape_str($obj->online).",";
		//if(!empty($obj->register_date)) $fields .= "register_date='".$mysqli->escape_str($obj->register_date)."',";
		//if(!empty($obj->register_ip)) $fields .= "register_ip='".$mysqli->escape_str($obj->register_ip)."',";
		
		if($obj->trash_status != null && $obj->trash_status >= 0) $fields .= "trash_status=".$mysqli->escape_str($obj->trash_status).",";
		
		
		if(isset($obj->explicit_content)) {
			if($update_empty_fields && empty($obj->explicit_content)) $fields .= "explicit_content=null,";
			elseif(!empty($obj->explicit_content)) $fields .= "explicit_content=".$mysqli->escape_str($obj->explicit_content).",";
		}
		//------------------------
		
		if(isset($obj->unconfirmed)) {
		    if($update_empty_fields && empty($obj->unconfirmed)) $fields .= "unconfirmed=null,";
		    elseif(!empty($obj->unconfirmed)) $fields .= "unconfirmed=".$mysqli->escape_str($obj->unconfirmed).",";
		}
		//------------------------
		
		if($obj->prelistening_index != null && $obj->prelistening_index >= 0) $fields .= "prelistening_index=".$mysqli->escape_str($obj->prelistening_index).",";
		if($obj->prelistening_duration != null && $obj->prelistening_duration >= 0) $fields .= "prelistening_duration=".$mysqli->escape_str($obj->prelistening_duration).",";
		//------------------------
		
		if(!empty($obj->external_id)) $fields .= "external_id='".$mysqli->escape_str($obj->external_id)."',";
		if(!empty($obj->shenzhen_id)) $fields .= "shenzhen_id=".$mysqli->escape_str($obj->shenzhen_id).",";
		
		//if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		//if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";


		if(strlen($fields) > 0) {
			
			$fields = substr($fields, 0, strlen($fields)-1);
			
				for($i=0; $i<count($track_ids_arr); $i++) {
					if($track_ids_arr[$i] > 0) {
						$where .= "id=".$track_ids_arr[$i]." OR ";
					}	
				}
			
			if(strlen($where) > 0) {
				$where = substr($where, 0, strlen($where)-3);
				$query = "UPDATE ".$Tables['track']." SET ".$fields." WHERE $where";
					
					if($mysqli->query($query))
						return TRUE;
					else
						return FALSE;
			}
			else {
				return FALSE;
			}
				
		}
		else
			return FALSE;
	}
	else
		return FALSE;
}


/**
 * Edit Track Time
 *
 * @param int
 * @param int (time in milliseconds)
 * @return bool
 */
function cug_edit_track_time($file_id, $time)
{
global $mysqli, $Tables;

	if($file_id > 0 && $time > 0) {
		$query = "UPDATE ".$Tables['track_file']." SET track_time=".$mysqli->escape_str($time)." WHERE id=$file_id";

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
 * Edit ISRC of the Track
 * 
 * @param int $track_id
 * @param int $client_id
 * @param string $isrc
 * @return boolean
 */
function cug_edit_track_isrc($track_id, $client_id, $isrc) {
	global $mysqli, $Tables;
	$result = false;
	
	if($track_id>0 && $client_id>0) {
		$query = "UPDATE {$Tables['track_client_rel']} SET isrc='".$mysqli->escape_str($isrc)."' WHERE track_id=$track_id AND client_id=$client_id";
		
		if($mysqli->query($query))
			$result = true;
	}
	
	return $result;
}


/**
 * Change 'TrackNum' or/and 'Hidden'
 * 
 * @param int $album_id
 * @param int $disc_id
 * @param int $disc_num
 * @param int $track_id
 * @param int $track_num
 * @param int $hidden (default: -1)
 * @return int
 */
function cug_edit_track_num_hidden($album_id, $disc_id, $disc_num, $track_id, $track_num, $hidden=-1) {
	global $mysqli, $Tables;
	$result = 0;
	
	if($album_id >0 && ($disc_id > 0 || $disc_num > 0) && $track_id > 0 && ($track_num > 0 || $hidden >= 0)) {
		$where = "";
		
		$where .= "album_id=".$mysqli->escape_str($album_id);
		$where .= ($disc_id > 0) ? " AND disc_id=".$mysqli->escape_str($disc_id) : "";
		$where .= ($disc_num > 0) ? " AND disc_num=".$mysqli->escape_str($disc_num) : "";
		$where .= " AND track_id=".$mysqli->escape_str($track_id);
		
		$fields = "";
		$fields .= ($track_num > 0) ?"track_num=".$mysqli->escape_str($track_num)."," : "";
		$fields .= ($hidden >= 0) ? "hidden=".$mysqli->escape_str($hidden)."," : "";
		$fields = substr($fields, 0, strlen($fields)-1);
		
		$query = "UPDATE {$Tables['track_album_rel']} SET ".$fields." WHERE ".$where;
		
			if($mysqli->query($query))
				$result = 1;	
			else 
				$result = -1;	
	}

	
	return $result;
}


/**
 * Edit Track Genre
 *
 * @param int
 * @param int (id of Genre)
 * @return bool
 */
function cug_edit_track_genre($track_id, $genre_id)
{
global $mysqli, $Tables;

	if($track_id > 0) {

		$set = ($genre_id == 0) ? " genre_id=null AND fileunder_id=null " : " genre_id=$genre_id ";
		$query = "UPDATE ".$Tables['track']." SET $set WHERE id=$track_id";

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
 * Get list of all related object ids for tracks - used in cug_del_tracks() function
 * @param array
 * @param array
 */
function cug_get_track_related_object_ids($track_ids)
{
global $mysqli, $Tables;
$result = array();

	if(count($track_ids) > 0) {
		$index = 0;
		foreach($track_ids as $track_id) {
			
			$result[$index]['track_id'] = $track_id;
			
			//get members
			$obj = new cug__track_member_rel();
			$obj->track_id = $track_id;
			$members = cug_get_track_member_rel($obj);
			$subindex = 0;
			unset($obj);
			
				if(!isset($result[$index]['members']))
					$result[$index]['members'] = array();
			
				
				foreach($members as $member) {
					
					if(!in_array($member['member_id'], $result[$index]['members'])) {
						$result[$index]['members'][$subindex] = $member['member_id'];
						$subindex ++;
					}	
				}
				
			unset($members);	
			//-------------------------------	
			
			//get clients
			$obj = new cug__track_client_rel();
			$obj->track_id = $track_id;
			$clients = cug_get_track_client_rel($obj);
			$subindex = 0;
			unset($obj);
				
				if(!isset($result[$index]['clients']))
					$result[$index]['clients'] = array();
				if(!isset($result[$index]['track_client_rel']))
					$result[$index]['track_client_rel'] = array();
			
				foreach($clients as $client) {
						
					if(!in_array($client['client_id'], $result[$index]['clients'])) {
						$result[$index]['clients'][$subindex] = $client['client_id'];
						$result[$index]['track_client_rel'][$subindex] = $client['id'];
						$subindex ++;
					}
				}
			
			unset($clients);
			//-------------------------------	
					

			//get publishers
			$publishers = cug_get_track_publisher_rel($track_id, "TRACK");
			$publishers_subindex = 0;
			
				if(!isset($result[$index]['clients'])) {
					$result[$index]['clients'] = array();
				}
				if(!isset($result[$index]['publishers'])) {
					$result[$index]['publishers'] = array();
				}
			
				foreach($publishers as $publisher) {	
					
					if(!in_array($publisher['publisher_id'], $result[$index]['clients'])) {
						$result[$index]['clients'][$subindex] = $publisher['publisher_id'];
						$result[$index]['publishers'][$publishers_subindex] = $publisher['publisher_id'];
						$subindex ++;
						$publishers_subindex ++;
					}
				}
			
			unset($publishers);
			//-------------------------------			
			
			
			//get compositions 
			$compositions = cug_get_track_compositions($track_id);
			$subindex = 0;
				
				if(!isset($result[$index]['compositions']))
					$result[$index]['compositions'] = array();
			
				foreach($compositions as $composition) {
				
					if(!in_array($composition['comp_id'], $result[$index]['compositions'])) {
						$result[$index]['compositions'][$subindex] = $composition['comp_id'];
						$subindex ++;
					}
				}
				
			unset($compositions);
			//-------------------------------

			
			//get albums
			$albums = cug_get_track_album_rel($track_id, "TRACK");
			$subindex = 0;
			
				if(!isset($result[$index]['albums']))
					$result[$index]['albums'] = array();
				if(!isset($result[$index]['track_album_rel']))
					$result[$index]['track_album_rel'] = array();
				
				
				foreach($albums as $album) {								
					if(!in_array($album['album_id'], $result[$index]['albums'])) {
						$result[$index]['albums'][$subindex] = $album['album_id'];
						$result[$index]['track_album_rel'][$subindex] = $album['id'];
						$subindex ++;
					}
				}
			
			unset($albums);
			//-------------------------------

			
			//get file info
			$track = cug_get_track($track_id, "ID");
			$result[$index]['file_id'] = (!empty($track->f_id)) ? $track->f_id : "";
				
			unset($track);
			//-------------------------------			
						

			
		$index ++;
		}
	}

return $result;	
}	


/**
 * Delete Related Objects to Track
 * @param array
 * @param integer (Default is 0)
 * @return void
 */
function cug_del_track_related_objects($related_objects, $user_id=0)
{
global $mysqli, $Tables;
$result = array();

	if(count($related_objects) > 0) {
		foreach($related_objects as $related_object) {
			
			//compositions
			if(count($related_object['compositions']) > 0) {
				foreach($related_object['compositions'] as $composition_id) {
					cug_del_composition($composition_id);
				}	
			}
			
			
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
 * Delete Tracks
 * @param array $track_ids
 * @param bool $delete_related_objects Default: false
 * @param integer $user_id Default: 0
 * @param bool $delete_culinks Default: true
 * @param bool $delete_cache Default: true
 * @param bool $update_cache Default: true
 * @param bool $del_audio_file Default: true
 * @param void
 */
function cug_del_tracks($track_ids, $delete_related_objects=false, $user_id=0, $delete_culinks=true, $delete_cache=true, $update_cache=true, $del_audio_file=true)
{
global $mysqli, $Tables;

	if(count($track_ids) > 0) {
		
		//get related objects
		$related_objects = cug_get_track_related_object_ids($track_ids);
		
		//delete relations
		cug_del_track_album_rel($track_ids);
		cug_del_track_member_rel($track_ids, array(), true);
		cug_del_track_publisher_rel($track_ids);
		cug_del_track_client_rel($track_ids);
		cug_del_track_composition_rel($track_ids);
		

		foreach($related_objects as $related_object) {
			
			// delete from 'track_wm1_list'
			if(isset($related_object['track_album_rel'])) {
				foreach($related_object['track_album_rel'] as $track_album_rel_id) {
					$query = "DELETE FROM ".$Tables['track_wm1']." WHERE track_album_rel_id=$track_album_rel_id";
					$mysqli->query($query);
				}
			}			
			
			// delete file
			if(isset($related_object['file_id']) && $related_object['file_id'] > 0) {
				cug_del_track_file_rel($related_object['track_id'], $related_object['file_id']);
				$mysqli->query("DELETE FROM ".$Tables['track_file']." WHERE id=".$related_object['file_id']);
				
				if($del_audio_file) {
				    cug_delete_track_file($related_object['file_id']);
				}
			}
			
			// delete footprints - disabled, because we don't have footprints in our DB anymore
			/*
			if(isset($related_object['file_id'])) {
				$file_id = $related_object['file_id'];
				$mysqli->query("DELETE FROM ".$Tables['footprint']." WHERE file_id=$file_id");
					
				for($key=0; $key<12; $key++) {
					for($tone=0; $tone<2; $tone++) {
						$fp_det_name = "fp_det_key_".$key."_tone_".$tone;
						$fp_nm_name  = "fp_nm_key_".$key."_tone_".$tone;
				
						$mysqli->query("DELETE FROM $fp_det_name WHERE file_id=$file_id");
						$mysqli->query("DELETE FROM $fp_nm_name WHERE file_id=$file_id");
					}
				}	
			}
			*/
			
			//delete track-release
			cug_del_track_release($related_object['track_id']);
			
			//delete statistics
			$r = $mysqli->query("SELECT id FROM ".$Tables['track_stat']." WHERE track_id=".$related_object['track_id']);
				if($r->num_rows) {
					while($arr = $r->fetch_array()) {
						$mysqli->query("DELETE FROM ".$Tables['artist_stat']." WHERE stat_track_id=".$arr['id']);
					}
				}							
			$mysqli->query("DELETE FROM ".$Tables['track_stat']." WHERE track_id=".$related_object['track_id']);
			
			
			//delete track from 'log_app_tracks' table
			$mysqli->query("DELETE FROM ".$Tables['log_app_tracks']." WHERE track_id=".$related_object['track_id']);
			
			//delete track from 'portal_playlist_track_rel'
			$mysqli->query("DELETE FROM ".$Tables['portal_playlist_track']." WHERE track_id=".$related_object['track_id']);
			
			//delete track from 'chart_track_list'
			$mysqli->query("DELETE FROM ".$Tables['chart_tracks']." WHERE track_id=".$related_object['track_id']);
				
			//delete track from 'chart_track_list_alt'
			$mysqli->query("DELETE FROM ".$Tables['chart_tracks_alt']." WHERE track_id=".$related_object['track_id']);
			
			//delete track from 'user_fav_track_list'
			$mysqli->query("DELETE FROM ".$Tables['user_fav_track']." WHERE track_id=".$related_object['track_id']);
			
			//delete track from 'user_listened_track_list'
			$mysqli->query("DELETE FROM ".$Tables['listened_track']." WHERE track_id=".$related_object['track_id']);
			

			
			//delete culinks
			if($delete_culinks) {
				cug_cache_del_track_culink($related_object['track_id']);
			}
			
			
			//delete cache
			if($delete_cache) {
				//delete from 'cache_tracks' table
				cug_cache_del_track($related_object['track_id']);
			}
			
			
			//cache tables
		    //update 'cache_albums' table
		    foreach($related_object['albums'] as $album_id) {
		        cug_cache_del_album($album_id);
		        
		        if($update_cache) {
		         cug_cache_add_album($album_id);
		        }
		    }
		    	
		    //update 'cache_members' table
		    foreach($related_object['members'] as $member_id) {
		        cug_cache_del_member($member_id);
		        
		        if($update_cache) {
		         cug_cache_add_member($member_id);
		        }
		    }			    
			
			
			//delete track
			$mysqli->query("DELETE FROM ".$Tables['track']." WHERE id=".$related_object['track_id']);
			
			//register log
			$obj = new cug__log_te();
			$obj->action_id = 4; //Delete
			$obj->subaction_id = 0;
			$obj->object_id = 1; //Audio Track
			$obj->object_item_id = $related_object['track_id'];
			$obj->subitem_id = (isset($related_object['file_id'])) ? $related_object['file_id'] : 0;
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
			cug_del_track_related_objects($related_objects, $user_id);
				
	}//end of if(count($track_ids) > 0)
}	


/**
 * Delete Composition-Member Relations (Only Roles: Composer(13), Lyricis(33))
 *
 * @param integer
 * @return bool
 */
function cug_del_comp_member_rel($comp_id)
{
global $mysqli, $Tables;

	if($comp_id > 0){
		$query = "DELETE FROM ".$Tables['composition_member_rel']." WHERE comp_id=$comp_id";

		if($mysqli->query($query)) {
			$tracks_arr = cug_get_track_composition_rel($comp_id, "COMPOSITION");

			for($i=0; $i<count($tracks_arr); $i++){
				$query = "DELETE FROM ".$Tables['track_member_rel']." WHERE track_id=".$tracks_arr[$i]['track_id']." AND (role_id=13 OR role_id=33)";
				$mysqli->query($query);
				return true;
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
 * Delete Track-Publisher Relation
 *
 * @param array
 * @param array (optional)
 * @return bool
 */
function cug_del_track_publisher_rel($track_ids, $publishers = array())
{
global $mysqli, $Tables;
$where_id = "(";
$where_publisher = "(";

	if(count($track_ids) > 0) {
		//-------------
		foreach($track_ids as $track_id) {
			$where_id .= ($track_id > 0) ? "track_id=$track_id OR " : "";
		}
		//-------------
		if(count($publishers)) {
			foreach($publishers as $publisher) {
				$where_publisher .= (!empty($publisher['publisher_id']) && $publisher['publisher_id'] > 0) ? "publisher_id=".$publisher['publisher_id']." OR " : "";
			}
		}
		//-------------

		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";

			if(strlen($where_publisher) > 1) {
				$where_publisher = substr($where_publisher, 0, strlen($where_publisher)-3).")";
			}
			else {
				$where_publisher = "";
			}

			$query = "DELETE FROM ".$Tables['track_publisher_rel']." WHERE $where_id";
			$query .= (strlen($where_publisher)) ? " AND $where_publisher" : "";
				
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
 * Delete Track-Member Relations (Except Roles: Composer(13), Lyricis(33))
 *
 * @param array of track IDs
 * @param array of member ids and their role ids, if this array is empty then all linked members will be deleted execpt of Composer and Lyricis
 * @param bool $delete_all_roles (if true then Composers and Lyricists also will be deleted)
 * @return bool
 */
function cug_del_track_member_rel($track_ids, $members_roles = array(), $delete_all_roles=false)
{
global $mysqli, $Tables;
$where_id = "(";
$where_member = "(";
$where_role = "(";

	if(count($track_ids) > 0) {
		//-------------
		foreach($track_ids as $track_id) {
			$where_id .= ($track_id > 0) ? "track_id=$track_id OR " : "";
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

			$query = "DELETE FROM ".$Tables['track_member_rel']." WHERE $where_id";
			$query .= (strlen($where_member)) ? " AND $where_member" : "";
			$query .= (strlen($where_role)) ? " AND $where_role" : "";
			$query .= (!$delete_all_roles) ? " AND role_id<>13 AND role_id<>33" : "";

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
 * Delete Track-Album Relations
 *
 * @param array
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @return bool
 */
function cug_del_track_album_rel($track_ids, $album_id=0, $disc_id=0)
{
global $mysqli, $Tables;


	if(count($track_ids)) {
		$where = "(";
		
		foreach($track_ids as $track_id) {
			$where .= ($track_id > 0) ? "track_id=$track_id OR " : "";
		}
		//--------------------
		
		if(strlen($where) > 1) {
			$where = substr($where, 0, strlen($where) - 4);
			$where .= ") AND";
			
				if($album_id > 0) $where .= " album_id=$album_id AND";
				if($disc_id > 0)  $where .= " disc_id=$disc_id AND";
					
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
	else {
		return false;
	}
}


/**
 * Delete Track-Client Relations
 *
 * @param array
 * @param array
 * @return bool
 */
function cug_del_track_client_rel($track_ids, $clients = array())
{
global $mysqli, $Tables;
$where_id 				= "(";
$where_client 			= "(";
$where_masterright 		= "(";
$where_copyright_p 		= "(";
$where_licensor 		= "(";
$where_licensee 		= "(";
$where_our_masterright 	= "(";
$where_isrc 			= "(";

	if(count($track_ids) > 0) {
		//-------------
		foreach($track_ids as $track_id) {
			$where_id .= ($track_id > 0) ? "track_id=$track_id OR " : "";
		}
		//-------------
		if(count($clients)) {
			foreach($clients as $client) {
				$where_client .= (isset($client['client_id']) && $client['client_id'] >= 0) ? "client_id=".$client['client_id']." OR " : "";
				$where_masterright .= (isset($client['masterright_id']) && $client['masterright_id'] >= 0) ? "masterright_id=".$client['masterright_id']." OR " : "";
				$where_copyright_p .= (isset($client['copyright_p_id']) && $client['copyright_p_id'] >= 0) ? "copyright_p=".$client['copyright_p_id']." OR " : "";
				$where_licensor .= (isset($client['licensor_id']) && $client['licensor_id'] >= 0) ? "licensor_id=".$client['licensor_id']." OR " : "";
				$where_licensee .= (isset($client['licensee_id']) && $client['licensee_id'] >= 0) ? "licensee_id=".$client['licensee_id']." OR " : "";
				$where_our_masterright .= (isset($client['our_masterright_id']) && $client['our_masterright_id'] >= 0) ? "our_masterright_id=".$client['our_masterright_id']." OR " : "";
				$where_isrc .= (!empty($client['isrc'])) ? "isrc='".$mysqli->escape_str($client['isrc'])."' OR " : "";
			}
		}
		//-------------

		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";
				
			if(strlen($where_client) > 1) {
				$where_client = substr($where_client, 0, strlen($where_client)-3).")";
			}
			else {
				$where_client = "";
			}
			//-----------
			if(strlen($where_masterright) > 1) {
				$where_masterright = substr($where_masterright, 0, strlen($where_masterright)-3).")";
			}
			else {
				$where_masterright = "";
			}
			//------------
			if(strlen($where_copyright_p) > 1) {
				$where_copyright_p = substr($where_copyright_p, 0, strlen($where_copyright_p)-3).")";
			}
			else {
				$where_copyright_p = "";
			}
			//------------
			if(strlen($where_licensor) > 1) {
				$where_licensor = substr($where_licensor, 0, strlen($where_licensor)-3).")";
			}
			else {
				$where_licensor = "";
			}
			//------------
			if(strlen($where_licensee) > 1) {
				$where_licensee = substr($where_licensee, 0, strlen($where_licensee)-3).")";
			}
			else {
				$where_licensee = "";
			}
			//------------
			if(strlen($where_our_masterright) > 1) {
				$where_our_masterright = substr($where_our_masterright, 0, strlen($where_our_masterright)-3).")";
			}
			else {
				$where_our_masterright = "";
			}
			//------------
			if(strlen($where_isrc) > 1) {
				$where_isrc = substr($where_isrc, 0, strlen($where_isrc)-3).")";
			}
			else {
				$where_isrc = "";
			}

			$query = "DELETE FROM ".$Tables['track_client_rel']." WHERE $where_id";
			$query .= (strlen($where_client)) ? " AND $where_client" : "";
			$query .= (strlen($where_masterright)) ? " AND $where_masterright" : "";
			$query .= (strlen($where_copyright_p)) ? " AND $where_copyright_p" : "";
			$query .= (strlen($where_licensor)) ? " AND $where_licensor" : "";
			$query .= (strlen($where_licensee)) ? " AND $where_licensee" : "";
			$query .= (strlen($where_our_masterright)) ? " AND $where_our_masterright" : "";
			$query .= (strlen($where_isrc)) ? " AND $where_isrc" : "";

				
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
 * Delete Track-Composition Relation
 *
 * @param array
 * @param array
 * @return bool
 */
function cug_del_track_composition_rel($track_ids, $compositions = array())
{
global $mysqli, $Tables;
$where_id = "(";
$where_composition = "(";

	if(count($track_ids) > 0) {
		//-------------
		foreach($track_ids as $track_id) {
			$where_id .= ($track_id > 0) ? "track_id=$track_id OR " : "";
		}
		//-------------
		if(count($compositions)) {
			foreach($compositions as $composition) {
				$where_composition .= (!empty($composition['comp_id']) && $composition['comp_id'] > 0) ? "comp_id=".$composition['comp_id']." OR " : "";
			}
		}
		//-------------

		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";

			if(strlen($where_composition) > 1) {
				$where_composition = substr($where_composition, 0, strlen($where_composition)-3).")";
			}
			else {
				$where_composition = "";
			}

			$query = "DELETE FROM ".$Tables['track_composition_rel']." WHERE $where_id";
			$query .= (strlen($where_composition)) ? " AND $where_composition" : "";

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
 * Delete Composition - Just only if it is not related to any track
 *
 * @param int
 * @return int
 */
function cug_del_composition($composition_id)
{
global $mysqli, $Tables;
		
	if($composition_id > 0) {
		$track_composition_rel = cug_get_track_composition_rel($composition_id, "COMPOSITION");
			
			if(count($track_composition_rel) == 0) { //if composition is not related to some track
				
				//delete Composition-Member Relations
				cug_del_comp_member_rel($composition_id);
				
				//delete Composition
				$query = "DELETE FROM ".$Tables['composition']." WHERE id=$composition_id";
					if($mysqli->query($query)) {
						return 1;
					}
					else {
						return -2; //error
					}
				//-----------------------	
			}
			else {
				return 0; //can't delete, because composition is related to some tracks
			}
	}
	else {
		return -1; // can't delete, no composition id
	}
		
}


/**
 * Update Tracks
 *
 * Used in API
 *
 * @param int $client_id
 * @param string $user_ip
 * @param string $tracks_data
 * @return number|array
 */
function cug_update_tracks($client_id, $user_ip, $tracks_data) {
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

	if(!empty($tracks_data)) {
		$arr = json_decode($tracks_data, true);
		
		//check JSON Data on errors
		$error_code = cug_json_last_error();
		if($error_code < 0) {
			return $error_code;
		}
		//-------------------------
		
		$total_tracks = !empty($arr['tracks']) ? count($arr['tracks']) : 0;
		
		if($total_tracks > 0) {
			//check mandatory fields
			$mandatory_fields = true;
			foreach($arr['tracks'] as $track) {
				if(empty($track['track_ext_id']) && empty($track['track_id'])) {
					$mandatory_fields = false;
					break;
				}
			}
				
			if(!$mandatory_fields) {
				return $ERRORS['NOT_ENOUGH_TRACK_FIELDS'];
			}
			//------------------------------
			
			//update tracks
			$result['total_tracks'] = 		$total_tracks;
			$result['tracks_success'] 		= 0;
			$result['tracks_failed'] 		= 0;
			$result['track_ids'] 			= "";
			
			foreach($arr['tracks'] as $track) {
				$status = 0; //no action
				
				//check track id
				$track_id_to_be_checked = !empty($track['track_id']) ? trim($track['track_id']) : 0;
				$track_ext_id_to_be_checked = !empty($track['track_ext_id']) ? trim($track['track_ext_id']) : 0;
					
				$track_id = cug_check_track_id($track_id_to_be_checked, $track_ext_id_to_be_checked, $client_id);
				$incoming_track_id = ($track_id_to_be_checked > 0) ? $track_id_to_be_checked : $track_ext_id_to_be_checked;
				//---------------------------
				
				
				if($track_id > 0) {
					$fields = 0;
					
					$track_obj = new cug__track();
					if(isset($track['unconfirmed'])) 		{ $track_obj->unconfirmed 		= trim($track['unconfirmed']); $fields ++; }
					if(isset($track['track_title'])) 		{ $track_obj->title 			= trim($track['track_title']); $fields ++; }
					if(isset($track['track_part'])) 		{ $track_obj->part 				= trim($track['track_part']); $fields ++; }
					if(isset($track['track_version'])) 		{ $track_obj->version 			= trim($track['track_version']); $fields ++; }
					if(isset($track['lang_id'])) 			{ $track_obj->lang_id 			= trim($track['lang_id']); $fields ++; }
					if(isset($track['genre_id'])) 			{ $track_obj->genre_id 			= trim($track['genre_id']); $fields ++; }
					if(isset($track['fileunder_id'])) 		{ $track_obj->fileunder_id 		= trim($track['fileunder_id']); $fields ++; }
					if(isset($track['rec_date'])) 			{ $track_obj->rec_date 			= cug_parse_date_for_mysql(trim($track['rec_date'])); $fields ++; }				
					
					if(isset($track['copyright_c'])) {
						$copyright_c = !empty($track['copyright_c']) ? trim($track['copyright_c']) : "";
						$copyright_c_id = cug_get_client_id_by_title($copyright_c, $insert_new_client=true);
						$track_obj->copyright_c = $copyright_c_id;
						$fields ++;
					}
					
					
					if($fields > 0) {
						$track_was_updadted = cug_edit_track(array($track_id), $track_obj, true);
					}
					
					//update TRACK-ALBUM relations
					if(!empty($track['track_num']) || isset($track['hidden'])) {
						$tracknum_or_hidden_was_updated = false;
						
						if((!empty($track['album_id']) || !empty($track['album_ext_id'])) && !empty($track['disc_num'])) {							
							//check albums id
							$album_id_to_be_checked = !empty($track['album_id']) ? trim($track['album_id']) : 0;
							$album_ext_id_to_be_checked = !empty($track['album_ext_id']) ? trim($track['album_ext_id']) : 0;
							
							$album_id = cug_check_album_id($album_id_to_be_checked, $album_ext_id_to_be_checked, $client_id);
							//---------------------------
							
							$disc_num = trim($track['disc_num']);
							
							if($album_id > 0) {
								//check album_id and disc_num
								$r = $mysqli->query("SELECT id FROM {$Tables['track_album_rel']} WHERE album_id=".$mysqli->escape_str($album_id)." AND disc_num=".$mysqli->escape_str($disc_num));
								if($r->num_rows) {
									$track_num = !empty($track['track_num']) ? $track['track_num'] : 0;
									$hidden = ($track['hidden'] >= 0) ? $track['hidden'] : -1;
									
									if(cug_edit_track_num_hidden($album_id, $disc_id=0, $disc_num, $track_id, $track_num, $hidden) > 0)
										$tracknum_or_hidden_was_updated = true;
								}
								else
									$tracknum_or_hidden_was_updated = false;
							}
							else 
								$tracknum_or_hidden_was_updated = false;
						}
					}
					//---------------------------------
			
					//update TRACK-FILE
					if(!empty($track['file'])) {
						$track_file_was_updated = false;
						
						//get file id
						$file_id = 0;
						
						if(!empty($track['file']['file_id'])) {
							$file_id = trim($track['file']['file_id']);
							
							//check file id
							$r = $mysqli->query("SELECT id FROM {$Tables['track_file']} WHERE id=".$mysqli->escape_str($file_id));
							if(!$r->num_rows) {
								$file_id = 0;
							}
						}
						else { //try to detect 'file_id' by 'album_id' or 'album_ext_id'						
							if(!empty($track['album_id']) || !empty($track['album_ext_id'])) {
								//check albums id
								$album_id_to_be_checked = !empty($track['album_id']) ? trim($track['album_id']) : 0;
								$album_ext_id_to_be_checked = !empty($track['album_ext_id']) ? trim($track['album_ext_id']) : 0;
									
								$album_id = cug_check_album_id($album_id_to_be_checked, $album_ext_id_to_be_checked, $client_id);
								//---------------------------
	
								if($album_id > 0)
									$file_id = cug_get_file_id($track_id, $album_id);	
							}
						}
						//----------------------
						
						if($file_id > 0) {
							$fields = 0;
							
							$file_obj = new cug__track_file();
							if(isset($track['file']['file_ext_name'])) 		{ $file_obj->file_ext_name 		= trim($track['file']['file_ext_name']); $fields ++; }
							if(isset($track['file']['file_type_id'])) 		{ $file_obj->f_track_type_id 	= trim($track['file']['file_type_id']); $fields ++; }
							if(isset($track['file']['file_format_id'])) 	{ $file_obj->f_format_id 		= trim($track['file']['file_format_id']); $fields ++; }
							if(isset($track['file']['time'])) 				{ $file_obj->f_track_time 		= trim($track['file']['time'])*1000; $fields ++; }
							if(isset($track['file']['file_size'])) 			{ $file_obj->f_size 			= trim($track['file']['file_size']); $fields ++; }
							if(isset($track['file']['file_brate'])) 		{ $file_obj->f_brate 			= trim($track['file']['file_brate']); $fields ++; }
							if(isset($track['file']['file_srate'])) 		{ $file_obj->f_srate 			= trim($track['file']['file_srate']); $fields ++; }
							
							if($fields > 0) {
								$track_file_was_updated = cug_edit_file_info($file_id, $file_obj, true);
							}
						}		
					}
					//-----------------------
					
					
					//update TRACK-MEMBER relations
					if(!empty($track['members']) && count($track['members']) > 0) {
						$updated_track_member_rel = 0;
						cug_del_track_member_rel(array($track_id), array(), $delete_all_roles=true); //delete all existing relations
						
						if(count($track['members']) == 1 && count($track['members'][0]) == 0) {
							$updated_track_member_rel = 1;
						}
						else {
							foreach($track['members'] as $member) {
								$member_id_to_be_checked = !empty($member['member_id']) ? trim($member['member_id']) : 0;
								$member_ext_id_to_be_checked = !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
								$member_role_id = !empty($member['role_id']) ? trim($member['role_id']) : 0;
								$isprimary = !empty($member['isprimary']) ? trim($member['isprimary']) : 0;
								
								$member_id = cug_check_member_id($member_id_to_be_checked, $member_ext_id_to_be_checked, $client_id);
								
									if($member_id > 0) {
										if(cug_reg_track_member_rel($track_id, $member_id, $member_role_id, $isprimary) > 0)
											$updated_track_member_rel += 1;	
									}
							}
						}
							
						
						//define status
						if($updated_track_member_rel == count($track['members']))
							$track_member_rel_status = true;
						else
							$track_member_rel_status = false;
					}
					//---------------------
					
					
					//update TRACK-PUBLISHER relations
					if(!empty($track['publishers']) && count($track['publishers']) > 0) {
						$updated_track_publisher_rel = 0;
						cug_del_track_publisher_rel(array($track_id)); //delete all existing relations

						if(count($track['publishers']) == 1 && count($track['publishers'][0]) == 0) {
							$updated_track_publisher_rel = 1;
						}
						else {						
							foreach($track['publishers'] as $publisher) {
								if(!empty($publisher['publisher_name'])) {
									$publisher_id = cug_get_client_id_by_title(trim($publisher['publisher_name']), $insert_new_client=true);
									if($publisher_id > 0) {
										if(cug_reg_track_publisher_rel($track_id, $publisher_id) > 0)
											$updated_track_publisher_rel += 1;
									}
								}
							}
						}
					
						//define status
						if($updated_track_publisher_rel == count($track['publishers']))
							$track_publisher_rel_status = true;
						else
							$track_publisher_rel_status = false;
					}
					//-----------------------
					
					//ISRC
					if(isset($track['isrc'])) {
						$isrc = trim($track['isrc']);
						
						//try to add if relation not exists yet
						cug_reg_track_client_rel($track_id, $client_id, $masterright_id=0, $licensor_id=0, $licensee_id=0, $copyright_p=0, $our_masterright_id=0, $isrc);
						
						//check existing relation
						$track_client_rel_obj = new cug__track_client_rel();
						$track_client_rel_obj->track_id = $track_id;
						$track_client_rel_obj->client_id = $client_id;
						$track_client_rel_obj->isrc = $isrc;
						
						$rel_arr = cug_get_track_client_rel($track_client_rel_obj);
						//-------------------------
						
						if(count($rel_arr) == 0) {
							if(cug_edit_track_isrc($track_id, $client_id, trim($track['isrc'])))
								$track_isrc_status = true;
							else 
								$track_isrc_status = false;
						}
					}
					//------------------------
					
					//define final status
					//----------------------
					if(isset($track_was_updadted)) {
						if($track_was_updadted)
							$status = 1; //OK
						else 
							$status = -4; //Internal Error
					}
					//----------------------
					if($status >= 0) {
						if(isset($track_member_rel_status)) {
							if($track_member_rel_status)
								$status = 1; //OK
							else 
								$status = -2; //Some Members were not updated
						}
					}
					//----------------------
					if($status >= 0) {
						if(isset($track_publisher_rel_status)) {
							if($track_publisher_rel_status)
								$status = 1; //OK
							else
								$status = -3; //Some Publishers were not updated
						}
					}
					//----------------------
					if($status >= 0) {
						if(isset($tracknum_or_hidden_was_updated)) {
							if($tracknum_or_hidden_was_updated)
								$status = 1; //OK
							else
								$status = -5; //'track_num' or 'hidden' was not updated
						}
					}
					//----------------------
					if($status >= 0) {
						if(isset($track_file_was_updated)) {
							if($track_file_was_updated)
								$status = 1; //OK
							else
								$status = -6; //'file' fields were not updated
						}
					}
					//----------------------
					if($status >= 0) {
						if(isset($track_isrc_status)) {
							if($track_isrc_status)
								$status = 1; //OK
							else
								$status = -7; //'isrc' was not updated
						}
					}				
					
				}
				else {
					$status = -1; //unknown track id
				}
				
					//check status
					if($status == 1) {
						$result['tracks_success'] += 1;
					}
					else {
						$result['tracks_failed'] += 1;
					}
					//-------------------
				
				$result['track_ids'] .= $incoming_track_id.":".$status.",";
			}
			
			$result['track_ids'] = !empty($result['track_ids']) ? substr($result['track_ids'], 0, strlen($result['track_ids']) - 1) : "";
			return $result;
		}
		else
			return $ERRORS['INVALID_TRACK_DATA_STRUCTURE'];
	}
	else 
		echo $ERRORS['NO_TRACK_DATA'];		
}

/**
 * Register Track-Release Relation
 *
 * @param int $track_id
 * @param int $track_album_rel_id
 * @param string $country_allowed
 * @param string $digital_release_date
 * @param int $price_code_id
 * @return boolean
 */
function cug_reg_track_release($track_id, $track_album_rel_id, $country_allowed, $digital_release_date, $price_code_id) {
    global $mysqli, $Tables;
    $result = false;

    if($track_id > 0 && $track_album_rel_id > 0 && $country_allowed && $digital_release_date && $price_code_id > 0) {
        $query = "INSERT INTO {$Tables['track_release']} (track_id, track_album_rel_id, country_allowed, dig_rel_date, price_code_id) VALUES(";
        $query .= "$track_id, $track_album_rel_id, '".$mysqli->escape_str($country_allowed)."', '".$mysqli->escape_str($digital_release_date)."', $price_code_id";
        $query .= ")";

        if($mysqli->query($query))
            $result = true;
    }

    return $result;
}


/**
 * Get Track Releases
 *
 * @param int $album_id
 * @return array
 */
function cug_get_track_release($track_id) {
    global $mysqli, $Tables;
    $result = array();

    if($track_id) {
        $query = "SELECT * FROM {$Tables['track_release']} WHERE track_id=$track_id";
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
 * Delete Track-Release Relation
 *
 * @param int $track_id
 * @return boolean
 */
function cug_del_track_release($track_id) {
    global $mysqli, $Tables;
    $result = false;

    if($track_id > 0) {
        $query = "DELETE FROM {$Tables['track_release']} WHERE track_id=$track_id";

        if($mysqli->query($query))
            $result = true;
    }

    return $result;
}

/**
 * Get Track Clients (from 'track_client_rel' table)
 *
 * @param int $track_id
 * @return array
 */
function cug_get_track_clients($track_id) {
    global $mysqli, $Tables;

    $result = array();
    $index = 0;

    $r = $mysqli->query("CALL get_track_clients($track_id)");

    if($r->num_rows) {
        while($arr = $r->fetch_assoc()) {
            $result[$index] = $arr;
            $index ++;
        }
    }

    if($mysqli->more_results())
        $mysqli->next_result();

    return $result;
}


/**
 * Check if Track belongs to Label Category
 *
 * @param int $track_id
 * @param int $label_cat_id
 * @return boolean
 */
function cug_is_track_from_label_category($track_id, $label_cat_id) {
    global $mysqli, $Tables;
    $result = false;

    if($track_id > 0 && $label_cat_id > 0) {
        $query = "SELECT tar.id FROM {$Tables['track_album_rel']} AS tar ";
        $query .= "INNER JOIN {$Tables['album_label_cat']} AS alc ON tar.album_id=alc.album_id ";
        $query .= "WHERE tar.track_id=$track_id AND alc.label_cat_id=$label_cat_id";
        $r = $mysqli->query($query);

        if($r->num_rows)
            $result = true;
    }

    return $result;
}


/**
 * Get Track's Owner (register_from) ID
 *
 * @param $track_id integer
 * @return integer
 */
function cug_get_track_owner_id($track_id) {
    global $mysqli, $Tables;

    if($track_id > 0) {
        $query = "SELECT register_from FROM ".$Tables['track']." WHERE id=$track_id";
        $r = $mysqli->query($query);

        if($r && $r->num_rows) {
            $arr = $r->fetch_assoc();
            return $arr['register_from'];
        }
    }

    return 0;
}
?>