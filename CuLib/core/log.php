<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Log
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------


/**
 * Log Class (for Tag Editor)
 *
 * @param	id (INT)
 * @param	user_id (INT)
 * @param	action_id (INT)
 * @param	subaction_id (INT)
 * @param	object_id (INT)
 * @param	object_item_id (INT)
 * @param	subitem_id (INT)
 * @param	subobject_id (INT)
 * @param	start_time (STRING - MySQL DATETIME Format)
 * @param	end_time (STRING - MySQL DATETIME Format)
 * @param	country_id (INT)
 * @param	ip (STRING)
 * @param	session_id (STRING - PHP Session ID)
 * @param	uniqid (STRING)
 * @param	update_time (STRING - MySQL TIMESTAMP Format)
 */
class cug__log_te
{
	public
	$id,
	$user_id,
	$action_id,
	$subaction_id,
	$object_id,
	$object_item_id,
	$subitem_id,
	$subobject_id,
	$start_time,
	$end_time,
	$country_id,
	$ip,
	$session_id,
	$uniq_id,
	$update_time;
}


/**
 * Log Class (for sending emails)
 * 
 * @param	bigint $id
 * @param	tinyint $mass_email
 * @param	bigint $user_id
 * @param	int $module_id
 * @param	int $module_details_id
 * @param	int $send_method_id
 * @param	int $email_cat_id
 * @param	string $email_from_addr
 * @param	string $email_to_addr
 * @param	string $email_subject
 * @param	string $email_body
 * @param	string $user_request_ip
 * @param	int $user_request_country_id
 * @param	string $user_request_time (MySQL TIMESTAMP Format)
 * @param	string $email_sent_time (MySQL TIMESTAMP Format)
 * @param	int $email_sent_status
 * @param	string $email_sent_error
 * @param	string $request_finish_time
 * @param	string $comments
 * @param	string $email_hash_code
 * @param	string $update_time (MySQL TIMESTAMP Format)
 */
class cug__log_email {
	public 
	$id,
	$mass_email,
	$user_id,
	$module_id,
	$module_details_id,
	$send_method_id,
	$email_cat_id,
	$email_from_addr,
	$email_to_addr,
	$email_subject,
	$email_body,
	$user_request_ip,
	$user_request_country_id,
	$user_request_time,
	$email_sent_time,
	$email_sent_status,
	$request_finish_time,
	$email_sent_error,
	$comments,
	$email_hash_code,
	$update_time;
}



/**
 * WebPage Log Class
 *
 * @param	$id INT
 * @param	$page_id INT
 * @param	$page_cat_id INT
 * @param	$item_id INT
 * @param	$subitem_id INT
 * @param	$action_id INT
 * @param	$user_id INT
 * @param	$country_id INT
 * @param	$ip STRING
 * @param	$request_time STRING - MySQL DATETIME Format
 * @param	$browser STRING
 * @param	$hua STRING
 * @param	$request_url STRING
 * @param	$referer_url STRING
 * @param	$referer_host STRING
 * @param	$ref_from STRING 
 * @param	$session_id STRING
 * @param	$update_time STRING - MySQL TIMESTAMP Format
 */
class cug__log_webpage
{
	public
	$id,
	$page_id,
	$page_cat_id,
	$item_id,
	$subitem_id,
	$action_id,
	$user_id,
	$country_id,
	$ip,
	$request_time,
	$browser,
	$hua,
	$request_url,
	$referer_url,
	$referer_host,
	$ref_from,
	$session_id,
	$update_time;
}


/**
 * WebPage Details Log Class
 *
 * @param	$id INT
 * @param	$page_log_id INT
 * @param	$page_id INT
 * @param	$page_item_id INT
 * @param	$action_id INT
 * @param	$subaction_id INT
 * @param	$object_id INT
 * @param	$item_id INT
 * @param	$subitem_id INT
 * @param	$portal_id INT
 * @param	$action_time STRING - MySQL DATETIME Format
 * @param	$update_time STRING - MySQL TIMESTAMP Format
 */
class cug__log_webpage_details
{
	public
	$id,
	$page_log_id,
	$page_id,
	$page_item_id,
	$action_id,
	$subaction_id,
	$object_id,
	$item_id,
	$subitem_id,
	$portal_id,
	$action_time,
	$update_time;
}


/**
 * Register New Log (for Tag Editor)
 *
 * @param object of cug__log_te class
 * @return integer
 */
