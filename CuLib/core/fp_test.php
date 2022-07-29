<?PHP 

class cug__fp {
	
	private $fp_tool;
	private $slash;

	/**
	 * Constructor
	 *
	 * @param string $var
	 */
	public function __construct($fp_tool, $slash){
		$this->fp_tool = $fp_tool;
		$this->slash = $slash;
	}

	
	/**
	 * Generate Footprint (v0.34)
	 * 
	 * @param string $audio_file
	 * @param string $params (Optional, default: -I:50 -F:1 -D:30)
	 * @param string $log_file (optional)
	 * @param string $delete_bif_fp_file (optional, default: true)
	 * @param string $delete_small_fp_file (optional, default: true)
	 * @return array
	 */
	public function gen_fp_034($audio_file, $params="-I:50 -F:1 -D:30", $log_file="", $delete_bif_fp_file=true, $delete_small_fp_file=true) {

		$big_fp_file 	= $this->gen_fp_034_big($audio_file, $params, $log_file);
		$small_fp_file 	= $this->gen_fp_034_small($big_fp_file);
		$fp_arr 		= $this->parse_fp_034($small_fp_file);
		
		if($delete_bif_fp_file) @unlink($big_fp_file);
		if($delete_small_fp_file) @unlink($small_fp_file);
	
		return $fp_arr;
	}
	

	/**
	 * Generate 'big' Footprint (v0.34)
	 * 
	 * @param string $audio_file
	 * @param string $params (Optional, default: -I:50 -F:1 -D:30)
	 * @param string $log_file (Optional)
	 * @return string
	 */
	private function gen_fp_034_big($audio_file, $params="-I:50 -F:1 -D:30", $log_file="") {
	
		if(!$log_file) {
			$path_parts = pathinfo($audio_file);
			$log_file = $path_parts['dirname'].$this->slash.$path_parts['filename']."_fp.txt";
		}
	
		$command = $this->fp_tool." \"{$audio_file}\" $params";
		$command .= " -L:\"$log_file\"";
		
		exec($command);
	
		@chmod($log_file, 0774);
		return $log_file;
	}
	
	
	/**
	 * Generate 'small' Footprint (v0.34)
	 * 
	 * @param string $big_fp_file
	 * @param string $log_file (Optional)
	 * @return string
	 */
	private function gen_fp_034_small($big_fp_file, $log_file="") {
	
		if(!$log_file) {
			$path_parts = pathinfo($big_fp_file);
			$log_file = $path_parts['dirname'].$this->slash.$path_parts['filename']."_small.txt";
		}
	
		$command = $this->fp_tool." -T \"$big_fp_file\" -O \"$log_file\"";
		
		exec($command, $buffer);
		
		@chmod($log_file, 0774);
		return $log_file;
	}
	
	
	/**
	 * Parse 'small' Footprint (v0.34)
	 * 
	 * @param string $small_fp_file
	 * @return array
	 */
	private function parse_fp_034($small_fp_file) {
		$result = array();
		$f = fopen($small_fp_file, "r");
	
		if($f) {
			$buffer = @fread($f, @filesize($small_fp_file));
			
			if($buffer) {
				$temp_arr = explode("\n", $buffer);
				$total_fp_blocks = count($temp_arr);
	
				for($i=0; $i<$total_fp_blocks; $i++) {
					$result[$i] = explode(" ", $temp_arr[$i]);
				}
			}
	
			fclose($f);
		}
	
		return $result;
	}
}


/**
 * Get File Info by File Index from Log
 * (used in API)
 * @param int $file_index (file_id or file_index)
 * @param string $search_in ('main_table' or 'incomings_table')
 * @return string
 */
function cug_fp_get_fileinfo_by_fileindex($file_index) {
	global $mysqli, $Tables;
	$result = array();
	$index = 0;
	
	$query = "SELECT * FROM ".$Tables['log_analyzed_audio_data']." WHERE file_index=$file_index";
	$r = $mysqli->query($query);
	
		if($r->num_rows) {
			while($arr = $r->fetch_array()) {
				$result[$index] = $arr;
				$index ++;
			}
		}
	
	return 	$result;
}

/**
 * Update 'last_use_time' in 'log_analyzed_audio_data' table
 * 
 * @param int $file_index
 * @return boolean
 */
function cug_fp_update_fileinfo_by_fileindex($file_index) {
	global $mysqli, $Tables;

	$query = "UPDATE ".$Tables['log_analyzed_audio_data']." SET last_use_time=NOW() WHERE file_index=$file_index";
	$r = $mysqli->query($query);

	if($mysqli->query($query)) {
		return true;
	}
	else 
		return false;
}


/**
 * Get KEY
 *
 * @param integer
 * @return array
 */
