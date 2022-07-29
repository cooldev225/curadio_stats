<?PHP 
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	LANGUAGE
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * language Class
 *
 * @param	id (INT)
 * @param	title (STRINIG)
 * @param	title_native (STRING)
 * @param	code_alpha2 (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__lang
{
	public
	$id,
	$title,
	$title_native,
	$code_alpha2,
	$update_time;
}


/**
 * Language Details Class
 *
 * @param	id (INT)
 * @param	lang_id (INT)
 * @param	subtitle (STRING)
 * @param	subtitle_native (STRING)
 * @param	region_id (INT)
 * @param	code_represent (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__lang_details
{
	public
	$id,
	$lang_id,
	$subtitle,
	$subtitle_native,
	$region_id,
	$code_represent,
	$update_time;
}



/**
 * Language-Module Relation Class
 *
 * @param	id (INT)
 * @param	lang_details_id (INT)
 * @param	module_id (INT)
 * @param	status (INT)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__lang_module_rel
{
	public
	$id,
	$lang_details_id,
	$module_id,
	$status,
	$uniqid,
	$update_time;
}


/**
 * Register New LANGUAGE Region
 *
 * @param string
 * @return integer
 */
function cug_reg_lang_region($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['lang_region'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['lang_region']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing LANGUAGE Region
 *
 * @param integer (ID of Existing LANGUAGE Region)
 * @param string
 * @return integer
 */
function cug_edit_lang_region($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['lang_region']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Get LANGUAGE Region
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_lang_region($item=0, $item_type="ID", $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$field = "title='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "ID") {
			$field = "id=".$mysqli->escape_str($item);
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['lang_region']." WHERE $field");
			if($r->num_rows) {
				$result[0] = $r->fetch_array();
			}
		}
	}
	else {
		
		if($sort_by == "TITLE") {
			$field = "title";
		}
		elseif($sort_by == "ID") {
			$field = "id";
		}
		
			if(!empty($field)) {
				$r = $mysqli->query("SELECT * FROM ".$Tables['lang_region']." ORDER BY $field $sort_type");
				while($arr = $r->fetch_array()) {
					$result[$index] = $arr;
					$index ++;
				}
			}
	}

return $result;
}


/**
 * Register New LANGUAGE
 *
 * @param object of Language Class
 * @return integer
 */
function cug_reg_lang($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->title_native)) {
			$fields .= "title_native,";
			$values .= "'".$mysqli->escape_str($obj->title_native)."',";
		}

		if(!empty($obj->code_alpha2)) {
			$fields .= "code_alpha2,";
			$values .= "'".$mysqli->escape_str($obj->code_alpha2)."',";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time) ";
			$values .= "'".$mysqli->escape_str($obj->update_time)."')";
		}


		$query = "INSERT INTO ".$Tables['lang'].$fields.$values;

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
 * Get LANGUAGE
 *
 * @param object of Language Class, default is NULL
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_lang($obj=NULL, $sort_by="ID", $sort_type="ASC")
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

		if(!empty($obj->title_native)) {
			$fields .= " title_native='".$obj->title_native."' AND";
		}

		if(!empty($obj->code_alpha2)) {
			$fields .= " code_alpha2='".$obj->code_alpha2."' AND";
		}
		
		if(!empty($obj->update_time)) {
			$fields .= " update_time='".$obj->update_time."' AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['lang']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['lang'];
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
 * Edit Existing LANGUAGE
 *
 * @param integer
 * @param object of Language Class
 * @return bool
 */
function cug_edit_lang($obj_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $obj_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->title_native)) $fields .= "title_native='".$mysqli->escape_str($obj->title_native)."',";
		if(!empty($obj->code_alpha2)) $fields .= "code_alpha2='".$mysqli->escape_str($obj->code_alpha2)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['lang']." SET ".$fields." WHERE id=".$obj_id;

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
 * Register New LANGUAGE Details
 *
 * @param object of Language Details Class
 * @return integer
 */
