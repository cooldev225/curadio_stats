<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Client
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Client Class
 *
 * @param	id (INT)
 * @param	title (STRING)
 * @param	dba (STRING)
 * @param	parent_id (INT)
 * @param	level (INT)
 * @param	country_id (INT)
 * @param	state (STRING)
 * @param	city (STRING)
 * @param	addr (STRING)
 * @param	url (STRING)
 * @param	zip_code (STRING)
 * @param	tel (STRING)
 * @param	fax (STRING)
 * @param	email (STRING)
 * @param	admin_name (STRING)
 * @param	admin_email (STRING)
 * @param	admin_phone (STRING)
 * @param	acc_name (STRING)
 * @param	acc_email (STRING)
 * @param	acc_phone (STRING)
 * @param	tech_name (STRING)
 * @param	tech_email (STRING)
 * @param	tech_phone (STRING)
 * @param	img_path - (STRING)
 * @param	img_34 (INT)
 * @param	img_64 (INT)
 * @param	img_174 (INT)
 * @param	img_300 (INT)
 * @param	img_600 (INT)
 * @param	img_orgn (INT - Size of the Original Image File)
 * @param	register_date (STRING - MySQL DATE Format)
 * @param	register_ip (STRING)
 * @param	status_id (INT)
 * @param	reg_module_id (INT)
 * @param	reg_module_details_id (INT)
 * @param	delivery_email (STRING)
 * @param	uniqid - STRING
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__client
{
	public
	$id,
	$title,
	$dba, // Doing Business As
	$parent_id,
	$level,
	$country_id,
	$state,
	$city,
	$addr,
	$url,
	$zip_code,
	$tel,
	$fax,
	$email,
	$admin_name,
	$admin_email,
	$admin_phone,
	$acc_name,
	$acc_email,
	$acc_phone,
	$tech_name,
	$tech_email,
	$tech_phone,
	$img_path,
	$img_34,
	$img_64,
	$img_174,
	$img_300,
	$img_600,
	$img_orgn,
	$register_date,
	$register_ip,
	$status_id,
	$reg_module_id,
	$reg_module_details_id,
	$delivery_email,
	$uniqid,
	$update_time;
}


/**
 * Get Business Type List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_business_type_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['client_businesstype']." ORDER BY ".$field." ".$sort_type);
		
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}	
		}

return $result;
}


/**
 * Get Business Type (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_business_type($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['client_businesstype'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['client_businesstype'], "title", "id=".$mysqli->escape_str($item));
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
 * Register New Business Type
 *
 * @param string
 * @return integer
 */
function cug_reg_business_type($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['client_businesstype'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['client_businesstype']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing Business Type
 *
 * @param integer (ID of Existing Business Type)
 * @param string (New Business Type)
 * @return integer
 */
function cug_edit_business_type($id, $new_title)
{
	global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['client_businesstype']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Get Client Type List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_client_type_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['client_type']." ORDER BY ".$field." ".$sort_type);
	
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}	
		}

return $result;
}


/**
 * Get Client Type (ID or TITLE)
 *
 * @param mixed (integer or string)
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_client_type($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['client_type'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['client_type'], "title", "id=".$mysqli->escape_str($item));
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
 * Register New Client Type
 *
 * @param string
 * @return integer
 */
