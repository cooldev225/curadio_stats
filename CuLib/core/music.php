<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Music
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------


/**
 * Music Period Class
 *
 * @param	id (INT)
 * @param	genre_id (INT)
 * @param	title (STRINIG)
 * @param	start_year (INT)
 * @param	end_year (INT)
 * @param	parent_id (INT)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__music_period
{
	public
	$id,
	$genre_id,
	$title,
	$start_year,
	$end_year,
	$parent_id,
	$update_time;
}


/**
 * Mood-Key-User Relation Class
 *
 * @param	id (INT)
 * @param	mood_id (INT)
 * @param	submood_id (INT)
 * @param	key_id (INT)
 * @param	user_id (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__mood_key_user_rel
{
	public
	$id,
	$mood_id,
	$submood_id,
	$key_id,
	$user_id,
	$uniqid,
	$update_time;
}


/**
 * Tempo-Genre-User Relation Class
 *
 * @param	id (INT)
 * @param	tempo_id (INT)
 * @param	min_bpm (INT)
 * @param	max_bpm (INT)
 * @param	genre_id (INT)
 * @param	user_id (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__tempo_genre_user_rel
{
	public
	$id,
	$tempo_id,
	$min_bpm,
	$max_bpm,
	$genre_id,
	$user_id,
	$uniqid,
	$update_time;
}


/**
 * Genre Class
 *
 * @param	id (INT)
 * @param	title (STRINIG)
 * @param	parent_id (INT)
 * @param	level (INT)
 * @param	status (INT)
 * @param	avatar (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__genre
{
	public
	$id,
	$title,
	$parent_id,
	$level,
	$status,
	$avatar,
	$update_time;
}

/**
 * Colour Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	hex_num (STRINIG)
 * @param	r (INT)
 * @param	g (INT)
 * @param	b (INT)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__colour
{
	public
	$id,
	$title,
	$hex_num,
	$r,
	$g,
	$b,
	$update_time;
}



/**
 * Get KEY List
 *
 * @param string -> 'KEY' or 'ID', default is 'KEY'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_key_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "KEY")
		$field = "key_val";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['key']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Register New KEY
 *
 * @param integer
 * @param STRINT
 * @param STRINT
 * @return integer
 */