function cug_fp_get_key($key_val)
{
global $mysqli, $Tables;
$result = array();

	if($key_val >= 0) {
		$r = $mysqli->query("SELECT * FROM ".$Tables['key']." WHERE key_val=$key_val");
			if($r->num_rows) {
				$arr = $r->fetch_array();
				$result['key_id'] = $arr['id'];
				$result['major_tone'] = $arr['major_tone'];
				$result['minor_tone'] = $arr['minor_tone'];
			}
	}
	
return 	$result;
}


/**
 * Get KEY ID
 *
 * @param integer
 * @return integer
 */
function cug_fp_get_key_id($key_val)
{
global $mysqli, $Tables;
$key_id = 0;

	if($key_val >= 0) {
		$r = $mysqli->query("SELECT id FROM ".$Tables['key']." WHERE key_val=$key_val");
			if($r->num_rows) {
				$arr = $r->fetch_array();
				$key_id =  $arr['id'];
			}
	}


return 	$key_id;
}


/**
 * Get Simial Tracks from Tony's server
 * 
 * @param array $fp_arr
 * @param int $en_sim (Optional, Similarity min range for ENGLISH music, default: 90)
 * @param int $cl_sim (Optional, Similarity min range for CLASSICAL music, default: 93)
 * @param int $ot_sim (Optional, Similarity min range for OTHER music, default: 93)
 * @param int $FPEQ (Optional, pass 1 to enable FPEQ, default: 0)
 * @return array
 */
function cug_fp_get_similars_from_tony($fp_arr, $en_sim=90, $cl_sim=93, $ot_sim=93, $FPEQ=0) {
	$result = array();
	
		if(count($fp_arr) > 0) {
			$fp_str = implode("|", $fp_arr);
			
			//$url = "http://85.214.53.194/FootPrintV2/footprint/similarity0915.action?fp=";
			$url = "http://81.169.212.126/FootPrintV2/footprint/similarity0915.action?fp=";
			$url .= $fp_str;
			$url .= "&matchType=en$".$en_sim."|cl$".$cl_sim."|ot$".$ot_sim;
			$url .= ($FPEQ) ? "&type=fpeq" : "";
			
			$data = @file_get_contents($url);
			
			if($data !== false) {
				$result = json_decode($data, true);
			}
		}
		
	return $result;	
}


/**
 * Get Simial Tracks from Tony's server (Multi Requests)
 *
 * @param array $fp_arrays
 * @param int $en_sim (Optional, Similarity min range for ENGLISH music, default: 90)
 * @param int $cl_sim (Optional, Similarity min range for CLASSICAL music, default: 93)
 * @param int $ot_sim (Optional, Similarity min range for OTHER music, default: 93)
 * @param number $FPEQ (Optional, pass 1 to enable FPEQ, default: 0)
 * @param string $usr (Optional, pass 'cugate' to get CUGATE Track IDs in the result, default: '')
 * @return array
 */
function cug_fp_get_similars_from_tony_multirequest($fp_arrays, $en_sim=90, $cl_sim=93, $ot_sim=93, $FPEQ=0, $usr="") {
	$result = array();

	if(count($fp_arrays) > 0) {
		//generate URLs
		$urls = array();
		
		foreach($fp_arrays as $fp_arr) {
			$fp_str = implode("|", $fp_arr);
				
			//$url = "http://85.214.53.194/FootPrintV2/footprint/similarity0915.action?fp=";
			$url = "http://81.169.212.126/FootPrintV2/footprint/similarity0915.action?fp=";
			$url .= $fp_str;
			$url .= "&matchType=en$".$en_sim."|cl$".$cl_sim."|ot$".$ot_sim;
			$url .= ($FPEQ) ? "&type=fpeq" : "";
			$url .= ($usr) ? "&usr=$usr" : "";
			
			//echo $url;
			$urls[] = $url;
		}
		//print_r($urls); return $result;
		
		//Send Requests
		if(count($urls) > 0) {
			$arr = multiRequest($urls);
			
			//Parse Response
			if(count($arr) > 0) {
				$index = 0;
				
				foreach($arr as $data) {
					$result[$index] = array();
					
					if($data !== false) {
						$result[$index] = json_decode($data, true);
					}

					$index ++;
				}
			}
			//-----------------
		}
		//------------
	}

	return $result;
}