function cug_reg_lang_details($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->lang_id)) {

		$fields = " (lang_id,";
		$values = " VALUES(".$mysqli->escape_str($obj->lang_id).",";

		if(!empty($obj->subtitle)) {
			$fields .= "subtitle,";
			$values .= "'".$mysqli->escape_str($obj->subtitle)."',";
		}

		if(!empty($obj->subtitle_native)) {
			$fields .= "subtitle_native,";
			$values .= "'".$mysqli->escape_str($obj->subtitle_native)."',";
		}

		if(!empty($obj->region_id)) {
			$fields .= "region_id,";
			$values .= $mysqli->escape_str($obj->region_id).",";
		}
		
		if(!empty($obj->code_represent)) {
			$fields .= "code_represent,";
			$values .= "'".$mysqli->escape_str($obj->code_represent)."',";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time) ";
			$values .= "'".$mysqli->escape_str($obj->update_time)."')";
		}


		$query = "INSERT INTO ".$Tables['lang_details'].$fields.$values;

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
 * Get LANGUAGE Details
 *
 * @param object of Language Details Class, default is NULL
 * @param string -> 'SUBTITLE', 'LANG', 'REGION' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_lang_details($obj=NULL, $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}

		if(!empty($obj->subtitle)) {
			$fields .= " subtitle='".$obj->subtitle."' AND";
		}

		if(!empty($obj->subtitle_native)) {
			$fields .= " subtitle_native='".$obj->subtitle_native."' AND";
		}

		if(!empty($obj->region_id)) {
			$fields .= " region_id=".$obj->region_id." AND";
		}

		if(!empty($obj->code_represent)) {
			$fields .= " code_represent='".$obj->code_represent."' AND";
		}

		if(!empty($obj->update_time)) {
			$fields .= " update_time='".$obj->update_time."' AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['lang_details']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['lang_details'];
	}
	//-----------------

	if($sort_by == "SUBTITLE")
		$sort_field = "subtitle";
	elseif ($sort_by == "LANG")
		$sort_field = "lang_id";
	elseif($sort_by == "REGION")
		$sort_field = "region_id";
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
 * Edit Existing LANGUAGE Details
 *
 * @param integer
 * @param object of Language Details Class
 * @return bool
 */
function cug_edit_lang_details($obj_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $obj_id > 0) {

		if(!empty($obj->lang_id)) $fields .= "lang_id=".$mysqli->escape_str($obj->lang_id).",";
		if(!empty($obj->subtitle)) $fields .= "subtitle='".$mysqli->escape_str($obj->subtitle)."',";
		if(!empty($obj->subtitle_native)) $fields .= "subtitle_native='".$mysqli->escape_str($obj->subtitle_native)."',";
		if(!empty($obj->region_id)) $fields .= "region_id=".$mysqli->escape_str($obj->region_id).",";
		if(!empty($obj->code_represent)) $fields .= "code_represent='".$mysqli->escape_str($obj->code_represent)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['lang_details']." SET ".$fields." WHERE id=".$obj_id;

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
 * Register New LANGUAGE-MODULE Relation
 *
 * @param object of Language-Module Class
 * @return integer
 */
function cug_reg_lang_module_rel($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->lang_details_id)) {

		$fields = " (lang_details_id,";
		$values = " VALUES(".$mysqli->escape_str($obj->lang_details_id).",";

		if(!empty($obj->module_id)) {
			$fields .= "module_id,";
			$values .= $mysqli->escape_str($obj->module_id).",";
		}

		if($obj->status >= 0) {
			$fields .= "status,";
			$values .= $mysqli->escape_str($obj->status).",";
		}

		if(!empty($obj->uniqid)) {
			$fields .= "uniqid,";
			$values .= "'".$mysqli->escape_str($obj->uniqid)."',";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time) ";
			$values .= "'".$mysqli->escape_str($obj->update_time)."')";
		}


		$query = "INSERT INTO ".$Tables['lang_module_rel'].$fields.$values;

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
 * Get LANGUAGE-MODULE Relation
 *
 * @param object of Language-Module Relation Class, default is NULL
 * @param string -> 'DETAILS', 'MODULE', 'STATUS' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_lang_module_rel($obj=NULL, $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";


	if(!empty($obj)) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND";
		}

		if(!empty($obj->lang_details_id)) {
			$fields .= " lang_details_id=".$obj->lang_details_id." AND";
		}

		if(!empty($obj->module_id)) {
			$fields .= " module_id=".$obj->module_id." AND";
		}

		if($obj->status >= 0) {
			$fields .= " status=".$obj->status." AND";
		}

		if(!empty($obj->uniqid)) {
			$fields .= " uniqid='".$obj->uniqid."' AND";
		}

		if(!empty($obj->update_time)) {
			$fields .= " update_time='".$obj->update_time."' AND";
		}
	}

	//-----------------
	if(!empty($fields)) {

		$fields = substr($fields, 0, strlen($fields)-3);
		$query = "SELECT * FROM ".$Tables['lang_module_rel']." WHERE ".$fields;
	}
	else {
		$query = "SELECT * FROM ".$Tables['lang_module_rel'];
	}
	//-----------------

	if($sort_by == "DETAILS")
		$sort_field = "lang_details_id";
	elseif ($sort_by == "MODULE")
	 $sort_field = "module_id";
	elseif ($sort_by == "STATUS")
	 $sort_field = "status";
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
 * Edit Existing LANGUAGE-MODULE Relation
 *
 * @param integer
 * @param object of Language-Module Relation Class
 * @return bool
 */
function cug_edit_lang_module_rel($obj_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $obj_id > 0) {

		if(!empty($obj->lang_details_id)) $fields .= "lang_details_id=".$mysqli->escape_str($obj->lang_details_id).",";
		if(!empty($obj->module_id)) $fields .= "module_id=".$mysqli->escape_str($obj->module_id).",";
		if($obj->status >= 0) $fields .= "status=".$mysqli->escape_str($obj->status).",";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['lang_module_rel']." SET ".$fields." WHERE id=".$obj_id;

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

?>