function cug_reg_key($key_val, $major_tone, $minor_tone)
{
global $mysqli, $Tables;

	if($key_val>=0 && !empty($major_tone) && !empty($minor_tone)) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['key']." WHERE key_val=$key_val AND major_tone='".$mysqli->escape_str($major_tone)."' AND minor_tone='".$mysqli->escape_str($minor_tone)."'");

		if( !$r->num_rows ) {

			$query = "INSERT INTO ".$Tables['key']." VALUES(NULL, $key_val, '".$mysqli->escape_str($major_tone)."', '".$mysqli->escape_str($minor_tone)."', NULL)";
				
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
 * Get KEY (ID or TITLE)
 *
 * @param integer
 * @param string -> 'KEY' or 'ID', default is 'KEY'
 * @return array
 */
function cug_get_key($item, $item_type="KEY")
{
global $mysqli, $Tables;
$result = array();

	if($item >= 0)
	{
		if($item_type == "KEY") {
			$filed = "key_val";
		}
		elseif($item_type == "ID") {
			$filed = "id";
		}


			if(!empty($filed)) {
				$query = "SELECT * FROM ".$Tables['key']." WHERE $filed=".$mysqli->escape_str($item);
				$r = $mysqli->query($query);
				
				if($r->num_rows) {
					$result[0] = $r->fetch_array();
				}	
			}
	}

return $result;
}


/**
 * Edit Existing KEY
 *
 * @param integer (ID of Existing KEY)
 * @param integer
 * @param string
 * @param string
 * @return integer
 */
function cug_edit_key($id, $new_key_val, $new_major_tone, $new_minor_tone)
{
global $mysqli, $Tables;

	if(!empty($id) && $new_key_val>=0 && !empty($new_major_tone) && !empty($new_minor_tone)) {
		if($mysqli->query("UPDATE ".$Tables['key']." SET key_val=$new_key_val, major_tone='".$mysqli->escape_str($new_major_tone)."', minor_tone='".$mysqli->escape_str($new_minor_tone)."' WHERE id=$id")) {
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
 * Get GENRE
 *
 * @param object of Genre Class, default is NULL
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
 function cug_get_genre($obj=NULL, $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}

		if(!empty($obj->title)) {
			$fields .= " title='".$obj->title."' AND";
		}

		if(isset($obj->parent_id) && $obj->parent_id >= 0) {
			$fields .= " parent_id=".$obj->parent_id." AND";
		}

		if(!empty($obj->level)) {
			$fields .= " level=".$obj->level." AND";
		}

		if(isset($obj->status) && $obj->status >= 0) {
			$fields .= " status=".$obj->status." AND";
		}
		
		if(!empty($obj->avatar)) {
			$fields .= " avatar='".$obj->avatar."' AND";
		}		

		if(!empty($obj->update_time)) {
			$fields .= " update_time='".$obj->update_time."' AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['genre']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['genre'];
	}
	//-----------------

	if($sort_by == "TITLE")
		$sort_field = "title";
	else
		$sort_field = "id";
	//-----------------

	$query .= " ORDER BY $sort_field $sort_type";
	$r = $mysqli->query($query);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}
	//-----------------

return $result;
}


/**
 * Register New GENRE/FILEUNDER
 *
 * @param string
 * @param string -> 'GENRE' or 'FILEUNDER', default is 'GENRE'
 * @param integer -> default is 0
 * @return integer
 */
function cug_reg_genre($object_title, $object_type="GENRE", $parent_object_id=0)
{
global $mysqli, $Tables;

	if(!empty($object_title) && $parent_object_id >= 0) {
		
		if($object_type == "GENRE") {
			$level = 1;
			$parent_id = 0;
		}
		elseif($object_type == "FILEUNDER") {
			$level = 2;
			$parent_id = $parent_object_id;
		}

		
			if(!empty($level)) {
				// Check for existing record
				$r = $mysqli->query("SELECT id FROM ".$Tables['genre']." WHERE level=$level AND parent_id=$parent_id AND title='".$mysqli->escape_str($object_title)."'");
				
				if( !$r->num_rows ) {
				
					$query = "INSERT INTO ".$Tables['genre']." VALUES(NULL, '".$mysqli->escape_str($object_title)."', $parent_id, $level, 1, '', NULL)";
				
					if($mysqli->query($query))
						return $mysqli->insert_id;
					else
						return -1; // Error, Can't insert data
				}
				else {
					return -2; // Already Exists
				}
			}
			return 0;

	}
	else
		return 0;
}


/**
 * Edit Existing GENRE/FILEUNDER
 *
 * @param integer (ID of Existing GENRE/FILEUNDER)
 * @param string 
 * @param integer -> 0 or 1, default is 1
 * @return integer
 */
function cug_edit_genre($id, $new_title, $status=1)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title) && ($status == 0 || $status == 1)) {
		
		if($r = $mysqli->query("SELECT level, parent_id FROM ".$Tables['genre']." WHERE id=$id")) { //check object type, is it GENRE or FILEUNDER
			$arr = $r->fetch_array();
				if($arr['level'] > 1) { //FILEUNDER
					
					//check for existing record
					if($r1 = $mysqli->query("SELECT id FROM ".$Tables['genre']." WHERE parent_id=".$arr['parent_id']." AND title='".$mysqli->escape_str($new_title)."'")) {
						if($r1->num_rows) {
							return -2; // New FILEUNDER Already Exists
						}
						else {
							if($mysqli->query("UPDATE ".$Tables['genre']." SET title='".$mysqli->escape_str($new_title)."',status=$status WHERE id=$id")) {
								return 1;
							}
							else {
								return -1; // Error
							}							
						}
					}
					else {
						return -4; //Unknown Error
					}
				}
				else { //GENRE
					
					//check for existing record
					if($r1 = $mysqli->query("SELECT id FROM ".$Tables['genre']." WHERE parent_id=0 AND title='".$mysqli->escape_str($new_title)."'")) {
						if($r1->num_rows) {
							return -2; // New GENRE Already Exists
						}
						else {
							if($mysqli->query("UPDATE ".$Tables['genre']." SET title='".$mysqli->escape_str($new_title)."',status=$status WHERE id=$id")) {
								return 1;
							}
							else {
								return -1; // Error
							}							
						}
					}
					else {
						return -4; //Unknown Error
					}
				}
		}
		else {
			return -3; //Unknown ID
		}
	}
	else {
		return 0;
	}
}



