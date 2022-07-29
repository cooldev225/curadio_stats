<?php
/**
* Get Duplicated Object Info (Table Name and Fields to be compared)
*
* @param string $object_name
* @return array
*/
function cug_get_duplicate_object_db_info($object_name){
	global $Tables;
	$result = array();
	$result['table'] = "";
	$result['fields'] = array();
	
	switch(strtoupper($object_name)) {
		//------------------------------------
		case 'ALBUM':
			$result['table'] = $Tables['album'];
			$result['fields'][0] = 'title';
			$result['fields'][1] = 'subtitle';
			$result['fields'][2] = 'title_version';
			$result['fields'][3] = 'catalogue_num';
			$result['fields'][4] = 'ean_code';
		break;
		//------------------------------------
		case 'TRACK':
			$result['table'] = $Tables['track'];
			$result['fields'][0] = 'title';
			$result['fields'][1] = 'part';
			$result['fields'][2] = 'version';
		break;
		//------------------------------------
		case 'MEMBER':
			$result['table'] = $Tables['member'];
			$result['fields'][0] = 'title';
			$result['fields'][1] = 'alias';
		break;
	}
	
	return $result;
}

/**
* Replace Special Characters in Duplicated Strings
*
* @param string $str
* @return string
*/
function cug_duplicates_replace_special_chars($str) {
    //replace multiple spaces, tabs, and line breaks with a single space
    $str = preg_replace('!\s+!', ' ', $str);
    
    //replace special chars with empty string
    $str = strtolower(preg_replace("/[\@;#'?*„|“<>\"\]\{\}\]:\\`~!,.$%^&*()\-_=]/", "", $str));
    
	return $str;
}

/**
* Compare Strings for Similarity (using levenshtein())
*
* @param string $str1
* @param string $str2
* @param int $exact_string_max_length ((Optional, default: 5))
* @param int $str_min_length (Optional, default: 10)
* @param int $levenshtein_min_distance (Optional, default: 1)
* @param int $str_middle_length (Optional, default: 13)
* @param int $levenshtein_middle_distance (Optional, default: 2)
* @param int $str_max_length (Optional, default: 20)
* @param int $levenshtein_max_distance (Optional, default: 3)
* @return boolean
*/
function cug_compare_strings_for_similarity_levenshtein($str1, $str2, $exact_string_max_length=5, $str_min_length=10, $levenshtein_min_distance=1, $str_middle_length=13, $levenshtein_middle_distance=2, $str_max_length=20, $levenshtein_max_distance=3) {
	$result = false;
	
	$str1_length = strlen($str1);
	$str2_length = strlen($str2);
	
	if(($str1_length > 0 && $str1_length <= 255) && ($str2_length > 0 && $str2_length <= 255)) {		
		
		if($str1_length <= $exact_string_max_length || $str2_length <= $exact_string_max_length) {
			if($str1 == $str2)
				$result = true;
		}
		else {
			//detect min length
			$min_length = ($str1_length < $str2_length) ? $str1_length : $str2_length;
			
			//define levenshtein distance
			if($min_length <= $str_min_length)
				$levenshtein_distance = $levenshtein_min_distance;
			elseif($min_length <= $str_middle_length)
				$levenshtein_distance = $levenshtein_middle_distance;
			elseif($min_length <= $str_max_length)
				$levenshtein_distance = $levenshtein_max_distance;
			else
				$levenshtein_distance = $levenshtein_max_distance + 1;
			
			//compare string
			if(levenshtein($str1, $str2) <= $levenshtein_distance)
				$result = true;	
		}
	}
	
	return $result;
}

