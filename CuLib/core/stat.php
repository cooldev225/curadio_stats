<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Statistics
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------



/**
 * Track's Statistics Class
 *
 * @param	id	- INT
 * @param	file_index	- INT
 * @param	track_id	- INT
 * @param	file_id	- INT
 * @param	wm1_code	- INT
 * @param	wm2_code	- INT
 * @param	user_id	- INT
 * @param	module_id	- INT
 * @param	module_details_id	- INT
 * @param	device_os	- STRING
 * @param	device_id	- STRING
 * @param	device_name	- STRING
 * @param	country_id	- INT
 * @param	ip	- STRING
 * @param	os	- STRING
 * @param	browser	- STRING
 * @param	action_id	- INT
 * @param	genre_id	- INT
 * @param	fileunder_id	- INT
 * @param	mood_id	- INT
 * @param	key_id	- INT
 * @param	tempo_id	- INT
 * @param	album_id	- INT
 * @param	action_time	-STRING (MySQL DATETIME Format)
 * @param	update_time	- STRING (MySQL TIMESTAMP Format)
 */
class cug__stat_track
{
	public $id;
	public $file_index;
	public $track_id;
	public $file_id;
	public $wm1_code;
	public $wm2_code;
	public $user_id;
	public $module_id;
	public $module_details_id;
	public $device_os;
	public $device_id;
	public $device_name;
	public $country_id;
	public $ip;
	public $os;
	public $browser;
	public $action_id;
	public $genre_id;
	public $fileunder_id;
	public $mood_id;
	public $key_id;
	public $tempo_id;
	public $album_id;
	public $action_time;
	public $update_time;
}



/**
 * Artist's Statistics Class
 *
 * @param	id	- INT
 * @param	stat_track_id	- INT
 * @param	user_id	- INT
 * @param	module_id	- INT
 * @param	module_details_id	- INT
 * @param	device_id	- STRING
 * @param	device_os	- STRING
 * @param	device_name	- STRING
 * @param	action_id	- INT
 * @param	artist_id	- INT
 * @param	action_time	-STRING (MySQL DATETIME Format)
 * @param	update_time	- STRING (MySQL TIMESTAMP Format)
 */
class cug__stat_artist
{
	public $id;
	public $stat_track_id;
	public $user_id;
	public $module_id;
	public $module_details_id;
	public $device_os;
	public $device_id;
	public $device_name;
	public $action_id;
	public $artist_id;
	public $action_time;
	public $update_time;
}




/**
 * Register Track's Statistic
 *
 * @param object of 'cug__stat_track' Class
 * @return integer
 */
