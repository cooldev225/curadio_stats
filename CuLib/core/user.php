<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	User
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * User Class
 *
 * @param	id (INT)
 * @param	nick_name (STRING)
 * @param	real_name (STRING)
 * @param	email (STRING)
 * @param	password (INT)
 * @param	country_id (INT)
 * @param	city (STRING)
 * @param	gender (INT)
 * @param	birth_date (STRING - MySQL DATE Format)
 * @param	img_path - (STRING)
 * @param	img_34 (INT)
 * @param	img_64 (INT)
 * @param	img_174 (INT)
 * @param	img_300 (INT)
 * @param	img_600 (INT)
 * @param	img_orgn (INT - Size of the Original Image File)
 * @param	reg_module_id (INT)
 * @param	reg_module_details_id (INT)
 * @param	fav_artist_id (INT)
 * @param	fav_genre_id (INT)
 * @param	register_date (STRING - MySQL DATETIME Format)
 * @param	register_date_finish (STRING - MySQL DATETIME Format)
 * @param	register_ip (STRING)
 * @param	last_login_date (STRING - MySQL DATETIME Format)
 * @param	last_login_ip (STRING)
 * @param	status (INT)
 * @param	uniqid - STRING
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */

class cug__user
{
	public
	$id,
	$nick_name,
	$real_name,
	$email,
	$password,
	$country_id,
	$city,
	$gender,
	$birth_date,
	$img_path,
	$img_34,
	$img_64,
	$img_174,
	$img_300,
	$img_600,
	$img_orgn,
	$reg_module_id,
	$reg_module_details_id,
	$fav_artist_id,
	$fav_genre_id,
	$register_date,
	$register_date_finish,
	$register_ip,
	$last_login_date,
	$last_login_ip,
	$status,
	$uniqid,
	$update_time;
}


/**
 * Register New User
 *
 * @param object of User Class
 * @return integer
 */