function cug_reg_client_type($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['client_type'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['client_type']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing Client Type
 *
 * @param integer (ID of Existing Client Type)
 * @param string (New Client Type)
 * @return integer
 */
function cug_edit_client_type($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['client_type']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Get Client WM2 Info
 *
 * @param integer $item
 * @param string $item_type ('CLIENT' or 'WM2', default is 'WM2')
 * @return array
 */
function cug_get_client_wm2($item, $item_type="WM2") {
	global $mysqli, $Tables;
	$result = array();
	$field = "";
	$index = 0;
	
	if(!empty($item)) {
		
		if($item_type == "WM2")
			$field = "wm2_code";
		elseif($item_type == "CLIENT") 
			$field = "client_id";
		
			if($field) {
				$query = "SELECT * FROM {$Tables['client_wm2']} WHERE $field=".$mysqli->escape_str($item);
				$r = $mysqli->query($query);
					if($r) {
						if($r->num_rows) {
							while($arr = $r->fetch_array(MYSQL_ASSOC)) {
								$result[$index] = $arr;
								$index ++;
							}
						}
					}
			}
		
	}
	
	return $result;
}

/**
 * Register Client WM2 Code
 * 
 * @param int $client_id
 * @param int $wm2_code
 * @param number $wm_status (0 or 1, default: 1)
 * @param string $register_date (optional)
 * @return number
 */
function cug_reg_client_wm2($client_id, $wm2_code, $wm_status=1, $register_date="") {
	global $mysqli, $Tables;

	if($client_id > 0 && $wm2_code > 0) {
		// Check for duplicate WM2 codes
		$arr = cug_get_client_wm2($wm2_code, $item_type="WM2");
			if(count($arr) == 0) {
				$query = "INSERT INTO ".$Tables['client_wm2']." VALUES(NULL, $client_id, $wm2_code, $wm_status, ";
				$query .= !empty($register_date) ? "'".$mysqli->escape_str($register_date)."', " : "NOW(), ";
				$query .= "NULL)";
					
				if($mysqli->query($query))
					return $mysqli->insert_id;
				else
					return -1; //Error inserting 
			}
			else
				return -2; //WM2 Code already exists
	}
	else
		return 0;
}



/**
 * Get Client-BusinessType Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'CLIENT', 'BUSINESSTYPE', 'UNIQID'; default is 'UNIQID')
 * @return array
 */
function cug_get_client_business_type_rel($item, $item_type="UNIQID")
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

			case 'CLIENT':
				$field = "client_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'BUSINESSTYPE':
				$field = "business_type";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['client_businesstype_rel']." WHERE ".$field."=".$value);

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
 * Register Client-BusinessType Relation
 *
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_client_business_type_rel($client_id, $business_type, $uniqid="")
{
global $mysqli, $Tables;

	if($client_id>0 && $business_type>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['client_businesstype_rel']." WHERE client_id=$client_id AND business_type=$business_type");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);


			$query = "INSERT INTO ".$Tables['client_businesstype_rel']." VALUES(NULL, $client_id, $business_type, '$uniq_id', NULL)";
				
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
 * Get Client-Type Relations
 *
 * @param mix (integer or string)
 * @param string ('ID', 'CLIENT', 'TYPE'; default is 'CLIENT')
 * @return array
 */
function cug_get_client_type_rel($item, $item_type="CLIENT")
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($item) && !empty($item_type)) {

		switch($item_type) {

			case 'ID':
				$field = "id";
				$value = $mysqli->escape_str($item);
				break;

			case 'CLIENT':
				$field = "client_id";
				$value = $mysqli->escape_str($item);
				break;

			case 'TYPE':
				$field = "type_id";
				$value = $mysqli->escape_str($item);
				break;

			default:
				$field = "";
				break;
		}


		if(!empty($field)) {

			$r = $mysqli->query("SELECT * FROM ".$Tables['client_type_rel']." WHERE ".$field."=".$value);

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
 * Register Client-Type Relation
 *
 * @param integer
 * @param integer
 * @return integer
 */
function cug_reg_client_type_rel($client_id, $type_id)
{
global $mysqli, $Tables;

	if($client_id>0 && $type_id>0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['client_type_rel']." WHERE client_id=$client_id AND type_id=$type_id");

		if( !$r->num_rows ) {

			$query = "INSERT INTO ".$Tables['client_type_rel']." VALUES(NULL, $client_id, $type_id, NULL)";

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
 * Register New Client
 *
 * @param object of Client Class
 * @return integer
 */
function cug_reg_client($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {

		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";

		if(!empty($obj->dba)) {
			$fields .= "dba,";
			$values .= "'".$mysqli->escape_str($obj->dba)."',";
		}

		if($obj->parent_id != null && $obj->parent_id>=0) {
			$fields .= "parent_id,";
			$values .= $mysqli->escape_str($obj->parent_id).",";
		}

		if(!empty($obj->level)) {
			$fields .= "level,";
			$values .= $mysqli->escape_str($obj->level).",";
		}

		if(!empty($obj->country_id)) {
			$fields .= "country_id,";
			$values .= $mysqli->escape_str($obj->country_id).",";
		}

		if(!empty($obj->state)) {
			$fields .= "state,";
			$values .= "'".$mysqli->escape_str($obj->state)."',";
		}

		if(!empty($obj->city)) {
			$fields .= "city,";
			$values .= "'".$mysqli->escape_str($obj->city)."',";
		}

		if(!empty($obj->addr)) {
			$fields .= "addr,";
			$values .= "'".$mysqli->escape_str($obj->addr)."',";
		}

		if(!empty($obj->url)) {
			$fields .= "url,";
			$values .= "'".$mysqli->escape_str($obj->url)."',";
		}

		if(!empty($obj->zip_code)) {
			$fields .= "zip_code,";
			$values .= "'".$mysqli->escape_str($obj->zip_code)."',";
		}

		if(!empty($obj->tel)) {
			$fields .= "tel,";
			$values .= "'".$mysqli->escape_str($obj->tel)."',";
		}

		if(!empty($obj->fax)) {
			$fields .= "fax,";
			$values .= "'".$mysqli->escape_str($obj->fax)."',";
		}

		if(!empty($obj->email)) {
			$fields .= "email,";
			$values .= "'".$mysqli->escape_str($obj->email)."',";
		}

		if(!empty($obj->admin_name)) {
			$fields .= "admin_name,";
			$values .= "'".$mysqli->escape_str($obj->admin_name)."',";
		}

		if(!empty($obj->admin_email)) {
			$fields .= "admin_email,";
			$values .= "'".$mysqli->escape_str($obj->admin_email)."',";
		}

		if(!empty($obj->admin_phone)) {
			$fields .= "admin_phone,";
			$values .= $mysqli->escape_str($obj->admin_phone).",";
		}

		if(!empty($obj->acc_name)) {
			$fields .= "acc_name,";
			$values .= "'".$mysqli->escape_str($obj->acc_name)."',";
		}
		
		if(!empty($obj->acc_email)) {
			$fields .= "acc_email,";
			$values .= "'".$mysqli->escape_str($obj->acc_email)."',";
		}
		
		if(!empty($obj->acc_phone)) {
			$fields .= "acc_phone,";
			$values .= "'".$mysqli->escape_str($obj->acc_phone)."',";
		}
		
		if(!empty($obj->tech_name)) {
			$fields .= "tech_name,";
			$values .= "'".$mysqli->escape_str($obj->tech_name)."',";
		}
		
		if(!empty($obj->tech_email)) {
			$fields .= "tech_email,";
			$values .= "'".$mysqli->escape_str($obj->tech_email)."',";
		}
		
		if(!empty($obj->tech_phone)) {
			$fields .= "tech_phone,";
			$values .= "'".$mysqli->escape_str($obj->tech_phone)."',";
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

		if(!empty($obj->register_date)) {
			$fields .= "register_date,";
			$values .= "'".$mysqli->escape_str($obj->register_date)."',";
		}
		
		if(!empty($obj->register_ip)) {
			$fields .= "register_ip,";
			$values .= "'".$mysqli->escape_str($obj->register_ip)."',";
		}
		
		if(!empty($obj->status_id)) {
			$fields .= "status_id,";
			$values .= $mysqli->escape_str($obj->status_id).",";
		}

		if(!empty($obj->reg_module_id)) {
			$fields .= "reg_module_id,";
			$values .= $mysqli->escape_str($obj->reg_module_id).",";
		}
		
		if(!empty($obj->reg_module_details_id)) {
			$fields .= "reg_module_details_id,";
			$values .= $mysqli->escape_str($obj->reg_module_details_id).",";
		}
		
		if(!empty($obj->delivery_email)) {
		    $fields .= "delivery_email,";
		    $values .= "'".$mysqli->escape_str($obj->delivery_email)."',";
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


		$query = "INSERT INTO ".$Tables['client'].$fields.$values;

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
 * Get Client Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID', 'TITLE' or 'ID', default is 'ID'
 * @return object of Client Class
 */
function cug_get_client($item, $item_type="ID")
{
global $mysqli, $Tables, $FILE_SERVER_URL;

	if(!empty($item)) {

		if($item_type == "ID") {
			$query = "SELECT * FROM ".$Tables['client']." WHERE id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$query = "SELECT * FROM ".$Tables['client']." WHERE uniqid='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "TITLE") {
			$query = "SELECT * FROM ".$Tables['client']." WHERE title='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}

		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__client();
					
				$obj->id				= $arr['id'];
				$obj->title				= $arr['title'];
				$obj->dba				= $arr['dba'];
				$obj->parent_id			= $arr['parent_id'];
				$obj->level				= $arr['level'];
				$obj->country_id		= $arr['country_id'];
				$obj->state				= $arr['state'];
				$obj->city				= $arr['city'];
				$obj->addr				= $arr['addr'];
				$obj->url				= $arr['url'];
				$obj->zip_code			= $arr['zip_code'];
				$obj->tel				= $arr['tel'];
				$obj->fax				= $arr['fax'];
				$obj->email				= $arr['email'];
				$obj->admin_name		= $arr['admin_name'];
				$obj->admin_email		= $arr['admin_email'];
				$obj->admin_phone		= $arr['admin_phone'];
				$obj->acc_name			= $arr['acc_name'];
				$obj->acc_email			= $arr['acc_email'];
				$obj->acc_phone			= $arr['acc_phone'];
				$obj->tech_name			= $arr['tech_name'];
				$obj->tech_email		= $arr['tech_email'];
				$obj->tech_phone		= $arr['tech_phone'];
				$obj->register_date		= $arr['register_date'];
				$obj->register_ip		= $arr['register_ip'];
				$obj->status_id			= $arr['status_id'];
				$obj->reg_module_id		= $arr['reg_module_id'];
				$obj->reg_module_details_id	= $arr['reg_module_details_id'];
				$obj->delivery_email	= $arr['delivery_email'];
				$obj->uniqid			= $arr['uniqid'];
				$obj->update_time		= $arr['update_time'];
					
				$img_path = !empty($obj->img_path) ? $obj->img_path : $FILE_SERVER_URL;
				//$img_path = !empty($obj->img_path) ? cug_get_url_protocol()."://".$obj->img_path : $FILE_SERVER_URL;
				
				$obj->img_34 = $img_path."/?o=client&i=".$obj->id."&s=34";
				$obj->img_64 = $img_path."/?o=client&i=".$obj->id."&s=64";
				$obj->img_174 = $img_path."/?o=client&i=".$obj->id."&s=174";
				$obj->img_300 = $img_path."/?o=client&i=".$obj->id."&s=300";
				$obj->img_600 = $img_path."/?o=client&i=".$obj->id."&s=600";
				$obj->img_orgn = $img_path."/?o=client&i=".$obj->id."&s=mega";
				
				$obj->img_34_num = $arr['img_34'];
				$obj->img_64_num = $arr['img_64'];
				$obj->img_174_num = $arr['img_174'];
				$obj->img_300_num = $arr['img_300'];
				$obj->img_600_num = $arr['img_600'];
				$obj->img_orgn_num = $arr['img_orgn'];
				
				/*
				if($arr['img_34'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', '34', $img_path);
					$obj->img_34 = $file_info['url'];
				}
				else { 
					$obj->img_34 = "";
				}
				//-------
				if($arr['img_64'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', '64', $img_path);
					$obj->img_64 = $file_info['url'];
				}
				else {
					$obj->img_64 = "";
				}
				//-------
				if($arr['img_174'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', '174', $img_path);
					$obj->img_174 = $file_info['url'];
				}
				else {
					$obj->img_174 = "";
				}
				//-------
				if($arr['img_300'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', '300', $img_path);
					$obj->img_300 = $file_info['url'];
				}
				else {
					$obj->img_300 = "";
				}
				//-------
				if($arr['img_600'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', '600', $img_path);
					$obj->img_600 = $file_info['url'];
				}
				else {
					$obj->img_600 = "";
				}
				//-------
				if($arr['img_orgn'] > 0) {
					$file_info = cug_get_obj_file_info($arr['id'], 'CLIENT', 'mega', $img_path);
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
 * Get Clients
 *
 * @param string
 * @param integer (default is 1)
 * @param string ('ID', 'TITLE' default is 'TITLE')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @return array
 */
function cug_get_clients($client_title, $limit=1, $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	//-------------------------
	if(strlen($client_title) == 1)
		$search_criteria = $mysqli->escape_str($client_title)."%";
	elseif(strlen($client_title) > 1)
	$search_criteria = "%".$mysqli->escape_str($client_title)."%";
	else
		return $result;
	//-------------------------
	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";
	//-------------------------


	$r = $mysqli->query("SELECT * FROM ".$Tables['client']." WHERE title LIKE '$search_criteria' ORDER BY ".$field." ".$sort_type." LIMIT $limit");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}

/**
 * Edit Existing Client
 *
 * @param integer
 * @param object of Client Class
 * @return bool
 */
function cug_edit_client($client_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $client_id > 0) {

		if(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		if(!empty($obj->dba)) $fields .= "dba='".$mysqli->escape_str($obj->dba)."',";
		if(!empty($obj->level)) $fields .= "level=".$mysqli->escape_str($obj->level).",";
		if(!empty($obj->country_id)) $fields .= "country_id=".$mysqli->escape_str($obj->country_id).",";
		if(!empty($obj->state)) $fields .= "state='".$mysqli->escape_str($obj->state)."',";
		if(!empty($obj->city)) $fields .= "city='".$mysqli->escape_str($obj->city)."',";
		if(!empty($obj->addr)) $fields .= "addr='".$mysqli->escape_str($obj->addr)."',";
		if(!empty($obj->url)) $fields .= "url='".$mysqli->escape_str($obj->url)."',";
		if(!empty($obj->zip_code)) $fields .= "zip_code='".$mysqli->escape_str($obj->zip_code)."',";
		if(!empty($obj->tel)) $fields .= "tel='".$mysqli->escape_str($obj->tel)."',";
		if(!empty($obj->fax)) $fields .= "fax='".$mysqli->escape_str($obj->fax)."',";
		if(!empty($obj->email)) $fields .= "email='".$mysqli->escape_str($obj->email)."',";
		if(!empty($obj->admin_name)) $fields .= "admin_name='".$mysqli->escape_str($obj->admin_name)."',";
		if(!empty($obj->admin_email)) $fields .= "admin_email='".$mysqli->escape_str($obj->admin_email)."',";
		if(!empty($obj->admin_phone)) $fields .= "admin_phone='".$mysqli->escape_str($obj->admin_phone)."',";
		if(!empty($obj->acc_name)) $fields .= "acc_name='".$mysqli->escape_str($obj->acc_name)."',";
		if(!empty($obj->acc_email)) $fields .= "acc_email='".$mysqli->escape_str($obj->acc_email)."',";
		if(!empty($obj->acc_phone)) $fields .= "acc_phone='".$mysqli->escape_str($obj->acc_phone)."',";
		if(!empty($obj->tech_name)) $fields .= "tech_name='".$mysqli->escape_str($obj->tech_name)."',";
		if(!empty($obj->tech_email)) $fields .= "tech_email='".$mysqli->escape_str($obj->tech_email)."',";
		if(!empty($obj->tech_phone)) $fields .= "tech_phone='".$mysqli->escape_str($obj->tech_phone)."',";
		if(!empty($obj->register_date)) $fields .= "register_date='".$mysqli->escape_str($obj->register_date)."',";
		if(!empty($obj->register_ip)) $fields .= "register_ip='".$mysqli->escape_str($obj->register_ip)."',";
		if(!empty($obj->status_id)) $fields .= "status_id=".$mysqli->escape_str($obj->status_id).",";
		if(!empty($obj->reg_module_id)) $fields .= "reg_module_id=".$mysqli->escape_str($obj->reg_module_id).",";
		if(!empty($obj->reg_module_details_id)) $fields .= "reg_module_details_id=".$mysqli->escape_str($obj->reg_module_details_id).",";
		
		if(!empty($obj->img_path)) $fields .= "img_path='".$mysqli->escape_str($obj->img_path)."',";
		if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= "img_34=".$mysqli->escape_str($obj->img_34).",";
		if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= "img_64=".$mysqli->escape_str($obj->img_64).",";
		if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= "img_174=".$mysqli->escape_str($obj->img_174).",";
		if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= "img_300=".$mysqli->escape_str($obj->img_300).",";
		if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= "img_600=".$mysqli->escape_str($obj->img_600).",";
		if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= "img_orgn=".$mysqli->escape_str($obj->img_orgn).",";
		
		if($obj->parent_id >= 0) $fields .= "parent_id=".$mysqli->escape_str($obj->parent_id).",";

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['client']." SET ".$fields." WHERE id=".$client_id;

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
 * Get List of Client IDs related to Tracks
 *
 * @return array
 */
function cug_get_track_client_list()
{
global $mysqli, $Tables;
$result = array();
$index = 0;

$r = $mysqli->query("CALL get_track_client_list()");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}
	
	if($mysqli->more_results())
		$mysqli->next_result();

return 	$result;
}


/**
 * Get List of Client IDs related to Albums
 *
 * @return array
 */
function cug_get_album_client_list()
{
global $mysqli, $Tables;
$result = array();
$index = 0;

$r = $mysqli->query("CALL get_album_client_list()");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

	if($mysqli->more_results())
		$mysqli->next_result();

return 	$result;
}


/**
 * Get List of Client IDs related to Members
 *
 * @return array
 */
function cug_get_member_client_list()
{
global $mysqli, $Tables;
$result = array();
$index = 0;

$r = $mysqli->query("CALL get_member_client_list()");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

	if($mysqli->more_results())
		$mysqli->next_result();

return 	$result;
}


/**
 * Get Publishers of the Track
 * @param	integer
 * @return array
 */
function cug_get_track_publishers($track_id)
{
global $mysqli, $Tables;
$result = array();
$index = 0;


	if(!empty($track_id)) {

		$r = $mysqli->query("CALL get_track_publishers($track_id)");
	
		if($r->num_rows) {
			while($arr = $r->fetch_assoc()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	
		if($mysqli->more_results())
			$mysqli->next_result();
	}

return 	$result;
}


/**
 * Delete Client - Just only if it is not related to any Object
 *
 * @param int
 * @param int (Default is 0)
 * @return int
 */
function cug_del_client($client_id, $user_id=0)
{
global $mysqli, $Tables;

	if($client_id > 0) {
		$obj = new cug__track_client_rel();
		$obj->client_id = $client_id;
		$obj->licensor_id = $client_id;
		$obj->licensee_id = $client_id;
		$obj->copyright_p = $client_id;
		
		$track_client_rel = cug_get_track_client_rel($obj, true);
		unset($obj);
			
		if(count($track_client_rel) == 0) {//if client is not related to any track
			$album_client_rel = cug_get_album_client_rel($client_id, "CLIENT");
			$album_licensee_rel = cug_get_album_client_rel($client_id, "LICENSEE");

			if(count($album_client_rel) == 0 && count($album_licensee_rel) == 0) {//if client is not related to any album
				$query = "SELECT id FROM ".$Tables['album']." WHERE copyright_c=$client_id OR copyright_p=$client_id OR register_from=$client_id";
				$r = $mysqli->query($query);
					if(!$r->num_rows) {//if client is not 'copyright_c', copyright_p' or 'register_from' for any album
						$query = "SELECT id FROM ".$Tables['track']." WHERE register_from=$client_id";
						$r1 = $mysqli->query($query);
							
							if(!$r1->num_rows) {//if client is not 'register_from' for any track
								
								$track_publisher_rel = cug_get_track_publisher_rel($client_id, "PUBLISHER");
								if(count($track_publisher_rel) == 0) {//if client is not related as publisher to any track
								
									//delete client_business_type_rel
									cug_del_client_business_type_rel($id=0, $client_id, $business_type=0, $uniqid="");
									
									//delete client_type_rel
									cug_del_client_type_rel($id=0, $client_id, $type_id=0);
									
									//delete client_wm2
									cug_del_client_wm2($id=0, $client_id, $wm2_code=0, $uniqid="");
									
									//delete client
									$mysqli->query("DELETE FROM ".$Tables['client']." WHERE id=$client_id");
									
									//register log
									$obj = new cug__log_te();
									$obj->action_id = 4; //Delete
									$obj->subaction_id = 0;
									$obj->object_id = 8; //Client
									$obj->object_item_id = $client_id;
									
									$arr = cug_get_logs_te($obj);
									
										if(count($arr) == 0) {
											$obj->subitem_id = 0;
											$obj->user_id = $user_id;
											$obj->country_id = cug_get_country_id_by_ip($_SERVER['REMOTE_ADDR']);
											$obj->ip = $_SERVER['REMOTE_ADDR'];
											$obj->session_id = session_id();
											$obj->start_time = @date("Y-m-d H:i:s");
											$obj->end_time = @date("Y-m-d H:i:s");
											
											cug_reg_log_te($obj);
										}
									
									unset($obj);	
									return 1; //OK
								}
								else {
									return -5; // can't delete, client is related as publisher to some track
								}
							}
							else {
								return -4; // can't delete, client is 'register_from' for some track
							}
					}
					else {
						return -3; // can't delete, client is 'copyright_c', copyright_p' or 'register_from' for some album
					}		
			}
			else {
				return -2; // can't delete, client is rerlated to some album
			}
		}
		else {
			return -1; // can't delete, client is rerlated to some track
		}
	}
	else {
		return 0; // can't delete, no client_id
	}

}


/**
 * Delete Client-BusinessType Relations
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return bool
 */
function cug_del_client_business_type_rel($id, $client_id=0, $business_type=0, $uniqid="")
{
global $mysqli, $Tables;
$where = "";


	if($id > 0) {
		$where = "id=$id";
	}
	elseif($uniqid) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if($client_id>0 && $business_type>0) {
			$where = "client_id=$client_id AND business_type=$business_type";
		}
		elseif($client_id > 0) {
			$where = "client_id=$client_id";
		}
		elseif($business_type > 0) {
			$where = "business_type=$business_type";
		}
	}
	//-----------------------
	
	if(strlen($where) > 0) {
		$query = "DELETE FROM ".$Tables['client_businesstype_rel']." WHERE $where";
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
 * Delete Client-Type Relations
 *
 * @param integer
 * @param integer
 * @param integer
 * @return bool
 */
function cug_del_client_type_rel($id, $client_id=0, $type_id=0)
{
global $mysqli, $Tables;
$where = "";


	if($id > 0) {
		$where = "id=$id";
	}
	else {
		if($client_id>0 && $type_id>0) {
			$where = "client_id=$client_id AND type_id=$type_id";
		}
		elseif($client_id > 0) {
			$where = "client_id=$client_id";
		}
		elseif($type_id > 0) {
			$where = "type_id=$type_id";
		}
	}
	//-----------------------

	if(strlen($where) > 0) {
		$query = "DELETE FROM ".$Tables['client_type_rel']." WHERE $where";
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
 * Delete Client-WM2
 *
 * @param integer
 * @param integer
 * @param integer
 * @return bool
 */
function cug_del_client_wm2($id, $client_id=0, $wm2_code=0)
{
global $mysqli, $Tables;
$where = "";


	if($id > 0) {
		$where = "id=$id";
	}
	else {
		if($client_id>0 && $wm2_code>0) {
			$where = "client_id=$client_id AND wm2_code=$wm2_code";
		}
		elseif($client_id > 0) {
			$where = "client_id=$client_id";
		}
		elseif($wm2_code > 0) {
			$where = "wm2_code=$wm2_code";
		}
	}
	//-----------------------

	if(strlen($where) > 0) {
		$query = "DELETE FROM ".$Tables['client_wm2']." WHERE $where";
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
 * Get Client ID by Client Title
 *
 * @param int $client_title
 * @param string $insert_new_client (default: false)
 * @return number
 */
function cug_get_client_id_by_title($client_title, $insert_new_client=false) {
	$client_id = 0;

	if($client_title) {
		$client = cug_get_client($client_title, "TITLE");

		if($client != null)
			$client_id = $client->id;
		elseif($insert_new_client) {
			$new_client = new cug__client();
			$new_client->title = $client_title;
			$new_client->register_ip = $_SERVER['REMOTE_ADDR'];
			$new_client->register_date = date("Y-m-d H:i:s");
			
			$client_id = cug_reg_client($new_client);
		}
	}

	return $client_id;
}


/**
 * Get Client Title by ID
 *
 * @param int $client_id
 * @return string
 */
function cug_get_client_title($client_id) {
	global $mysqli, $Tables;
	$client_title = "";

	if($client_id > 0) {
		$query = "SELECT title FROM ".$Tables['client']." WHERE id=".$mysqli->escape_str($client_id);
		$r = $mysqli->query($query);
		
			if($r->num_rows) {
				$row = $r->fetch_array();
				$client_title = $row['title'];
			}
	}

	return $client_title;
}


/**
 * Get WM1 Info
 * 
 * @param int $wm1_code
 * @return array
 */
function cug_get_wm1_info($wm1_code) {
	$result = array();
	
	if($wm1_code > 0) {
		$obj = new cug__track_wm1();
		$obj->wm1_code = $wm1_code;
		$arr = cug_get_track_wm1($obj);
		
		if(count($arr) > 0) {
			$wm1_client_id = !empty($arr[0]['client_id']) ? $arr[0]['client_id'] : 0;
			$client_obj = cug_get_client($wm1_client_id);
				
			$result['owner_info']['owner_id'] 		= $wm1_client_id;
			$result['owner_info']['owner_title'] 	= !empty($client_obj->title) ? $client_obj->title : "";
			$result['owner_info']['owner_url'] 		= !empty($client_obj->url) ? $client_obj->url : "";
			$result['owner_info']['owner_logo_url'] = !empty($client_obj->img_64) ? $client_obj->img_64 : "";						
			
			$result['wm_status'] = !empty($arr[0]['wm_status']) ? $arr[0]['wm_status'] : 0;
			$result['licensee_id'] = !empty($arr[0]['licensee_id']) ? $arr[0]['licensee_id'] : 0;
			$result['track_album_rel_id'] = !empty($arr[0]['track_album_rel_id']) ? $arr[0]['track_album_rel_id'] : 0;	

			
			$arr = cug_get_track_album_rel($result['track_album_rel_id'], $item_type="ID");
			
			$result['album_id'] = !empty($arr[0]['album_id']) ? $arr[0]['album_id'] : 0;
			$result['track_id'] = !empty($arr[0]['track_id']) ? $arr[0]['track_id'] : 0;
			$result['disc_num'] = !empty($arr[0]['disc_num']) ? $arr[0]['disc_num'] : 0;
			$result['track_num'] = !empty($arr[0]['track_num']) ? $arr[0]['track_num'] : 0;
		}
	}
	
	return $result;
}


/**
 * Get WM2 Info
 * 
 * @param int $wm2_code
 * @return array
 */
function cug_get_wm2_info($wm2_code) {
	$result = array();

	if($wm2_code > 0) {
		$arr = cug_get_client_wm2($wm2_code, $item_type="WM2");

		if(count($arr) > 0) {
			$wm2_client_id = $arr[0]['client_id'];
			$client_obj = cug_get_client($wm2_client_id);
			
			$result['owner_info']['owner_id'] 		= $wm2_client_id;
			$result['owner_info']['owner_title'] 	= !empty($client_obj->title) ? $client_obj->title : "";
			$result['owner_info']['owner_url'] 		= !empty($client_obj->url) ? $client_obj->url : "";
			$result['owner_info']['owner_logo_url'] = !empty($client_obj->img_64) ? $client_obj->img_64 : "";
			
			$result['wm_status'] = !empty($arr[0]['wm_status']) ? $arr[0]['wm_status'] : 0;
		}
	}

	return $result;
}


/**
 * Get Owners (Client IDs) of the Objects
 * 
 * @param string $object ('ALBUM', 'TRACK', or 'MEMBER')
 * @param array $object_ids
 * @return array
 */
function cug_get_owners_of_objects($object, $object_ids) {
    global $mysqli, $Tables;
    $result = array();
    $table = "";
    
    if(count($object_ids) > 0) {
        switch(strtoupper($object)) {
            //--------------------
            case 'ALBUM':
                $table = $Tables['album'];
                break;
                //--------------------
            case 'TRACK':
                $table = $Tables['track'];
                break;
                //--------------------
            case 'MEMBER':
                $table = $Tables['member'];
                break;
                //--------------------
        }
        
        if($table) {
            $where_in = "";
            foreach($object_ids as $val) {
                $where_in .= $val.",";
            }
            $where_in = rtrim($where_in, ",");
        
            $query = "SELECT DISTINCT(register_from) FROM $table WHERE id IN($where_in)";
            $r = $mysqli->query($query);
            if($r && $r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $result[] = $row['register_from'];
                }
            }
        } 
    }
        
    return $result;
}
?>