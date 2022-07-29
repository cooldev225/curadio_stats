<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	CULINK (SPOTIFY)
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2015
 * @version		1.0
 */

// ------------------------------------------------------------------------


class cug__spotify {
	
	private $URL_API = "https://api.spotify.com/v1/";
	private $COPYRIGHTS = array();
	public $ERR_LOG_FILE = "error_log.txt";
	
	
	/**
	 * Constructor
	 */
	public function __construct() {
		//List of Copyrights of the Albums and Tracks, search result will be filtered by this list with presented ptiorities
		//if you don't want to use such of filter then just comment this section
		$this->COPYRIGHTS[0] = "Cugate Ltd";
		$this->COPYRIGHTS[1] = "Acewonder Ltd";
	}
	
	/**
	 * Call Request
	 * 
	 * @param string $url
	 * @return string
	 */
	private function call($url) {
		
		if($ch = curl_init()) {
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'User-Agent: Spotify API Console v0.1'));
			curl_setopt($ch, CURLOPT_ENCODING, "gzip");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		
			$result = curl_exec($ch);
			$error = curl_error($ch);
		
				if($error) {
					$this->write_in_file($this->ERR_LOG_FILE, @date("Y-m-d H:i:s")."\t".$error."\n\n");
				}
			//var_dump(curl_getinfo($ch));
		