/**
 * Delete GENRE/FILEUNDER
 *
 * @param integer
 * @return integer
 */
function cug_del_genre($object_id)
{
global $mysqli, $Tables;
	
	if($object_id) {
		//check if TRACK and/or ALBUM are linked to actual Genre ID
		$query1 = "SELECT id FROM ".$Tables['track']." WHERE genre_id=$object_id OR fileunder_id=$object_id";
		$query2 = "SELECT id FROM ".$Tables['album']." WHERE genre_id=$object_id OR fileunder_id=$object_id";
		
		$r1 = $mysqli->query($query1);
			if(!$r1->num_rows) {
				$r2 = $mysqli->query($query2);
					if(!$r2->num_rows) {
						if($mysqli->query("DELETE FROM ".$Tables['genre']." WHERE id=$object_id")) {
							return 1; // OK - Genre was deleted
						}
						else {
							return -1; // Error - Can't Delete
						}
					}
					else {
						return -2; // Genre is linked to ALBUM
					}
			}
			else {
				return -3; // Genre is linked to TRACK
			}
	}
	else {
		return 0;
	}
}


/**
 * Get TEMPO List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_tempo_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['tempo']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get TEMPO (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_tempo($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['tempo'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['tempo'], "title", "id=".$mysqli->escape_str($item));
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
 * Get TEMPO Info
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return array
 */
function cug_get_tempo_info($item, $item_type="ID")
{
global $mysqli, $Tables;
$result = array();

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$field = "title='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "ID") {
			$field = "id=".$mysqli->escape_str($item);
		}

		
			if(!empty($field)) {
				
				$r = $mysqli->query("SELECT * FROM ".$Tables['tempo']." WHERE $field");
				if($r->num_rows) {
					$result[0] = $r->fetch_array();
				}
			}
	}
	
return $result;
}


/**
 * Edit Existing TEMPO
 *
 * @param integer (ID of Existing TEMPO)
 * @param string
 * @return integer
 */
