<?PHP

/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Member
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Member Class
 *
 * @param	title - (STRING)
 * @param	alias - (STRING)
 * @param	used_name - INT (1 - title;  2 - alias;)
 * @param	member_type_id - (INT)
 * @param	standard_role_id - (INT)
 * @param	gender_id - (INT)
 * @param	genre_id - (INT)
 * @param	tag_status_id - (INT)
 * @param	birth_date - STRING (MySQL DATE Format)
 * @param	birth_country_id - (INT)
 * @param	birth_place - (STRING)
 * @param	death_date - STRING (MySQL DATE Format)
 * @param	death_country_id - (INT)
 * @param	death_place - (STRING)
 * @param	img_path - (STRING)
 * @param	img_34 (INT)
 * @param	img_64 (INT)
 * @param	img_174 (INT)
 * @param	img_300 (INT)
 * @param	img_600 (INT)
 * @param	img_orgn (INT - Size of the Original Image File)
 * @param	bio_url (STRING)
 * @param	fan_url (STRING)
 * @param	member_url (STRING)
 * @param	worktime_from - STRING (MySQL DATE Format)
 * @param	worktime_to - STRING (MySQL DATE Format)
 * @param	trash_status (INT)
 * @param	register_from (INT)
 * @param	register_date STRING (MySQL DATE Format) 
 * @param	register_ip (STRING)
 * @param	online (STRING)
 * @param	uniqid - (STRING)
 * @param	external_id (STRING)
 * @param	shenzhen_id (INT)
 * @param	update_time - STRING (MySQL TIMESTAMP Format)
 */
class cug__member
{
public $id;
public $title;
public $alias;
public $used_name;
public $member_type_id;
public $standard_role_id;
public $gender_id;
public $genre_id;
public $tag_status_id;
public $birth_date;
public $birth_country_id;
public $birth_place;
public $death_date;
public $death_country_id;
public $death_place;
public $img_path;
public $img_34;
public $img_64;
public $img_174;
public $img_300;
public $img_600;
public $img_orgn;
public $bio_url;
public $fan_url;
public $member_url;
public $worktime_from;
public $worktime_to;
public $trash_status;
public $register_from;
public $register_date;
public $register_ip;
public $online;
public $uniqid;
public $external_id;
public $shenzhen_id;
public $update_time;
}


/**
 * Get Member Type List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_member_type_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";
	
	
$r = $mysqli->query("SELECT * FROM ".$Tables['member_type']." ORDER BY ".$field." ".$sort_type);
	
	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}	
	}

return $result;	
}



/**
 * Get Member Type (ID or TITLE)
 * 
 * @param mixed (integer or string) 
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
 function cug_get_member_type($item, $item_type="ID")
{
global $mysqli, $Tables;	
	
	 if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['member_type'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['member_type'], "title", "id=".$mysqli->escape_str($item));
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
 * Register New Member Type
 *
 * @param string
 * @return integer
 */
