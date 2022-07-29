<?PHP
/**
 * Register New Link
 * 
 * @param string $object_name (ALBUM, TRACK, MEMBER)
 * @param int $object_id
 * @param int $parent_object_id (when $object_name=TRACK then pass AlbumID here)
 * @param int $link_cat_id
 * @param int $portal_id
 * @param int $product_id
 * @param int $product_type_id
 * @param int $query_type_id
 * @param string $url
 * @param int $country_id
 * @param string $id_on_portal
 * @param string $parent_object_id_on_portal (only when $object_name=TRACK)
 * @param string $price_code
 * @param int $price_amount
 * @param string $price_formatted
 * @param int $affiliate (1[yes] or 0[no])
 * @param int $link_status (1[verified] or 0[not verified])
 * @param int $match_percent
 * @param int $match_step
 * @param string $reg_date (Optional, example: 2014-11-30 22:09:49, default: current date/time)
 * @param int $is_link_temporary (Optional, 1 or 0, default: 0)
 * @return boolean
 */
function cug_cache_culink_reg_link($object_name, $object_id, $parent_object_id, $link_cat_id, $portal_id, $product_id, $product_type_id, $query_type_id, $url, $country_id, $id_on_portal, $parent_object_id_on_portal, $price_code, $price_amount, $price_formatted, $affiliate, $link_status, $match_percent, $match_step, $reg_date="", $is_link_temporary=0) {
	global $mysqli_cache, $Tables;
	
	if($object_name && $object_id > 0 && $link_cat_id > 0 && $portal_id > 0 && $query_type_id > 0 && $url) {	
		switch(strtoupper($object_name)) {
			//-----------------
			case 'ALBUM':
			//-----------------	
				$table = $Tables['cache_culink_album'];
				
				$object_hashcode_str = $object_id.$link_cat_id.$portal_id.$product_id.$product_type_id.$query_type_id;
				$object_hashcode_str .= !empty($url) ? $url : "";
				$object_hashcode_str .= !empty($country_id) ? $country_id : "";
				$object_hashcode_str .= !empty($id_on_portal) ? $id_on_portal : "";
				$object_hashcode_str .= !empty($price_amount) ? $price_amount : "";
				$object_hashcode_str .= !empty($price_code) ? $price_code : "";
				$object_hashcode_str .= !empty($price_formatted) ? $price_formatted : "";
				$object_hashcode_str .= !empty($affiliate) ? $affiliate : "";
				$object_hashcode_str .= !empty($is_link_temporary) ? $is_link_temporary : "";
				
				$object_hashcode = hash("sha256", $object_hashcode_str, false);
				
				$fields = " (album_id, hash_val, link_cat_id, portal_id, query_type_id,";
				$fields .= !empty($product_id) ? "product_id," : "";
				$fields .= !empty($product_type_id) ? "product_type_id," : "";
				$fields .= !empty($url) ? "url," : "";
				$fields .= !empty($country_id) ? "country_id," : "";
				$fields .= !empty($id_on_portal) ? "id_on_portal," : "";
				$fields .= !empty($price_amount) ? "price_amount," : "";
				$fields .= !empty($price_code) ? "price_code," : "";
				$fields .= !empty($price_formatted) ? "price_formatted," : "";
				$fields .= !empty($match_percent) ? "match_percent," : "";
				$fields .= ($match_step > 0) ? "match_step," : "";
				$fields .= ($affiliate > 0) ? "affiliate," : "";
				$fields .= ($link_status > 0) ? "link_status," : "";
				$fields .= ($is_link_temporary > 0) ? "temporary," : "";
				$fields .= "reg_date)";
				
				$values = " VALUES($object_id, '$object_hashcode', $link_cat_id, $portal_id, $query_type_id,";
				$values .= !empty($product_id) ? $product_id."," : "";
				$values .= !empty($product_type_id) ? $product_type_id."," : "";
				$values .= !empty($url) ? "'".$mysqli_cache->escape_str($url)."'," : "";
				$values .= !empty($country_id) ? $mysqli_cache->escape_str($country_id)."," : "";
				$values .= !empty($id_on_portal) ? "'".$mysqli_cache->escape_str($id_on_portal)."'," : "";
				$values .= !empty($price_amount) ? $mysqli_cache->escape_str($price_amount)."," : "";
				$values .= !empty($price_code) ? "'".$mysqli_cache->escape_str($price_code)."'," : "";
				$values .= !empty($price_formatted) ? "'".$mysqli_cache->escape_str($price_formatted)."'," : "";
				$values .= !empty($match_percent) ? "$match_percent," : "";
				$values .= ($match_step > 0) ? "$match_step," : "";
				$values .= ($affiliate > 0) ? "$affiliate," : "";
				$values .= ($link_status > 0) ? "$link_status," : "";
				$values .= ($is_link_temporary > 0) ? "$is_link_temporary," : "";
				$values .= !empty($reg_date) ? "'".$reg_date."')" : "NOW())";
			break;
			
			//------------------
			case 'TRACK':
			//------------------
				$table = $Tables['cache_culink_track'];
				
				$object_hashcode_str = $object_id.$parent_object_id.$link_cat_id.$portal_id.$product_id.$product_type_id.$query_type_id;
				$object_hashcode_str .= !empty($url) ? $url : "";
				$object_hashcode_str .= !empty($country_id) ? $country_id : "";
				$object_hashcode_str .= !empty($id_on_portal) ? $id_on_portal : "";
				$object_hashcode_str .= !empty($price_amount) ? $price_amount : "";
				$object_hashcode_str .= !empty($price_code) ? $price_code : "";
				$object_hashcode_str .= !empty($price_formatted) ? $price_formatted : "";
				$object_hashcode_str .= !empty($affiliate) ? $affiliate : "";
				
				$object_hashcode = hash("sha256", $object_hashcode_str, false);
				
				$fields = " (track_id, album_id, hash_val, link_cat_id, portal_id, query_type_id,";
				$fields .= !empty($product_id) ? "product_id," : "";
				$fields .= !empty($product_type_id) ? "product_type_id," : "";
				$fields .= !empty($url) ? "url," : "";
				$fields .= !empty($country_id) ? "country_id," : "";
				$fields .= !empty($id_on_portal) ? "id_on_portal," : "";
				$fields .= !empty($price_amount) ? "price_amount," : "";
				$fields .= !empty($price_code) ? "price_code," : "";
				$fields .= !empty($price_formatted) ? "price_formatted," : "";
				$fields .= !empty($match_percent) ? "match_percent," : "";
				$fields .= ($match_step > 0) ? "match_step," : "";
				$fields .= !empty($parent_object_id_on_portal) ? "album_id_on_portal," : "";
				$fields .= ($affiliate > 0) ? "affiliate," : "";
				$fields .= ($link_status > 0) ? "link_status," : "";
				$fields .= "reg_date)";
				
				$values = " VALUES($object_id, $parent_object_id, '$object_hashcode', $link_cat_id, $portal_id,  $query_type_id,";
				$values .= !empty($product_id) ? $product_id."," : "";
				$values .= !empty($product_type_id) ? $product_type_id."," : "";
				$values .= !empty($url) ? "'".$mysqli_cache->escape_str($url)."'," : "";
				$values .= !empty($country_id) ? $mysqli_cache->escape_str($country_id)."," : "";
				$values .= !empty($id_on_portal) ? "'".$mysqli_cache->escape_str($id_on_portal)."'," : "";
				$values .= !empty($price_amount) ? "$price_amount," : "";
				$values .= !empty($price_code) ? "'".$mysqli_cache->escape_str($price_code)."'," : "";
				$values .= !empty($price_formatted) ? "'".$mysqli_cache->escape_str($price_formatted)."'," : "";
				$values .= !empty($match_percent) ? "$match_percent," : "";
				$values .= ($match_step > 0) ? "$match_step," : "";
				$values .= !empty($parent_object_id_on_portal) ? "'".$mysqli_cache->escape_str($parent_object_id_on_portal)."'," : "";
				$values .= ($affiliate > 0) ? "$affiliate," : "";
				$values .= ($link_status > 0) ? "$link_status," : "";
				$values .= !empty($reg_date) ? "'".$reg_date."')" : "NOW())";
			break;
			
			//-------------------
			case 'MEMBER':
			//-------------------	
				$table = $Tables['cache_culink_member'];
				
				$object_hashcode_str = $object_id.$link_cat_id.$portal_id.$product_id.$product_type_id.$query_type_id;
				$object_hashcode_str .= !empty($url) ? $url : "";
				$object_hashcode_str .= !empty($country_id) ? $country_id : "";
				$object_hashcode_str .= !empty($id_on_portal) ? $id_on_portal : "";
				$object_hashcode_str .= !empty($price_amount) ? $price_amount : "";
				$object_hashcode_str .= !empty($price_code) ? $price_code : "";
				$object_hashcode_str .= !empty($price_formatted) ? $price_formatted : "";
				$object_hashcode_str .= !empty($affiliate) ? $affiliate : "";
				
				$object_hashcode = hash("sha256", $object_hashcode_str, false);
				
				$fields = " (member_id, hash_val, link_cat_id, portal_id, query_type_id,";
				$fields .= !empty($product_id) ? "product_id," : "";
				$fields .= !empty($product_type_id) ? "product_type_id," : "";
				$fields .= !empty($url) ? "url," : "";
				$fields .= !empty($country_id) ? "country_id," : "";
				$fields .= !empty($id_on_portal) ? "id_on_portal," : "";
				$fields .= !empty($price_amount) ? "price_amount," : "";
				$fields .= !empty($price_code) ? "price_code," : "";
				$fields .= !empty($price_formatted) ? "price_formatted," : "";
				$fields .= !empty($match_percent) ? "match_percent," : "";
				$fields .= ($match_step > 0) ? "match_step," : "";
				$fields .= ($affiliate > 0) ? "affiliate," : "";
				$fields .= ($link_status > 0) ? "link_status," : "";
				$fields .= "reg_date)";
				
				$values = " VALUES($object_id, '$object_hashcode', $link_cat_id, $portal_id, $query_type_id,";
				$values .= !empty($product_id) ? $product_id."," : "";
				$values .= !empty($product_type_id) ? $product_type_id."," : "";
				$values .= !empty($url) ? "'".$mysqli_cache->escape_str($url)."'," : "";
				$values .= !empty($country_id) ? $mysqli_cache->escape_str($country_id)."," : "";
				$values .= !empty($id_on_portal) ? "'".$mysqli_cache->escape_str($id_on_portal)."'," : "";
				$values .= !empty($price_amount) ? "$price_amount," : "";
				$values .= !empty($price_code) ? "'".$mysqli_cache->escape_str($price_code)."'," : "";
				$values .= !empty($price_formatted) ? "'".$mysqli_cache->escape_str($price_formatted)."'," : "";
				$values .= !empty($match_percent) ? "$match_percent," : "";
				$values .= ($match_step > 0) ? "$match_step," : "";
				$values .= ($affiliate > 0) ? "$affiliate," : "";
				$values .= ($link_status > 0) ? "$link_status," : "";
				$values .= !empty($reg_date) ? "'".$reg_date."')" : "NOW())";
			break;
		}
		
		//----------------------
		if($table) {
			$query = "INSERT INTO $table $fields $values";
			
			if($mysqli_cache->query($query))
				return true;
		}
	}
	
	return false;
}