/**
 * Get identical track from similars (Tony)
 * 
 * @param array $similar_blocks_arr
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_get_identical_track_from_similars_tony($similar_blocks_arr, $field_iindex="slId") {
	$result = array();

	//sort array by 'similarity' - SORT_DESC
	$arr = array();
	foreach($similar_blocks_arr as $key=>$similar_blocks) {
		$arr[$key] = cug_sort_array($similar_blocks, $sort_by_column='similarity', $sort_type=SORT_DESC);
	}
	//----------------------------
	//print_r($arr);

	//start processing
	$ids_arr_tmp = array();
	foreach($arr as $key=>$similar_blocks) {
		if(count($similar_blocks) > 0) {
			foreach($similar_blocks as $key2 => $similar_block) {
				//choose track_id and it's high percentages from all blocks
				$percetanges = array();
				if(!empty($similar_block[$field_iindex]) && !empty($similar_block['similarity']) && !in_array($similar_block[$field_iindex], $ids_arr_tmp)) {
					$ids_arr_tmp[] = $similar_block[$field_iindex];
					$percetanges = cug_fp_choose_percentages_from_similar_blocks_by_track_id_tony($arr, $similar_block[$field_iindex], $field_iindex);
					//print_r($percetanges);
						
					//check conditions
					$match = cug_fp_check_conditions_for_percentages_tony($percetanges);
						
					if($match) {
						$result = $similar_block;
						break 2; //break both foreach loops
					}
				}
			}
		}
	}


	return $result;
}



/**
 * Choose Percentages from Similar Blocks by Track ID  (Tony)
 * 
 * @param array $arr
 * @param int $track_id
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_choose_percentages_from_similar_blocks_by_track_id_tony($arr, $track_id, $field_iindex="slId") {
	$result = array();

	foreach($arr as $key => $similar_blocks) {
		if(count($similar_blocks) > 0) {
			foreach($similar_blocks as $key2 => $similar_block) {
				if(!empty($similar_block[$field_iindex]) && !empty($similar_block['similarity']) && $similar_block[$field_iindex] == $track_id) {
					$result[] = $similar_block['similarity'];
					break; //choose only one similarity from each footprint's result, uncomment this line if you want to choose all similarities from whole result (all blocks)
				}
			}
		}

	}


	return $result;
}


/**
 * Check Conditions for Percentages (Tony),
 * used for finding itentical track
 * 
 * @param array $arr
 * @return boolean
 */
function cug_fp_check_conditions_for_percentages_tony($arr) {
	$match = false;

	rsort($arr); //sort from high to low

	// 1 Block
	if(count($arr) == 1) {
		$match = true;
		for($i=0; $i<1; $i++) {
			if($arr[$i] < 99) {
				$match = false;
				break;
			}
		}
	
		if($match)
			return $match;
	}
	
	// 2 Blocks
	elseif(!$match && count($arr) == 2) {
		$match = true;
		for($i=0; $i<2; $i++) {
			if($arr[$i] < 98) {
				$match = false;
				break;
			}
		}

		if($match)
			return $match;
	}

	// 3 Block
	elseif(!$match && count($arr) == 3) {
		$match = true; 
		for($i=0; $i<3; $i++) {
			if($arr[$i] < 97) {
				$match = false;
				break;
			}
		}

		if($match)
			return $match;
	}

	// 4 Block
	elseif(!$match && count($arr) == 4) {
		$match = true;
		for($i=0; $i<4; $i++) {
			if($arr[$i] < 94.9) {
				$match = false;
				break;
			}
		}

		if($match)
			return $match;
	}

	// 5 Block
	elseif(!$match && count($arr) == 5) {
		$match = true;
		for($i=0; $i<5; $i++) {
			if($arr[$i] < 94) {
				$match = false;
				break;
			}
		}

		if($match)
			return $match;
	}
	
	// more than 5 Blocks
	elseif(!$match && count($arr) > 5) {
		$match = true;
		for($i=0; $i<count($arr); $i++) {
			if($arr[$i] < 93) {
				$match = false;
				break;
			}
		}
	
		if($match)
			return $match;
	}

	return $match;
}