/**
* Get Duplicated Objects
*
* @param string $object_name
* @param int $object_id (if passed then $duplicate_group_id parameter will be ignored)
* @param int $duplicate_group_id (Optional, default: 0)
* @return array
*/
function cug_get_duplicates($object_name, $object_id, $duplicate_group_id=0) {
	global $mysqli;
	$result = array();
	
	$object_db_info = cug_get_duplicate_object_db_info($object_name);
    
	//get duplicate_group_id value for $object_id (if any)
	if($object_id > 0) {
		$arr = $mysqli->get_field_val($object_db_info['table'], $get_field="duplicate_group_id", $where="id=$object_id");
		$duplicate_group_id = !empty($arr[0]['duplicate_group_id']) ? $arr[0]['duplicate_group_id'] : 0;
	}
	
	//get duplicates
	if($duplicate_group_id > 0) {
		$query = "SELECT * FROM ".$object_db_info['table']." WHERE duplicate_group_id = ".$duplicate_group_id;
		$r = $mysqli->query($query);
		
		if($r) {
			if($r->num_rows) {
				while($row = $r->fetch_array(MYSQLI_ASSOC)) {
	                $result[] = $row;
				}
			}
		}
	}
	
	return $result;  
}

/**
* Edit Duplicate Group ID
*
* @param string $object_name
* @param array object_ids_arr
* @param int $duplicate_group_id
* @param boolean $duplicate_check_status (Optional, set 'duplicate_check_status' field to 1 if true, default: true)
* @return void
*/
function cug_edit_duplicate_group_id($object_name, $object_ids_arr=array(), $duplicate_group_id, $duplicate_check_status=true) {
	global $mysqli;
	
	$object_db_info = cug_get_duplicate_object_db_info($object_name);
	   
	if(count($object_ids_arr) > 0) {
		$id_list = implode(",", $object_ids_arr);
	    
	    $mysqli->query("UPDATE ".$object_db_info['table']." SET duplicate_group_id = ".$duplicate_group_id." WHERE id IN(".$id_list.")");
	    
		if($duplicate_check_status) {
			$mysqli->query("UPDATE ".$object_db_info['table']." SET duplicate_check_status = 1 WHERE id IN(".$id_list.")");
		}
	}
}


/**
* Remove Objects from Duplicate Group
*
* @param string $object_name
* @param array object_ids_arr
* @param boolean $duplicate_group_id (Optional, set 'duplicate_group_id' field to NULL if true, default: true)
* @param boolean $duplicate_check_status (Optional, set 'duplicate_check_status' field to NULL if true, default: false)
* @return void
*/
function cug_del_objects_from_duplicates($object_name, $object_ids_arr=array(), $duplicate_group_id=true, $duplicate_check_status=false) {
	global $mysqli;
	
	$object_db_info = cug_get_duplicate_object_db_info($object_name);
	
	if(count($object_ids_arr) > 0) {
		$id_list = implode(",", $object_ids_arr);
	    
	    if($duplicate_group_id){
	        $mysqli->query("UPDATE ".$object_db_info['table']." SET duplicate_group_id = null WHERE id IN(".$id_list.")");
	    }
	    
	    if($duplicate_check_status){
	        $mysqli->query("UPDATE ".$object_db_info['table']." SET duplicate_check_status = null WHERE id IN(".$id_list.")");
	    }
	}
}



/**
 * Define Duplicate Group ID 
 * 
 * used in cug_check_for_duplicates() function
 * 
 * @param int $id1
 * @param int $id2
 * @param int $group_id_1
 * @param int $group_id_2
 * @return int
 */
function cug_define_duplicate_group_id($id1, $id2, $group_id_1, $group_id_2) {
	$result = 0;
	
	if(!$group_id_1 && !$group_id_2)
		$result = ($id1 < $id2) ? $id1 : $id2;
	elseif($group_id_1 > 0 && !$group_id_2)
		$result = $group_id_1;
	elseif($group_id_2 > 0 && !$group_id_1)
		$result = $group_id_2;
	
	return $result;
}


/**
 * Write Log File
 * 
 * @param string $file (log filename)
 * @param string $data (data to be written in log file)
 * @param string $mode (Optional, default: 'a')
 * @return void
 */
function cug_duplicate_write_log_file($file, $data, $mode="a") {
	if($f = fopen($file, $mode)) {
		fwrite($f, $data, strlen($data));
		fclose($f);
	}
}