/**
 * Check for existing Link
 * 
 * @param string $object_name (ALBUM, TRACK, MEMBER)
 * @param int $object_id
 * @param int $parent_object_id (when $object_name=TRACK then pass AlbumID here)
 * @param int $portal_id
 * @param int $link_cat_id
 * @param int $product_id
 * @param int $product_type_id
 * @param int $query_type_id
 * @param int $affiliate
 * @param int $country_id (Optional, default: 0, means do not check by this parameter)
 * @return Ambigous (boolean or string, false on error, empty string if not exists, hashcode if exists)
 */
function cug_cache_culink_check_link($object_name, $object_id, $parent_object_id, $portal_id, $link_cat_id, $product_id, $product_type_id, $query_type_id,  $affiliate, $country_id=0) {
	global $mysqli_cache, $Tables;
	$result = false;
	
	if($object_name && $object_id > 0 && $portal_id > 0 && $link_cat_id > 0 && $product_id > 0 && $product_type_id > 0 && $query_type_id > 0) {
		switch(strtoupper($object_name)) {
			//-------------------
			case 'ALBUM':
			//-------------------
				$table = $Tables['cache_culink_album'];
				$object_field_name = "album_id";
			break;
			
			//-------------------
			case 'TRACK':
			//-------------------
				$table = $Tables['cache_culink_track'];
				$object_field_name = "track_id";
			break;
				
			//-------------------
			case 'MEMBER':
			//-------------------
				$table = $Tables['cache_culink_member'];
				$object_field_name = "member_id";
			break;
		}
		//------------------
		
		if($table) {
			$where = "WHERE $object_field_name=$object_id AND portal_id=$portal_id AND link_cat_id=$link_cat_id AND product_id=$product_id AND product_type_id=$product_type_id AND query_type_id=$query_type_id AND";
			$where .= !empty($parent_object_id) ? " album_id=$parent_object_id AND" : "";
			$where .= !empty($country_id) ? " country_id=$country_id AND" : "";
			$where .= !empty($affiliate) ? " affiliate=$affiliate AND" : "";
			
			$where = rtrim($where, "AND");
			
			$query = "SELECT * FROM $table $where";
			$r = $mysqli_cache->query($query);
			
				if($r){
					$row = $r->fetch_assoc();
					$result = $row['hash_val'];
				}
				else 
					$result = "";
		}
	}
	
	return $result;
}


/**
 * Delete Link
 * 
 * @param string $object_name (ALBUM, TRACK, MEMBER)
 * @param int $object_id
 * @param string $hashcode
 * @return boolean
 */
function cug_cache_culink_del_link($object_name, $object_id, $hashcode) {
	global $mysqli_cache, $Tables;
	$result = false;
	
	if($object_name && $object_id > 0 && $hashcode) {
		switch(strtoupper($object_name)) {
			//-------------------
			case 'ALBUM':
			//-------------------
				$table = $Tables['cache_culink_album'];
				$object_field_name = "album_id";
			break;
					
			//-------------------
			case 'TRACK':
			//-------------------
				$table = $Tables['cache_culink_track'];
				$object_field_name = "track_id";
			break;
		
			//-------------------
			case 'MEMBER':
			//-------------------
				$table = $Tables['cache_culink_member'];
				$object_field_name = "member_id";
			break;
		}
		//----------------
		
		if($table) {
			$query = "DELETE FROM $table WHERE $object_field_name=$object_id AND hash_val='$hashcode'";
			if($mysqli_cache->query($query))
				$result = true;
		}
		
	}
	
	return $result;
}
?>