function cug_reg_stat_track($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";

	if(!empty($obj->action_id)) {

		$fields = " (action_id,";
		$values = " VALUES(".$mysqli->escape_str($obj->action_id).",";

		if(!empty($obj->file_index)) {
			$fields .= "file_index,";
			$values .= $mysqli->escape_str($obj->file_index).",";
		}

		if(!empty($obj->track_id)) {
			$fields .= "track_id,";
			$values .= $mysqli->escape_str($obj->track_id).",";
		}

		if(!empty($obj->file_id)) {
			$fields .= "file_id,";
			$values .= $mysqli->escape_str($obj->file_id).",";
		}

		if(!empty($obj->wm1_code)) {
			$fields .= "wm1_code,";
			$values .= $mysqli->escape_str($obj->wm1_code).",";
		}

		if(!empty($obj->wm2_code)) {
			$fields .= "wm2_code,";
			$values .= $mysqli->escape_str($obj->wm2_code).",";
		}

		if(!empty($obj->user_id)) {
			$fields .= "user_id,";
			$values .= $mysqli->escape_str($obj->user_id).",";
		}

		if(!empty($obj->module_id)) {
			$fields .= "module_id,";
			$values .= $mysqli->escape_str($obj->module_id).",";
		}

		if(!empty($obj->module_details_id)) {
			$fields .= "module_details_id,";
			$values .= $mysqli->escape_str($obj->module_details_id).",";
		}
		
		if(!empty($obj->device_os)) {
			$fields .= "device_os,";
			$values .= "'".$mysqli->escape_str($obj->device_os)."',";
		}

		if(!empty($obj->device_id)) {
			$fields .= "device_id,";
			$values .= "'".$mysqli->escape_str($obj->device_id)."',";
		}

		if(!empty($obj->device_name)) {
			$fields .= "device_name,";
			$values .= "'".$mysqli->escape_str($obj->device_name)."',";
		}

		if(!empty($obj->country_id)) {
			$fields .= "country_id,";
			$values .= $mysqli->escape_str($obj->country_id).",";
		}

		if(!empty($obj->ip)) {
			$fields .= "ip,";
			$values .= "'".$mysqli->escape_str($obj->ip)."',";
		}

		if(!empty($obj->os)) {
			$fields .= "os,";
			$values .= "'".$mysqli->escape_str($obj->os)."',";
		}

		if(!empty($obj->browser)) {
			$fields .= "browser,";
			$values .= "'".$mysqli->escape_str($obj->browser)."',";
		}

		if(!empty($obj->genre_id)) {
			$fields .= "genre_id,";
			$values .= $mysqli->escape_str($obj->genre_id).",";
		}
		
		if(!empty($obj->fileunder_id)) {
			$fields .= "fileunder_id,";
			$values .= $mysqli->escape_str($obj->fileunder_id).",";
		}

		if(!empty($obj->mood_id)) {
			$fields .= "mood_id,";
			$values .= $mysqli->escape_str($obj->mood_id).",";
		}

		if(!empty($obj->key_id)) {
			$fields .= "key_id,";
			$values .= $mysqli->escape_str($obj->key_id).",";
		}

		if(!empty($obj->album_id)) {
			$fields .= "album_id,";
			$values .= $mysqli->escape_str($obj->album_id).",";
		}

		
		$fields .= "action_time,";
		if(!empty($obj->action_time)) {
			$values .= "'".$mysqli->escape_str($obj->action_time)."',";
		}
		else {
			$values .= "'".@date("Y-m-d H:i:s")."',";
		}

		if(!empty($obj->update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}		

		$fields = substr($fields, 0, strlen($fields)-1).")";
		$values = substr($values, 0, strlen($values)-1).")";
		
		$query = "INSERT INTO ".$Tables['stat_track'].$fields.$values;
		
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
 * Register Artist's Statistic
 *
 * @param object of 'cug__stat_artist' Class
 * @return integer
 */
function cug_reg_stat_artist($obj)
{
global $mysqli, $Tables;
$fields = "";
$values = "";


	if(!empty($obj->stat_track_id) && !empty($obj->action_id) && !empty($obj->artist_id)) {

		$fields = " (stat_track_id,action_id,artist_id,";
		$values = " VALUES(".$mysqli->escape_str($obj->stat_track_id).",";
		$values .= $mysqli->escape_str($obj->action_id).",";
		$values .= $mysqli->escape_str($obj->artist_id).",";

		if(!empty($obj->user_id)) {
			$fields .= "user_id,";
			$values .= $mysqli->escape_str($obj->user_id).",";
		}

		if(!empty($obj->module_id)) {
			$fields .= "module_id,";
			$values .= $mysqli->escape_str($obj->module_id).",";
		}

		if(!empty($obj->module_details_id)) {
			$fields .= "module_details_id,";
			$values .= $mysqli->escape_str($obj->module_details_id).",";
		}

		if(!empty($obj->device_os)) {
			$fields .= "device_os,";
			$values .= "'".$mysqli->escape_str($obj->device_os)."',";
		}
		
		if(!empty($obj->device_id)) {
			$fields .= "device_id,";
			$values .= "'".$mysqli->escape_str($obj->device_id)."',";
		}

		if(!empty($obj->device_name)) {
			$fields .= "device_name,";
			$values .= "'".$mysqli->escape_str($obj->device_name)."',";
		}

		$fields .= "action_time,";
		if(!empty($obj->action_time)) {
			$values .= "'".$mysqli->escape_str($obj->action_time)."',";
		}
		else {
			$values .= "'".@date("Y-m-d H:i:s")."',";
		}	

		if(!empty($obj->update_time)) {
			$fields .= "update_time,";
			$values .= "'".$mysqli->escape_str($obj->update_time)."',";
		}		

		$fields = substr($fields, 0, strlen($fields)-1).")";
		$values = substr($values, 0, strlen($values)-1).")";		
		
		$query = "INSERT INTO ".$Tables['stat_artist'].$fields.$values;

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
 * Get WebPage Statistics
 *
 * @param $obj OBJECT - 'cug__log_webpage' class
 * @param $request_time_from STRING - MySQL DATETIME Format, Default null
 * @param $request_time_to STRING - MySQL DATETIME Format, Default null
 * @return INT
 */
function cug_get_stat_webpage($obj, $request_time_from=null, $request_time_to=null)
{
global $mysqli, $Tables;
$fields = "";
$result = 0;

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
		$query = "SELECT COUNT(id) FROM ".$Tables['log_webpage']." WHERE ".$fields;
		$r = $mysqli->query($query);

			if($r) {
				$arr = $r->fetch_array();
				$result = $arr[0];
			}
	}

return $result;
}


/**
 * Get WebPage Details Statistics
 *
 * @param $obj OBJECT - 'cug__log_webpage_details' class
 * @param $action_time_from STRING - MySQL DATETIME Format, Default null
 * @param $action_time_to STRING - MySQL DATETIME Format, Default null
 * @return INT
 */
function cug_get_stat_webpage_det($obj, $action_time_from=null, $action_time_to=null)
{
global $mysqli, $Tables;
$fields = "";
$result = 0;

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
		$query = "SELECT COUNT(id) FROM ".$Tables['log_webpage_det']." WHERE ".$fields;
		$r = $mysqli->query($query);

		if($r->num_rows) {
			$arr = $r->fetch_array();
			$result = $arr[0];
		}
	}

return $result;
}


/**
 * Get WebPage Details Statistics
 *
 * @param $obj OBJECT - 'cug__log_webpage_details' class
 * @param $action_time_from STRING - MySQL DATETIME Format, Default null
 * @param $action_time_to STRING - MySQL DATETIME Format, Default null
 * @return Array
 */
function cug_get_stat_webpage_det_portals_count($obj, $action_time_from=null, $action_time_to=null)
{
global $mysqli, $Tables;
$fields = "";
$result = array();
$index = 0;

	if($obj) {

		if(!empty($obj->id)) {
			$fields .= "lwpd.id=".$obj->id." AND ";
		}
		else {
			if($obj->page_log_id != null && $obj->page_log_id >= 0) {
				$fields .= "lwpd.page_log_id=".$obj->page_log_id." AND ";
			}
			if($obj->page_id != null && $obj->page_id >= 0) {
				$fields .= "lwpd.page_id=".$obj->page_id." AND ";
			}
			if($obj->page_item_id != null && $obj->page_item_id >= 0) {
				$fields .= "lwpd.page_item_id=".$obj->page_item_id." AND ";
			}
			if($obj->action_id != null && $obj->action_id >= 0) {
				$fields .= "lwpd.action_id=".$obj->action_id." AND ";
			}
			if($obj->subaction_id != null && $obj->subaction_id >= 0) {
				$fields .= "lwpd.subaction_id=".$obj->subaction_id." AND ";
			}
			if($obj->object_id != null && $obj->object_id >= 0) {
				$fields .= "lwpd.object_id=".$obj->object_id." AND ";
			}
			if($obj->item_id != null && $obj->item_id >= 0) {
				$fields .= "lwpd.item_id=".$obj->item_id." AND ";
			}
			if($obj->subitem_id != null && $obj->subitem_id >= 0) {
				$fields .= "lwpd.subitem_id=".$obj->subitem_id." AND ";
			}
			if($obj->portal_id != null && $obj->portal_id >= 0) {
				$fields .= "lwpd.portal_id=".$obj->portal_id." AND ";
			}


			//action_time
			if(!empty($obj->action_time)) {
				$fields .= "lwpd.action_time='".$mysqli->escape_str($obj->action_time)."' AND ";
			}
			else {
				if($action_time_from) {
					$fields .= "lwpd.action_time>='".$action_time_from."' AND ";
					if($action_time_to) {
						$fields .= "lwpd.action_time<='".$action_time_to."' AND ";
					}
				}
				elseif($action_time_to) {
					$fields .= "lwpd.action_time<='".$action_time_to."' AND ";
				}
			}
			//---------------------

			if(!empty($obj->update_time)) {
				$fields .= "lwpd.update_time='".$mysqli->escape_str($obj->update_time)."' AND ";
			}
		}

		$fields = substr($fields, 0, strlen($fields)-4);
		$query = "SELECT lwpd.portal_id, ccp.title AS portal_title, COUNT(lwpd.portal_id) as quant FROM ".$Tables['log_webpage_det']." AS lwpd LEFT JOIN ".$Tables['cache_culink_portal']." AS ccp ON lwpd.portal_id=ccp.id WHERE ".$fields." GROUP BY portal_id ORDER BY quant DESC";
		
		$r = $mysqli->query($query);

		if($r) {
			while($arr = $r->fetch_array(MYSQLI_ASSOC)) {
				$result[$arr['portal_title']] = $arr['quant'];
				$result[$index] = $arr;
				$index ++;
			}
		}
		
	}

return $result;
}
?>