function cug_reg_member_type($title)
{
global $mysqli, $Tables;	

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['member_type'], "id", "title='".$mysqli->escape_str($title)."'");
			
			if(empty($result[0]['id'])) {
				
				if($mysqli->query("INSERT INTO ".$Tables['member_type']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing Member Type
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_member_type($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['member_type']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Get Member Role List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_member_role_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


$r = $mysqli->query("SELECT * FROM ".$Tables['member_role']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}	
	}

return $result;
}


/**
 * Get Member Role (ID or TITLE)
 *
 * @param mixed (integer or string) 
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @return mixed (integer or string)
 */
function cug_get_member_role($item, $item_type="ID")
{
global $mysqli, $Tables;

	if(!empty($item))
	{
		if($item_type == "TITLE") {
			$result = $mysqli->get_field_val($Tables['member_role'], "id", "title='".$mysqli->escape_str($item)."'");
		}
		elseif($item_type == "ID") {
			$result = $mysqli->get_field_val($Tables['member_role'], "title", "id=".$mysqli->escape_str($item));
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
 * Register New Member Role
 *
 * @param string
 * @return integer
 */
function cug_reg_member_role($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['member_role'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['member_role']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
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
 * Edit Existing Member Role
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_member_role($id, $new_title)
{
global $mysqli, $Tables;

	if(!empty($id) && !empty($new_title)) {
		if($mysqli->query("UPDATE ".$Tables['member_role']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
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
 * Register New Member
 *
 * @param object of member Class
 * @return integer
 */
function cug_reg_member($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->title)) {
				
		$fields = " (title,";
		$values = " VALUES('".$mysqli->escape_str($obj->title)."',";
		
		if(!empty($obj->alias)) {
			$fields .= "alias,";
			$values .= "'".$mysqli->escape_str($obj->alias)."',";
		}
		
		if(!empty($obj->used_name)) {
			$fields .= "used_name,";
			$values .= $mysqli->escape_str($obj->used_name).",";			
		}
		
		if(!empty($obj->member_type_id)) {
			$fields .= "member_type_id,";
			$values .= $mysqli->escape_str($obj->member_type_id).",";
		}
		
		if(!empty($obj->standard_role_id)) {
			$fields .= "standard_role_id,";
			$values .= $mysqli->escape_str($obj->standard_role_id).",";
		}		

		if(!empty($obj->gender_id)) {
			$fields .= "gender_id,";
			$values .= $mysqli->escape_str($obj->gender_id).",";
		}		
		
		if(!empty($obj->birth_date)) {
			$fields .= "birth_date,";
			$values .= "'".$mysqli->escape_str($obj->birth_date)."',";
		}

		if(!empty($obj->birth_country_id)) {
			$fields .= "birth_country_id,";
			$values .= $mysqli->escape_str($obj->birth_country_id).",";
		}

		if(!empty($obj->birth_place)) {
			$fields .= "birth_place,";
			$values .= "'".$mysqli->escape_str($obj->birth_place)."',";
		}

		if(!empty($obj->death_date)) {
			$fields .= "death_date,";
			$values .= "'".$mysqli->escape_str($obj->death_date)."',";
		}

		if(!empty($obj->death_country_id)) {
			$fields .= "death_country_id,";
			$values .= $mysqli->escape_str($obj->death_country_id).",";
		}

		if(!empty($obj->death_place)) {
			$fields .= "death_place,";
			$values .= "'".$mysqli->escape_str($obj->death_place)."',";
		}

		if(!empty($obj->bio_url)) {
			$fields .= "bio_url,";
			$values .= "'".$mysqli->escape_str($obj->bio_url)."',";
		}

		if(!empty($obj->fan_url)) {
			$fields .= "fan_url,";
			$values .= "'".$mysqli->escape_str($obj->fan_url)."',";
		}
		
		if(!empty($obj->member_url)) {
			$fields .= "member_url,";
			$values .= "'".$mysqli->escape_str($obj->member_url)."',";
		}

		if(!empty($obj->worktime_from)) {
			$fields .= "worktime_from,";
			$values .= "'".$mysqli->escape_str($obj->worktime_from)."',";
		}

		if(!empty($obj->worktime_to)) {
			$fields .= "worktime_to,";
			$values .= "'".$mysqli->escape_str($obj->worktime_to)."',";
		}
		
		$fields .= "tag_status_id,";
		if(!empty($obj->tag_status_id)) {
			$values .= $mysqli->escape_str($obj->tag_status_id).",";
		}
		else {
			$values .= "1,"; // Unchecked
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
		
		if($obj->trash_status != null && $obj->trash_status >= 0) {
			$fields .= "trash_status,";
			$values .= $obj->trash_status.",";
		}		

		if(!empty($obj->external_id)) {
			$fields .= "external_id,";
			$values .= "'".$mysqli->escape_str($obj->external_id)."',";
		}
		
		if(!empty($obj->shenzhen_id)) {
		    $fields .= "shenzhen_id,";
		    $values .= $mysqli->escape_str($obj->shenzhen_id).",";
		}
		
		if(!empty($obj->register_from)) {
			$fields .= "register_from,";
			$values .= $mysqli->escape_str($obj->register_from).",";
		}
		
		if(!empty($obj->online)) {
			$fields .= "online,";
			$values .= $mysqli->escape_str($obj->online).",";
		}		
		
		if(!empty($obj->register_date)) {
			$fields .= "register_date,";
			$values .= "'".$mysqli->escape_str($obj->register_date)."',";
		}
		
		if(!empty($obj->register_ip)) {
			$fields .= "register_ip,";
			$values .= "'".$mysqli->escape_str($obj->register_ip)."',";
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
		
		
		$query = "INSERT INTO ".$Tables['member'].$fields.$values;
		//echo PHP_EOL.$query.PHP_EOL;
		
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
 * Get Member Info
 *
 * @param mixed (integer or string)
 * @param string -> 'UNIQID', 'TITLE' or 'ID', default is 'ID'
 * @return object of cug__member Class
 */
function cug_get_member($item, $item_type="ID")
{
global $mysqli, $Tables, $FILE_SERVER_URL;


	if(!empty($item)) {
		
		if($item_type == "ID") {
			$query = "SELECT * FROM ".$Tables['member']." WHERE id=".$mysqli->escape_str($item);
		}
		elseif($item_type == "UNIQID") {
			$query = "SELECT * FROM ".$Tables['member']." WHERE uniqid='".$mysqli->escape_str($item)."'";
		}
		elseif($item_type == "TITLE") {
			$query = "SELECT * FROM ".$Tables['member']." WHERE title='".$mysqli->escape_str($item)."'";
		}
		else {
			return NULL;
		}
		
		$r = $mysqli->query($query);
			if($r) {
				
				$arr = $r->fetch_array();
				if($arr) {
					
					$obj = new cug__member();
					
					$obj->id					= $arr['id'];
					$obj->title					= $arr['title'];
					$obj->alias					= $arr['alias'];
					$obj->used_name				= $arr['used_name'];
					$obj->member_type_id		= $arr['member_type_id'];
					$obj->standard_role_id		= $arr['standard_role_id'];
					$obj->gender_id				= $arr['gender_id'];
					$obj->genre_id				= $arr['genre_id'];
					$obj->tag_status_id			= $arr['tag_status_id'];
					$obj->birth_date			= $arr['birth_date'];
					$obj->birth_country_id		= $arr['birth_country_id'];
					$obj->birth_place			= $arr['birth_place'];
					$obj->death_date			= $arr['death_date'];
					$obj->death_country_id		= $arr['death_country_id'];
					$obj->death_place			= $arr['death_place'];
					$obj->bio_url				= $arr['bio_url'];
					$obj->fan_url				= $arr['fan_url'];
					$obj->member_url			= $arr['member_url'];
					$obj->worktime_from			= $arr['worktime_from'];
					$obj->worktime_to			= $arr['worktime_to'];
					$obj->img_path				= $arr['img_path'];
					$obj->trash_status			= $arr['trash_status'];
					$obj->register_from			= $arr['register_from'];
					$obj->online				= $arr['online'];
					$obj->register_date			= $arr['register_date'];
					$obj->register_ip			= $arr['register_ip'];					
					$obj->uniqid				= $arr['uniqid'];
					$obj->external_id			= $arr['external_id'];
					$obj->shenzhen_id			= $arr['shenzhen_id'];
					$obj->update_time			= $arr['update_time'];

										
					$img_path = !empty($obj->img_path) ? $obj->img_path : $FILE_SERVER_URL;
					//$img_path = !empty($obj->img_path) ? cug_get_url_protocol()."://".$obj->img_path : $FILE_SERVER_URL;
						
					$obj->img_34 = $img_path."/?o=member&i=".$obj->id."&s=34&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					$obj->img_64 = $img_path."/?o=member&i=".$obj->id."&s=64&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					$obj->img_174 = $img_path."/?o=member&i=".$obj->id."&s=174&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					$obj->img_300 = $img_path."/?o=member&i=".$obj->id."&s=300&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					$obj->img_600 = $img_path."/?o=member&i=".$obj->id."&s=600&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					$obj->img_orgn = $img_path."/?o=member&i=".$obj->id."&s=mega&mt=".$obj->member_type_id."&mg=".$obj->gender_id;
					
					$obj->img_34_num = $arr['img_34'];
					$obj->img_64_num = $arr['img_64'];
					$obj->img_174_num = $arr['img_174'];
					$obj->img_300_num = $arr['img_300'];
					$obj->img_600_num = $arr['img_600'];
					$obj->img_orgn_num = $arr['img_orgn'];
					
					/*
					if($arr['img_34'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', '34', $img_path);
						$obj->img_34 = $file_info['url'];
					}
					else { 
						$obj->img_34 = "";
					}
					//-------
					if($arr['img_64'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', '64', $img_path);
						$obj->img_64 = $file_info['url'];
					}
					else {
						$obj->img_64 = "";
					}
					//-------
					if($arr['img_174'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', '174', $img_path);
						$obj->img_174 = $file_info['url'];
					}
					else {
						$obj->img_174 = "";
					}
					//-------
					if($arr['img_300'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', '300', $img_path);
						$obj->img_300 = $file_info['url'];
					}
					else {
						$obj->img_300 = "";
					}
					//-------
					if($arr['img_600'] == 1) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', '600', $img_path);
						$obj->img_600 = $file_info['url'];
					}
					else {
						$obj->img_600 = "";
					}
					//-------
					if($arr['img_orgn'] > 0) {
						$file_info = cug_get_obj_file_info($arr['id'], 'MEMBER', 'mega', $img_path);
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
 * Get Members
 *
 * @param string
 * @param integer (default is 1)
 * @param string ('ID', 'TITLE' default is 'TITLE')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @return array
 */
function cug_get_members($member_title, $limit=1, $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

$search_criteria_cleaned = $member_title;

	//-------------------------
	if(strlen($member_title) == 1)
		$search_criteria = $mysqli->escape_str($member_title)."%";
	elseif(strlen($member_title) > 1)
		$search_criteria = "%".$mysqli->escape_str($member_title)."%";
	else 
		return $result;
	//-------------------------
	if($sort_by == "TITLE")
		$field = "FIELD(alias, '".$mysqli->escape_str($search_criteria_cleaned)."') DESC, FIELD(title, '".$mysqli->escape_str($search_criteria_cleaned)."') DESC, alias $sort_type, title $sort_type"; 
	else
		$field = "id $sort_type";
	//-------------------------		
	

	$r = $mysqli->query("SELECT * FROM ".$Tables['member']." WHERE title LIKE '$search_criteria' OR alias LIKE '$search_criteria' ORDER BY ".$field." LIMIT $limit");

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}



/**
 * Edit Existing Member
 *
 * @param array od Member IDs
 * @param object of Member Class
 * @param bool (Update empty fields or not, default is false)
 * @return bool
 */
function cug_edit_member($member_ids_arr, $obj, $update_empty_fields=false)
{
global $mysqli, $Tables;
$fields = "";
$where = "";

	if(count($member_ids_arr) > 0 && !empty($member_ids_arr[0])) {
		
		if(isset($obj->title)) {
			if($update_empty_fields && empty($obj->title)) $fields .= "title='',";
			elseif(!empty($obj->title)) $fields .= "title='".$mysqli->escape_str($obj->title)."',";
		}
		//------------------
		if(isset($obj->alias)) {
			if($update_empty_fields && empty($obj->alias)) $fields .= "alias=null,";
			elseif(!empty($obj->alias)) $fields .= "alias='".$mysqli->escape_str($obj->alias)."',";
		}
		//------------------
		if(isset($obj->used_name)) {
			if($update_empty_fields && empty($obj->used_name)) $fields .= "used_name=null,";
			elseif($obj->used_name != null && $obj->used_name >= 0) $fields .= "used_name=".$mysqli->escape_str($obj->used_name).",";
		}
		//------------------
		if(isset($obj->member_type_id)) {
			if($update_empty_fields && empty($obj->member_type_id)) $fields .= "member_type_id=null,";
			elseif($obj->member_type_id != null && $obj->member_type_id >= 0) $fields .= "member_type_id=".$mysqli->escape_str($obj->member_type_id).",";
		}
		//------------------
		if(isset($obj->standard_role_id)) {
			if($update_empty_fields && empty($obj->standard_role_id)) $fields .= "standard_role_id=null,";
			elseif($obj->standard_role_id != null && $obj->standard_role_id >= 0) $fields .= "standard_role_id=".$mysqli->escape_str($obj->standard_role_id).",";
		}
		//------------------
		if(isset($obj->gender_id)) {
			if($update_empty_fields && empty($obj->gender_id)) $fields .= "gender_id=0,";
			elseif($obj->gender_id != null && $obj->gender_id >= 0) $fields .= "gender_id=".$mysqli->escape_str($obj->gender_id).",";
		}
		//------------------
		if(isset($obj->birth_date)) {
			if($update_empty_fields && empty($obj->birth_date)) $fields .= "birth_date=null,";
			elseif(!empty($obj->birth_date)) $fields .= "birth_date='".$mysqli->escape_str($obj->birth_date)."',";
		}
		//------------------
		if(isset($obj->birth_country_id)) {
			if($update_empty_fields && empty($obj->birth_country_id)) $fields .= "birth_country_id=null,";
			elseif($obj->birth_country_id != null && $obj->birth_country_id >= 0) $fields .= "birth_country_id=".$mysqli->escape_str($obj->birth_country_id).",";
		}
		//------------------
		if(isset($obj->birth_place)) {
			if($update_empty_fields && empty($obj->birth_place)) $fields .= "birth_place=null,";
			elseif(!empty($obj->birth_place)) $fields .= "birth_place='".$mysqli->escape_str($obj->birth_place)."',";
		}
		//------------------
		if(isset($obj->death_date)) {
			if($update_empty_fields && empty($obj->death_date)) $fields .= "death_date=null,";
			elseif(!empty($obj->death_date)) $fields .= "death_date='".$mysqli->escape_str($obj->death_date)."',";
		}
		//------------------
		if(isset($obj->death_country_id)) {
			if($update_empty_fields && empty($obj->death_country_id)) $fields .= "death_country_id=null,";
			elseif($obj->death_country_id != null && $obj->death_country_id >= 0) $fields .= "death_country_id=".$mysqli->escape_str($obj->death_country_id).",";
		}
		//------------------
		if(isset($obj->death_place)) {
			if($update_empty_fields && empty($obj->death_place)) $fields .= "death_place=null,";
			elseif(!empty($obj->death_place)) $fields .= "death_place='".$mysqli->escape_str($obj->death_place)."',";
		}
		//------------------
		if(isset($obj->bio_url)) {
			if($update_empty_fields && empty($obj->bio_url)) $fields .= "bio_url=null,";
			elseif(!empty($obj->bio_url)) $fields .= "bio_url='".$mysqli->escape_str($obj->bio_url)."',";
		}
		//------------------
		if(isset($obj->fan_url)) {
			if($update_empty_fields && empty($obj->fan_url)) $fields .= "fan_url=null,";
			elseif(!empty($obj->fan_url)) $fields .= "fan_url='".$mysqli->escape_str($obj->fan_url)."',";
		}
		//------------------
		if(isset($obj->member_url)) {
			if($update_empty_fields && empty($obj->member_url)) $fields .= "member_url=null,";
			elseif(!empty($obj->member_url)) $fields .= "member_url='".$mysqli->escape_str($obj->member_url)."',";
		}
		//------------------
		if(isset($obj->worktime_from)) {
			if($update_empty_fields && empty($obj->worktime_from)) $fields .= "worktime_from=null,";
			elseif(!empty($obj->worktime_from)) $fields .= "worktime_from='".$mysqli->escape_str($obj->worktime_from)."',";
		}
		//------------------
		if(isset($obj->worktime_to)) {
			if($update_empty_fields && empty($obj->worktime_to)) $fields .= "worktime_to=null,";
			elseif(!empty($obj->worktime_to)) $fields .= "worktime_to='".$mysqli->escape_str($obj->worktime_to)."',";
		}

		
		if(!empty($obj->img_path)) $fields .= "img_path='".$mysqli->escape_str($obj->img_path)."',";
		if(!empty($obj->register_from)) $fields .= "register_from=".$mysqli->escape_str($obj->register_from).",";
		if(!empty($obj->online)) $fields .= "online=".$mysqli->escape_str($obj->online).",";
		if($obj->trash_status != null && $obj->trash_status >= 0) $fields .= "trash_status=".$mysqli->escape_str($obj->trash_status).",";
		if(!empty($obj->external_id)) $fields .= "external_id='".$mysqli->escape_str($obj->external_id)."',";
		if(!empty($obj->shenzhen_id)) $fields .= "shenzhen_id=".$mysqli->escape_str($obj->shenzhen_id).",";
		if(!empty($obj->tag_status_id)) $fields .= "tag_status_id=".$mysqli->escape_str($obj->tag_status_id).",";
		
		
		if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= "img_34=".$mysqli->escape_str($obj->img_34).",";
		if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= "img_64=".$mysqli->escape_str($obj->img_64).",";
		if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= "img_174=".$mysqli->escape_str($obj->img_174).",";
		if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= "img_300=".$mysqli->escape_str($obj->img_300).",";
		if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= "img_600=".$mysqli->escape_str($obj->img_600).",";
		if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= "img_orgn=".$mysqli->escape_str($obj->img_orgn).",";
		
		
			if(strlen($fields) > 0) {
				$fields = substr($fields, 0, strlen($fields)-1);
			
				for($i=0; $i<count($member_ids_arr); $i++) {
					if($member_ids_arr[$i] > 0) {
						$where .= "id=".$member_ids_arr[$i]." OR ";
					}	
				}
				//------------------
				if(strlen($where) > 0) {
					$where = substr($where, 0, strlen($where)-3);
					$query = "UPDATE ".$Tables['member']." SET ".$fields." WHERE $where";
						
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
 * Get Members of Track
 *
 * @param integer
 * @param integer (default is 0)
 * @param bool (default is false) 
 * @return array
 */
function cug_get_track_members($track_id, $role_id=0, $unique_members=false)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($track_id)) {

		$unique = ($unique_members) ? 1 : 0;
		$r = $mysqli->query("CALL get_track_members($track_id, $role_id, $unique)");
	
			if($r->num_rows) {
				while($arr = $r->fetch_assoc()) {
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
 * Get Members of Album
 *
 * @param integer
 * @param integer (default is 0)
 * @return array
 */
function cug_get_album_members($album_id, $role_id=0)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($album_id)) {

		$r = $mysqli->query("CALL get_album_members($album_id, $role_id)");

		if($r->num_rows) {
			while($arr = $r->fetch_assoc()) {
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
 * Get all unique Members of Album Tracks
 * 
 * @param int $album_id
 * @return array
 */
function cug_get_album_tracks_members($album_id) {
    global $mysqli, $Tables;
    $result = array();
    
    if($album_id > 0) {
        $query = "SELECT DISTINCT(m.id) AS member_id, m.title AS member_title, m.alias AS member_alias FROM {$Tables['track_album_rel']} AS tar ";
        $query .= "INNER JOIN {$Tables['track_member_rel']} AS tmr ON tar.track_id=tmr.track_id ";
        $query .= "INNER JOIN {$Tables['member']} AS m ON tmr.member_id=m.id ";
        $query .= "WHERE tar.album_id=$album_id";
        
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
 * Get Members of Composition
 *
 * @param integer
 * @param integer 
 * @param integer (default is 0)
 * @return array
 */
function cug_get_comp_members($track_id, $comp_id, $role_id=0)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if(!empty($track_id) || !empty($comp_id)) {


		$r = $mysqli->query("CALL get_comp_members($track_id, $comp_id, $role_id)");

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
 * Get Members list for TE
 *
 * @param string (default is '0')
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
 * @param string ('TITLE', 'ALIAS')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @param integer (default is 0)
 * @param integer (default is -1)
 * @return array
 */
function cug_get_member_list_te($register_from='0', $tag_status=0, $search_term="null", $is_member_title=0, $is_member_alias=0, $member_type_id=0, $standard_role_id=0, $gender_id=0, $user_id=0, $action_id=0, $object_id=0, $trash_status=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=30, $online=-1, $mbr_with_no_img=0, $is_member_id=0, $duplicate_group_id=0, $member_related_objects_owner=0)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;

	// First Query
	$query  = "CALL get_member_list_te('$register_from',$tag_status,";
	$query .= ($search_term=="null" || !$search_term) ? "null," : ((strlen($search_term) > 2) ? "'%".$mysqli->escape_str($search_term)."%'," : "'".$mysqli->escape_str($search_term)."%',");
	$query .= "$is_member_title,$is_member_alias,$member_type_id,$standard_role_id,$gender_id,";
	$query .= "$user_id,$action_id,$object_id,$trash_status,'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant,$mbr_with_no_img,$online,$is_member_id,$duplicate_group_id,$member_related_objects_owner);";

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
 * Get Member's related tracks
 *
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0-1)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param string ('TITLE', 'ID')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @param integer (default is 0)
 * @param integer (default is 30)
 * @return array
 */
function cug_get_member_related_tracks($register_from=0, $tag_status=0, $trash_status=-1, $member_id=0, $role_id=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=30)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;

	// First Query
	$query  = "CALL get_member_related_tracks($register_from,$tag_status,$trash_status,$member_id,$role_id,";
	$query .= "'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant);";

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
 * Get Member's related tracks numbers grouped by roles
 *
 * @param integer $member_id
 * @param array $role_ids_arr (Optional)
 * @param int $register_from (Optional)
 * @param int $label_category_id (Optional)
 * @return array
 */
function cug_get_member_related_tracks_nums($member_id, $role_ids_arr=array(), $register_from=0, $label_category_id=0)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if($member_id > 0) {
		//get total related tracks number
	    $query = "SELECT COUNT(DISTINCT tmr.track_id) AS quant FROM {$Tables['track_member_rel']} AS tmr ";
	    $query .= ($register_from > 0) ? "INNER JOIN {$Tables['track']} AS t ON tmr.track_id=t.id " : "";
	    $query .= ($register_from > 0 && $label_category_id > 0) ? "INNER JOIN {$Tables['track_album_rel']} AS tar ON tmr.track_id=tar.track_id " : "";
	    $query .= ($register_from > 0 && $label_category_id > 0) ? "INNER JOIN {$Tables['album_label_cat']} AS alc ON tar.album_id=alc.album_id " : "";
	    $query .= "WHERE tmr.member_id=$member_id ";
	    $query .= ($register_from > 0) ? "AND t.register_from=$register_from " : "";
	    $query .= ($register_from > 0 && $label_category_id > 0) ? "AND alc.label_cat_id=$label_category_id " : "";
	    $query .= "GROUP BY tmr.member_id";
	    
	    //execute query
		$r = $mysqli->query($query);
		
		if($r->num_rows) {
			$arr = $r->fetch_array();
			$result['total_tracks'] = !empty($arr['quant']) ? $arr['quant'] : 0;
		}
		else {
			$result['total_tracks'] = 0;
		}
		
		
		//get related tracks numbers grouped by role ids
		$query = "SELECT role_id, role_title, count(role_id) AS quant FROM (";
    		$query .= "SELECT tmr.role_id AS role_id, mr.title AS role_title, count(tmr.track_id) AS quant FROM ".$Tables['track_member_rel']." AS tmr ";
    		$query .= ($register_from > 0) ? "INNER JOIN {$Tables['track']} AS t ON tmr.track_id=t.id " : "";
    		$query .= ($register_from > 0 && $label_category_id > 0) ? "INNER JOIN {$Tables['track_album_rel']} AS tar ON tmr.track_id=tar.track_id " : "";
    		$query .= ($register_from > 0 && $label_category_id > 0) ? "INNER JOIN {$Tables['album_label_cat']} AS alc ON tar.album_id=alc.album_id " : "";
    		$query .= "LEFT JOIN {$Tables['member_role']} AS mr ON tmr.role_id=mr.id ";
    		$query .= "WHERE tmr.member_id=$member_id ";
    		$query .= ($register_from > 0) ? "AND t.register_from=$register_from " : "";
    		$query .= ($register_from > 0 && $label_category_id > 0) ? "AND alc.label_cat_id=$label_category_id " : "";
    		
    		
    		$where = "";
    			if(count($role_ids_arr) > 0) {				
    				foreach($role_ids_arr as $role_id) {
    					$where .= "tmr.role_id=$role_id OR ";
    				}
    				//----------------
    					if($where) {
    						$where = " AND (" . substr($where, 0, strlen($where)-4) . ") ";
    					}				
    			}
    			
    		$query .= $where." GROUP BY tmr.track_id"; 
    	$query .= ") AS t ";
    	$query .= "GROUP BY role_id";
		//echo $query;
		
		//execute query
		$r = $mysqli->query($query);
		
			if($r->num_rows) {
				while($arr = $r->fetch_array()) {
					$result['tracks'][$index]['role_id'] 	= $arr['role_id'];
					$result['tracks'][$index]['role_title'] = $arr['role_title'];
					$result['tracks'][$index]['tracks_num'] = $arr['quant'];
					$index ++;
				}
			}
			else {
				$result['tracks'] = array();
			}		
	}

return $result;
}


/**
 * Get Member's related albums
 *
 * @param integer
 * @param integet
 * @return array
 */
function cug_get_member_related_albums_ids($member_id, $limit=30, $register_from=0, $genre_id=0, $must_album_id=0, $online=-1, $label_category_id=0)
{
global $mysqli, $Tables;
$result = array();
$index = 0;
	
	if($member_id > 0) {
		
	    
	    //put $must_album_id on first position in the result array
		if($must_album_id) {
		    if($label_category_id > 0) {
		        if(cug_is_album_from_label_category($must_album_id, $label_category_id)){
		            $result[$index]['id'] = $must_album_id;
		            $index ++;
		        }
		    }
		    else {
		        $result[$index]['id'] = $must_album_id;
		        $index ++;
		    }
		}
		//--------------------
		
        //generate query
		$query = "SELECT amr.album_id FROM {$Tables['album_member']} AS amr ";
		$query .= "INNER JOIN {$Tables['album']} AS a ON amr.album_id=a.id ";
		$query .= ($label_category_id > 0) ? "INNER JOIN {$Tables['album_label_cat']} AS alc ON amr.album_id=alc.album_id " : "";
		$query .= "WHERE amr.member_id=$member_id ";
		$query .= ($register_from > 0) ? "AND a.register_from=$register_from " : "";
		$query .= ($label_category_id > 0) ? "AND alc.label_cat_id=$label_category_id " : "";
		$query .= ($genre_id > 0) ? "AND a.genre_id=$genre_id " : "";
		if($online >= 0) { $query .= ($online == 0) ? "AND (a.online=NULL OR a.online=0) " : "AND a.online=$online "; }
		$query .= ($must_album_id > 0) ? "AND amr.album_id<>$must_album_id " : "";
		$query .= "GROUP BY amr.album_id LIMIT 0, $limit";
		
		//execute query
		$r = $mysqli->query($query);
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index]['id'] = $arr['album_id'];
				$index ++;
			}
		}
		//echo $query;	
		//----------------------
		if(count($result) < $limit) {
		    //generate query
		    $query = "SELECT tar.album_id FROM {$Tables['track_member_rel']} AS tmr ";
		    $query .= "INNER JOIN {$Tables['track_album_rel']} AS tar ON tmr.track_id=tar.track_id ";
		    $query .= "INNER JOIN {$Tables['album']} AS a ON tar.album_id=a.id ";
		    $query .= ($label_category_id > 0) ? "INNER JOIN {$Tables['album_label_cat']} AS alc ON tar.album_id=alc.album_id " : "";
		    $query .= "WHERE tmr.member_id=$member_id ";
		    $query .= ($register_from > 0) ? "AND a.register_from=$register_from " : "";
		    $query .= ($label_category_id > 0) ? "AND alc.label_cat_id=$label_category_id " : "";
		    if($online >= 0) { $query .= ($online == 0) ? "AND (a.online=NULL OR a.online=0) " : "AND a.online=$online "; }
		    $query .= "GROUP BY tar.album_id";
		    
			
			//execute query
			$r = $mysqli->query($query);
			$index = count($result);
			
				if($r->num_rows) {
					while($arr = $r->fetch_array()) {
						if(count($result) < $limit) {
							$albumid_exists = false;
							
							foreach($result as $album) {
								if($album['id'] == $arr['album_id']) {
									$albumid_exists = true;
									break;
								}
							}
							
								if(!$albumid_exists) {
									$result[$index]['id'] = $arr['album_id'];
									$index ++;
								}
						}
						else 
							break;
					}//end of while
				}
	
		}	
	}
	
return $result;
}


/**
 * Get Member's Labels (all) through their Tracks Albums
 *
 * @param integer
 * @param string ('TITLE', 'ID')
 * @param string ('ASC' or 'DESC', default is 'ASC')
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @return array
 */
function cug_get_member_labels_all($member_id=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=0)
{
global $mysqli, $Tables;
$result_arr = array();
$result_index = 0;
$index = 0;

	// First Query
	$query  = "CALL get_member_labels_all($member_id,";
	$query .= "'".$mysqli->escape_str($sort_field)."','".$mysqli->escape_str($sort_method)."',$limit_from,$limit_quant);";

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
 * Get Member's Name or Alias (based on 'used_name')
 *
 * @param string
 * @param string
 * @param integer ( 1[Name] or 2[Alias], default is 2)
 * @return string
 */
function cug_get_member_name_or_alias($member_name, $member_alias, $used_name=2)
{
	if($used_name == 1 || $used_name == 2) {
		if($used_name == 2 && !empty($member_alias)) {
			return $member_alias;
		}
		else {
			return $member_name;
		}
	}
	else {
		return $member_name;
	}
}


/**
 * Get Member Name
 * 
 * @param id $member_id
 * @return string
 */
function cug_get_member_name($member_id) {
    $result = "";
    
    if($member_id > 0) {
        $member_obj = cug_get_member($member_id);
        
        if(!is_null($member_obj))
            $result = cug_get_member_name_or_alias($member_obj->title, $member_obj->alias, $member_obj->used_name);
    }
    
    return $result;
}


/**
 * CHeck if Member exists
 * 
 * @param string $member_title
 * @param string $member_alias
 * @param int $register_from (Optional, default: 0)
 * @return boolean
 */
function cug_is_member_exists($member_title, $member_alias, $register_from=0) {
    global $mysqli, $Tables;
    $result = false;
    
    if($member_title || $member_alias) {
        $query = "SELECT id FROM {$Tables['member']} WHERE";
        $query .= ($register_from > 0) ? " register_from=$register_from AND" : "";
        $query .= ($member_title) ? " title='".$mysqli->escape_str($member_title)."' AND" : "";
        $query .= ($member_alias) ? " alias='".$mysqli->escape_str($member_alias)."' AND" : "";
        
        $query = rtrim($query, "AND");
        
        $r = $mysqli->query($query);
        if($r && $r->num_rows) {
            $result = true;
        }
    }

    return $result;
}


/**
 * Delete Member
 *
 * @param int $member_id
 * @param int $user_id (Default is 0)
 * @param bool $force_delete (if TRUE then Member will be deleted without checking of existing relations, default is FALSE)
 * @param bool $delete_culinks Default: true
 * @param bool $delete_cache Default: true
 * @return int
 */
function cug_del_member($member_id, $user_id=0, $force_delete=false, $delete_culinks=true, $delete_cache=true)
{
global $mysqli, $Tables;

	if($member_id > 0) {
		
		//collect all albums and tracks for delete candidate members
		if($delete_cache) {
			$tracks = cug_get_member_related_tracks($register_from=0, $tag_status=0, $trash_status=-1, $member_id, $role_id=0, $sort_field="TITLE", $sort_method="ASC", $limit_from=0, $limit_quant=0);
			$albums = cug_get_member_related_albums($member_id, 0, 0);
		}
		//----------------------------		
		
		if(!$force_delete) {
			$obj = new cug__track_member_rel();
			$obj->member_id = $member_id;
			$track_member_rel = cug_get_track_member_rel($obj);
			unset($obj);
						
			if(count($track_member_rel) == 0) {//if member is not related to any track
				$album_member_rel = cug_get_album_member_rel($member_id, "MEMBER");
					
					if(count($album_member_rel) == 0) {//if member is not related to any album
						$obj = new cug__composition_member_rel();
						$obj->member_id = $member_id;
						$composition_member_rel = cug_get_composition_member_rel($obj);
						unset($obj);
						
							if(count($composition_member_rel) > 0) {//if member is related to some composition
								return -3; // can't delete, member is rerlated to some composition
							}
					}
					else {
						return -2; // can't delete, member is rerlated to some album
					}
			}
			else {
				return -1; // can't delete, member is rerlated to some track
			}
		}
		else { //force delete
			cug_del_member_track_rel( array($member_id) );
			cug_del_member_composition_rel( array($member_id) );
			cug_del_member_album_rel( array($member_id) );
		}
		
		
		
		//start deleting member
		//-----------------------
		
		//update 'copyright_c' field in 'track_list' table
		$query = "UPDATE ".$Tables['track']." SET copyright_c=null WHERE copyright_c=$member_id";
		$mysqli->query($query);
			
		//delete statistics
		$mysqli->query("DELETE FROM ".$Tables['artist_stat']." WHERE artist_id=$member_id");
		
		//delete chart_members
		$mysqli->query("DELETE FROM ".$Tables['chart_members']." WHERE member_id=$member_id");
		
		//delete user_fav_artist_list
		$mysqli->query("DELETE FROM ".$Tables['user_fav_artist']." WHERE artist_id=$member_id");
		
		//delete image file
		$obj = cug_get_member($member_id, "ID");
		cug_delete_obj_img($member_id, "MEMBER", $obj->img_path);
		unset($obj);

		
		//delete culinks
		if($delete_culinks) {
			cug_cache_del_member_culink($member_id);
		}
		
		//update cache tables
		if($delete_cache) {
			//update 'cache_members' table
			cug_cache_del_member($member_id);
			
			//update 'cache_albums' table
			if(!empty($albums[0])) {
				foreach($albums[0] as $album) {
					cug_cache_del_album($album['id']);
					cug_cache_add_album($album['id']);
				}
			}
				
			//update 'cache_tracks' table
			if(!empty($tracks[0])) {
				foreach($tracks[0] as $track) {
					cug_cache_del_track($track['id']);
					cug_cache_add_track($track['id']);
				}	
			}		
		}
		//--------------------------
		
		
		//delete Member
		$mysqli->query("DELETE FROM ".$Tables['member']." WHERE id=$member_id");
		$mysqli->query("DELETE FROM ".$Tables['member_more_info']." WHERE member_id=$member_id");
		
		//register log
		$obj = new cug__log_te();
			
		$obj->action_id = 4; //Delete
		$obj->subaction_id = 0;
		$obj->object_id = 4; //Member
		$obj->object_item_id = $member_id;
			
		$arr = cug_get_logs_te($obj);
			
			if(count($arr) == 0) {
				$obj->subitem_id = 0;
				$obj->user_id = $user_id;
				
				$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "127.0.0.1";
				$obj->country_id = cug_get_country_id_by_ip($ip);
				$obj->ip = $ip;
				
				$obj->session_id = session_id();
				$obj->start_time = @date("Y-m-d H:i:s");
				$obj->end_time = @date("Y-m-d H:i:s");
					
				cug_reg_log_te($obj);
			}
			
		unset($obj);
		return 1; //OK		
		
	}
	else {
		return 0; // can't delete, no member_id
	}		

}



/**
 * Delete Member-Track Relations
 *
 * @param array of Member IDs
 * @return bool
 */
function cug_del_member_track_rel($member_ids)
{
global $mysqli, $Tables;
$where_id = "(";

	if(count($member_ids) > 0) {
		//-------------
		foreach($member_ids as $member_id) {
			$where_id .= ($member_id > 0) ? "member_id=$member_id OR " : "";
		}
		//-------------


		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";
			$query = "DELETE FROM ".$Tables['track_member_rel']." WHERE $where_id";

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
 * Delete Member-Composition Relations
 *
 * @param array of Member IDs
 * @return bool
 */
function cug_del_member_composition_rel($member_ids)
{
global $mysqli, $Tables;
$where_id = "(";

	if(count($member_ids) > 0) {
		//-------------
		foreach($member_ids as $member_id) {
			$where_id .= ($member_id > 0) ? "member_id=$member_id OR " : "";
		}
		//-------------


		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";
			$query = "DELETE FROM ".$Tables['composition_member_rel']." WHERE $where_id";

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
 * Delete Member-Album Relations
 *
 * @param array of Member IDs
 * @return bool
 */
function cug_del_member_album_rel($member_ids)
{
global $mysqli, $Tables;
$where_id = "(";

	if(count($member_ids) > 0) {
		//-------------
		foreach($member_ids as $member_id) {
			$where_id .= ($member_id > 0) ? "member_id=$member_id OR " : "";
		}
		//-------------


		if(strlen($where_id) > 1) {
			$where_id = substr($where_id, 0, strlen($where_id)-3).")";
			$query = "DELETE FROM ".$Tables['album_member']." WHERE $where_id";

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
 * Get current Tag Status of the Member
 *
 * @param integer (id of the Member)
 * @return integer
 */
function cug_get_member_tag_status($member_id)
{
global $mysqli, $Tables;

	if($member_id > 0) {
		$query = "SELECT tag_status_id FROM ".$Tables['member']." WHERE id=$member_id";
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
 * Register Member's Additional Data 
 * 
 * @param int $member_id
 * @param string $bio
 * @param string $info
 * @param string $ext_img_url (External Image URL)
 * @return number
 */
function cug_reg_member_more_info($member_id, $bio, $info, $ext_img_url="") {
	global $mysqli, $Tables;
	
	if($member_id > 0 && ($bio || $info || $ext_img_url)) {
		
		//check for existing record
		$query = "SELECT id FROM {$Tables['member_more_info']} WHERE member_id=$member_id";
		$r = $mysqli->query($query);
			if($r->num_rows) {
				$arr = $r->fetch_array();
				return $arr['id'];
			}
		
		//register new record	
		$fields = "member_id,";
		$values = $member_id.",";
		
		if($info) {
			$fields .= "info,";
			$values .= "'".$mysqli->escape_str($info)."',";
		}
		//------------------
		if($bio) {
			$fields .= "bio,";
			$values .= "'".$mysqli->escape_str($bio)."',";
		}
		//------------------
		if($ext_img_url) {
			$fields .= "ext_img_url,";
			$values .= "'".$mysqli->escape_str($ext_img_url)."',";
		}		
		
		
		$reg_date = date("Y-m-d H:i:s");
		
		
		$fields .= "register_date";
		$values .= "'".$reg_date."'";
		
		$query = "INSERT INTO {$Tables['member_more_info']} (".$fields.") VALUES(".$values.")";
		
			if($mysqli->query($query))
				return $mysqli->insert_id;
			else 
				return -1;
	}
	else 
		return 0;
}


/**
 * Edit Member's Additional Data
 *
 * Used in API
 *
 * @param int $member_id
 * @param bool $update_empty_fields
 * @param string $bio
 * @param string $info
 * @param string $ext_img_url (External Image URL)
 * @param bool $register_as_new default: false
 * @return number
 */
function cug_edit_member_more_info($member_id, $update_empty_fields, $bio, $info, $ext_img_url, $register_as_new=false) {
	global $mysqli, $Tables;

	if($member_id > 0) {

		//check for existing record
		$query = "SELECT id, ext_img_url FROM {$Tables['member_more_info']} WHERE member_id=$member_id";
		$r = $mysqli->query($query);
		//--------------------------
			if($r->num_rows) {
				$row = $r->fetch_array();
				$ext_img_url_old = $row['ext_img_url'];
				
				$values = "";				

				//check fields
				if(isset($bio)) {
					if(!empty($bio))
						$values .= "bio='".$mysqli->escape_str($bio)."',";
					elseif($update_empty_fields) 
						$values .= "bio=null,";
				}
				//---------
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
						
						$query = "UPDATE {$Tables['member_more_info']} SET $values WHERE member_id=$member_id";
							if($mysqli->query($query)) {
							    //update image statuse
							    if(isset($ext_img_url)) {
							        if(empty($ext_img_url)) {
							            if($update_empty_fields) {
							                $status_arr = array(0,0,0,0,0,0,0);
							                cug_update_obj_img_status("MEMBER", $member_id, $status_arr);
							            }
							        }
							        else {
							            $status_arr = array(0,0,0,0,0,0,0);
							            cug_update_obj_img_status("MEMBER", $member_id, $status_arr);
							        }
							    }
								
								return true;
							}
					}
			}
			else {
				if($register_as_new) {
					if(cug_reg_member_more_info($member_id, $bio, $info, $ext_img_url) > 0)
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
 * Import Members
 * 
 * Used in API
 * 
 * @param int $client_id
 * @param string $user_ip
 * @param string $member_data
 * @param bool $online (optional, default: false)
 * @param bool $import_same_member (optional, default: false; Import Member anyway if it exists already from other Clients)
 * @return number|array
 */
function cug_import_members($client_id, $user_ip, $members_data, $online=false, $import_same_member=false) {
	global $mysqli, $Tables, $ERRORS, $FILE_SERVER_URL;
	$result = array();

	if(!$client_id) {
		return $ERRORS['INVALID_CLIENT_ID'];
	}
	//-----------------
	if(!$user_ip) {
		$user_ip = $_SERVER['REMOTE_ADDR'];
	}
	//-----------------
	if(!empty($members_data)) {
		$arr = json_decode($members_data, true);
				
		//check JSON Data on errors
		$error_code = cug_json_last_error();
		if($error_code < 0) {
			return $error_code;
		}
		//-------------------------
		
		$total_members = !empty($arr['members']) ? count($arr['members']) : 0;

		if($total_members > 0) {
			//check mandatory fields
			$mandatory_fields = true;
			foreach($arr['members'] as $member) {
				if(empty($member['member_ext_id']) || empty($member['member_name'])) {
					$mandatory_fields = false;
					break;
				}
			}
				
			if(!$mandatory_fields) {
				return $ERRORS['NOT_ENOUGH_MEMBER_FIELDS'];
			}
			//------------------------------

			//register members
			$result['total_members'] = $total_members;
			$result['members_success'] 		= 0;
			$result['members_failed'] 		= 0;
			$result['memeber_ids'] 	= "";
				
			$reg_date = @date("Y-m-d H:i:s");
				
			foreach($arr['members'] as $member) {
				//check for existing member
				if($import_same_member) {
				    if($client_id == 3066) //Shenzhen (Tony)
				        $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND shenzhen_id=".$mysqli->escape_str(trim($member['member_ext_id']));
				    else 
				        $query = "SELECT id FROM {$Tables['member']} WHERE register_from=$client_id AND external_id='".$mysqli->escape_str(trim($member['member_ext_id']))."'";				    				    
				}
				else {
				    $query = "SELECT id FROM {$Tables['member']} WHERE title='".$mysqli->escape_str(trim($member['member_name']))."'";
				    $query .= !empty($member['member_alias']) ? " OR alias='".$mysqli->escape_str(trim($member['member_alias']))."'" : "";
				}
				    
				$r = $mysqli->query($query);

				if($r->num_rows) {
					$row = $r->fetch_array();
					$member_id = $row['id'];
					
					if($client_id == 3066 && !$import_same_member) //Shenzhen (Tony)
					    $mysqli->query("UPDATE {$Tables['member']} SET shenzhen_id=".$mysqli->escape_str(trim($member['member_ext_id']))." WHERE id=$member_id");
				}
				else {
					$new_member = new cug__member();					
					
					if($client_id == 3066) //Shenzhen (Tony)
					    $new_member->shenzhen_id = trim($member['member_ext_id']);
					else 
					    $new_member->external_id = trim($member['member_ext_id']);
					
					$new_member->title 			= trim($member['member_name']);
					$new_member->alias 				= !empty($member['member_alias']) ? trim($member['member_alias']) : "";
					$new_member->used_name 			= !empty($member['used_name']) ? trim($member['used_name']) : 0;
					$new_member->member_type_id 	= !empty($member['member_type_id']) ? trim($member['member_type_id']) : 0;
					$new_member->standard_role_id 	= !empty($member['standard_role_id']) ? trim($member['standard_role_id']) : 0;
					$new_member->gender_id 			= !empty($member['gender_id']) ? trim($member['gender_id']) : 0;
					$new_member->birth_date 		= !empty($member['birth_date']) ? cug_parse_date_for_mysql(trim($member['birth_date'])) : "";
					$new_member->birth_country_id 	= !empty($member['birth_country_id']) ? trim($member['birth_country_id']) : 0;
					$new_member->birth_place 		= !empty($member['birth_place']) ? trim($member['birth_place']) : "";
					$new_member->death_date 		= !empty($member['death_date']) ? cug_parse_date_for_mysql(trim($member['death_date'])) : "";
					$new_member->death_country_id 	= !empty($member['death_country_id']) ? trim($member['death_country_id']) : 0;
					$new_member->death_place 		= !empty($member['death_place']) ? trim($member['death_place']) : "";
					$new_member->worktime_from 		= !empty($member['worktime_from']) ? cug_parse_date_for_mysql(trim($member['worktime_from'])) : "";
					$new_member->worktime_to 		= !empty($member['worktime_to']) ? cug_parse_date_for_mysql(trim($member['worktime_to'])) : "";
					$new_member->member_url 		= !empty($member['official_url']) ? trim($member['official_url']) : "";
						
					$new_member->register_from 	= $client_id;
					$new_member->register_date 	= $reg_date;
					$new_member->register_ip 	= $user_ip;
					$new_member->img_path		= $FILE_SERVER_URL;
					if($online) $new_member->online = 1;
					
					$member_id = cug_reg_member($new_member);
				}

				if($member_id > 0) {
					$result['members_success'] += 1;

					//register additional info
					$biography 			= !empty($member['bio_text']) ? trim($member['bio_text']) : "";
					$img_url 			= !empty($member['img_url']) ? trim($member['img_url']) : "";
					$additional_info 	= !empty($member['additional_info']) ? trim($member['additional_info']) : "";

					if($biography || $img_url || $additional_info) {
						cug_reg_member_more_info($member_id, $biography, $additional_info, $img_url);
					}
				}
				else {
					$result['members_failed'] += 1;
				}
					
				$result['memeber_ids'] .= $member_id.",";
			}
				
			$result['memeber_ids'] = !empty($result['memeber_ids']) ? substr($result['memeber_ids'], 0, strlen($result['memeber_ids']) - 1) : "";
			return $result;
		}
		else {
			return $ERRORS['INVALID_MEMBER_DATA_STRUCTURE'];
		}
	}
	else {
		return $ERRORS['NO_MEMBER_DATA'];
	}
}


/**
 * Update Members
 *
 * Used in API
 *
 * @param int $client_id
 * @param string $user_ip
 * @param string $member_data
 * @return number|array
 */
function cug_update_members($client_id, $user_ip, $members_data) {
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
	if(!empty($members_data)) {
		$arr = json_decode($members_data, true);

		//check JSON Data on errors
		$error_code = cug_json_last_error();
		if($error_code < 0) {
			return $error_code;
		}
		//-------------------------

		$total_members = !empty($arr['members']) ? count($arr['members']) : 0;

		if($total_members > 0) {
			//check mandatory fields
			$mandatory_fields = true;
			foreach($arr['members'] as $member) {
				if(empty($member['member_ext_id']) && empty($member['member_id'])) {
					$mandatory_fields = false;
					break;
				}
			}

			if(!$mandatory_fields) {
				return $ERRORS['NOT_ENOUGH_MEMBER_FIELDS'];
			}
			//------------------------------

			
			//update members
			$result['total_members'] = $total_members;
			$result['members_success'] 		= 0;
			$result['members_failed'] 		= 0;
			$result['memeber_ids'] 	= "";

			foreach($arr['members'] as $member) {
				$status = 0; //no action
				
				//check member id			
				$member_id_to_be_checked = !empty($member['member_id']) ? trim($member['member_id']) : 0;
				$member_ext_id_to_be_checked = !empty($member['member_ext_id']) ? trim($member['member_ext_id']) : "";
				$member_role_id = !empty($member['role_id']) ? trim($member['role_id']) : 0;
				
				$member_id = cug_check_member_id($member_id_to_be_checked, $member_ext_id_to_be_checked, $client_id);
				$incoming_member_id = ($member_id_to_be_checked > 0) ? $member_id_to_be_checked : $member_ext_id_to_be_checked;
				//---------------------------
								

				if($member_id > 0) {
					$status = cug_get_incoming_member_status($member);
					
					if($status == 0) {
						$fields = 0;
						
						$member_obj = new cug__member();
						if(isset($member['member_name'])) 		{ $member_obj->title 			= trim($member['member_name']); $fields ++; }
						if(isset($member['member_alias'])) 		{ $member_obj->alias 			= trim($member['member_alias']); $fields ++; }
						if(isset($member['used_name'])) 		{ $member_obj->used_name 		= trim($member['used_name']); $fields ++; }
						if(isset($member['member_type_id'])) 	{ $member_obj->member_type_id 	= trim($member['member_type_id']); $fields ++; }
						if(isset($member['standard_role_id'])) 	{ $member_obj->standard_role_id = trim($member['standard_role_id']); $fields ++; }
						if(isset($member['gender_id'])) 		{ $member_obj->gender_id 		= trim($member['gender_id']); $fields ++; }
						if(isset($member['birth_date'])) 		{ $member_obj->birth_date 		= cug_parse_date_for_mysql(trim($member['birth_date'])); $fields ++; }
						if(isset($member['birth_country_id'])) 	{ $member_obj->birth_country_id = trim($member['birth_country_id']); $fields ++; }
						if(isset($member['birth_place'])) 		{ $member_obj->birth_place 		= trim($member['birth_place']); $fields ++; }
						if(isset($member['death_date'])) 		{ $member_obj->death_date 		= cug_parse_date_for_mysql(trim($member['death_date'])); $fields ++; }
						if(isset($member['death_country_id'])) 	{ $member_obj->death_country_id = trim($member['death_country_id']); $fields ++; }
						if(isset($member['death_place'])) 		{ $member_obj->death_place 		= trim($member['death_place']); $fields ++; }
						if(isset($member['worktime_from'])) 	{ $member_obj->worktime_from 	= cug_parse_date_for_mysql(trim($member['worktime_from'])); $fields ++; }
						if(isset($member['worktime_to'])) 		{ $member_obj->worktime_to 		= cug_parse_date_for_mysql(trim($member['worktime_to'])); $fields ++; }
						if(isset($member['official_url'])) 		{ $member_obj->member_url 		= trim($member['official_url']); $fields ++; }
						
						if($fields > 0) {
							$member_was_updadted = cug_edit_member(array($member_id), $member_obj, true);
						}
	
						//update additional info
						$biography 			= isset($member['bio_text']) ? trim($member['bio_text']) : null;
						$img_url 			= isset($member['img_url']) ? trim($member['img_url']) : null;
						$additional_info 	= isset($member['additional_info']) ? trim($member['additional_info']) : null;
	
						if(isset($biography) || isset($img_url) || isset($additional_info)) {
							$member_moreinfo_was_updadted = cug_edit_member_more_info($member_id, $update_empty_fields=true, $biography, $additional_info, $img_url, $register_as_new=true);
						}
						
							//define status
							if(isset($member_was_updadted) || isset($member_moreinfo_was_updadted)) {
								if((isset($member_was_updadted) && $member_was_updadted) || (isset($member_moreinfo_was_updadted) && $member_moreinfo_was_updadted)) {
									$status = 1; // OK
								}
								else {
									$status = -4; //internal error
								}
							}
					}
				}
				else {
					$status = -1; //unknown member id
				}
				
					//check status
					if($status == 1) {
						$result['members_success'] += 1;
					}
					else {
						$result['members_failed'] += 1;
					}
					//-------------------
				
				$result['memeber_ids'] .= $incoming_member_id.":".$status.",";
			}

			$result['memeber_ids'] = !empty($result['memeber_ids']) ? substr($result['memeber_ids'], 0, strlen($result['memeber_ids']) - 1) : "";
			return $result;
		}
		else {
			return $ERRORS['INVALID_MEMBER_DATA_STRUCTURE'];
		}
	}
	else {
		return $ERRORS['NO_MEMBER_DATA'];
	}
}



/**
 * Update Status of External Image for the Member
 * 
 * @param int $member_id
 * @param int $status (1 - was Downloaded;  0 - Was not downloaded;)
 * @return boolean
 */
function cug_edit_member_more_info_img_status($member_id, $status) {
	global $mysqli, $Tables;
	
	if($member_id > 0) {
		$query = "UPDATE {$Tables['member_more_info']} SET ext_img_downloaded=".$mysqli->escape_str($status)." WHERE member_id=$member_id";
			if($mysqli->query($query)) {
				return true;
			}
	}
	
	return false;
}


/**
 * Get Incoming Member's status
 * 
 * Used in API
 * 
 * Check if necessary fields are provided correctly
 * @param array $member
 * @return number
 */
function cug_get_incoming_member_status($member) {
	$status = 0;

	
	if(isset($member['member_name']) && trim($member['member_name']) == "") {
		$status = -2; //empty member name was provided
	}
	elseif(isset($member['member_name']) || isset($member['member_alias'])) {
		if(!isset($member['used_name']) || empty($member['used_name'])) {
			$status = -3; //'used_name' is empty or was not provided at all
		}
		elseif($member['used_name'] == 2 && empty($member['member_alias'])) {
			$status = -5; //'member_alias' must be provided whereas 'used_name'=2
		}
	}
	elseif(isset($member['used_name'])) {
		$status = -4; //either 'member_name' or 'member_alias' must be provided
	}
	
	return $status;
}


/**
 * Check Member ID
 *
 * @param int $member_id
 * @param int $member_ext_id
 * @param int $client_id (default: 0)
 * @return int
 */
function cug_check_member_id($member_id, $member_ext_id, $client_id=0) {
	global $mysqli, $Tables;
	$field = "";
	$result = 0;

	if($member_id) {
		$member_id_str = $mysqli->escape_str($member_id);
		$field = "id";
	}
	elseif($member_ext_id) {
	    if($client_id == 3066) {//Shenzhen (Tony)
		    $member_id_str = $mysqli->escape_str($member_ext_id);
		    $field = "shenzhen_id";
	    }
	    else {
	        $member_id_str = "'".$mysqli->escape_str($member_ext_id)."'";
	        $field = "external_id";	        
	    }
	}
	//------------------------

	if($field) {
		$query = "SELECT id FROM {$Tables['member']} WHERE $field=".$member_id_str;
		$query .= ($client_id > 0 && $client_id != 3066) ? " AND register_from=$client_id" : "";
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
 * Check if Member belongs to Label Category
 *
 * @param int $member_id
 * @param int $label_cat_id
 * @return boolean
 */
function cug_is_member_from_label_category($member_id, $label_cat_id) {
    global $mysqli, $Tables;
    $result = false;

    if($member_id > 0 && $label_cat_id > 0) {
        //through albums
        $query = "SELECT amr.album_id FROM {$Tables['album_member']} AS amr ";
        $query .= "INNER JOIN {$Tables['album_label_cat']} AS alc ON amr.album_id=alc.album_id ";
        $query .= "WHERE amr.member_id=$member_id ";
        $query .= "AND alc.label_cat_id=$label_cat_id ";
        
        $r = $mysqli->query($query);
        if(!$r->num_rows) {
            //through tracks
            $query = "SELECT tmr.id FROM {$Tables['track_member_rel']} AS tmr ";
            $query .= "INNER JOIN {$Tables['track_album_rel']} AS tar ON tar.track_id=tmr.track_id ";
            $query .= "INNER JOIN {$Tables['album_label_cat']} AS alc ON tar.album_id=alc.album_id ";
            $query .= "WHERE tmr.member_id=$member_id AND alc.label_cat_id=$label_cat_id";
            
            $r = $mysqli->query($query);            
            if($r->num_rows)
                $result = true;
        }
        else {
            $result = true;
        }

    }

    return $result;
}


/**
 * Get Member's Owner (register_from) ID
 *
 * @param $member_id integer
 * @return integer
 */
function cug_get_member_owner_id($member_id) {
    global $mysqli, $Tables;

    if($member_id > 0) {
        $query = "SELECT register_from FROM ".$Tables['member']." WHERE id=$member_id";
        $r = $mysqli->query($query);

        if($r && $r->num_rows) {
            $arr = $r->fetch_assoc();
            return $arr['register_from'];
        }
    }

    return 0;
}
?>