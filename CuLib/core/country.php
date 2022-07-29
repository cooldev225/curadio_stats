<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Country
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Get Country
 *
 * @param string -> default is''
 * @param string -> 'TITLE', 'ID', 'CODE2', 'CODE3', 'CODENUM', 'CODEDIAL'; default is 'ID'
 * @param string -> 'TITLE', 'ID', 'CODE2', 'CODE3', 'CODENUM', 'CODEDIAL'; default is 'TITLE'
 * @param string -> 'ASC' or 'DESC'; default is 'ASC'
 * @return array
 */
 function cug_get_country($item="", $item_type="ID", $sort_by="TITLE", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$index = 0;
$query = "";

	if(!empty($item)) {
		
		switch($item_type) {
			
			case 'ID':
				$field = "id";
				break;
				
			case 'TITLE':
				$field = "title";
				break;

			case 'CODE2':
				$field = "code_alpha2";
				break;

			case 'CODE3':
				$field = "code_alpha3";
				break;

			case 'CODENUM':
				$field = "code_num";
				break;

			case 'CODEDIAL':
				$field = "code_dial";
				break;

			default:
				$field = "";
				break;
		}
		
		
			if($field) {
				
				if($field=="id"){
					$query = "SELECT * FROM ".$Tables['country']." WHERE ".$field."=".$mysqli->escape_str($item);
				}
				else {
					$query = "SELECT * FROM ".$Tables['country']." WHERE ".$field."='".$mysqli->escape_str($item)."'";
				}	
			}
	}
	else {
		$query = "SELECT * FROM ".$Tables['country'];
	}
	//--------------------------

		if($query) {
			if($sort_by == "ID")
				$sort_field = "id";
			elseif($sort_by == "CODE2")
				$sort_field = "code_alpha2";
			elseif($sort_by == "CODE3")
				$sort_field = "code_alpha3";
			elseif($sort_by == "CODENUM")
				$sort_field = "code_num";
			elseif($sort_by == "CODEDIAL")
				$sort_field = "code_dial";
			else
				$sort_field = "title";
			
			
			$query .= " ORDER BY $sort_field $sort_type";
			$r = $mysqli->query($query);
			
			if($r) {
				while($arr = $r->fetch_array()) {
					$result[$index] = $arr;
					$index ++;
				}
			}	
		}	
		

return $result;	
}


/**
 * Register New Country
 *
 * @param string
 * @param string
 * @param string
 * @param string
 * @param integer
 * @param integer - Has Image or not (0 - No;  1 - Yes;)
 * @return integer
 */
function cug_reg_country($title, $code_alpha2, $code_alpha3, $code_num, $code_dial, $img_flag)
{
global $mysqli, $Tables;

	if(!empty($title)) {
		
		//check for existing record
		$result = $mysqli->get_field_val($Tables['country'], "id", "title='".$mysqli->escape_str($title)."'");
		if(empty($result[0]['id'])) {
			
			$insert_query = "INSERT INTO ".$Tables['country']." VALUES(NULL, '".$mysqli->escape_str($title)."', '".$mysqli->escape_str($code_alpha2)."', '".$mysqli->escape_str($code_alpha3)."', '".$mysqli->escape_str($code_num)."', ".$mysqli->escape_str($code_dial).", ".$mysqli->escape_str($img_flag).", NULL)";
			if($mysqli->query($insert_query)) {
				return $mysqli->insert_id;
			}
			else {
				return -1; // DB Error
			}
		}
		else {
			return -2; // Already Exists
		}
		
	}
	else {
		return 0;
	}

}


/**
 * Get Country ID by IP
 *
 * @param string (ip address)
 * @return integer
 */
function cug_get_country_id_by_ip($ip)
{
//new method
//---------------
$ip_details_arr = dbip_lookup($ip);

	if(count($ip_details_arr)) {
		$country_code = $ip_details_arr['country'];
		$country_arr = cug_get_country($country_code, "CODE2");
		
			if(count($country_arr[0])) {
				return $country_arr[0]['id'];
			}
	}
	else {
		return 0;
	}	

/*
// old method
//----------------
 
global $mysqli, $Tables;
 
	if($ip) {
		if($ip_num = ip2long($ip)) {
			$query = "SELECT code_alpha2 FROM ".$Tables['country_ip']." WHERE $ip_num BETWEEN ip_from AND ip_to";
			
				if($r = $mysqli->query($query)) {
					$arr = $r->fetch_array();
					$code_alpha2 = $arr[0];
					
					if($code_alpha2) {
						$query = "SELECT id FROM ".$Tables['country']." WHERE code_alpha2='$code_alpha2'";
							if($r = $mysqli->query($query)) {
								$arr = $r->fetch_array();
								return $arr['id'];
							}
					}
				}
			
		}
	}
return 0;	
*/	
	
	
}



/**
 * Get Country Details
 *
 * @param string (ip address)
 * @return array
 */
function dbip_lookup($addr)
{
global $mysqli, $Tables;	
$result = array();

	if($addr_type = dbip_addr_type($addr)) {
		$query = "SELECT * FROM ".$Tables['dbip']." WHERE addr_type='$addr_type' AND ip_start <= '".$mysqli->escape_str(inet_pton($addr))."' ORDER BY ip_start DESC LIMIT 1";

		if($r = $mysqli->query($query)) {
			if($row = $r->fetch_array()) {
				$result['addr_type'] 	= $row['addr_type'];
				$result['ip_start'] 	= inet_ntop($row['ip_start']);
				$result['ip_end'] 		= inet_ntop($row['ip_end']);
				$result['country'] 		= $row['country'];
				$result['stateprov'] 	= $row['stateprov'];
				$result['city'] 		= $row['city'];
			}
		}
	}

return $result;
}




/**
 * Get Type of IP Address (IPV4 or IPV6)
 *
 * @param string (ip address)
 * @return string
 */
function dbip_addr_type($addr)
{
	if (ip2long($addr) !== false) {
		return "ipv4";
	} else if (preg_match('/^[0-9a-fA-F:]+$/', $addr) && @inet_pton($addr)) {
		return "ipv6";
	} else {
		return "";
	}

}
?>