/**
* Compare Objects for Duplicates
*
* @param string $object_name
* @param array  $object_ids_arr (Optional)
* @param string $object_title (Optional)
* @param string $log_file (Optional, Log File Name, if passed then log file will be created, default: empty string)
* @param int $start_id (Optional)
* @param int $limit_from (Optional)
* @param int $limit_num (Optional)
* @param int $client_id (Optional)
* @return int (in case of $object_title will return group_id, in case of $object_ids_arr will return 1, in case of all objects comparison will return 1, in case of error will return 0)
* 
* @tutorial When $object_title is passed then only $object_title will be used for comparison, when $object_ids_arr is passed then only $object_ids_arr will be used for comparison, if none of them will not be passed then all objects from relevant table will be compared; Parameters $start_id, $limit_from and $limit_num will be used only in case of all objects comparison.
*/
function cug_check_for_duplicates($object_name, $object_ids_arr=array(), $object_title="", $log_file="", $start_id=0, $limit_from=0, $limit_num=0, $client_id=0) {
	global $mysqli;
	
    $object_db_info = cug_get_duplicate_object_db_info($object_name);
    
    //write log file
    if($log_file) {
    	$log_file_data = "CHECK FOR DUPLICATES ($object_name)". PHP_EOL. "============================================". PHP_EOL;
    	$log_file_data .= "Parameters: ";
    	cug_duplicate_write_log_file($log_file, $log_file_data, $mode="w");
    }
    $fields = "id,".implode(",", $object_db_info['fields']).",duplicate_group_id"; 
    //define objects and their fields to be compared
    $objects_tobe_compared = array();
    
    if($object_title) { //when $object_title was passed
    	$object_title = strtolower(trim(cug_duplicates_replace_special_chars($object_title)));
    	$objects_tobe_compared[0]['title'] = $object_title;
    	$query = "";
    	
    	$log_file_data = "Object Title - $object_title";
    }
    elseif(count($object_ids_arr) > 0) { //when $object_ids_arr was passed
    	$id_list = implode(",", $object_ids_arr);  	
    	$query = "SELECT $fields FROM {$object_db_info['table']} WHERE id IN ($id_list)";
    	
    	$log_file_data = "Object IDs, total - ".count($object_ids_arr);
    }
    else { // select all objects with NULL value in 'duplicate_check_status' field
    	$query = "SELECT $fields FROM {$object_db_info['table']} WHERE";
    	$query .= ($start_id > 0) ? " id>=$start_id AND" : "";
    	$query .= ($client_id > 0) ? " register_from=$client_id AND" : "";
    	$query .= " duplicate_check_status IS null ORDER BY id";
    	$query .= ($limit_from >= 0 && $limit_num > 0) ? " LIMIT $limit_from, $limit_num" : "";
    	
    	$log_file_data = "None, Start ID - $start_id, Limit From - $limit_from, Limit Num - $limit_num, Client ID - $client_id";
    }
    //-----------------------------
    
    //write log file
    if($log_file) {
    	$log_file_data .= PHP_EOL.PHP_EOL. "Start Process". PHP_EOL;
    	cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
    }
    //---------------------------
    
    
    //collect objects to be compared from DB
   	if($query) {
   		$log_file_data = @date("Y-m-d H:i:s"). " - ". "Collect Objects to be compared from DB". PHP_EOL;
   		$r = $mysqli->query($query, MYSQLI_USE_RESULT);
   		 
   		if($r) {
   			while($row = $r->fetch_assoc()) {
   				$objects_tobe_compared[ $row['id'] ]['group_id'] = $row['duplicate_group_id'];
   				
   				foreach($object_db_info['fields'] as $field) {
   					$objects_tobe_compared[ $row['id'] ][$field] = strtolower(trim(cug_duplicates_replace_special_chars($row[$field])));
   				}
   			}
   			
   			$log_file_data .= @date("Y-m-d H:i:s"). " - ". "Finish Objects Collection". PHP_EOL;
   		}    		
   	}
    //-----------------------------

   	
   	if(count($objects_tobe_compared) == 0)
   		return 0; //error
  	
   	
   	//write log file
   	if($log_file) {
   		$log_file_data .= @date("Y-m-d H:i:s"). " - ". "Collect all Objects from Relevant Table". PHP_EOL;
   		cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
   	}
   	//---------------------------   	
   	
   	//get all objects from relevant table
   	$all_objects = array();
   	$query = "SELECT $fields FROM {$object_db_info['table']} ORDER BY id";
   	$r = $mysqli->query($query, MYSQLI_USE_RESULT);
   	
   	if($r) {
   		while($row = $r->fetch_assoc()) {
   			$all_objects[ $row['id'] ][ 'group_id' ] = $row['duplicate_group_id'];
   				
   			foreach($object_db_info['fields'] as $field) {
   				$all_objects[ $row['id'] ][ $field ] = strtolower(trim(cug_duplicates_replace_special_chars($row[$field])));
   			}
   		}
   		
   		//write log file
   		if($log_file) {
   			$log_file_data = @date("Y-m-d H:i:s"). " - ". "Finish Objects Collection, Total Objects - ". count($all_objects). PHP_EOL.PHP_EOL;
   			$log_file_data .= "Start Comparisson Process".PHP_EOL;
   			cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
   		}
   		//---------------------------   		
   		
   	}
   	//echo PHP_EOL.memory_get_peak_usage(true)." - ".memory_get_usage(true)." after get all objects";
   	//-----------------------------
   	//-----------------------------
   	
   	
    //start comparison of $objects_tobe_compared and $all_objects arrays
	switch(strtoupper($object_name)) {
		//----------------
		case 'MEMBER':
		//----------------	
			foreach($objects_tobe_compared as $id1 => $field1) {
				$id_list = "";
				$group_id = 0;				
				$log_file_data = @date("Y-m-d H:i:s"). " id: $id1, ". "title: {$field1['title']}, ". "group_id: "; 
						
				foreach($all_objects as $id2 => $field2) {	
					if($id1 != $id2) { //to avoid comparison of same objects
						
						if( // compare fields
						cug_compare_strings_for_similarity_levenshtein($field1['title'], $field2['title']) || 
						cug_compare_strings_for_similarity_levenshtein($field1['title'], $field2['alias']) || 
						( !$object_title && 
								(	cug_compare_strings_for_similarity_levenshtein($field1['alias'], $field2['title']) ||
									cug_compare_strings_for_similarity_levenshtein($field1['alias'], $field2['alias'])	) )
	   					) {
							//define group id
							if(!$group_id) {
								if(!$object_title) {
									$group_id = cug_define_duplicate_group_id($id1, $id2, $field1['group_id'], $field2['group_id']);									
								}
								else {
									$group_id = ($field2['group_id']) ? $field2['group_id'] : $id2;
								}
							}
							//---------------------
							
							if($group_id) {
								$id_list .= "$id2,";
                                $all_objects[$id2] = null;
                                unset($all_objects[$id2]);
								//$all_objects[$id2]['group_id'] = $group_id; //update group_id in $all_objects array because it must be synchronized to DB table for next usage in loop
							}
						}
					}
				}//end of foreach
				
				//update table
				if($id_list) {
					if(!$object_title)
						$id_list .= $id1;
					else 
						$id_list = rtrim($id_list, ",");
					
					$query = "UPDATE {$object_db_info['table']} SET duplicate_group_id=$group_id, duplicate_check_status=1 WHERE id IN($id_list)";
					$mysqli->query($query);
					
					$log_file_data .= $group_id. PHP_EOL;
				}
				else{
					$log_file_data .= "0". PHP_EOL;
                    $query = "UPDATE {$object_db_info['table']} SET  duplicate_check_status=1 WHERE id = $id1";
					$mysqli->query($query);
                }
				//----------------------	

				//write log file
				if($log_file) {
					cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
				}
				//---------------------------
			}
		break;
		
		
		//----------------
		case 'ALBUM':
		//----------------
			foreach($objects_tobe_compared as $id1 => $field1) {
				$id_list = "";
				$group_id = 0;
				$log_file_data = @date("Y-m-d H:i:s"). " id: $id1, ". "title: {$field1['title']}, ". "group_id: ";
				
				foreach($all_objects as $id2 => $field2) {					
					if($id1 != $id2) { //to avoid comparison of same objects
						
						$str1 = ($object_title) ? $field1['title'] : $field1['title'].$field1['subtitle'].$field1['title_version'].$field1['ean_code'];	 
						$str2 = $field2['title'].$field2['subtitle'].$field2['title_version'].$field2['ean_code'];
						
						// compare fields
						if( (!$object_title && $field1['catalogue_num']==$field2['catalogue_num']) && (cug_compare_strings_for_similarity_levenshtein($str1, $str2)) ){
							//define group id
							if(!$group_id) {
								if(!$object_title) {
									$group_id = cug_define_duplicate_group_id($id1, $id2, $field1['group_id'], $field2['group_id']);
								}
								else {
									$group_id = ($field2['group_id']) ? $field2['group_id'] : $id2;
								}
							}
							//---------------------
								
							if($group_id) {
								$id_list .= "$id2,";
                                $all_objects[$id2] = null;
								unset($all_objects[$id2]);
                                //$all_objects[$id2]['group_id'] = $group_id; //keep group_id because $all_objects array must be synchronized to DB table for next usage in loop
							}
						}
					}
					
				}// end of foreach

				
				//update table
				if($id_list) {
					if(!$object_title)
						$id_list .= $id1;
					else
						$id_list = rtrim($id_list, ",");
						
					$query = "UPDATE {$object_db_info['table']} SET duplicate_group_id=$group_id, duplicate_check_status=1 WHERE id IN($id_list)";
					$mysqli->query($query);
					
					$log_file_data .= $group_id. PHP_EOL;
				}
				else{
					$log_file_data .= "0". PHP_EOL;
                    $query = "UPDATE {$object_db_info['table']} SET  duplicate_check_status=1 WHERE id = $id1";
					$mysqli->query($query);
                }
				//----------------------
				
				//write log file
				if($log_file) {
					cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
				}
				//---------------------------
			}
		break;

		
		//----------------
		case 'TRACK':
		//----------------
			foreach($objects_tobe_compared as $id1 => $field1) {
				$id_list = "";
				$group_id = 0;
				$log_file_data = @date("Y-m-d H:i:s"). " id: $id1, ". "title: {$field1['title']}, ". "group_id: ";
				
				foreach($all_objects as $id2 => $field2) {
					if($id1 != $id2) { //to avoid comparison of same objects
			
						$str1 = ($object_title) ? $field1['title'] : $field1['title'].$field1['part'].$field1['version'];
						$str2 = $field2['title'].$field2['part'].$field2['version'];
			
						// compare fields
						if( cug_compare_strings_for_similarity_levenshtein($str1, $str2)){
							//define group id
							if(!$group_id) {
								if(!$object_title) {
									$group_id = cug_define_duplicate_group_id($id1, $id2, $field1['group_id'], $field2['group_id']);
								}
								else {
									$group_id = ($field2['group_id']) ? $field2['group_id'] : $id2;
								}
							}
							//---------------------
			
							if($group_id) {
								$id_list .= "$id2,";
                                $all_objects[$id2] = null;
								unset($all_objects[$id2]);
                                //$all_objects[$id2]['group_id'] = $group_id; //keep group_id because $all_objects array must be synchronized to DB table for next usage in loop
							}
						}
					}
						
				}// end of foreach
					
				//update table
				if($id_list) {
					if(!$object_title)
						$id_list .= $id1;
					else
						$id_list = rtrim($id_list, ",");
			
					$query = "UPDATE {$object_db_info['table']} SET duplicate_group_id=$group_id, duplicate_check_status=1 WHERE id IN($id_list)";
					$mysqli->query($query);
					
					$log_file_data .= $group_id. PHP_EOL;
				}
				else{
					$log_file_data .= "0". PHP_EOL;
                    $query = "UPDATE {$object_db_info['table']} SET  duplicate_check_status=1 WHERE id = $id1";
					$mysqli->query($query);
                }
				//----------------------
				
				//write log file
				if($log_file) {
					cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
				}
				//---------------------------
			}
		break;
	}

	//write log file
	if($log_file) {
		$log_file_data = PHP_EOL. @date("Y-m-d H:i:s"). " Finish";
		cug_duplicate_write_log_file($log_file, $log_file_data, $mode="a");
	}
	//---------------------------
    
	if($object_title && $group_id)
		return $group_id;
	else
    	return 1;
}	

?>