function cug_edit_tempo($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['tempo']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New TEMPO
 *
 * @param string
 * @return integer
 */
function cug_reg_tempo($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['tempo'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['tempo']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Get MOOD List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_mood_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['mood']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get MOOD (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_mood($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['mood'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['mood'], "title", "id=".$mysqli->escape_str($item));
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
 * Get MOOD Info
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return array
 */
function cug_get_mood_info($item, $item_type="ID")
{
global $mysqli, $Tables;
$result = array();

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$field = "title='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "ID") {
			$field = "id=".$mysqli->escape_str($item);
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['mood']." WHERE $field");
			if($r->num_rows) {
				$result[0] = $r->fetch_array();
			}
		}
	}

return $result;
}


/**
 * Edit Existing MOOD
 *
 * @param integer (ID of Existing MOOD)
 * @param string
 * @return integer
 */
function cug_edit_mood($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['mood']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New MOOD
 *
 * @param string
 * @return integer
 */
function cug_reg_mood($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['mood'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['mood']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Get SUBMOOD List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_submood_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['submood']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get SUBMOOD (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_submood($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['submood'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['submood'], "title", "id=".$mysqli->escape_str($item));
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
 * Get SUBMOOD Info
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return array
 */
function cug_get_submood_info($item, $item_type="ID")
{
global $mysqli, $Tables;
$result = array();

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$field = "title='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "ID") {
			$field = "id=".$mysqli->escape_str($item);
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['submood']." WHERE $field");
			if($r->num_rows) {
				$result[0] = $r->fetch_array();
			}
		}
	}

	return $result;
}


/**
 * Edit Existing SUBMOOD
 *
 * @param integer (ID of Existing SUBMOOD)
 * @param string
 * @return integer
 */
function cug_edit_submood($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['submood']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New SUBMOOD
 *
 * @param string
 * @return integer
 */
function cug_reg_submood($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['submood'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['submood']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Register New MusicPeriod
 *
 * @param object of MusicPeriod Class
 * @return integer
 */
function cug_reg_music_period($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->genre_id)) {
			$fields .= "genre_id,";
			$values .= $mysqli->escape_str($obj->genre_id).",";
		}

		if(!empty($obj->start_year)) {
			$fields .= "start_year,";
			$values .= $mysqli->escape_str($obj->start_year).",";
		}

		if(!empty($obj->end_year)) {
			$fields .= "end_year,";
			$values .= $mysqli->escape_str($obj->end_year).",";
		}

		if(!empty($obj->parent_id)) {
			$fields .= "parent_id,";
			$values .= $mysqli->escape_str($obj->parent_id).",";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time) ";
			$values .= "'".$mysqli->escape_str($obj->update_time)."')";
		}
		else {
			$fields .= "update_time) ";
			$values .= "NULL)";
		}


		$query = "INSERT INTO ".$Tables['music_period'].$fields.$values;

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
 * Get MuicPeriod List
 *
 * @param object of MuicPeriod Class, default is NULL
 * @param string -> 'TITLE', 'GENRE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_music_period_list($obj=NULL, $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {
		
		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}
	
		if(!empty($obj->genre_id)) {
			$fields .= " genre_id=".$obj->genre_id." AND";
		}
	
		if(!empty($obj->title)) {
			$fields .= " title='".$obj->title."' AND";
		}
	
		if(!empty($obj->start_year)) {
			$fields .= " start_year=".$obj->start_year." AND";
		}
	
		if(!empty($obj->end_year)) {
			$fields .= " end_year=".$obj->end_year." AND";
		}
	}
	
		//-----------------
		if(!empty($fields)) {
	
			$fields = substr($fields, 0, strlen($fields)-3);
			$query = "SELECT * FROM ".$Tables['music_period']." WHERE ".$fields;
		}
		else {
			$query = "SELECT * FROM ".$Tables['music_period'];
		}
		//-----------------
		
			if($sort_by == "TITLE")
				$sort_field = "title";
			elseif ($sort_by == "GENRE")
				$sort_field = "genre_id";
			else
				$sort_field = "id";
		//-----------------
		
				$query .= " ORDER BY $sort_field $sort_type";
				$r = $mysqli->query($query);
				
				if($r->num_rows) {
					while($arr = $r->fetch_array()) {
						$result[$index] = $arr;
						$index ++;
					}
				}
				//-----------------
		
return $result;
}



/**
 * Edit Existing MusicPeriod
 *
 * @param integer
 * @param object of MusicPeriod Class
 * @return bool
 */
function cug_edit_music_period($music_period_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $music_period_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->genre_id)) $fields .= "genre_id=".$mysqli->escape_str($obj->genre_id).",";
		if(!empty($obj->start_year)) $fields .= "start_year=".$mysqli->escape_str($obj->start_year).",";
		if(!empty($obj->end_year)) $fields .= "end_year=".$mysqli->escape_str($obj->end_year).",";
		if(!empty($obj->parent_id)) $fields .= "parent_id=".$mysqli->escape_str($obj->parent_id).",";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['music_period']." SET ".$fields." WHERE id=".$music_period_id;

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
 * Register New Mood-Key-User Relation
 *
 * @param object of Music-Key-User Relation Class
 * @return integer
 */
function cug_reg_mood_key_user_rel($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->mood_id) && !empty($obj->submood_id) && !empty($obj->key_id) && !empty($obj->user_id)) {

		if(!empty($obj->mood_id)) {
			$fields = " mood_id,";
			$values = $mysqli->escape_str($obj->mood_id).",";
		}
		//------------------
		if(!empty($obj->submood_id)) {
			$fields .= " submood_id,";
			$values .= $mysqli->escape_str($obj->submood_id).",";
		}
		//------------------
		if(!empty($obj->key_id)) {
			$fields .= " key_id,";
			$values .= $mysqli->escape_str($obj->key_id).",";
		}
		//------------------
		if(!empty($obj->user_id)) {
			$fields .= " user_id,";
			$values .= $mysqli->escape_str($obj->user_id).",";
		}
		//------------------
		if(!empty($obj->update_time)) {
			$fields .= " update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}
		//-------------------
		if(!empty($obj->uniqid)) {
			$uniqid = $obj->uniqid;
		}
		else {
			$uniqid = uniqid();
		}
		$fields .= " uniqid";
		$values .= "'".$uniqid."'";
		//------------------


		$query = "INSERT INTO ".$Tables['mood_key_user_rel']."(".$fields.") VALUES(".$values.")";

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
 * Get Mood-Key-User Relation List
 *
 * @param object of Mood-Key-User Relation Class, default is NULL
 * @param string -> 'KEY', 'USER' or 'ID', default is 'KEY'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_mood_key_user_list($obj=NULL, $sort_by="KEY", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}

		if(!empty($obj->mood_id)) {
			$fields .= " mood_id=".$obj->mood_id." AND";
		}

		if(!empty($obj->submood_id)) {
			$fields .= " submood_id=".$obj->submood_id." AND";
		}

		if(!empty($obj->key_id)) {
			$fields .= " key_id=".$obj->key_id." AND";
		}

		if(!empty($obj->user_id)) {
			$fields .= " user_id=".$obj->user_id." AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['mood_key_user_rel']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['mood_key_user_rel'];
	}
	//-----------------

	if($sort_by == "KEY")
		$sort_field = "key_id";
	elseif ($sort_by == "USER")
		$sort_field = "user_id";
	else
		$sort_field = "id";
	//-----------------

	$query .= " ORDER BY $sort_field $sort_type";
	$r = $mysqli->query($query);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}
	//-----------------

	return $result;
}


