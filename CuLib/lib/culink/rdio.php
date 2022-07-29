<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	CULINK (RDIO)
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2015
 * @version		1.0
 */

// ------------------------------------------------------------------------


class cug__rdio {
	
	private $URL_API = "http://api.rdio.com/1/";
	private $LABELS = array();
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		# List of Labels of the Albums and Tracks, search result will be filtered by this list with presented ptiorities
		# if you don't want to use such of filter then just comment this section
		$this->LABELS [0] = "HDC";
		$this->LABELS [1] = "High Definition Classics";
		$this->LABELS [2] = "HDJ";
		$this->LABELS [3] = "High Definition Jazz";
		$this->LABELS [4] = "Beaux";
		$this->LABELS [5] = "Futurex";
		$this->LABELS [6] = "Music & Highlights";
		$this->LABELS [7] = "Masters Of The Last Century";
		
	}
	
	
	public function test() {
		if($ch = curl_init()) {
			$url_params = "method=search&";
			$url_params .= "types=Artist&";
			$url_params .= "query=".urlencode("Taylor Swift")."&";
			$url_params .= "start=0&";
			$url_params .= "count=2";
			
			curl_setopt($ch, CURLOPT_URL, $this->URL_API);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $url_params);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
			curl_setopt($ch, CURLOPT_POST, true);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
			
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//curl_setopt($ch, CURLOPT_CAINFO, "cugate.pem");
			
			curl_setopt($ch, CURLOPT_ENCODING, "gzip");
			
			curl_setopt($ch, CURLOPT_HEADER, true); // returns Response Header as well
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // TRUE to return the transfer as a string of the return value of curl_exec()
			
			
			$result = curl_exec($ch);
			return $result;
		}
	}
	
	
	/**
	 * Lookup Item Info by Item ID
	 *
	 * @param string $item
	 * @param string $item_id
	 * @return string
	 */
	public function lookup_item_info($item, $item_id) {
	
		if($item && $item_id) {
			$url = $this->URL_API.$item."/".$item_id;	
			return file_get_contents($url);
		}
		else
			return "";
	}
	
	
	/**
	 * Get Item Info by Item ID
	 *
	 * @param string $item
	 * @param string $item_id
	 * @return array
	 */
	public function get_item_info($item, $item_id) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
	
		$result_json = $this->lookup_item_info($item, $item_id);
	
			if(!$result_json) {
				$result[0] = "FAILED";
				return $result;
			}
		
			$result_arr = json_decode($result_json, true);
			
				if(empty($result_arr) || !count($result_arr)) {
					$result[0] = "FAILED";
					return $result;
				}
				//elseif(!empty($result_arr['data']) && !empty($result_arr['total']) && $result_arr['total'] > 0) {
					$arr = $this->parse_item_info($result_arr);
			
						if(count($arr) > 0) {
							$result[0] = "FOUND";
							$result[1] = $arr;
						}
				//}
	
		return $result;
	}

	
	/**
	 * Parse Item Info
	 *
	 * @param array $arr
	 * @return array
	 */
	private function parse_item_info($arr) {
		$result = array();
		
		$result = $arr;
		return $result;
	}

	
	/**
	 * Search Item by Term
	 *
	 * @param string $item
	 * @param string $term
	 * @param int $index (default=0)
	 * @param int $limit (default=0)
	 * @return string
	 */
	public function search_item_info($item, $term, $index=0, $limit=200) {
		
		if($item && $term) {
			$url = $this->URL_API."search/$item?q=".urlencode($term);
			$url .= ($index > 0) ? "&index=".$index : "";
			$url .= ($limit > 0) ? "&limit=".$limit : "";
		
			return file_get_contents($url);
		}
		else
			return "";
	}
	
	
	/**
	 * Search Item
	 *
	 * @param string $item
	 * @param string $term1
	 * @param string $term2 (optional)
	 * @param string $term3 (optional)
	 * @param string $term4 (optional)
	 * @param array $filters_arr (additional filters array, optional)
	 * @param number $index (default=0)
	 * @param number $limit (default=0)
	 * @return array
	 */
	public function search_item($item, $term1, $term2="", $term3="", $term4="", $filters_arr=array(), $index=0, $limit=200) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		
			if(($item == "track" || $item == "album") && $term2) {
				$search_term = $term1." ".$term2;
			}
			else {
				$search_term = $term1;
			}
		
		$result_json = $this->search_item_info($item, $search_term, $index, $limit);
		//echo PHP_EOL.$result_json.PHP_EOL;
		
		
			if(!$result_json) {
				$result[0] = "FAILED";
				return $result;
			}
		
		$result_arr = json_decode($result_json, true);
		//print_r($result_arr);	
			
			if(empty($result_arr) || !count($result_arr)) {
				$result[0] = "FAILED";
				return $result;
			}			
			elseif(!empty($result_arr['data']) && !empty($result_arr['total']) && $result_arr['total'] > 0) {
				$arr = $this->parse_search_result($item, $result_arr, $term1, $term2, $term3, $term4, $filters_arr);
					
					if(!empty($arr[0]) && $arr[0] == "FAILED") {
						$result[0] = "FAILED";
						return $result;
					}
				
					if(count($arr) > 0) {
						$result[0] = "FOUND";
						$result[1] = $arr;
					}
			}

			
		return $result;
	}
	
	
	/**
	 * Parse Search Result
	 *
	 * @param string $item
	 * @param array $result_arr (search result array)
	 * @param string $term1
	 * @param string $term2 (optional)
	 * @param string $term3 (optional)
	 * @param string $term4 (optional)
	 * @param array $filters_arr (additional filters array, optional)
	 * @return array
	 */
	private function parse_search_result($item, $result_arr, $term1, $term2="", $term3="", $term4="", $filters_arr=array()) {
		$result = array();
		$sort_arr = array();
		$index = 0;
		
		switch($item) {
			//--------------
			case 'album':
			//--------------	
				$total_tracks = ($term3) ? $term3 : 0;
				$release_year = ($term4) ? $term4 : 0;
				
				foreach($result_arr['data'] as $key => $object) {
					$album_artist = !empty($object['artist']['name']) ? $object['artist']['name'] : "";
					$album_artist = (!$album_artist) ? (!empty($object['artist']['data'][0]['name']) ? $object['artist']['data'][0]['name'] : "") : $album_artist;
					$trackCount = !empty($object['nb_tracks']) ? $object['nb_tracks'] : 0;
					
					$album_artist = $this->remove_special_chars($album_artist, " \t");
					$term2 = $this->remove_special_chars($term2, " \t");
					
					if(strtoupper($album_artist) == strtoupper($term2)) {
						$album_title = !empty($object['title']) ? $object['title'] : "";
						
						$album_title = $this->replace_special_chars($album_title);
						$term1 = $this->replace_special_chars($term1);
							
						$album_title = $this->remove_special_chars($album_title, " \t’:'-_()[]„\"");
						$term1 = $this->remove_special_chars($term1, " \t’:'-_()[]„\"");
						
						similar_text(strtoupper($term1), strtoupper($album_title), $perc);
						
						if($perc >= 88 && 
						((strtoupper($album_artist) != "VARIOUSARTISTS") ||
						(strtoupper($album_artist) == "VARIOUSARTISTS" && (int)$total_tracks == (int)$trackCount))) {
							
							$sort_arr[$index]['item_index'] = $key;
							$sort_arr[$index]['perc'] = $perc;
							$sort_arr[$index]['album_id'] = !empty($object['id']) ? $object['id'] : "";
							$sort_arr[$index]['total_tracks'] = $trackCount;
							
							//get additional info about album
							$album_info = $this->get_item_info($item, $sort_arr[$index]['album_id']);
							
								if($album_info[0] == "FAILED") {
									$result[0] = "FAILED";
									return $result;
								}
								
							//get album tracks
							$album_tracks = $this->get_album_tracks($sort_arr[$index]['album_id']);
									
								if($album_tracks[0] == "FAILED") {
									$result[0] = "FAILED";
									return $result;
								}								
							
							$sort_arr[$index]['label'] = !empty($album_info[1]['label']) ? $album_info[1]['label'] : "";
							$sort_arr[$index]['rel_year'] = !empty($album_info[1]['release_date']) ? $this->extract_rel_year($album_info[1]['release_date']) : 0;
							
								if(!empty($album_tracks[1])) {
									$track_index = 0;
										foreach($album_tracks[1] as $track) {
											$sort_arr[$index]['tracks'][$track_index]['id'] = $track['id'];
											$sort_arr[$index]['tracks'][$track_index]['title'] = $track['title'];
											$sort_arr[$index]['tracks'][$track_index]['track_num'] = $track['track_position'];
											$sort_arr[$index]['tracks'][$track_index]['disc_num'] = $track['disk_number'];
											$sort_arr[$index]['tracks'][$track_index]['link'] = $track['link'];
											$sort_arr[$index]['tracks'][$track_index]['album_id'] = $sort_arr[$index]['album_id'];
	
											$track_index ++;
										}
								}
								
						$index ++;
						}
					}
				}
				
				//---------------
				if(count($sort_arr) > 0) {
					$this->array_sort_by_column($sort_arr, 'perc', SORT_DESC);
					//print_r($sort_arr);
					
					//check album content (step 4)
					$album_content_is_matched = false;
					$matched_album_content = 0;
					
					if(count($sort_arr) == 1 && (int)$total_tracks != (int)$sort_arr[0]['total_tracks'] && $sort_arr[0]['perc'] == 100 && strtoupper($album_artist) != "VARIOUSARTISTS") {
						//check release year
						if($release_year && $sort_arr[0]['rel_year']) {
							if((int)$release_year == (int)$sort_arr[0]['rel_year']) {
								$album_content_is_matched = true;
								$match_step = 42;
							}
							else
								$album_content_is_matched = false;
						}
						else {
							$album_content_is_matched = true;
							$match_step = 43;
						}
						//-----------------------
							
						if($album_content_is_matched) {
							$hight_index = $sort_arr[0]['item_index'];
							$hight_perc = $sort_arr[0]['perc'];
							
							$result = $result_arr['data'][$hight_index];
							$result['tracks'] = $sort_arr[0]['tracks'];
							
							$result['match_percent'] = $hight_perc;
							$result['match_step'] = $match_step;
								
							$matched_album_content = 1;
						}
					
					}

					
					if(count($filters_arr) > 0 && !$album_content_is_matched) {
					
						foreach($sort_arr as $arr) {
							$matched_tracks = 0;
								
							if(!empty($arr['tracks']) && count($arr['tracks']) == count($filters_arr)) {
								$index = 0;
								foreach($arr['tracks'] as $track) {
									$track_title = $filters_arr[$index]['title'];
									$track_title .= !empty($filters_arr[$index]['part']) ? ": ".$filters_arr[$index]['part'] : "";
									$track_title .= (empty($filters_arr[$index]['part']) && !empty($filters_arr[$index]['version'])) ? ": ".$filters_arr[$index]['version'] : "";
					
									$track_title = $this->replace_special_chars($track_title);
									$object_name = $this->replace_special_chars($track['title']);
					
									$track_title = $this->remove_special_chars($track_title, " \t’,':-_()[]„\"");
									$object_name = $this->remove_special_chars($track['title'], " \t’,':-_()[]„\"");
					
									similar_text(strtoupper($track_title), strtoupper($object_name), $perc);
									/*
									 echo PHP_EOL.$perc.PHP_EOL;
									echo strtoupper($track_title).PHP_EOL;
									echo strtoupper($object_name).PHP_EOL;
									*/
									if($perc > 90)
										$matched_tracks ++;
									else
										break;
										
									$index ++;
								}
									
							}
					
								
							if($matched_tracks == (int)$total_tracks) {
								//check release year
								if($release_year && $arr['rel_year']) {
									if((int)$release_year == (int)$arr['rel_year']) {
										$album_content_is_matched = true;
										$match_step = 40;
									}
									else
										$album_content_is_matched = false;
								}
								else {
									$album_content_is_matched = true;
									$match_step = 41;
								}
					
								//-----------------------------
									
								if($album_content_is_matched) {
									$hight_index = $arr['item_index'];
									$hight_perc = $arr['perc'];
									
									$result = $result_arr['data'][$hight_index];
									$result['tracks'] = $arr['tracks'];
									
									$result['match_percent'] = $hight_perc;
									$result['match_step'] = $match_step;
										
									$matched_album_content ++;
					
								}
							}
					
						}//foreach
					}

					
					//Check Labels (step 5)
					$label_found = false;
					if($matched_album_content != 1) {//if there is no matched album or more then one matched album
					
						$arr = $this->check_labels($sort_arr, $release_year);
						if(count($arr) == 0) {
							$arr = $this->check_labels($sort_arr, 0);
							$match_step = 51;
						}
						else
							$match_step = 50;
							
					
						if(count($arr) > 0) {
							$label_found = true;
								
							$result = $result_arr['data'][$arr['high_index']];
							$result['tracks'] = $sort_arr[$arr['sort_arr_index']]['tracks'];

							$result['match_percent'] = $arr['match_percent'];
							$result['match_step'] = $match_step;
						}
					
					}
					//------------------
					
					//Choose Album with highest similarity (step 6)
					if($matched_album_content != 1 && !$label_found) {
						$hight_index = $sort_arr[0]['item_index'];
						$hight_perc = $sort_arr[0]['perc'];
						
						$result = $result_arr['data'][$hight_index];
						$result['tracks'] = $sort_arr[0]['tracks'];
					
						$result['match_percent'] = $hight_perc;
						$result['match_step'] = 60;
					}					
					
				}
				
			break;
			
			
			//--------------
			case 'artist':
			//--------------
				if(!empty($result_arr['data'])) {
					foreach($result_arr['data'] as $artist) {

						$artist_name = !empty($artist['name']) ? $artist['name'] : "";
							
						$artist_name = $this->replace_special_chars($artist_name);
						$term1 = $this->replace_special_chars($term1);
							
						$artist_name = $this->remove_special_chars($artist_name, " \t");
						$term1 = $this->remove_special_chars($term1, " \t");
				
								
							if(strtoupper($artist_name) == strtoupper($term1)) {
								$result = $this->parse_item_info($artist);
								$result['match_percent'] = 100;
									
								break;
							}

							
					}
				}				
			break;
			
			
			//--------------
			case 'track':
			//--------------
				if(!empty($result_arr['data'])) {
					foreach($result_arr['data'] as $key => $object) {
				
						//track title
						$track_title1 = $term1; //title
						$track_title1 .= !empty($term3) ? ": ".$term3 : ""; //part
						$track_title1 .= (empty($term3) && !empty($term4)) ? ": ".$term4 : ""; //version
						
						$track_title2 = !empty($object['title']) ? $object['title'] : "";
						
						
						$track_title1 = $this->replace_special_chars($track_title1);
						$track_title1 = $this->remove_special_chars($track_title1, " \t’,':-_()[]„\"");
						
						$track_title2 = $this->replace_special_chars($track_title2);
						$track_title2 = $this->remove_special_chars($track_title2, " \t’,':-_()[]„\"");
						//------------------------------
						
						//artist name
						$artist_name1 = $term2;
						
						$artist_name2 = !empty($object['artist']['name']) ? $object['artist']['name'] : "";
						$artist_name2 = (!$artist_name2) ? (!empty($object['artist']['data'][0]['name']) ? $object['artist']['data'][0]['name'] : "") : $artist_name2;


						$artist_name1 = $this->replace_special_chars($artist_name1);
						$artist_name1 = $this->remove_special_chars($artist_name1, " \t’,':-_()[]„\"");
						
						$artist_name2 = $this->replace_special_chars($artist_name2);
						$artist_name2 = $this->remove_special_chars($artist_name2, " \t’,':-_()[]„\"");
						//-------------------------------
				
							if(strtoupper($artist_name1) == strtoupper($artist_name2)) {
								similar_text(strtoupper($track_title1), strtoupper($track_title2), $perc);
								
									if($perc >= 98) {
										$sort_arr[$index]['item_index'] = $key;
										$sort_arr[$index]['perc'] = $perc;
										$index ++;
									}
							}
					
					}
					//--------------------
					
					if(count($sort_arr) > 0) {
						$this->array_sort_by_column($sort_arr, 'perc', SORT_DESC);
						
						$hight_index = $sort_arr[0]['item_index'];
						$hight_perc = $sort_arr[0]['perc'];
						
						$result = $this->parse_item_info($result_arr['data'][$hight_index]);
						$result['match_percent'] = $hight_perc;
					}
				}				
			break;
		}
		
		return $result;
	}

	
	/**
	 * Lookup Album Tracks
	 *
	 * @param string $album_id
	 * @return string
	 */
	public function lookup_album_tracks($album_id) {
	
		if($album_id) {
			$url = $this->URL_API."album/".$album_id."/tracks";
			return file_get_contents($url);
		}
		else
			return "";
	}	
	

	/**
	 * Get Album Tracks
	 *
	 * @param string $album_id
	 * @return array
	 */
	public function get_album_tracks($album_id) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
	
		$result_json = $this->lookup_album_tracks($album_id);
	
			if(!$result_json) {
				$result[0] = "FAILED";
				return $result;
			}
	
		$result_arr = json_decode($result_json, true);
			
			if(empty($result_arr) || !count($result_arr)) {
				$result[0] = "FAILED";
				return $result;
			}

			
			if(!empty($result_arr['data'])) {
				$arr = $this->parse_album_tracks($album_id, $result_arr['data']);
				$result[0] = "FOUND";
				$result[1] = $arr;
			}

	
		return $result;
	}	
	
	
	/**
	 * 
	 * @param string $album_id
	 * @param array $tracks_arr
	 * @return array
	 */
	private function parse_album_tracks($album_id, $tracks_arr) {
		if($album_id) {
			foreach($tracks_arr as $key => $track) {
				$tracks_arr[$key]['album_id'] = $album_id;
			}
		}
		
		return $tracks_arr;
	}
	
	
	
	/**
	 * Remove Special Characters from String
	 *
	 * @param string $string
	 * @param string $chars
	 * @return string
	 */
	private function remove_special_chars($string, $chars) {
		$result = $string;
	
			for($i=0; $i<strlen($chars); $i++) {
				$result = str_replace($chars[$i], "", $result);
			}
	
		return $result;
	}
	
	
	/**
	 * Replace Special Characters
	 *
	 * @param string $string
	 * @return string
	 */
	private function replace_special_chars($string) {
	
		$result = $string;
		$search_char 	= array("é", "ä", "å", "&");
		$replace_char 	= array("e", "a", "a", "and");
	
			for($i=0; $i<count($search_char); $i++) {
				$result = str_replace($search_char[$i], $replace_char[$i], $result);
			}
	
		return $result;
	}
	
	
	/**
	 * Extract Release Year
	 *
	 * @param string $date
	 * @return string
	 */
	private function extract_rel_year($date) {
		$result = 0;
		$arr = explode("-", $date);
	
			if(!empty($arr[0]) && strlen($arr[0]) == 4) {
				$result = $arr[0];
			}
		
		return $result;
	}
	
	/**
	 * Sort array by given column/field name
	 *
	 * @param array $arr (reference of the array to be sorted)
	 * @param string $col
	 * @param string $dir
	 * @return void
	 */
	private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
	
			foreach ($arr as $key => $row) {
				$sort_col[$key] = $row[$col];
			}
	
		array_multisort($sort_col, $dir, $arr);
	}
	
	
	/**
	 * Check Label
	 *
	 * @param array $sort_arr
	 * @param int $release_year
	 * @return array
	 */
	private function check_labels($sort_arr, $release_year) {
		$result = array();
		$label_found = false;
	
		foreach($sort_arr as $arr_perc) {
			$hight_perc = $arr_perc['perc'];
	
			foreach($this->LABELS as $label) {
				foreach($sort_arr as $key=>$arr) {
					if(stripos($arr['label'], $label) !== false && $arr['perc'] == $hight_perc) {
	
						//check release year
						if($release_year && $arr['rel_year']) {
							if((int)$release_year == (int)$arr['rel_year'])
								$label_found = true;
							else
								$label_found = false;
						}
						else
							$label_found = true;
						//-----------------------------
	
						if($label_found) {
								
							$high_index = $arr['item_index'];
	
							$result['high_index'] = $high_index;
							$result['match_percent'] = $arr['perc'];
							$result['sort_arr_index'] = $key;
						}
					}
	
					if($label_found)
						break;
				}
				if($label_found)
					break;
			}
			if($label_found)
				break;
		}
	
		return $result;
	}
		
}
?>