<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Object
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Register New Object
 *
 * @param string
 * @return integer
 */
function cug_reg_object($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['object'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['object']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Register New Module
 *
 * @param string
 * @return integer
 */
function cug_reg_module($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['module'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['module']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing Object
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_object($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['object']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Edit Existing Module
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_module($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['module']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Get Object (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_object($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['object'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['object'], "title", "id=".$mysqli->escape_str($item));
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
 * Get Module (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_module($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['module'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['module'], "title", "id=".$mysqli->escape_str($item));
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
 * Get Object List
 *
 * @param string -> 'TITLE' or 'ID', default is 'TITLE'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_object_list($sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['object']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get Module List
 *
 * @param string -> 'TITLE' or 'ID', default is 'TITLE'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_module_list($sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['module']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get Object title by Object ID
 *
 * @param INTEGER (ID of the Object)
 * @param string (for which Object)
 * @return string
 */
function cug_get_object_title($id, $object)
{
global $mysqli, $Tables;
$table = "";
$result = "";


	if($id > 0 && $object) {
		
		switch($object) {
			case "COLOUR":
				$table = $Tables['colour'];
				$title = "title";
			break;
			//---------------
			case "GENRE":
				$table = $Tables['genre'];
				$title = "title";
			break;
			//---------------
			case "LANGUAGE":
				$table = $Tables['lang'];
				$title = "title";
			break;			
			//---------------
			case "LANGDETAILS":
				$table = $Tables['lang_details'];
				$title = "subtitle";
			break;
			//---------------			
			case "COUNTRY":
				$table = $Tables['country'];
				$title = "title";
			break;
			//---------------
			case "MASTERRIGHT":
				$table = $Tables['track_masterright'];
				$title = "title";
			break;
			//---------------
			case "ALBUM_PACKAGE":
				$table = $Tables['album_package'];
				$title = "title";
			break;
			//---------------
			case "ALBUM_FORMAT":
				$table = $Tables['album_format'];
				$title = "title";
			break;
			//---------------
			case "ALBUM_TYPE":
				$table = $Tables['album_type'];
				$title = "title";
			break;
			//---------------
			case "MEMBER_TYPE":
				$table = $Tables['member_type'];
				$title = "title";
			break;
			//---------------
			case "MEMBER_ROLE":
				$table = $Tables['member_role'];
				$title = "title";
			break;
			//---------------			
			case "GENDER":
				$table = $Tables['gender'];
				$title = "title";
			break;
			
		}
		//----------------------
		
			if($table) {
				$query = "SELECT $title FROM ".$table." WHERE id=".$id;
				$r = $mysqli->query($query);
				
					if($r->num_rows) {
						$arr = $r->fetch_array();
						$result = $arr[$title];
					}
			}
	}

return $result;
}

/**
 * Get Object ID by Object Title
 *
 * @param STRING (Title of the Object)
 * @param string (for which Object)
 * @return string
 */
function cug_get_object_id($title, $object)
{
global $mysqli, $Tables;
$table = "";
$result = "";


	if($title && $object) {

		switch($object) {
			case "COLOUR":
				$table = $Tables['colour'];
				$title_field = "title";
			break;
			//---------------
			case "GENRE":
				$table = $Tables['genre'];
				$title_field = "title";
			break;
			//---------------
			case "LANGUAGE":
				$table = $Tables['lang'];
				$title_field = "title";
			break;
			//---------------
			case "COUNTRY":
				$table = $Tables['country'];
				$title_field = "title";
			break;
			//---------------
			case "MASTERRIGHT":
				$table = $Tables['track_masterright'];
				$title_field = "title";
			break;
			//---------------
			case "ALBUM":
				$table = $Tables['album'];
				$title_field = "title";
			break;
			//---------------
			case "MEMBER":
				$table = $Tables['member'];
				$title_field = "alias";
			break;
			//---------------
			case "CLIENT":
				$table = $Tables['client'];
				$title_field = "title";
			break;				
				
		}
		//----------------------

		if($table) {
			$query = "SELECT id FROM ".$table." WHERE $title_field='".$mysqli->escape_str($title)."'";
			$r = $mysqli->query($query);

			if($r->num_rows) {
				$arr = $r->fetch_array();
				$result = $arr['id'];
			}
		}
	}

return $result;
}
?>