/**
 * Edit Existing Mood-Key-User Relation
 *
 * @param integer
 * @param object of Mood-Key-User Relation Class
 * @return bool
 */
function cug_edit_mood_key_user_rel($object_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $object_id > 0) {

		if(!empty($obj->mood_id)) $fields .= "mood_id=".$mysqli->escape_str($obj->mood_id).",";
		if(!empty($obj->submood_id)) $fields .= "submood_id=".$mysqli->escape_str($obj->submood_id).",";
		if(!empty($obj->key_id)) $fields .= "key_id=".$mysqli->escape_str($obj->key_id).",";
		if(!empty($obj->user_id)) $fields .= "user_id=".$mysqli->escape_str($obj->user_id).",";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['mood_key_user_rel']." SET ".$fields." WHERE id=".$object_id;

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
 * Register New Tempo-Genre-User Relation
 *
 * @param object of Tempo-Genre-User Relation Class
 * @return integer
 */
function cug_reg_tempo_genre_user_rel($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->tempo_id) && !empty($obj->min_bpm) && !empty($obj->max_bpm) && !empty($obj->genre_id) && !empty($obj->user_id)) {

		if(!empty($obj->tempo_id)) {
			$fields = " tempo_id,";
			$values = $mysqli->escape_str($obj->tempo_id).",";
		}
		//------------------
		if(!empty($obj->min_bpm)) {
			$fields .= " min_bpm,";
			$values .= $mysqli->escape_str($obj->min_bpm).",";
		}
		//------------------
		if(!empty($obj->max_bpm)) {
			$fields .= " max_bpm,";
			$values .= $mysqli->escape_str($obj->max_bpm).",";
		}
		//------------------
		if(!empty($obj->genre_id)) {
			$fields .= " genre_id,";
			$values .= $mysqli->escape_str($obj->genre_id).",";
		}
		//------------------
		if(!empty($obj->user_id)) {
			$fields .= " user_id,";
			$values .= $mysqli->escape_str($obj->user_id).",";
		}
		//------------------
		if(!empty($obj->update_time)) {
			$fields .= " update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}
		//-------------------
		if(!empty($obj->uniqid)) {
			$uniqid = $obj->uniqid;
		}
		else {
			$uniqid = uniqid();
		}
		$fields .= " uniqid";
		$values .= "'".$uniqid."'";
		//------------------


		$query = "INSERT INTO ".$Tables['tempo_genre_user_rel']."(".$fields.") VALUES(".$values.")";

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
 * Edit Existing Tempo-Genre-User Relation
 *
 * @param integer
 * @param object of Tempo-Genre-User Relation Class
 * @return bool
 */
function cug_edit_tempo_genre_user_rel($object_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $object_id > 0) {

		if(!empty($obj->tempo_id)) $fields .= "tempo_id=".$mysqli->escape_str($obj->tempo_id).",";
		if(!empty($obj->min_bpm)) $fields .= "min_bpm=".$mysqli->escape_str($obj->min_bpm).",";
		if(!empty($obj->max_bpm)) $fields .= "max_bpm=".$mysqli->escape_str($obj->max_bpm).",";
		if(!empty($obj->genre_id)) $fields .= "genre_id=".$mysqli->escape_str($obj->genre_id).",";
		if(!empty($obj->user_id)) $fields .= "user_id=".$mysqli->escape_str($obj->user_id).",";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['tempo_genre_user_rel']." SET ".$fields." WHERE id=".$object_id;

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
 * Get Tempo-Genre-User Relation List
 *
 * @param object of Tempo-Genre-User Relation Class, default is NULL
 * @param string -> 'MINBPM', 'MAXBPM' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_tempo_genre_user_list($obj=NULL, $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}

		if(!empty($obj->tempo_id)) {
			$fields .= " tempo_id=".$obj->tempo_id." AND";
		}

		if(!empty($obj->min_bpm)) {
			$fields .= " min_bpm<=".$obj->min_bpm." AND";
		}

		if(!empty($obj->max_bpm)) {
			$fields .= " max_bpm>=".$obj->max_bpm." AND";
		}

		if(!empty($obj->genre_id)) {
			$fields .= " genre_id=".$obj->genre_id." AND";
		}
		
		if(!empty($obj->user_id)) {
			$fields .= " user_id=".$obj->user_id." AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['tempo_genre_user_rel']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['tempo_genre_user_rel'];
	}
	//-----------------

	if($sort_by == "MINBPM")
		$sort_field = "min_bpm";
	elseif ($sort_by == "MAXBPM")
	$sort_field = "max_bpm";
	else
		$sort_field = "id";
	//-----------------

	$query .= " ORDER BY $sort_field $sort_type";
	$r = $mysqli->query($query);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}
	//-----------------

	return $result;
}

/**
 * Get Colours
 *
 * @param object of Colour Class
 * @param integer (default is 1)
 * @param string ('ID', 'TITLE' default is 'TITLE')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @return array
 */
function cug_get_colours($obj, $limit=1, $LIKE=true, $sort_by="TITLE", $sort_type="ASC")
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

		if($LIKE){
			if(strlen($obj->title) == 1)
				$fields .= " title LIKE '".$mysqli->escape_str($obj->title)."%' AND";
			elseif(strlen($obj->title) > 1)
				$fields .= " title LIKE '%".$mysqli->escape_str($obj->title)."%' AND";
		}
		else {
			$fields .= " title='".$mysqli->escape_str($obj->title)."' AND";
		}
	}
	//------------------------
	if(!empty($obj->hex_num)) {
		$fields .= " hex_num=".$mysqli->escape_str($obj->hex_num)." AND";
	}
	//------------------------
	if(!empty($obj->r)) {
		$fields .= " r=".$mysqli->escape_str($obj->r)." AND";
	}
	//------------------------
	if(!empty($obj->g)) {
		$fields .= " g=".$mysqli->escape_str($obj->g)." AND";
	}
	//------------------------
	if(!empty($obj->b)) {
		$fields .= " b=".$mysqli->escape_str($obj->b)." AND";
	}


	//----------------
	if($sort_by == "id")
		$sort_field = "id";
	else
		$sort_field = "title";


	//-------------------
	if(!empty($fields)) {
		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['colour']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['colour'];
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
 * Register New Colour
 *
 * @param object of Colour Class
 * @return integer
 */
function cug_reg_colour($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";


		if(!empty($obj->hex_num)) {
			$fields .= "hex_num,";
			$values .= "'".$mysqli->escape_str($obj->hex_num)."',";
		}
		//------
		if(isset($obj->r) && $obj->r >= 0) {
			$fields .= "r,";
			$values .= $mysqli->escape_str($obj->r).",";
		}
		//------
		if(isset($obj->g) && $obj->g >= 0) {
			$fields .= "g,";
			$values .= $mysqli->escape_str($obj->g).",";
		}
		//------
		if(isset($obj->b) && $obj->b >= 0) {
			$fields .= "b,";
			$values .= $mysqli->escape_str($obj->b).",";
		}		
		//------
		$fields .= "update_time) ";
			if(!empty($obj->update_time)) {
				$values .= "'".$mysqli->escape_str($obj->update_time)."')";
			}
			else {
				$values .= "CURRENT_TIMESTAMP)";
			}
		//-----

		$query = "INSERT INTO ".$Tables['colour'].$fields.$values;

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
?>