function cug_reg_user($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->email) && !empty($obj->password)) {	
		
		$fields .= "(email,";
		$values .= " VALUES('".$mysqli->escape_str($obj->email)."',";
		
		$fields .= "password,";
		$values .= "MD5('".$obj->password."'),";

		
		if(!empty($obj->nick_name)) {
			$fields .= "nick_name,";
			$values .= "'".$mysqli->escape_str($obj->nick_name)."',";
		}
		

		if(!empty($obj->reg_module_id) && $obj->reg_module_id > 0) {
			$fields .= "reg_module_id,";
			$values .= $mysqli->escape_str($obj->reg_module_id).",";
		}
		
		
		if(!empty($obj->reg_module_details_id) && $obj->reg_module_details_id > 0) {
			$fields .= "reg_module_details_id,";
			$values .= $mysqli->escape_str($obj->reg_module_details_id).",";
		}
		
		
		if(!empty($obj->real_name)) {
			$fields .= "real_name,";
			$values .= "'".$mysqli->escape_str($obj->real_name)."',";
		}

		if(!empty($obj->country_id) && $obj->country_id > 0) {
			$fields .= "country_id,";
			$values .= $mysqli->escape_str($obj->country_id).",";
		}
		
		if(!empty($obj->city)) {
			$fields .= "city,";
			$values .= "'".$mysqli->escape_str($obj->city)."',";
		}
		
		if(!empty($obj->gender) && $obj->gender > 0) {
			$fields .= "gender,";
			$values .= $mysqli->escape_str($obj->gender).",";
		}
		
		if(!empty($obj->birth_date)) {
			$fields .= "birth_date,";
			$values .= "'".$mysqli->escape_str($obj->birth_date)."',";
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


		if(!empty($obj->fav_artist_id)) {
			$fields .= "fav_artist_id,";
			$values .= $mysqli->escape_str($obj->fav_artist_id).",";
		}

		if(!empty($obj->fav_genre_id)) {
			$fields .= "fav_genre_id,";
			$values .= $mysqli->escape_str($obj->fav_genre_id).",";
		}
	//--------------------------
		$fields .= "register_date,";
		if(!empty($obj->register_date)) {
			$values .= "'".$mysqli->escape_str($obj->register_date)."',";
		}
		else {
			$values .= "'".@date("Y-m-d H:i:s")."',";
		}
		
		
		if(!empty($obj->register_date_finish)) {
			$fields .= "register_date_finish,";
			$values .= "'".$mysqli->escape_str($obj->register_date_finish)."',";
		}
	//---------------------------
		$fields .= "register_ip,";
		if(!empty($obj->register_ip)) {
			$values .= "'".$mysqli->escape_str($obj->register_ip)."',";
		}
		else {
			$values .= "'".$_SERVER["REMOTE_ADDR"]."',";
		}
	//---------------------------	

		if(!empty($obj->last_login_date)) {
			$fields .= "last_login_date,";
			$values .= "'".$mysqli->escape_str($obj->last_login_date)."',";
		}
		
		if(!empty($obj->last_login_ip)) {
			$fields .= "last_login_ip,";
			$values .= "'".$mysqli->escape_str($obj->last_login_ip)."',";
		}
		
		
		if($obj->status != null && $obj->status >= 0) {
			$fields .= "status,";
			$values .= $obj->status.",";
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


		$query = "INSERT INTO ".$Tables['user'].$fields.$values;

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
 * Check User's email if it is exists or not
 * 
 * @param string $email
 * @return boolean
 */
function cug_check_user_email($email) {
	global $mysqli, $Tables;
	
		if($email) {
			$query = "SELECT id FROM {$Tables['user']} WHERE email='".$mysqli->escape_str($email)."'";
			$r = $mysqli->query($query);
			
			if($r->num_rows) {
				return true;
			}
		}
	
	return false;
}


/**
 * Get User Info
 *
 * @param string
 * @param string
 * @return object of cug__user Class
 */
function cug_get_user($email, $password)
{
global $mysqli, $Tables, $FILE_SERVER_URL;

	if(!empty($email) && !empty($password)) {

		$query = "SELECT * FROM ".$Tables['user']." WHERE email='".$mysqli->escape_str($email)."' AND password=MD5('".$mysqli->escape_str($password)."')";
	
		$r = $mysqli->query($query);
		if($r) {

			$arr = $r->fetch_array();
			if($arr) {
					
				$obj = new cug__user();
					
				$obj->id					= $arr['id'];
				$obj->nick_name				= $arr['nick_name'];
				$obj->real_name				= $arr['real_name'];
				$obj->email					= $arr['email'];
				$obj->country_id			= $arr['country_id'];
				$obj->city					= $arr['city'];
				$obj->gender				= $arr['gender'];
				$obj->birth_date			= $arr['birth_date'];				
				$obj->fav_artist_id			= $arr['fav_artist_id'];
				$obj->fav_genre_id			= $arr['fav_genre_id'];
				$obj->register_date			= $arr['register_date'];
				$obj->register_date_finish	= $arr['register_date_finish'];
				$obj->register_ip			= $arr['register_ip'];
				$obj->last_login_date		= $arr['last_login_date'];
				$obj->last_login_ip			= $arr['last_login_ip'];
				$obj->status				= $arr['status'];
				$obj->uniqid				= $arr['uniqid'];
				$obj->update_time			= $arr['update_time'];
				
				$obj->reg_module_id	= $arr['reg_module_id'];
				$obj->reg_module_details_id = $arr['reg_module_details_id'];

				$img_path = !empty($obj->img_path) ? cug_get_url_protocol()."://".$obj->img_path : $FILE_SERVER_URL;
				
				if($arr['img_34'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', '34', $FILE_SERVER_URL);
					$obj->img_34 = $file_info['url'];
				}
				else {
					$obj->img_34 = "";
				}
				//-------
				if($arr['img_64'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', '64', $FILE_SERVER_URL);
					$obj->img_64 = $file_info['url'];
				}
				else {
					$obj->img_64 = "";
				}
				//-------
				if($arr['img_174'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', '174', $FILE_SERVER_URL);
					$obj->img_174 = $file_info['url'];
				}
				else {
					$obj->img_174 = "";
				}
				//-------
				if($arr['img_300'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', '300', $FILE_SERVER_URL);
					$obj->img_300 = $file_info['url'];
				}
				else {
					$obj->img_300 = "";
				}
				//-------
				if($arr['img_600'] == 1) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', '600', $FILE_SERVER_URL);
					$obj->img_600 = $file_info['url'];
				}
				else {
					$obj->img_600 = "";
				}
				//-------
				if($arr['img_orgn'] > 0) {
					$file_info = cug_get_obj_file_info($arr['id'], 'USER', 'mega', $FILE_SERVER_URL);
					$obj->img_orgn = $file_info['url'];
				}
				else {
					$obj->img_orgn = "";
				}
				//-------
					
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
 * Edit Existing User
 *
 * @param integer
 * @param object of cug__user Class
 * @return integer
 */
function cug_edit_user($user_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $user_id > 0) {

		if(!empty($obj->nick_name)) $fields .= "nick_name='".$mysqli->escape_str($obj->nick_name)."',";
		if(!empty($obj->real_name)) $fields .= "real_name='".$mysqli->escape_str($obj->real_name)."',";
		//if(!empty($obj->email)) $fields .= "email='".$mysqli->escape_str($obj->email)."',";
		if(!empty($obj->password)) $fields .= "password=MD5('".$mysqli->escape_str($obj->password)."'),";
		if(!empty($obj->country_id)) $fields .= "country_id=".$mysqli->escape_str($obj->country_id).",";
		if(!empty($obj->city)) $fields .= "city='".$mysqli->escape_str($obj->city)."',";
		if(!empty($obj->gender)) $fields .= "gender=".$mysqli->escape_str($obj->gender).",";
		if(!empty($obj->birth_date)) $fields .= "birth_date='".$mysqli->escape_str($obj->birth_date)."',";
		if(!empty($obj->reg_module_id)) $fields .= "reg_module_id=".$mysqli->escape_str($obj->reg_module_id).",";
		if(!empty($obj->reg_module_details_id)) $fields .= "reg_module_details_id=".$mysqli->escape_str($obj->reg_module_details_id).",";
		if(!empty($obj->fav_artist_id)) $fields .= "fav_artist_id=".$mysqli->escape_str($obj->fav_artist_id).",";
		if(!empty($obj->fav_genre_id)) $fields .= "fav_genre_id=".$mysqli->escape_str($obj->fav_genre_id).",";
		if(!empty($obj->register_date)) $fields .= "register_date='".$mysqli->escape_str($obj->register_date)."',";
		if(!empty($obj->register_date_finish)) $fields .= "register_date_finish='".$mysqli->escape_str($obj->register_date_finish)."',";
		if(!empty($obj->register_ip)) $fields .= "register_ip='".$mysqli->escape_str($obj->register_ip)."',";
		if(!empty($obj->last_login_date)) $fields .= "last_login_date='".$mysqli->escape_str($obj->last_login_date)."',";
		if(!empty($obj->last_login_ip)) $fields .= "last_login_ip='".$mysqli->escape_str($obj->last_login_ip)."',";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		if(!empty($obj->update_time)) $fields .= "update_time='".$mysqli->escape_str($obj->update_time)."',";
		if($obj->status != null && $obj->status >= 0) $fields .= "status=".$mysqli->escape_str($obj->status).",";
		if(!empty($obj->img_path)) $fields .= "img_path='".$mysqli->escape_str($obj->img_path)."',";
		if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= "img_34=".$mysqli->escape_str($obj->img_34).",";
		if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= "img_64=".$mysqli->escape_str($obj->img_64).",";
		if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= "img_174=".$mysqli->escape_str($obj->img_174).",";
		if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= "img_300=".$mysqli->escape_str($obj->img_300).",";
		if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= "img_600=".$mysqli->escape_str($obj->img_600).",";
		if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= "img_orgn=".$mysqli->escape_str($obj->img_orgn).",";
		

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-1);
			$query = "UPDATE ".$Tables['user']." SET ".$fields." WHERE id=".$user_id;

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
 * Register New User-Group
 *
 * @param string
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_user_group($title, $client_id, $uniqid="")
{
global $mysqli, $Tables;

	if(!empty($title) && $client_id > 0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['user_group']." WHERE client_id=$client_id AND title='".$mysqli->escape_str($title)."'");

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, '".$mysqli->escape_str($title)."', $client_id, '".$uniq_id."', NULL)";
			$query = "INSERT INTO ".$Tables['user_group']." VALUES".$values;

			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else {
			return -2; // already exist
		}
	}
	else
		return 0;
}


/**
 * Get User-Group
 *
 * @param integer (default is 0)
 * @param string (default is '')
 * @param integer (default is 0)
 * @param string (default is '')
 * @param string ('ID' or 'TITLE', default is 'TITLE')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_user_groups($id=0, $title="", $client_id=0, $uniqid="", $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	// generate search query
	if($id > 0) {
		$fields = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$fields = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if(!empty($title)) {
			$fields = "title='".$mysqli->escape_str($title)."' AND ";
		}
		if($client_id > 0) {
			$fields .= "client_id=".$client_id." AND";
		}
		
			if(strlen($fields) > 0) {
				$fields = substr($fields, 0, strlen($fields)-4); // remove ' AND'
			}
	}
	//--------------------
	

	if($sort_by == "TITLE")
		$sort_field = "title";
	else
		$sort_field = "id";
	//-------------------
		
	if(!empty($fields)) {
		$query = "SELECT * FROM ".$Tables['user_group']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type;
	}
	else {
		$query = "SELECT * FROM ".$Tables['user_group']." ORDER BY ".$sort_field." ".$sort_type;
	}
		
		//execute query
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
 * Get User Clients IDs
 * 
 * @param int $user_id
 * @return array
 */
function cug_get_user_clients($user_id) {
	global $mysqli, $Tables;
	$result = array();
	
		if($user_id > 0) {
			$arr = cug_get_user_group_rel($id=0, $user_id, $group_id=0);
			foreach($arr as $group) {
				$group_id = $group['group_id'];
				$arr2 = cug_get_user_groups($group_id);
				$result[] = cug_get_client($arr2[0]['client_id']);
			}
		}
	
	return $result;
}


/**
 * Edit User-Group
 *
 * @param integer
 * @param string
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_user_group($id, $title, $client_id, $uniqid="")
{
global $mysqli, $Tables;

	if($id > 0) {
		$where = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		return 0;
	}
	//--------------------
	
	
	if(!empty($title) && $client_id > 0) {
		
		// check for existing record
		$query = "SELECT id FROM ".$Tables['user_group']." WHERE client_id=$client_id AND title='".$mysqli->escape_str($title)."'";
		$r = $mysqli->query($query);
		
		if( !$r->num_rows ) {
			$query = "UPDATE ".$Tables['user_group']." SET title='".$mysqli->escape_str($title)."', client_id=$client_id WHERE $where";
				
				if($mysqli->query($query))
					return 1;
				else
					return -1;
		}
		else {
			return -2; // already exists
		}
	}
	else {
		return 0;
	}
	
}

/**
 * Register New User-Group Relation
 *
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_user_group_rel($user_id, $group_id, $uniqid="")
{
global $mysqli, $Tables;

	if($user_id > 0 && $group_id > 0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['user_group_rel']." WHERE user_id=$user_id AND group_id=".$group_id);

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, $user_id, $group_id, '".$uniq_id."', NULL)";
			$query = "INSERT INTO ".$Tables['user_group_rel']." VALUES".$values;

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
 * Get User-Group Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @param string ('USER' or 'GROUP', default is 'USER')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_user_group_rel($id, $user_id, $group_id, $uniqid="", $sort_by="USER", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	if($id > 0) {
		$fields = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$fields = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if($user_id  > 0) {
			$fields = "user_id=$user_id AND ";
		}
		if($group_id > 0) {
			$fields .= "group_id=".$group_id." AND";
		}

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-4); // remove ' AND'
		}
	}
	//-------------------------


	if(!empty($fields)) {

		if($sort_by == "GROUP")
			$sort_field = "group_id";
		else
			$sort_field = "user_id";

		$r = $mysqli->query("SELECT * FROM ".$Tables['user_group_rel']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type);

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
 * Edit User-Group Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_user_group_rel($id, $user_id, $group_id, $uniqid="")
{
global $mysqli, $Tables;

	if($id > 0) {
		$where = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		return 0;
	}
	//--------------------


	if($user_id > 0 && $group_id > 0) {

		// check for existing record
		$query = "SELECT id FROM ".$Tables['user_group_rel']." WHERE user_id=$user_id AND group_id=$group_id";
		$r = $mysqli->query($query);

		if( !$r->num_rows ) {
			$query = "UPDATE ".$Tables['user_group_rel']." SET user_id=$user_id, group_id=$group_id WHERE $where";

			if($mysqli->query($query))
				return 1;
			else
				return -1;
		}
		else {
			return -2; // already exists
		}
	}
	else {
		return 0;
	}

}


/**
 * Register New UserGroup-Right Relation
 *
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_usergroup_right_rel($group_id, $right_id, $uniqid="")
{
global $mysqli, $Tables;

	if($group_id > 0 && $right_id > 0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['usergroup_right_rel']." WHERE group_id=$group_id AND right_id=".$right_id);

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, $group_id, $right_id, '".$uniq_id."', NULL)";
			$query = "INSERT INTO ".$Tables['usergroup_right_rel']." VALUES".$values;

			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else {
			return -2; // already exist
		}
	}
	else
		return 0;
}


/**
 * Get UserGroup-Right Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @param string ('RIGHT' or 'GROUP', default is 'RIGHT')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_usergroup_right_rel($id, $group_id, $right_id, $uniqid="", $sort_by="RIGHT", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	if($id > 0) {
		$fields = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$fields = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if($group_id  > 0) {
			$fields = "group_id=$group_id AND ";
		}
		if($right_id > 0) {
			$fields .= "right_id=".$right_id." AND";
		}

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-4); // remove ' AND'
		}
	}
	//-------------------------


	if(!empty($fields)) {

		if($sort_by == "GROUP")
			$sort_field = "group_id";
		else
			$sort_field = "right_id";

		$r = $mysqli->query("SELECT * FROM ".$Tables['usergroup_right_rel']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type);

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
 * Edit UserGroup-Right Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_usergroup_right_rel($id, $group_id, $right_id, $uniqid="")
{
global $mysqli, $Tables;

	if($id > 0) {
		$where = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		return 0;
	}
	//--------------------


	if($group_id > 0 && $right_id > 0) {

		// check for existing record
		$query = "SELECT id FROM ".$Tables['usergroup_right_rel']." WHERE group_id=$group_id AND right_id=$right_id";
		$r = $mysqli->query($query);

		if( !$r->num_rows ) {
			$query = "UPDATE ".$Tables['usergroup_right_rel']." SET group_id=$group_id, right_id=$right_id WHERE $where";

			if($mysqli->query($query))
				return 1;
			else
				return -1;
		}
		else {
			return -2; // already exists
		}
	}
	else {
		return 0;
	}

}



/**
 * Register New User-Right Relation
 *
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_reg_user_right_rel($user_id, $right_id, $uniqid="")
{
global $mysqli, $Tables;

	if($user_id > 0 && $right_id > 0) {

		// Check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['user_right_rel']." WHERE user_id=$user_id AND right_id=".$right_id);

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, $user_id, $right_id, '".$uniq_id."', NULL)";
			$query = "INSERT INTO ".$Tables['user_right_rel']." VALUES".$values;

			if($mysqli->query($query))
				return $mysqli->insert_id;
			else
				return -1;
		}
		else {
			return -2; // already exist
		}
	}
	else
		return 0;
}


/**
 * Get User-Right Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @param string ('RIGHT' or 'USER', default is 'RIGHT')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_user_right_rel($id, $user_id, $right_id, $uniqid="", $sort_by="RIGHT", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	if($id > 0) {
		$fields = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$fields = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if($user_id  > 0) {
			$fields = "user_id=$user_id AND ";
		}
		if($right_id > 0) {
			$fields .= "right_id=".$right_id." AND";
		}

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-4); // remove ' AND'
		}
	}
	//-------------------------


	if(!empty($fields)) {

		if($sort_by == "USER")
			$sort_field = "user_id";
		else
			$sort_field = "right_id";

		$r = $mysqli->query("SELECT * FROM ".$Tables['user_right_rel']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type);

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
 * Edit User-Right Relation
 *
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_user_right_rel($id, $user_id, $right_id, $uniqid="")
{
global $mysqli, $Tables;

	if($id > 0) {
		$where = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		return 0;
	}
	//--------------------


	if($user_id > 0 && $right_id > 0) {

		// check for existing record
		$query = "SELECT id FROM ".$Tables['user_right_rel']." WHERE user_id=$user_id AND right_id=$right_id";
		$r = $mysqli->query($query);

		if( !$r->num_rows ) {
			$query = "UPDATE ".$Tables['user_right_rel']." SET user_id=$user_id, right_id=$right_id WHERE $where";

			if($mysqli->query($query))
				return 1;
			else
				return -1;
		}
		else {
			return -2; // already exists
		}
	}
	else {
		return 0;
	}

}


/**
 * Register New Right-Filter
 *
 * @param string
 * @return integer
 */
function cug_reg_right_filter($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['right_filter'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['right_filter']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
				return $mysqli->insert_id;
			}
			else {
				return -1; // Error
			}
		}
		else {
			return -2; // already exist
		}
	}
	else {
		return 0;
	}
}


/**
 * Get Right-Filter
 *
 * @param integer (default is 0)
 * @param string (default is '')
 * @param string ('TITLE' or 'ID', default is 'TITLE')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_right_filters($id=0, $title="", $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if($id > 0) {
		$where = " WHERE id=$id";
	}
	elseif(!empty($title)) {
		$where = " WHERE title='".$mysqli->escape_str($title)."'";
	}
	else {
		$where = "";
	}
	//--------------------------

	if($sort_by == "ID")
		$sort_field = "id";
	else
		$sort_field = "title";
	//--------------------------
	
	$r = $mysqli->query("SELECT * FROM ".$Tables['right_filter']."$where ORDER BY ".$sort_field." ".$sort_type);
	
	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;	
}


/**
 * Edit Existing Right-Filter
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_right_filter($id, $new_title)
{
global $mysqli, $Tables;

	if($id > 0 && !empty($new_title)) {
		
		// check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['right_filter']." WHERE title='".$mysqli->escape_str($new_title)."'");
		
		if( !$r->num_rows ) {
			if($mysqli->query("UPDATE ".$Tables['right_filter']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
				return 1;
			}
			else {
				return -1; // Error
			}
		}
		else {
			return -2; // already exist
		}

	}
	else {
		return 0;
	}
}


/**
 * Register New Action
 *
 * @param string
 * @return integer
 */
function cug_reg_action($title)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		$result = $mysqli->get_field_val($Tables['action'], "id", "title='".$mysqli->escape_str($title)."'");
			
		if(empty($result[0]['id'])) {

			if($mysqli->query("INSERT INTO ".$Tables['action']." VALUES(NULL, '".$mysqli->escape_str($title)."', NULL)")) {
				return $mysqli->insert_id;
			}
			else {
				return -1; // Error
			}
		}
		else {
			return -2; // already exist
		}
	}
	else {
		return 0;
	}
}


/**
 * Get Action
 *
 * @param integer (default is 0)
 * @param string (default is '')
 * @param string ('TITLE' or 'ID', default is 'TITLE')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_actions($id=0, $title="", $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;

	if($id > 0) {
		$where = " WHERE id=$id";
	}
	elseif(!empty($title)) {
		$where = " WHERE title='".$mysqli->escape_str($title)."'";
	}
	else {
		$where = "";
	}
	//--------------------------

	if($sort_by == "ID")
		$sort_field = "id";
	else
		$sort_field = "title";
	//--------------------------

	$r = $mysqli->query("SELECT * FROM ".$Tables['action']."$where ORDER BY ".$sort_field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}
	
return $result;
}


/**
 * Edit Existing Action
 *
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_action($id, $new_title)
{
global $mysqli, $Tables;

	if($id > 0 && !empty($new_title)) {

		// check for existing record
		$r = $mysqli->query("SELECT id FROM ".$Tables['action']." WHERE title='".$mysqli->escape_str($new_title)."'");

		if( !$r->num_rows ) {
			if($mysqli->query("UPDATE ".$Tables['action']." SET title='".$mysqli->escape_str($new_title)."' WHERE id=$id")) {
				return 1;
			}
			else {
				return -1; // Error
			}
		}
		else {
			return -2; // already exist
		}

	}
	else {
		return 0;
	}
}


/**
 * Register New Right
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer (default is 0)
 * @param string
 * @return integer
 */
function cug_reg_right($module_id, $action_id, $object_id, $filter_id=0, $uniqid="")
{
global $mysqli, $Tables;

	if($module_id>0 && $action_id>0 && $object_id>0) {

		// Check for existing record
		$query = "SELECT id FROM ".$Tables['right']." WHERE module_id=$module_id AND action_id=$action_id AND object_id=$object_id";
		$query .= ($filter_id>0) ? " AND filter_id=$filter_id" : "";
		
		$r = $mysqli->query($query);

		if( !$r->num_rows ) {

			if(!$uniqid)
				$uniq_id = uniqid();
			else
				$uniq_id = $mysqli->escape_str($uniqid);

			$values = "(NULL, $module_id, $action_id, $object_id, ";
			$values .= ($filter_id>0) ? " $filter_id, " : "NULL, ";
			$values .= "'".$uniq_id."', NULL)";
			
			$query = "INSERT INTO ".$Tables['right']." VALUES".$values;

				if($mysqli->query($query))
					return $mysqli->insert_id;
				else
					return -1;
		}
		else {
			return -2; // already exist
		}
	}
	else
		return 0;
}


/**
 * Get Right(s)
 *
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param string (default is '')
 * @param string ('ID' or 'MODULE' or 'ACTION' or 'OBJECT' or 'FILTER', default is 'ID')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_right($right_id=0, $module_id=0, $action_id=0, $object_id=0, $filter_id=0, $uniqid="", $sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	//generates search query
	if($right_id > 0) {
		$fields = "id=".$right_id;
	}
	elseif(!empty($uniqid)) {
		$fields = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		if($module_id  > 0) {
			$fields = "module_id=$module_id AND ";
		}
		if($action_id > 0) {
			$fields .= "action_id=".$action_id." AND ";
		}
		if($object_id > 0) {
			$fields .= "object_id=".$object_id." AND ";
		}
		if($filter_id > 0) {
			$fields .= "filter_id=".$filter_id." AND";
		}

		if(strlen($fields) > 0) {
			$fields = substr($fields, 0, strlen($fields)-4); // remove ' AND'
		}
	}
	//-------------------------

	if($sort_by == "MODULE") $sort_field = "module_id";
	elseif($sort_by == "ACTION") $sort_field = "action_id";
	elseif($sort_by == "OBJECT") $sort_field = "object_id";
	elseif($sort_by == "FILTER") $sort_field = "filter_id";
	else $sort_field = "id";
	
	//-------------------------
	if(!empty($fields)) {
		$query = "SELECT * FROM ".$Tables['right']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type;
	}
	else {
		$query = "SELECT * FROM ".$Tables['right']." ORDER BY ".$sort_field." ".$sort_type;
	}	
		
	//execute query
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
 * Edit Right
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param string
 * @return integer
 */
function cug_edit_right($id, $module_id, $action_id, $object_id, $filter_id, $uniqid="")
{
global $mysqli, $Tables;

	if($id > 0) {
		$where = "id=".$id;
	}
	elseif(!empty($uniqid)) {
		$where = "uniqid='".$mysqli->escape_str($uniqid)."'";
	}
	else {
		return 0;
	}
	//--------------------


	if($module_id>0 && $action_id>0 && $object_id>0 && $filter_id>0) {

		// check for existing record
		$query = "SELECT id FROM ".$Tables['right']." WHERE module_id=$module_id AND action_id=$action_id AND object_id=$object_id AND filter_id=$filter_id";
		$r = $mysqli->query($query);

		if( !$r->num_rows ) {
			$query = "UPDATE ".$Tables['right']." SET module_id=$module_id, action_id=$action_id, object_id=$object_id, filter_id=$filter_id WHERE $where";

			if($mysqli->query($query))
				return 1;
			else
				return -1;
		}
		else {
			return -2; // already exists
		}
	}
	else {
		return 0;
	}

}


/**
 * Get Rights from VIEW
 *
 * @param integer (default is 0)
 * @return array
 */
function cug_get_rights_view($id=0)
{
global $mysqli, $Views;
$rights_arr = array();
$index = 0;

	if($id > 0)
		$r = $mysqli->query("SELECT * FROM ".$Views['rights']." WHERE right_id=$id");
	else 
		$r = $mysqli->query("SELECT * FROM ".$Views['rights']);
	
	
	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$rights_arr[$index] = $arr;
			$index ++;
		}
	}
	
return $rights_arr;	
}


/**
 * Get Users by Group IDs
 * 
 * @param int|array $group_ids
 * @param bool $distinct_users (Optional, default: false)
 * @return array
 */
function cug_get_users_by_groups($group_ids, $distinct_users=false) {
	global $mysqli, $Tables;
	$result = array();
	$group_ids_arr = array();
	
	if(!is_array($group_ids)) {
		if((int)$group_ids > 0) {
			$group_ids_arr[0] = $group_ids;
		}
	}
	else {
		$group_ids_arr = $group_ids;
	}
	//---------------------
	
	if(count($group_ids_arr) > 0) {
		//get user ids
		$where = " WHERE";
		foreach($group_ids_arr as $group_id) {
			$where .= " ugr.group_id=$group_id OR";
		}
		$where = rtrim($where, "OR");
		
		if($distinct_users) {
			$query = "SELECT DISTINCT(u.id) FROM {$Tables['user']} AS u";
			$query .= " INNER JOIN {$Tables['user_group_rel']} AS ugr ON ugr.user_id = u.id";
			$query .= $where;
			
			$r = $mysqli->query($query);
			if($r->num_rows) {
				// get user info
				$where = " WHERE";
				while($row = $r->fetch_array()) {
					$where .= " id={$row['id']} OR";
				}
				$where = rtrim($where, "OR");
				
				$query2 = "SELECT * FROM {$Tables['user']} $where";
				$r2 = $mysqli->query($query2);
				if($r2->num_rows) {
					while($row2 = $r2->fetch_array(MYSQL_ASSOC)) {
						$result[] = $row2;
					}
				}
			}
		}
		else { //users related to more then one group  might be repeated
			$query = "SELECT u.*, ugr.group_id AS group_id FROM {$Tables['user']} AS u";
			$query .= " INNER JOIN {$Tables['user_group_rel']} AS ugr ON ugr.user_id = u.id";
			$query .= $where;
			
			$r = $mysqli->query($query);
			if($r->num_rows) {
				while($row = $r->fetch_array(MYSQL_ASSOC)) {
					$result[] = $row;
				}				
			}
		}
		
	}
	
	return $result;
}


/**
 * Get Users
 *
 * @param object of User Class (default os NULL)
 * @param string ('ID' or 'REALNAME', default is 'REALNAME')
 * @param string ('DESC' or 'ASC', default is 'ASC')
 * @return array
 */
function cug_get_users($obj=null, $sort_by="REALNAME", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$fields = "";

	if($obj) {
		
		// generate search query
		if(!empty($obj->id)) 
			$fields .= "id=".$obj->id." AND";
		elseif(!empty($obj->uniqid)) 
			$fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."' AND";
		else {
			if($obj->status != null && $obj->status>=0) $fields .= " status=".$mysqli->escape_str($obj->status)." AND";
			if($obj->gender != null && $obj->gender>=0) $fields .= " gender=".$mysqli->escape_str($obj->gender)." AND";
			if($obj->fav_artist_id != null && $obj->fav_artist_id>=0) $fields .= " fav_artist_id=".$mysqli->escape_str($obj->fav_artist_id)." AND";
			if($obj->fav_genre_id != null && $obj->fav_genre_id>=0) $fields .= " fav_genre_id=".$mysqli->escape_str($obj->fav_genre_id)." AND";
			if($obj->reg_module_id != null && $obj->reg_module_id>=0) $fields .= " reg_module_id=".$mysqli->escape_str($obj->reg_module_id)." AND";
			if($obj->reg_module_details_id != null && $obj->reg_module_details_id>=0) $fields .= " reg_module_details_id=".$mysqli->escape_str($obj->reg_module_details_id)." AND";
			
			if($obj->img_34 != null && $obj->img_34 >= 0) $fields .= " img_34=".$mysqli->escape_str($obj->img_34).",";
			if($obj->img_64 != null && $obj->img_64 >= 0) $fields .= " img_64=".$mysqli->escape_str($obj->img_64).",";
			if($obj->img_174 != null && $obj->img_174 >= 0) $fields .= " img_174=".$mysqli->escape_str($obj->img_174).",";
			if($obj->img_300 != null && $obj->img_300 >= 0) $fields .= " img_300=".$mysqli->escape_str($obj->img_300).",";
			if($obj->img_600 != null && $obj->img_600 >= 0) $fields .= " img_600=".$mysqli->escape_str($obj->img_600).",";
			if($obj->img_orgn != null && $obj->img_orgn >= 0) $fields .= " img_orgn=".$mysqli->escape_str($obj->img_orgn).",";
			
			//-----------
			if(!empty($obj->nick_name)) {
				if(strlen($obj->nick_name) > 1)
					$fields .= " nick_name LIKE '%".$mysqli->escape_str($obj->nick_name)."%' AND";
				else 
					$fields .= " nick_name LIKE '".$mysqli->escape_str($obj->nick_name)."%' AND";
			}
			//-----------
			if(!empty($obj->real_name)) {
				if(strlen($obj->real_name) > 1)
					$fields .= " real_name LIKE '%".$mysqli->escape_str($obj->real_name)."%' AND";
				else
					$fields .= " real_name LIKE '".$mysqli->escape_str($obj->real_name)."%' AND";
			}
			//-----------
			if(!empty($obj->email)) {
				if(strlen($obj->email) > 1)
					$fields .= " email LIKE '%".$mysqli->escape_str($obj->email)."%' AND";
				else
					$fields .= " email LIKE '".$mysqli->escape_str($obj->email)."%' AND";
			}
			//-----------
			if(!empty($obj->country_id)) $fields .= " country_id=".$mysqli->escape_str($obj->country_id)." AND";
			//-----------
			if(!empty($obj->city)) {
				if(strlen($obj->city) > 1)
					$fields .= " city LIKE '%".$mysqli->escape_str($obj->city)."%' AND";
				else
					$fields .= " city LIKE '".$mysqli->escape_str($obj->city)."%' AND";
			}
			//-----------
			if(!empty($obj->birth_date)) $fields .= " birth_date='".$mysqli->escape_str($obj->birth_date)."' AND";
			if(!empty($obj->register_date)) $fields .= " register_date='".$mysqli->escape_str($obj->register_date)."' AND";
			if(!empty($obj->register_date_finish)) $fields .= " register_date_finish='".$mysqli->escape_str($obj->register_date_finish)."' AND";
			if(!empty($obj->register_ip)) $fields .= " register_ip='".$mysqli->escape_str($obj->register_ip)."' AND";
			if(!empty($obj->last_login_date)) $fields .= " last_login_date='".$mysqli->escape_str($obj->last_login_date)."' AND";
			if(!empty($obj->last_login_ip)) $fields .= " last_login_ip='".$mysqli->escape_str($obj->last_login_ip)."' AND";
			if(!empty($obj->update_time)) $fields .= " update_time='".$mysqli->escape_str($obj->update_time)."' AND";
		}
	}

	//-------------------------
	if($sort_by == "REALNAME")
		$sort_field = "real_name";
	else
		$sort_field = "id";
	//-------------------------
	if(!empty($fields)) {
		$fields = substr($fields, 0, strlen($fields)-4); // remove last ' AND'
		$query = "SELECT * FROM ".$Tables['user']." WHERE ".$fields." ORDER BY ".$sort_field." ".$sort_type;
	}
	else {
		$query = "SELECT * FROM ".$Tables['user']." ORDER BY ".$sort_field." ".$sort_type;
	}
	
	//execute query
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
 * Get Rights
 *
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)
 * @param integer (default is 0)  
 * @return array
 */
 function cug_get_rights($user_id=0, $group_id=0, $module_id=0, $action_id=0, $object_id=0, $filter_id=0)
{
global $mysqli, $Tables;
$result = array();
$rights = array();
$user_group_right_ids = array();
$user_group_ids = array();

$index = 0;
$fields = "";


	// get rights
	$rights = cug_get_right(0, $module_id, $action_id, $object_id, $filter_id);
	
	//get user's own right ids
	if($user_id > 0) {
		$user_right_ids = cug_get_user_right_rel(0, $user_id, 0);
		
			for($i=0; $i<count($user_right_ids); $i++) {
				$user_group_right_ids[$index] = $user_right_ids[$i]['right_id'];
				$index ++;
			}

		//get user's all group ids
		$group_ids = cug_get_user_group_rel(0, $user_id, 0, "", "GROUP");
		
			for($i=0; $i<count($group_ids); $i++) {
				$user_group_ids[$i] = $group_ids[$i]['group_id'];
			}
	}
	
	
	//get group right ids
	if( $group_id > 0 && (array_search($group_id, $user_group_ids) === false) ) {
		$user_group_ids[count($user_group_ids)] = $group_id;
	}
	$total_group_ids = count($user_group_ids);
	
		for($i=0; $i<$total_group_ids; $i++) {
			$group_right_ids = cug_get_usergroup_right_rel(0, $user_group_ids[$i], 0);
				for($j=0; $j<count($group_right_ids); $j++) {
					$user_group_right_ids[$index] = $group_right_ids[$j]['right_id'];
					$index ++;
				}
		$index = 0;		
		}

	
	//get final result, compare '$user_group_right_ids' and '$rights' arrays
	if(count($user_group_right_ids) > 0) {
		$right_count = count($rights);
		
			for($i=0; $i<$right_count; $i++) {
				if( array_search($rights[$i]['id'], $user_group_right_ids) !== false) {
					$result[$index] = $rights[$i];
					$index ++;
				}
			}			
	
		return $result;
	}
	else {	    
		return $result;
	}

}


/**
 * Check if User is allowed to use Module
 * 
 * @param int $user_id
 * @param int $module_id
 * @return boolean
 */
function cug_check_user_module_access($user_id, $module_id) {
	global $mysqli, $Tables;
	$result = false;

	if($user_id > 0 && $module_id > 0) {		
		$user_module_status = cug_get_user_module_status($user_id, $module_id);
		
		if($user_module_status == 0) {//status not defined
			//check user's group
			$groups_arr = cug_get_user_group_rel($id=0, $user_id, $group_id=0);
			
				foreach($groups_arr as $arr) {
					$group_id = $arr['group_id'];
					
					if(cug_check_user_group_module_access($group_id, $module_id)) {
						$result = true;
						break;
					}
				}
		}
		elseif($user_module_status == 6 || $user_module_status == 1 || $user_module_status == 2) { //IDs from 'user_status_list', 6 - Allowed; 1 - Active; 2 - Approved;
			$result = true;
		}
		else {
			$result = false;
		}

	}

	return $result;
}



/**
 * Get User's Status in the Module
 *
 * @param int $user_id
 * @param int $module_id
 * @return int (ID of the Status, 0 means no entry in the table)
 */
function cug_get_user_module_status($user_id, $module_id) {
	global $mysqli, $Tables;
	$result = 0;

	if($user_id > 0 && $module_id > 0) {
		//check user's status
		$query = "SELECT status FROM {$Tables['user_module_rel']} WHERE user_id=".$mysqli->escape_str($user_id)." AND module_id=".$mysqli->escape_str($module_id);
		$r = $mysqli->query($query);

			if($r) {
				if($r->num_rows) {
					$row = $r->fetch_array();
					$result = $row['status'];
				}
			}
	}

	return $result;
}


/**
 * Check if User's Group is allowed to use Module
 * 
 * @param int $group_id
 * @param int $module_id
 * @return boolean
 */
function cug_check_user_group_module_access($group_id, $module_id) {
	global $mysqli, $Tables;
	$result = false;

	if($group_id > 0 && $module_id > 0) {
		$query = "SELECT id FROM {$Tables['user_group_module_rel']} WHERE group_id=".$mysqli->escape_str($group_id)." AND module_id=".$mysqli->escape_str($module_id);
		$r = $mysqli->query($query);

			if($r) {
				if($r->num_rows) {
					$result = true;
				}
			}
	}
	
	return $result;
}


/**
 * Delete User
 * 
 * @param int $user_id
 * @param boolean $del_related_objects
 * @return boolean
 */
function cug_del_user($user_id, $del_related_objects=true) {
	global $mysqli, $Tables;
	
	if($user_id > 0) {
		//delete related objects
		if($del_related_objects) {
			//
		}
		
		//delete user
		$query = "DELETE FROM {$Tables['user']} WHERE id=$user_id";
		$mysqli->query($query);
		
		//delete logs
		//
		
		return true;
	}
	
	return false;
}


/**
 * Check User's Password for complexity
 * 
 * @param string $pass
 * @return boolean
 */
function cug_check_user_pass_complexity($password) {
	if(strlen($password) >= 8)
		return true;
	else 
		return false;
}


/**
 * Get product owners ids (client ids) from 'te_user_product_owner_rel' and 'te_user_group_product_owner_rel' tables for the user;
 * Used in Tag Editor
 * 
 * @param int $user_id
 * @return array
 */
function cug_get_allowed_product_owner_ids_te($user_id) {
    global $mysqli, $Tables;
    $result = array();
    
    $query = "SELECT product_owner AS client_id, c.title AS client_title FROM {$Tables['te_user_product_owner_rel']} AS upor ";
    $query .= "INNER JOIN {$Tables['client']} AS c ON upor.product_owner=c.id ";
    $query .= "WHERE user_id=$user_id ";
    $query .= "UNION ";
    $query .= "SELECT DISTINCT(ugpor.product_owner) AS client_id, c.title AS client_title FROM {$Tables['te_user_group_product_owner_rel']} AS ugpor ";
    $query .= "INNER JOIN {$Tables['user_group_rel']} AS ugr ON ugpor.user_group_id=ugr.group_id ";
    $query .= "INNER JOIN {$Tables['client']} AS c ON ugpor.product_owner=c.id ";
    $query .= "WHERE ugr.user_id=$user_id";

    $r = $mysqli->query($query);
    if($r && $r->num_rows) {
        $index = 0;
        while($row = $r->fetch_assoc()) {
            $result[$index]['client_id'] = $row['client_id'];
            $result[$index]['client_title'] = $row['client_title'];
            $index ++;
        }
    }
    
    return $result;
}


/**
 * Get Client of the User or/and Group
 * 
 * @param int $user_id
 * @param int $group_id (Opional, default: 0)
 * @param string $return_field (Optional, 'id' or '*', default: 'id')
 * @return int|array
 */
function cug_get_user_group_client($user_id, $group_id=0, $return_field="id") {
    global $mysqli, $Tables;
    $result = 0;
    
    if($user_id > 0 || $group_id > 0) {
        if($return_field != "id" && $return_field != "*")
            $return_field = "id";
        
        $query = "SELECT c.$return_field FROM client_list AS c ";
        $query .= "INNER JOIN user_group_list AS ugl ON ugl.client_id=c.id ";
        $query .= "INNER JOIN user_group_rel AS ugr ON ugl.id=ugr.group_id ";
        $query .= "INNER JOIN user_list AS u ON ugr.user_id=u.id ";
        $query .= "WHERE";
        $query .= ($user_id > 0) ? " u.id=$user_id AND" : "";
        $query .= ($group_id > 0) ? " ugl.id=$group_id AND" : "";
        
        $query = rtrim($query, "AND");
        $query .= " LIMIT 1";
        
        $r = $mysqli->query($query);
        if($r && $r->num_rows) {
            $row = $r->fetch_assoc();
            
            if($return_field == "id")
                $result = $row['id'];
            else 
                $result = $row;
        }
    }
    
    return $result;
}
?>