/**
 * Collect Similar Tracks from tracks list received from Tony
 * 
 * @param array $similars_arr
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_collect_similars_tony($similars_arr, $field_iindex="slId") {
	$result = array();

	$unique_ids = cug_fp_choose_unique_track_ids_from_similars_tony($similars_arr, $field_iindex);
	//print_r($unique_ids);
	$arr = cug_fp_calculate_summarized_percentages_tony($similars_arr, $unique_ids, $field_iindex);
	//print_r($arr);

	if(count($arr) > 0) {
		$arr = cug_fp_calculate_average_similarity_tony($arr);
		$arr = cug_sort_array($arr, 'similarity_avg', $sort_type=SORT_DESC);
		//print_r($arr);
		$result = cug_fp_choose_top_similar_tracks_tony($arr, 10, 1);
		//print_r($result);
	}

	return $result;
}


/**
 * Choose Unique Track IDs from Similars (Tony)
 * 
 * @param array $similars_arr
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_choose_unique_track_ids_from_similars_tony($similars_arr, $field_iindex="slId") {
	$result = array();
	$tmp_arr = array();
	$tmp_index = 0;

	foreach($similars_arr as $similar_blocks) {
		foreach($similar_blocks as $block) {
			if(!empty($block[$field_iindex]) && !empty($block['mediaName']) && !empty($block['mediaArtist'])) {
    		    $track_id = $block[$field_iindex];
    				
    			$colums_val_arr = array();
    			$colums_val_arr['mediaName'] = $block['mediaName'];
    			$colums_val_arr['mediaArtist'] = $block['mediaArtist'];
    				
    			if(!in_array($track_id, $result) && !cug_search_in_array($tmp_arr, $colums_val_arr)) {
    				$result[] = $track_id;
    
    				$tmp_arr[$tmp_index]['mediaName'] = $block['mediaName'];
    				$tmp_arr[$tmp_index]['mediaArtist'] = $block['mediaArtist'];
    				$tmp_index ++;
    			}
			}
		}
	}

	return $result;
}


/**
 * Calculate Summarized Percentages from Similars (Tony)
 * 
 * @param array $similars_arr
 * @param array $unique_ids
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_calculate_summarized_percentages_tony($similars_arr, $unique_ids, $field_iindex="slId") {
	$result = array();
	$index = 0;

	foreach($unique_ids as $track_id) {
		$result[$index][$field_iindex] = $track_id;
		$result[$index]['similarity_sum'] = 0;
		$result[$index]['blocks_num'] = 0;

		foreach($similars_arr as $similar_blocks) {
			foreach($similar_blocks as $block) {
				if(!empty($block[$field_iindex]) && !empty($block['similarity'])) {
    			    if($block[$field_iindex] == $track_id) {
    					$result[$index]['similarity_sum'] += $block['similarity'];
    					$result[$index]['blocks_num'] += 1;
    				}
				}
			}
		}

		$index ++;
	}

	return $result;
}


/**
 * Calculate Avarage of Similarity (Tony)
 * 
 * @param array $arr
 * @return array
 */
function cug_fp_calculate_average_similarity_tony($arr) {
	foreach($arr as $key => $val) {
		$arr[$key]['similarity_avg'] = round($val['similarity_sum'] / $val['blocks_num'], 2);
	}

	return $arr;
}


/**
 * Choose Top N Similar Tracks (Tony)
 * 
 * @param array $arr
 * @param number $top_num (Optional, default: 10)
 * @param number $min_matched_bocks_num (Optional, default: 2)
 * @return array
 */
function cug_fp_choose_top_similar_tracks_tony($arr, $top_num=10, $min_matched_bocks_num=2) {
	$result = array();
	$selected_tracks = 1;

	//select top 10 similar tracks where matched 'blocks_num' > 1
	foreach($arr as $val) {
		if($val['blocks_num'] >= $min_matched_bocks_num) {
			if($selected_tracks <= $top_num) {
				$result[] = $val;
				$selected_tracks ++;
			}
			else
				break;
		}
	}

	return $result;
}


/**
 * Parse Track Info received from Tony
 * 
 * @param array $arr
 * @param string $field_iindex (Optional, pass 'track_id' if you have CUGATE Track IDs in result array, default is 'slId')
 * @return array
 */
function cug_fp_parse_track_info_tony($arr, $field_index="slId") {
	$result = array();
	
	if(count($arr) > 0) {
	    if($field_index == "slId") {
    		$shenzhen_id = !empty($arr[$field_index]) ? $arr[$field_index] : "";
    		
    		if($shenzhen_id) {
    			$track_id = cug_get_trackid_by_shenzhen_id($shenzhen_id);
    			$result = cug_cache_get_track($track_id);
    		}
	    }
	    elseif($field_index == "track_id") { //when directly CUGATE Track ID is provided
	        $track_id = !empty($arr[$field_index]) ? $arr[$field_index] : "";
	        
	        if($track_id)
	           $result = cug_cache_get_track($track_id);
	    }
	}

	return $result;
}


/**
 * Eliminate identical track title from similars (Tony)
 * 
 * @param string $track_title
 * @param array $similars_arr
 * @return array
 */
function cug_fp_eliminate_identical_track_from_similars_tony($track_title, $similars_arr) {
	$result = array();
	
	if($track_title) {
		foreach($similars_arr as $arr) {
			if($arr['track_title'] != $track_title)
				$result[] = $arr;
		}
	}
	
	return $result;
}

/**
 * Eliminate identical track id from similars (Tony)
 *
 * @param int $track_id
 * @param array $similars_arr
 * @return array
 */
function cug_fp_eliminate_identical_track_id_from_similars_tony($track_id, $similars_arr) {
    $result = array();

    if($track_id) {
        foreach($similars_arr as $arr) {
            if($arr['track_id'] != $track_id)
                $result[] = $arr;
        }
    }

    return $result;
}
?>