			return $result;
		}
		else {
			$this->write_in_file($this->ERR_LOG_FILE, @date("Y-m-d H:i:s")."\t"."Cannot Initialize curl_init()"."\n\n");
			return "";
		}
				
	}
	
	
	/**
	 * Lookup Item Info by Item ID
	 *
	 * @param string $item
	 * @param string $item_id
	 * @param int $offset	Default is 0
	 * @param int $limit	Default is 50
	 * @return string
	 */
	public function lookup_item_info($item, $item_id, $offset=0, $limit=50) {
	
		$url = $this->URL_API.$item."/".$item_id;
		$url .= ($offset >= 0 && $limit > 0) ? "?offset=$offset&limit=$limit" : "";
		
		return $this->call($url);	
	}
	
	
	/**
	 * Check Response Data
	 * 
	 * @param string $result_json
	 * @param string $url
	 * @return array
	 */
	private function check_response($result_json, $url) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		
		
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
			elseif(!empty($result_arr['error'])) {
				//log error
				$data = @date("Y-m-d H:i:s")."\t";
				$data .= "Response Status"."\n";
				$data .= "Code: ".$result_arr['error']['status']."\n";
				$data .= "Msg: ".$result_arr['error']['message']."\n";
										
				$data .= "URL: ".$url."\n\n";
					
				$this->write_in_file($this->ERR_LOG_FILE, $data);
				//------------------
					
					if((int)$result_arr['error']['status'] != 400) {
						$result[0] = "FAILED";
						return $result;
					}
					else {
						$result[0] = "NOT FOUND";
						return $result;
					}
			}
			
		
		$result[0] = "FOUND";
		$result[1] = $result_arr;
		
		return $result;		
	}
	
	
	/**
	 * Get Item Info by Item ID
	 *
	 * @param string $item
	 * @param string $item_id
	 * @param int $offset	Default is 0
	 * @param int $limit	Default is 50
	 * @return array
	 */
	public function get_item_info($item, $item_id, $offset=0, $limit=50) {
		
		$url = $this->URL_API.$item."/".$item_id;
		$url .= ($offset >= 0 && $limit > 0) ? "?offset=$offset&limit=$limit" : "";
		
		$result_json = $this->lookup_item_info($item, $item_id, $offset, $limit);
		$result = $this->check_response($result_json, $url);
	
			if(count($result[1]) > 0) {
				$result[1] = $this->parse_item_info($item, $result[1]);
			}
			
		return $result;
	}


	
	/**
	 * Parse Item Info
	 *
	 * @param $item	STRING
	 * @param $arr	ARRAY
	 * @return ARRAY
	 */
	private function parse_item_info($item, $arr) {
		$result = array();
	
		switch($item) {
			//-----------------
			case "albums":
				//-----------------
				$result['type'] 		= $arr['type'];
				$result['album_type'] 	= $arr['album_type'];
				$result['id'] 			= $arr['id'];
				$result['name'] 		= $arr['name'];
				$result['upc'] 			= $arr['external_ids']['upc'];
				$result['release_date'] = $arr['release_date'];
				$result['release_year'] = $this->extract_rel_year($arr['release_date']);
				$result['url'] 			= $arr['external_urls']['spotify'];
				$result['copyrights'] 	= $arr['copyrights'];
				$result['artists'] 		= $arr['artists'];
				$result['images'] 		= $arr['images'];
					
				$result['tracks_total'] 		= $arr['tracks']['total'];
				$result['tracks_limit'] 		= $arr['tracks']['limit'];
				$result['tracks_next_url'] 		= $arr['tracks']['next'];
				$result['tracks_offset'] 		= $arr['tracks']['offset'];
				$result['tracks_previous_url'] 	= $arr['tracks']['previous'];
					
				foreach($arr['tracks']['items'] as $key => $track) {
					$result['tracks'][$key]['id'] 			= $track['id'];
					$result['tracks'][$key]['name'] 		= $track['name'];
					$result['tracks'][$key]['duration']		= $track['duration_ms'];
					$result['tracks'][$key]['track_num'] 	= $track['track_number'];
					$result['tracks'][$key]['disc_num'] 	= $track['disc_number'];
					$result['tracks'][$key]['link'] 		= $track['external_urls']['spotify'];
					$result['tracks'][$key]['album_id'] 	= $arr['id'];
					$result['tracks'][$key]['artists'] 		= $track['artists'];
				}
					
				break;
		}
	
		return $result;
	}	
	
	
	/**
	 * Search Item
	 *
	 * @param string $type
	 * @param string $query
	 * @param int $offset	Default is 0
	 * @param int $limit	Default is 50
	 * @return string
	 */
	public function search_item_info($type, $query, $offset=0, $limit=50) {
		
		$url = $this->URL_API."search?q=".urlencode($query)."&type=".$type;
		$url .= ($offset >= 0 && $limit > 0) ? "&offset=$offset&limit=$limit" : "";
		
		return $this->call($url);
	}
	
	
	/**
	 * Search Item
	 *
	 * @param string $type
	 * @param string $term1
	 * @param string $term2 (optional)
	 * @param string $term3 (optional)
	 * @param string $term4 (optional)
	 * @param array $filters_arr (additional filters array, optional)
	 * @param number $offset (default=0)
	 * @param number $limit (default=50)
	 * @return array
	 */
	public function search_item($type, $term1, $term2="", $term3="", $term4="", $filters_arr=array(), $offset=0, $limit=50) {
		
		$url = $this->URL_API."search?q=".urlencode($term1)."&type=".$type;
		$url .= ($offset >= 0 && $limit > 0) ? "&offset=$offset&limit=$limit" : "";
			
		$result_json = $this->search_item_info($type, $term1, $offset, $limit);
		$result = $this->check_response($result_json, $url);
		
		
			if(count($result[1]) > 0) {//print_r($result[1]['tracks']['items'][0]); return array();
				$arr = $this->parse_search_result($type, $result[1], $term1, $term2, $term3, $term4, $filters_arr);
		
					if(!empty($arr[0]) && $arr[0] == "FAILED") {
						$result[0] = "FAILED";
						return $result;
					}
					//---------------------
					if(count($arr) > 0) {
						$result[0] = "FOUND";
						$result[1] = $arr;
					}
					else {
						$result[0] = "NOT FOUND";
						$result[1] = array();
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
				$total_tracks1 = ($term3) ? $term3 : 0;
				$release_year1 = ($term4) ? $term4 : 0;
				
				
				foreach($result_arr['albums']['items'] as $key => $object) {
					$album_title = !empty($object['name']) ? $object['name'] : "";
					
					$album_title = $this->replace_special_chars($album_title);
					$term1 = $this->replace_special_chars($term1);
					
					$album_title = $this->remove_special_chars($album_title, " \t’:'-_()[]„\"");
					$term1 = $this->remove_special_chars($term1, " \t’:'-_()[]„\"");
					
					$album_id = $object['id'];
					$album_link = $object['external_urls']['spotify'];
					
					similar_text(strtoupper($term1), strtoupper($album_title), $perc);
					
					
					if($perc >= 88) {
						//get additional info
						$album_info = $this->get_item_info("albums", $album_id, $offset=0, $limit=1);
						
							if($album_info[0] == "FAILED") {
								$result[0] = "FAILED";
								return $result;
							}
							
						$album_artist 	= !empty($album_info[1]['artists'][0]['name']) ? $album_info[1]['artists'][0]['name'] : "";
						$total_tracks2 	= !empty($album_info[1]['tracks']['total']) ? $album_info[1]['tracks']['total'] : 0;
						$copyright		= !empty($album_info[1]['copyrights'][0]['text']) ? $album_info[1]['copyrights'][0]['text'] : "";
						$release_year2 	= !empty($album_info[1]['release_date']) ? $this->extract_rel_year($album_info[1]['release_date']) : 0;
						$next_url 		= $album_info[1]['tracks']['next'];
						//--------------------------
						
						
						$album_artist = $this->remove_special_chars($album_artist, " \t");
						$term2 = $this->remove_special_chars($term2, " \t");
												
						//echo $key." - ".$perc." - ".$album_id." - ".$album_artist." - ".$term2.PHP_EOL;
						
						if(strtoupper($album_artist) == strtoupper($term2)) {
							if((strtoupper($album_artist) != "VARIOUSARTISTS") || 
								(strtoupper($album_artist) == "VARIOUSARTISTS" && (int)$total_tracks1 == (int)$total_tracks2)) {
								$sort_arr[$index]['item_index'] 	= $key;
								$sort_arr[$index]['perc'] 			= $perc;
								$sort_arr[$index]['id'] 			= $album_id;
								$sort_arr[$index]['total_tracks'] 	= $total_tracks2;
								$sort_arr[$index]['copyright'] 		= $copyright;
								$sort_arr[$index]['rel_year'] 		= $release_year2;
								$sort_arr[$index]['link']	 		= $album_link;
								
								//collect all tracks
								$album_tracks = $album_info[1]['tracks']['items'];
								$track_index = 0;
								
								for($i=1; $i<=ceil(($total_tracks2 / 50)); $i++) {//each result contains maximum 50 tracks
									
										foreach($album_tracks as $track) {
											$sort_arr[$index]['tracks'][$track_index]['id'] 		= $track['id'];
											$sort_arr[$index]['tracks'][$track_index]['title'] 		= $track['name'];
											$sort_arr[$index]['tracks'][$track_index]['track_num'] 	= $track['track_number'];
											$sort_arr[$index]['tracks'][$track_index]['disc_num'] 	= $track['disc_number'];
											$sort_arr[$index]['tracks'][$track_index]['link'] 		= $track['external_urls']['spotify'];
											$sort_arr[$index]['tracks'][$track_index]['album_id'] 	= $sort_arr[$index]['id'];
											
											$track_index ++;
										}
										
									//echo PHP_EOL.$next_url.PHP_EOL;
									if($next_url) {
										$url_arr = $this->parse_next_url($next_url);
										
										//get next 50 tracks
										$temp_arr = $this->get_item_info("albums/".$sort_arr[$index]['id'], "tracks", $url_arr['offset'], $url_arr['limit']);
										
											if($temp_arr[0] == "FAILED") {
												$result[0] = "FAILED";
												return $result;
											}

										$album_tracks = $temp_arr[1]['items'];
										$next_url = $temp_arr[1]['next'];
									}	
								}
								//------------------- 
								
								$index ++;
							}
						}

					}
					
				}
				
				//print_r($sort_arr);
				//return $result;
				
				//---------------
				if(count($sort_arr) > 0) {
					$this->array_sort_by_column($sort_arr, 'perc', SORT_DESC);
					//print_r($sort_arr);
					
					//check album content (step 4)
					$album_content_is_matched = false;
					$matched_album_content = 0;
					
					if(count($sort_arr) == 1 && (int)$total_tracks1 != (int)$sort_arr[0]['total_tracks'] && $sort_arr[0]['perc'] == 100 && strtoupper($album_artist) != "VARIOUSARTISTS") {
						//check release year
						if($release_year1 && $sort_arr[0]['rel_year']) {
							if((int)$release_year1 == (int)$sort_arr[0]['rel_year']) {
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
							$result = $sort_arr[0];
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
					
								
							if($matched_tracks == (int)$total_tracks1) {
								//check release year
								if($release_year1 && $arr['rel_year']) {
									if((int)$release_year1 == (int)$arr['rel_year']) {
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
									$result = $arr;
									$result['match_step'] = $match_step;
										
									$matched_album_content ++;
								}
							}
					
						}//foreach
					}

					
					//Check Copyrights (step 5)
					$copyright_found = false;
					if($matched_album_content != 1) {//if there is no matched album or more then one matched album
					
						$arr = $this->check_copyrights($sort_arr, $release_year1);
							if(count($arr) == 0) {
								$arr = $this->check_copyrights($sort_arr, 0);
								$match_step = 51;
							}
							else
								$match_step = 50;
							
					
						if(count($arr) > 0) {
							$copyright_found = true;
								
							$result = $sort_arr[ $arr['sort_arr_index'] ];
							$result['match_step'] = $match_step;
						}
					
					}
					//------------------
					
					//Choose Album with highest similarity (step 6)
					if($matched_album_content != 1 && !$copyright_found) {
						$result = $sort_arr[0];
						$result['match_step'] = 60;
					}					
					
				}
				
			break;
			
			
			//--------------
			case 'artist':
			//--------------
				if(!empty($result_arr['artists']['items'])) {
					foreach($result_arr['artists']['items'] as $artist) {

						$artist_name = !empty($artist['name']) ? $artist['name'] : "";
							
						$artist_name = $this->replace_special_chars($artist_name);
						$term1 = $this->replace_special_chars($term1);
							
						$artist_name = $this->remove_special_chars($artist_name, " \t");
						$term1 = $this->remove_special_chars($term1, " \t");
				
								
							if(strtoupper($artist_name) == strtoupper($term1)) {
								$result['id'] = $artist['id'];
								$result['name'] = $artist['name'];
								$result['url'] = $artist['external_urls']['spotify'];
								$result['perc'] = 100;
									
								break;
							}

							
					}
				}				
			break;
			
			
			//--------------
			case 'track':
			//--------------
				if(!empty($result_arr['tracks']['items'])) {
					foreach($result_arr['tracks']['items'] as $key => $object) {
						
						$index = 0;
				
						//track title
						$track_title1 = $term1; //title
						$track_title1 .= !empty($term3) ? ": ".$term3 : ""; //part
						$track_title1 .= (empty($term3) && !empty($term4)) ? ": ".$term4 : ""; //version
						
						$track_title2 = !empty($object['name']) ? $object['name'] : "";
						
						
						$track_title1 = $this->replace_special_chars($track_title1);
						$track_title1 = $this->remove_special_chars($track_title1, " \t’,':-_()[]„\"");
						
						$track_title2 = $this->replace_special_chars($track_title2);
						$track_title2 = $this->remove_special_chars($track_title2, " \t’,':-_()[]„\"");
						//------------------------------
						
						//artist name
						$artist_name1 = $term2;
						$artist_name1 = $this->replace_special_chars($artist_name1);
						$artist_name1 = $this->remove_special_chars($artist_name1, " \t’,':-_()[]„\"");
						
						$artist_name2 = array();
							foreach($object['artists'] as $key1 => $artist) {
								$artist_name2[$key1] = $artist['name'];
								$artist_name2[$key1] = $this->replace_special_chars($artist_name2[$key1]);
								$artist_name2[$key1] = $this->remove_special_chars($artist_name2[$key1], " \t’,':-_()[]„\"");
								
									if(strtoupper($artist_name1) == strtoupper($artist_name2[$key1])) {
										similar_text(strtoupper($track_title1), strtoupper($track_title2), $perc);
									
											if($perc >= 98) {
												$sort_arr[$index]['item_index'] = $key;
												$sort_arr[$index]['perc'] = $perc;
												$index ++;
												
												break;
											}
									}
							}
					
					}
					//--------------------
					
					if(count($sort_arr) > 0) {
						$this->array_sort_by_column($sort_arr, 'perc', SORT_DESC);
						
						$hight_index = $sort_arr[0]['item_index'];
						$hight_perc = $sort_arr[0]['perc'];
						
						$result = $result_arr['tracks']['items'][$hight_index];
						$result['perc'] = $hight_perc;
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
	 * Check Copyrights
	 *
	 * @param array $sort_arr
	 * @param int $release_year
	 * @return array
	 */
	private function check_copyrights($sort_arr, $release_year) {
		$result = array();
		$found = false;
	
		foreach($sort_arr as $arr_perc) {
			$hight_perc = $arr_perc['perc'];
	
			foreach($this->COPYRIGHTS as $copyright) {
				foreach($sort_arr as $key=>$arr) {
					if(stripos($arr['copyright'], $copyright) !== false && $arr['perc'] == $hight_perc) {
	
						//check release year
						if($release_year && $arr['rel_year']) {
							if((int)$release_year == (int)$arr['rel_year'])
								$found = true;
							else
								$found = false;
						}
						else
							$found = true;
						//-----------------------------
	
						if($found) {
								
							$high_index = $arr['item_index'];
	
							$result['high_index'] = $high_index;
							$result['match_percent'] = $arr['perc'];
							$result['sort_arr_index'] = $key;
						}
					}
	
					if($found)
						break;
				}
				if($found)
					break;
			}
			if($found)
				break;
		}
	
		return $result;
	}
	
	
	/**
	 * 
	 * @param string $file
	 * @param string $data
	 * @param string $mode	Default is 'a'
	 */
	public function write_in_file($file, $data, $mode="a") {
		if($f = fopen($file, $mode)) {
			fwrite($f, $data, strlen($data));
			fclose($f);
		}
	}
	
	
	/**
	 * Parse URL (extract 'offset' and 'limit' param values)
	 * 
	 * @param $url	STRING
	 * @return ARRAY
	 */
	private function parse_next_url($url) {
		$result = array();
		$result['offset'] = 0;
		$result['limit'] = 0;
		
		$arr = parse_url($url);
		
			if(!empty($arr['query'])) {
				$query_arr = array();
				parse_str($arr['query'], $query_arr);
				
				$result['offset'] = !empty($query_arr['offset']) ? $query_arr['offset'] : 0;
				$result['limit']  = !empty($query_arr['limit']) ? $query_arr['limit'] : 0;
			}
			
		return $result;	
	}
		
}
?>