function cug_reg_log_te($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";

	if(isset($obj->user_id) && !empty($obj->action_id) && !empty($obj->object_id) && !empty($obj->object_item_id)) {
		
		$fields = " (user_id,action_id,object_id,object_item_id,";
		$values = " VALUES(".$obj->user_id.",".$obj->action_id.",".$obj->object_id.",".$obj->object_item_id.",";
		
		if($obj->subaction_id != null && $obj->subaction_id >= 0) {
			$fields .= "subaction_id,";
			$values .= $obj->subaction_id.",";
		}
		
		if($obj->subitem_id != null && $obj->subitem_id >= 0) {
			$fields .= "subitem_id,";
			$values .= $obj->subitem_id.",";
		}
		
		if($obj->subobject_id != null && $obj->subobject_id >= 0) {
			$fields .= "subobject_id,";
			$values .= $obj->subobject_id.",";
		}
		
		if(!empty($obj->start_time)) {
			$fields .= "start_time,";
			$values .= "'".$mysqli->escape_str($obj->start_time)."',";
		}
		
		if(!empty($obj->end_time)) {
			$fields .= "end_time,";
			$values .= "'".$mysqli->escape_str($obj->end_time)."',";
		}
		
		if($obj->country_id != null && $obj->country_id >= 0) {
			$fields .= "country_id,";
			$values .= $obj->country_id.",";
		}
		
		if(!empty($obj->ip)) {
			$fields .= "ip,";
			$values .= "'".$mysqli->escape_str($obj->ip)."',";
		}
		
		if(!empty($obj->session_id)) {
			$fields .= "session_id,";
			$values .= "'".$mysqli->escape_str($obj->session_id)."',";
		}
		
		$fields .= "uniqid)";
		if(!empty($obj->f_uniqid)) {
			$values .= "'".$mysqli->escape_str($obj->f_uniqid)."')";
		}
		else {
			$uniqid = uniqid();
			$values .= "'".$uniqid."')";
		}
		
		
		$query = "INSERT INTO ".$Tables['log_te'].$fields.$values;
		
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
 * Edit Existing Log (Tag Editor)
 *
 * @param integer (ID of Existing Log)
 * @param object of cug__log_te class
 * @return bool
 */
function cug_edit_log_te($log_id, $obj)
{
global $mysqli, $Tables;
$fields = "";

	if($obj && $log_id > 0) {
		
		if($obj->user_id != null && $obj->user_id >= 0) $fields .= "user_id=".$obj->user_id.",";
		if($obj->action_id != null && $obj->action_id >= 0) $fields .= "action_id=".$obj->action_id.",";
		if($obj->subaction_id != null && $obj->subaction_id >= 0) $fields .= "subaction_id=".$obj->subaction_id.",";
		if($obj->object_id != null && $obj->object_id >= 0) $fields .= "object_id=".$obj->object_id.",";
		if($obj->object_item_id != null && $obj->object_item_id >= 0) $fields .= "object_item_id=".$obj->object_item_id.",";
		if(!empty($obj->start_time)) $fields .= "start_time='".$mysqli->escape_str($obj->start_time)."',";
		if(!empty($obj->end_time)) $fields .= "end_time='".$mysqli->escape_str($obj->end_time)."',";
		if($obj->country_id != null && $obj->country_id >= 0) $fields .= "country_id=".$obj->country_id.",";
		if(!empty($obj->ip)) $fields .= "ip='".$mysqli->escape_str($obj->ip)."',";
		if(!empty($obj->session_id)) $fields .= "session_id='".$mysqli->escape_str($obj->session_id)."',";
		if(!empty($obj->uniqid)) $fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."',";
		
			if(strlen($fields) > 0) {
				$fields = substr($fields, 0, strlen($fields)-1);
				$query = "UPDATE ".$Tables['log_te']." SET ".$fields." WHERE id=".$log_id;
			
					if($mysqli->query($query))
						return TRUE;
					else
						return FALSE;
			}
			else
				return FALSE;
	}
	else {
		return FALSE;
	}
}


/**
 * Get Log (for Tag Editor)
 *
 * @param object of cug__log_te class
 * @return array
 */
function cug_get_logs_te($obj)
{
global $mysqli, $Tables;
$fields = "";
$result = array();
$index = 0;

	if($obj) {
		
		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND ";
		}
		elseif(!empty($obj->uniqid)) {
			$fields .= "uniqid='".$mysqli->escape_str($obj->uniqid)."' AND ";
		}
		else {
			if($obj->user_id != null && $obj->user_id >= 0) {
				$fields .= "user_id=".$obj->user_id." AND ";
			}
			if($obj->action_id != null && $obj->action_id >= 0) {
				$fields .= "action_id=".$obj->action_id." AND ";
			}
			if($obj->subaction_id != null && $obj->subaction_id >= 0) {
				$fields .= "subaction_id=".$obj->subaction_id." AND ";
			}
			if($obj->object_id != null && $obj->object_id >= 0) {
				$fields .= "object_id=".$obj->object_id." AND ";
			}
			if($obj->object_item_id != null && $obj->object_item_id >= 0) {
				$fields .= "object_item_id=".$obj->object_item_id." AND ";
			}
			if($obj->subitem_id != null && $obj->subitem_id >= 0) {
				$fields .= "subitem_id=".$obj->subitem_id." AND ";
			}
			if(!empty($obj->start_time)) {
				$fields .= "start_time='".$mysqli->escape_str($obj->start_time)."' AND ";
			}
			if(!empty($obj->end_time)) {
				$fields .= "end_time='".$mysqli->escape_str($obj->end_time)."' AND ";
			}
			if($obj->country_id != null && $obj->country_id >= 0) {
				$fields .= "country_id=".$obj->country_id." AND ";
			}
			if(!empty($obj->ip)) {
				$fields .= "ip='".$mysqli->escape_str($obj->ip)."' AND ";
			}
			if(!empty($obj->hua)) {
				$fields .= "hua='".$mysqli->escape_str($obj->hua)."' AND ";
			}
			if(!empty($obj->update_time)) {
				$fields .= "update_time='".$mysqli->escape_str($obj->update_time)."' AND ";
			}
		}
		
		$fields = substr($fields, 0, strlen($fields)-4);
		$query = "SELECT * FROM ".$Tables['log_te']." WHERE ".$fields;
		$r = $mysqli->query($query);
		
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
 * Delete Garbage Logs - entries without 'end_time' values (for Tag Editor)
 *
 * @param integer (Timeout in seconds)
 * @return bool
 */
function cug_del_logs_garbage_te($timeout, $object_id=0)
{
global $mysqli, $Tables;
$diff = $timeout + 10; //give some time to cug_edit_log_te() function

	if($timeout > 0) {
		if($object_id > 0) $subquery = " object_id=$object_id AND "; else $subquery = "";
		
		$query = "DELETE FROM ".$Tables['log_te']." WHERE $subquery end_time IS NULL AND TIMESTAMPDIFF(SECOND, start_time, NOW()) >= $diff";

			if($mysqli->query($query))
				return true;
			else
				return false;
	}
	else {
		return false;
	}
}


/**
 * Delete own Garbage Logs - entries only of specific user and objects (for Tag Editor)
 *
 * @param string (PHP Session ID)
 * @param integer
 * @param integer
 * @param integer
 * @param array
 * @return bool
 */
function cug_del_logs_garbage_own_te($session_id, $user_id, $action_id, $object_id, $object_item_ids)
{
global $mysqli, $Tables;
$ids = "";

	if($session_id && $user_id > 0 && $action_id > 0 && $object_id > 0 && count($object_item_ids) > 0) {
		
		foreach($object_item_ids as $object_item_id) {
			$ids .= "object_item_id=$object_item_id OR ";
		}
		
			if($ids) {
				$ids = substr($ids, 0, strlen($ids)-3);
				$query = "DELETE FROM ".$Tables['log_te']." WHERE end_time IS NULL AND action_id=$action_id AND user_id=$user_id AND object_id=$object_id AND ($ids) AND session_id='$session_id'";
			
					if($mysqli->query($query))
						return true;
					else
						return false;
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
 * Get Active Logs - entries where 'end_time' is NULL (for Tag Editor)
 *
 * @param integer (Timeout in seconds)
 * @param integer
 * @param integer
 * @param integer
 * @param integer (default is 0)
 * @return array
 */
function cug_get_active_logs_te($timeout, $action_id, $object_id, $object_item_id, $subitem_id=0)
{
global $mysqli, $Tables;	
$result = array();
$index = 0;

$query = "SELECT * FROM ".$Tables['log_te']." WHERE end_time IS NULL AND (TIMESTAMPDIFF(SECOND, start_time, NOW()) < $timeout)";
$query_part = "";

	if($action_id > 0)
		$query_part .= "action_id=$action_id AND ";
	if($object_id > 0)
		$query_part .= "object_id=$object_id AND ";
	if($object_item_id > 0)
		$query_part .= "object_item_id=$object_item_id AND ";
	if($subitem_id > 0)
		$query_part .= "subitem_id=$subitem_id AND ";
	
	
		if(strlen($query_part) > 0) {
			$query_part = substr($query_part, 0, strlen($query_part)-4);
			$query = $query." AND ".$query_part;
		}
	//echo $query;	
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
 * Get Last Log - Entry where 'end_time' is not NULL (for Tag Editor)
 *
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer
 * @param integer (default is 0)
 * @return array
 */
function cug_get_last_log_te($user_id, $action_id, $subaction_id, $object_id, $object_item_id, $subitem_id=0)
{
global $mysqli, $Tables;
$result = array();
$index = 0;

$query_part = "";

	if($user_id > 0)
		$query_part .= "user_id=$user_id AND ";
	if($action_id > 0)
		$query_part .= "action_id=$action_id AND ";
	if($subaction_id > 0)
		$query_part .= "subaction_id=$subaction_id AND ";	
	if($object_id > 0)
		$query_part .= "object_id=$object_id AND ";
	if($object_item_id > 0)
		$query_part .= "object_item_id=$object_item_id AND ";
	if($subitem_id > 0)
		$query_part .= "subitem_id=$subitem_id AND ";
	
	
	if(strlen($query_part) > 0) {
		$query_part = substr($query_part, 0, strlen($query_part)-4);
		$query = "SELECT * FROM ".$Tables['log_te']." WHERE $query_part AND end_time IS NOT NULL ORDER BY end_time DESC LIMIT 1";
	}
	else {
		$query = "SELECT * FROM ".$Tables['log_te']." WHERE end_time IS NOT NULL ORDER BY end_time DESC LIMIT 1";
	}

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
 * Get WebPage Log
 *
 * @param $obj OBJECT - 'cug__log_webpage' class
 * @param $request_time_from STRING - MySQL DATETIME Format, Default null
 * @param $request_time_to STRING - MySQL DATETIME Format, Default null
 * @param $limit_from INT - Default 0  
 * @param $limit_num INT - Default 1000
 * @return Array
 */
function cug_get_logs_webpage($obj, $request_time_from=null, $request_time_to=null, $limit_from=0, $limit_num=1000)
{
global $mysqli, $Tables;
$fields = "";
$result = array();

	if($obj) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND ";
		}
		else {
			if($obj->page_id != null && $obj->page_id >= 0) {
				$fields .= "page_id=".$obj->page_id." AND ";
			}
			if($obj->page_cat_id != null && $obj->page_cat_id >= 0) {
				$fields .= "page_cat_id=".$obj->page_cat_id." AND ";
			}
			if($obj->item_id != null && $obj->item_id >= 0) {
				$fields .= "item_id=".$obj->item_id." AND ";
			}
			if($obj->subitem_id != null && $obj->subitem_id >= 0) {
				$fields .= "subitem_id=".$obj->subitem_id." AND ";
			}
			if($obj->action_id != null && $obj->action_id >= 0) {
				$fields .= "action_id=".$obj->action_id." AND ";
			}
			if($obj->user_id != null && $obj->user_id >= 0) {
				$fields .= "user_id=".$obj->user_id." AND ";
			}
			if($obj->country_id != null && $obj->country_id >= 0) {
				$fields .= "country_id=".$obj->country_id." AND ";
			}			
			if(!empty($obj->ip)) {
				$fields .= "ip='".$mysqli->escape_str($obj->ip)."' AND ";
			}
			
			//request time
			if(!empty($obj->request_time)) {
				$fields .= "request_time='".$mysqli->escape_str($obj->request_time)."' AND ";
			}
			else {
				if($request_time_from) {
					$fields .= "request_time>='".$request_time_from."' AND ";
					if($request_time_to) {
						$fields .= "request_time<='".$request_time_to."' AND ";
					}
				}
				elseif($request_time_to) {
					$fields .= "request_time<='".$request_time_to."' AND ";
				}
			}
			//---------------------
			
			
			if(!empty($obj->browser)) {
				$fields .= "browser='".$mysqli->escape_str($obj->browser)."' AND ";
			}
			if(!empty($obj->hua)) {
				$fields .= "hua='".$mysqli->escape_str($obj->hua)."' AND ";
			}
			if(!empty($obj->request_url)) {
				$fields .= "request_url='".$mysqli->escape_str($obj->request_url)."' AND ";
			}
			if(!empty($obj->referer_url)) {
				$fields .= "referer_url='".$mysqli->escape_str($obj->referer_url)."' AND ";
			}
			if(!empty($obj->referer_host)) {
				$fields .= "referer_host='".$mysqli->escape_str($obj->referer_host)."' AND ";
			}
			if(!empty($obj->session_id)) {
				$fields .= "session_id='".$mysqli->escape_str($obj->session_id)."' AND ";
			}						
			if(!empty($obj->update_time)) {
				$fields .= "update_time='".$mysqli->escape_str($obj->update_time)."' AND ";
			}
		}

		$fields = substr($fields, 0, strlen($fields)-4);
		$query = "SELECT * FROM ".$Tables['log_webpage']." WHERE ".$fields. "LIMIT $limit_from, $limit_num";
		$r = $mysqli->query($query);

		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[] = $arr;
			}
		}
	}

return $result;
}



/**
 * Get WebPage Details Log
 *
 * @param $obj OBJECT - 'cug__log_webpage_details' class
 * @param $action_time_from STRING - MySQL DATETIME Format, Default null
 * @param $action_time_to STRING - MySQL DATETIME Format, Default null
 * @param $limit_from INT - Default 0
 * @param $limit_num INT - Default 1000
 * @return Array
 */
function cug_get_logs_webpage_det($obj, $action_time_from=null, $action_time_to=null, $limit_from=0, $limit_num=1000)
{
global $mysqli, $Tables;
$fields = "";
$result = array();
$index = 0;

	if($obj) {

		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND ";
		}
		else {
			if($obj->page_log_id != null && $obj->page_log_id >= 0) {
				$fields .= "page_log_id=".$obj->page_log_id." AND ";
			}
			if($obj->page_id != null && $obj->page_id >= 0) {
				$fields .= "page_id=".$obj->page_id." AND ";
			}
			if($obj->page_item_id != null && $obj->page_item_id >= 0) {
				$fields .= "page_item_id=".$obj->page_item_id." AND ";
			}			
			if($obj->action_id != null && $obj->action_id >= 0) {
				$fields .= "action_id=".$obj->action_id." AND ";
			}
			if($obj->subaction_id != null && $obj->subaction_id >= 0) {
				$fields .= "subaction_id=".$obj->subaction_id." AND ";
			}
			if($obj->object_id != null && $obj->object_id >= 0) {
				$fields .= "object_id=".$obj->object_id." AND ";
			}
			if($obj->item_id != null && $obj->item_id >= 0) {
				$fields .= "item_id=".$obj->item_id." AND ";
			}
			if($obj->subitem_id != null && $obj->subitem_id >= 0) {
				$fields .= "subitem_id=".$obj->subitem_id." AND ";
			}
			if($obj->portal_id != null && $obj->portal_id >= 0) {
				$fields .= "portal_id=".$obj->portal_id." AND ";
			}

				
			//action_time
			if(!empty($obj->action_time)) {
				$fields .= "action_time='".$mysqli->escape_str($obj->action_time)."' AND ";
			}
			else {
				if($action_time_from) {
					$fields .= "action_time>='".$action_time_from."' AND ";
					if($action_time_to) {
						$fields .= "action_time<='".$action_time_to."' AND ";
					}
				}
				elseif($action_time_to) {
					$fields .= "action_time<='".$action_time_to."' AND ";
				}
			}
			//---------------------
				
			if(!empty($obj->update_time)) {
				$fields .= "update_time='".$mysqli->escape_str($obj->update_time)."' AND ";
			}
		}

		$fields = substr($fields, 0, strlen($fields)-4);
		$query = "SELECT * FROM ".$Tables['log_webpage_det']." WHERE ".$fields. "LIMIT $limit_from, $limit_num";
		$r = $mysqli->query($query);

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
 * Register WebPage Log
 *
 * @param $obj OBJECT - 'cug__log_webpage' class
 * @return integer 
 */
function cug_reg_log_webpage($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";

	if(!empty($obj->action_id) && !empty($obj->page_id) && !empty($obj->page_cat_id)) {

		$fields = " (action_id,page_id,page_cat_id,";
		$values = " VALUES(".$obj->action_id.",".$obj->page_id.",".$obj->page_cat_id.",";

		if($obj->item_id != null && $obj->item_id >= 0) {
			$fields .= "item_id,";
			$values .= $obj->item_id.",";
		}

		if($obj->subitem_id != null && $obj->subitem_id >= 0) {
			$fields .= "subitem_id,";
			$values .= $obj->subitem_id.",";
		}
		
		if($obj->user_id != null && $obj->user_id >= 0) {
			$fields .= "user_id,";
			$values .= $obj->user_id.",";
		}
		
		if($obj->country_id != null && $obj->country_id >= 0) {
			$fields .= "country_id,";
			$values .= $obj->country_id.",";
		}
		
		//ip
		if(!empty($obj->ip)) {
			$ip = $obj->ip;
		}
		else {
			$ip = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
		}
			if($ip) {
				$fields .= "ip,";
				$values .= "'".$mysqli->escape_str($ip)."',";
			}
		//------------------

		//request_time	
		if(!empty($obj->request_time)) {
			$request_time = $obj->request_time;
		}
		else {
			$request_time = @date("Y-m-d H:i:s");
		}
		$fields .= "request_time,";
		$values .= "'".$mysqli->escape_str($request_time)."',";
		//------------------

		
		if(!empty($obj->browser)) {
			$fields .= "browser,";
			$values .= "'".$mysqli->escape_str($obj->browser)."',";
		}
		
		if(!empty($obj->ref_from)) {
			$fields .= "ref_from,";
			$values .= "'".$mysqli->escape_str($obj->ref_from)."',";
		}

		//hua
		if(!empty($obj->hua)) {
			$hua = $obj->hua;
		}
		else {
			$hua = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		}
			if($hua) {
				$fields .= "hua,";
				$values .= "'".$mysqli->escape_str($hua)."',";
			}
		//------------------
		
		//request_url
		if(!empty($obj->request_url)) {
			$request_url = $obj->request_url;
		}
		else {
			$request_url = !empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME']."://" : "";
			$request_url .= !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : "";
			$request_url .= !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
		}
			if($request_url) {
				$fields .= "request_url,";
				$values .= "'".$mysqli->escape_str($request_url)."',";
			}
		//------------------

		//referer_url, referer_host
		if(!empty($obj->referer_url)) {
			$referer_url = $obj->referer_url;
		}
		else {
			$referer_url = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";
		}
			
			if($referer_url) {
				$fields .= "referer_url,";
				$values .= "'".$mysqli->escape_str($referer_url)."',";
				
				$arr = parse_url($referer_url);
					if(!empty($arr['host'])) {
						$fields .= "referer_host,";
						$values .= "'".$mysqli->escape_str($arr['host'])."',";
					}
			}
		//------------------		

		if(!empty($obj->session_id)) {
			$fields .= "session_id,";
			$values .= "'".$mysqli->escape_str($obj->session_id)."',";
		}

		//update_time
		if(!empty($obj->update_time)) {
			$update_time = $obj->update_time;
		}
		else {
			$update_time = "null";
		}
		$fields .= "update_time)";
		$values .= ($update_time == "null") ? "null)" : "'".$mysqli->escape_str($update_time)."')";
		//------------------		

		$query = "INSERT INTO ".$Tables['log_webpage'].$fields.$values;

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
 * Register WebPage Details Log
 *
 * @param $obj OBJECT - 'cug__log_webpage_details' class
 * @return integer
 */
function cug_reg_log_webpage_det($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";

	if(!empty($obj->page_log_id) && !empty($obj->action_id) && !empty($obj->object_id)) {

		$fields = " (page_log_id,action_id,object_id,";
		$values = " VALUES(".$obj->page_log_id.",".$obj->action_id.",".$obj->object_id.",";

		if($obj->page_id != null && $obj->page_id >= 0) {
			$fields .= "page_id,";
			$values .= $obj->page_id.",";
		}
		
		if($obj->page_item_id != null && $obj->page_item_id >= 0) {
			$fields .= "page_item_id,";
			$values .= $obj->page_item_id.",";
		}
		
		if($obj->subaction_id != null && $obj->subaction_id >= 0) {
			$fields .= "subaction_id,";
			$values .= $obj->subaction_id.",";
		}
		
		if($obj->item_id != null && $obj->item_id >= 0) {
			$fields .= "item_id,";
			$values .= $obj->item_id.",";
		}

		if($obj->subitem_id != null && $obj->subitem_id >= 0) {
			$fields .= "subitem_id,";
			$values .= $obj->subitem_id.",";
		}
		
		if($obj->portal_id != null && $obj->portal_id >= 0) {
			$fields .= "portal_id,";
			$values .= $obj->portal_id.",";
		}

		//action_time
		if(!empty($obj->action_time)) {
			$action_time = $obj->action_time;
		}
		else {
			$action_time = @date("Y-m-d H:i:s");
		}
		$fields .= "action_time,";
		$values .= "'".$mysqli->escape_str($action_time)."',";
		//------------------		
		
		
		//update_time
		if(!empty($obj->update_time)) {
			$update_time = $obj->update_time;
		}
		else {
			$update_time = "null";
		}
		$fields .= "update_time)";
		$values .= ($update_time == "null") ? "null)" : "'".$mysqli->escape_str($update_time)."')";
		//------------------

		
		$query = "INSERT INTO ".$Tables['log_webpage_det'].$fields.$values;

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
 * Register New Log (for sending email)
 * 
 * Mandatory Fields: module_id, send_method_id, email_cat_id, email_from_addr, email_hash_code, email_sent_status
 * 
 * @param resource $obj (cug__log_email class)
 * @return number (1 - OK;  -1 - Error registering; 0 - no mandatory fields; )
 */
function cug_reg_log_email($obj) {
	global $mysqli, $Tables;
	$fields = "";
	$values = "";

	if(!empty($obj->module_id) && 
		!empty($obj->send_method_id) && 
		!empty($obj->email_cat_id) && 
		!empty($obj->email_from_addr) &&
		!empty($obj->email_hash_code) &&
		isset($obj->email_sent_status)) {

		$fields = " (module_id,send_method_id,email_cat_id,email_from_addr,email_hash_code,email_sent_status,";
		$values = " VALUES(".$obj->module_id.",".$obj->send_method_id.",".$obj->email_cat_id.",'".$mysqli->escape_str($obj->email_from_addr)."','".$mysqli->escape_str($obj->email_hash_code)."',".$obj->email_sent_status.",";

		if(!empty($obj->mass_email)) {
			$fields .= "mass_email,";
			$values .= $obj->mass_email.",";
		}
		//-----------------------
		if(!empty($obj->user_id)) {
			$fields .= "user_id,";
			$values .= $obj->user_id.",";
		}
		//-----------------------
		if(!empty($obj->module_details_id)) {
			$fields .= "module_details_id,";
			$values .= $obj->module_details_id.",";
		}
		//-----------------------
		if(!empty($obj->email_to_addr)) {
			$fields .= "email_to_addr,";
			$values .= "'".$mysqli->escape_str($obj->email_to_addr)."',";
		}
		//-----------------------
		if(!empty($obj->email_subject)) {
			$fields .= "email_subject,";
			$values .= "'".$mysqli->escape_str($obj->email_subject)."',";
		}
		//-----------------------
		if(!empty($obj->email_body)) {
			$fields .= "email_body,";
			$values .= "'".$mysqli->escape_str($obj->email_body)."',";
		}
		//-----------------------
		if(!empty($obj->user_request_ip)) {
			$fields .= "user_request_ip,";
			$values .= "'".$mysqli->escape_str($obj->user_request_ip)."',";
		}
		//-----------------------
		if(!empty($obj->user_request_country_id)) {
			$fields .= "user_request_country_id,";
			$values .= $obj->user_request_country_id.",";
		}
		//-----------------------
		if(!empty($obj->user_request_time)) {
			$fields .= "user_request_time,";
			$values .= "'".$mysqli->escape_str($obj->user_request_time)."',";
		}
		//-----------------------
		if(!empty($obj->email_sent_time)) {
			$fields .= "email_sent_time,";
			$values .= "'".$mysqli->escape_str($obj->email_sent_time)."',";
		}
		//-----------------------
		if(!empty($obj->request_finish_time)) {
			$fields .= "request_finish_time,";
			$values .= "'".$mysqli->escape_str($obj->request_finish_time)."',";
		}
		//-----------------------		
		if(!empty($obj->email_sent_error)) {
			$fields .= "email_sent_error,";
			$values .= "'".$mysqli->escape_str($obj->email_sent_error)."',";
		}
		//-----------------------
		if(!empty($obj->comments)) {
			$fields .= "comments,";
			$values .= "'".$mysqli->escape_str($obj->comments)."',";
		}
		//-----------------------
		
		$fields = rtrim($fields, ",").")";
		$values = rtrim($values, ",").")";

		$query = "INSERT INTO ".$Tables['log_email_send'].$fields.$values;

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
 * Get Logs for sent emails
 * 
 * @param resource $obj (cug__log_email class)
 * @param int $limit_from (Optional, default: 0)
 * @param int $limit_quant (Optional, default: 30)
 * @return array
 */
function cug_get_logs_email($obj, $limit_from=0, $limit_quant=30) {
	global $mysqli, $Tables;
	$fields = "";
	$result = array();
	$index = 0;


	if($obj) {
		if(!empty($obj->id)) {
			$fields .= "id=".$obj->id." AND ";
		}
		else {
			if(isset($obj->mass_email) && $obj->mass_email >= 0) {
				if($obj->mass_email == 1)
					$fields .= "mass_email=1 AND ";
				elseif($obj->mass_email == 0)
					$fields .= "(mass_email=0 OR mass_email is NULL) AND ";
			}
			if(!empty($obj->user_id)) {
				$fields .= "user_id=".$obj->user_id." AND ";
			}
			if(!empty($obj->module_id)) {
				$fields .= "module_id=".$obj->module_id." AND ";
			}
			if(!empty($obj->module_details_id)) {
				$fields .= "module_details_id=".$obj->module_details_id." AND ";
			}
			if(!empty($obj->send_method_id)) {
				$fields .= "send_method_id=".$obj->send_method_id." AND ";
			}
			if(!empty($obj->email_cat_id)) {
				$fields .= "email_cat_id=".$obj->email_cat_id." AND ";
			}
			if(!empty($obj->user_request_ip)) {
				$fields .= "user_request_ip='".$mysqli->escape_str($obj->user_request_ip)."' AND ";
			}
			if(!empty($obj->user_request_country_id)) {
				$fields .= "user_request_country_id=".$obj->user_request_country_id." AND ";
			}
			if(isset($obj->email_sent_status) && $obj->email_sent_status >= 0) {
				$fields .= "email_sent_status=".$obj->email_sent_status." AND ";
			}
		}

		if($fields) {
			$fields = substr($fields, 0, strlen($fields)-4);
			$query = "SELECT * FROM ".$Tables['log_email_send']." WHERE ".$fields;
			$query .= " LIMIT $limit_from, $limit_quant";
			//echo $query.PHP_EOL;
			$r = $mysqli->query($query);
	
				if($r->num_rows) {
					while($arr = $r->fetch_array(MYSQL_ASSOC)) {
						$result[$index] = $arr;
						$index ++;
					}
				}
		}	
	}

	return $result;
}


/**
 * Edit Log of sent Emails
 * 
 * @param resource $obj (cug__log_email class)
 * @return boolean
 */
function cug_edit_log_email($obj) {
	global $mysqli, $Tables;
	$fields = "";

	if($obj->id != null && $obj->id > 0) {
		if($obj->mass_email != null && $obj->mass_email >= 0) $fields .= "mass_email=".$obj->mass_email.",";
		if(!empty($obj->user_id)) $fields .= "user_id=".$obj->user_id.",";
		if(!empty($obj->module_id)) $fields .= "module_id=".$obj->module_id.",";
		if(!empty($obj->module_details_id)) $fields .= "module_details_id=".$obj->module_details_id.",";
		if(!empty($obj->send_method_id)) $fields .= "send_method_id=".$obj->send_method_id.",";
		if(!empty($obj->email_cat_id)) $fields .= "email_cat_id=".$obj->email_cat_id.",";
		if(!empty($obj->email_from_addr)) $fields .= "email_from_addr='".$mysqli->escape_str($obj->email_from_addr)."',";
		if(!empty($obj->email_to_addr)) $fields .= "email_to_addr='".$mysqli->escape_str($obj->email_to_addr)."',";
		if(!empty($obj->email_subject)) $fields .= "email_subject='".$mysqli->escape_str($obj->email_subject)."',";
		if(!empty($obj->email_body)) $fields .= "email_body='".$mysqli->escape_str($obj->email_body)."',";
		if(!empty($obj->user_request_ip)) $fields .= "user_request_ip='".$mysqli->escape_str($obj->user_request_ip)."',";
		if(!empty($obj->user_request_country_id)) $fields .= "user_request_country_id=".$obj->user_request_country_id.",";
		if(!empty($obj->user_request_time)) $fields .= "user_request_time='".$mysqli->escape_str($obj->user_request_time)."',";
		if(!empty($obj->email_sent_time)) $fields .= "email_sent_time='".$mysqli->escape_str($obj->email_sent_time)."',";
		if(!empty($obj->email_sent_status)) $fields .= "email_sent_status=".$obj->email_sent_status.",";
		if(!empty($obj->request_finish_time)) $fields .= "request_finish_time='".$mysqli->escape_str($obj->request_finish_time)."',";
		if(!empty($obj->email_sent_error)) $fields .= "email_sent_error='".$mysqli->escape_str($obj->email_sent_error)."',";
		if(!empty($obj->comments)) $fields .= "comments='".$mysqli->escape_str($obj->comments)."',";
		if(!empty($obj->email_hash_code)) $fields .= "email_hash_code='".$mysqli->escape_str($obj->email_hash_code)."',";
		

		if(strlen($fields) > 0) {
			$fields = rtrim($fields, ",");
			$query = "UPDATE ".$Tables['log_email_send']." SET ".$fields." WHERE id=".$obj->id;
				
			if($mysqli->query($query))
				return true;
			else
				return false;
		}
		else
			return false;
	}
	else {
		return false;
	}
}


/**
 * Delete Log (TE)
 * 
 * @param int $log_id
 * @return void
 */
function cug_del_log_te($log_id) {
    global $mysqli, $Tables;
    
    if($log_id > 0) {
        $mysqli->query("DELETE FROM {$Tables['log_te']} WHERE id=$log_id");
    }
}
?>