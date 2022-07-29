<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	CULINK (ITUNES)
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2015
 * @version		1.0
 */

// ------------------------------------------------------------------------

class cug__itunes {
	
	//(G)lobal (V)ariables
	private $GV = array();
	public $COUNTRY = array();
	private $URL_SEARCH = "http://ax.phobos.apple.com.edgesuite.net/WebObjects/MZStoreServices.woa/wa/wsSearch";
	private $URL_ITEM_INFO = "https://itunes.apple.com/lookup";
	
	private $COPYRIGHTS = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		
		//List of Copyrights of the Albums and Tracks, search result will be filtered by this list with presented ptiorities
		//if you don't want to use such of filter then just comment this section
		$this->COPYRIGHTS[0] = "Cugate ltd";
		$this->COPYRIGHTS[1] = "Acewonder ltd";
		$this->COPYRIGHTS[2] = "Sinostate";
		$this->COPYRIGHTS[3] = "Unlimited Media";
		$this->COPYRIGHTS[4] = "HDC - High Definition Classical (Distribution by X5MG)";
		$this->COPYRIGHTS[5] = "X5 Music Group";
		
		//The media type you want to search for in the iTunes Store
		$this->GV['media'][0]['type'] = "movie";
		$this->GV['media'][1]['type'] = "podcast";
		$this->GV['media'][2]['type'] = "music";
		$this->GV['media'][3]['type'] = "musicVideo";
		$this->GV['media'][4]['type'] = "audiobook";
		$this->GV['media'][5]['type'] = "shortFilm";
		$this->GV['media'][6]['type'] = "tvShow";
		$this->GV['media'][7]['type'] = "all";

		//The type of results you want the iTunes Store to return, relative to the specified media type
		$this->GV['media'][0]['entity'][0] = "movieArtist";
		$this->GV['media'][0]['entity'][1] = "movie";
		
		$this->GV['media'][1]['entity'][0] = "podcastAuthor";
		$this->GV['media'][1]['entity'][1] = "podcast";
		
		$this->GV['media'][2]['entity'][0] = "musicArtist";
		$this->GV['media'][2]['entity'][1] = "musicTrack";
		$this->GV['media'][2]['entity'][2] = "album";
		$this->GV['media'][2]['entity'][3] = "musicVideo";
		$this->GV['media'][2]['entity'][4] = "mix";
		
		$this->GV['media'][2]['attr'][0] = "artistTerm";
		$this->GV['media'][2]['attr'][1] = "songTerm";
		$this->GV['media'][2]['attr'][2] = "albumTerm";	
		
		$this->GV['media'][3]['entity'][0] = "musicArtist";
		$this->GV['media'][3]['entity'][1] = "musicVideo";
		
		$this->GV['media'][4]['entity'][0] = "audiobookAuthor";
		$this->GV['media'][4]['entity'][1] = "audiobook";
		
		$this->GV['media'][5]['entity'][0] = "shortFilmArtist";
		$this->GV['media'][5]['entity'][1] = "shortFilm";
		
		$this->GV['media'][6]['entity'][0] = "tvEpisode";
		$this->GV['media'][6]['entity'][1] = "tvSeason";
		
		$this->GV['media'][7]['entity'][0] = "movie";
		$this->GV['media'][7]['entity'][1] = "album";
		$this->GV['media'][7]['entity'][2] = "allArtist";
		$this->GV['media'][7]['entity'][3] = "podcast";
		$this->GV['media'][7]['entity'][4] = "musicVideo";
		$this->GV['media'][7]['entity'][5] = "mix";
		$this->GV['media'][7]['entity'][6] = "audiobook";
		$this->GV['media'][7]['entity'][7] = "tvSeason";
		$this->GV['media'][7]['entity'][8] = "allTrack";

		
		//Country
		$this->COUNTRY[0]['code'] = "US"; //USA
		$this->COUNTRY[0]['id'] = 198;
		$this->COUNTRY[1]['code'] = "CA"; //CANADA
		$this->COUNTRY[1]['id'] = 34;
		$this->COUNTRY[2]['code'] = "BR"; //BRAZIL
		$this->COUNTRY[2]['id'] = 27;
		$this->COUNTRY[3]['code'] = "AR"; //ARGENTINA
		$this->COUNTRY[3]['id'] = 8;
		$this->COUNTRY[4]['code'] = "MX"; //MEXICO
		$this->COUNTRY[4]['id'] = 124;
		
		$this->COUNTRY[5]['code'] = "DE"; //GERMANY
		$this->COUNTRY[5]['id'] = 70;
		$this->COUNTRY[6]['code'] = "FR"; //FRANCE
		$this->COUNTRY[6]['id'] = 65;
		$this->COUNTRY[7]['code'] = "ES"; //SPAIN
		$this->COUNTRY[7]['id'] = 173;
		$this->COUNTRY[8]['code'] = "GB"; //UNITED KINGDOM
		$this->COUNTRY[8]['id'] = 197;
		$this->COUNTRY[9]['code'] = "SE"; //SWEDEN
		$this->COUNTRY[9]['id'] = 181;
		$this->COUNTRY[10]['code'] = "IT"; //ITALY
		$this->COUNTRY[10]['id'] = 92;
		$this->COUNTRY[11]['code'] = "GR"; //GREECE
		$this->COUNTRY[11]['id'] = 72;
		$this->COUNTRY[12]['code'] = "PL"; //POLAND
		$this->COUNTRY[12]['id'] = 152;
		$this->COUNTRY[13]['code'] = "PT"; //PORTUGAL
		$this->COUNTRY[13]['id'] = 153;
		$this->COUNTRY[14]['code'] = "NL"; //NETHERLANDS
		$this->COUNTRY[14]['id'] = 135;
		$this->COUNTRY[15]['code'] = "UA"; //UKRAINE
		$this->COUNTRY[15]['id'] = 195;
		$this->COUNTRY[16]['code'] = "TR"; //TURKEY
		$this->COUNTRY[16]['id'] = 192;
		$this->COUNTRY[17]['code'] = "LV"; //LATVIA
		$this->COUNTRY[17]['id'] = 104;
		$this->COUNTRY[18]['code'] = "LT"; //LITHUANIA
		$this->COUNTRY[18]['id'] = 110;
		$this->COUNTRY[19]['code'] = "NO"; //NORWAY
		$this->COUNTRY[19]['id'] = 143;
		$this->COUNTRY[20]['code'] = "CH"; //SWITZERLAND
		$this->COUNTRY[20]['id'] = 182;
		$this->COUNTRY[21]['code'] = "RU"; //RUSSIA
		$this->COUNTRY[21]['id'] = 157;
		
		$this->COUNTRY[22]['code'] = "IN"; //INDIA
		$this->COUNTRY[22]['id'] = 85;
		$this->COUNTRY[23]['code'] = "IL"; //ISRAEL
		$this->COUNTRY[23]['id'] = 91;
		$this->COUNTRY[24]['code'] = "CN"; //CHINA
		$this->COUNTRY[24]['id'] = 41;
		$this->COUNTRY[25]['code'] = "JP"; //JAPAN
		$this->COUNTRY[25]['id'] = 94;		
	}
	
	
	
	/**
	 * Lookup Item Info by Item ID
	 * 
	 * @param string $item_id
	 * @param string $country_code (optional)
	 * @param string $entity (optional)
	 * @param int $limit (default=1)
	 * @return string
	 */
	public function lookup_item_info($item_id, $country_code="", $entity="", $limit=1) {
		
		$url = $this->URL_ITEM_INFO."?id=".$item_id;
		$url .= ($country_code) ? "&country=".$country_code : "";
		$url .= ($entity) ? "&entity=".$entity : "";
		$url .= ($limit) ? "&limit=".$limit : "";
				
		return file_get_contents($url);
	}
	
	
	/**
	 * Get Item Info by Item ID
	 * 
	 * @param string $item_id
	 * @param string $country_code (optional)
	 * @return array
	 */
	public function get_item_info($item_id, $country_code="") {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		
		$result_json = $this->lookup_item_info($item_id, $country_code);
		
			if(!$result_json) {
				$result[0] = "FAILED";
				return $result;
			}
		
		$result_arr = json_decode($result_json, true);
		
			if(!count($result_arr)) {
				$result[0] = "FAILED";
				return $result;
			}
			
			//---------------------
			if(!empty($result_arr['results'][0])) {
				$arr = $this->parse_item_info($result_arr['results'][0]);
				
					if(count($arr) > 0) {
						$result[0] = "FOUND";
						$result[1] = $arr;
					}
			}
		
		return $result;		
	}
	
	
	/**
	 * Parse Item Info
	 * 
	 * @param array $result_arr
	 * @return array
	 */
	private function parse_item_info($result_arr) {
		$result = array();
		$OBJECT_TYPE = "";
		$tags = array();
		
		if(!empty($result_arr['wrapperType'])) {
			//---------------------------
			if($result_arr['wrapperType'] == "collection" && ($result_arr['collectionType'] == "Album" || $result_arr['collectionType'] == "Compilation")) {
				$OBJECT_TYPE = "ALBUM";
			}
			elseif($result_arr['wrapperType'] == "track" && $result_arr['kind'] == "song") {
				$OBJECT_TYPE = "TRACK";
			}
			elseif($result_arr['wrapperType'] == "artist") {
				$OBJECT_TYPE = "ARTIST";
			}			
			//---------------------------
		
			switch($OBJECT_TYPE) {
				//---------------
				case "ALBUM":
				//---------------
					$tags['object_type'] 		= "collectionType";
					$tags['object_id'] 			= "collectionId";
					$tags['object_name'] 		= "collectionName";					
					$tags['object_tracks_num'] 	= "trackCount";
					$tags['object_url'] 		= "collectionViewUrl";
					$tags['object_copyright'] 	= "copyright";
					$tags['object_rel_date'] 	= "releaseDate";
					$tags['object_genre'] 		= "primaryGenreName";
					$tags['object_country'] 	= "country";
					$tags['object_genre'] 		= "primaryGenreName";
					$tags['object_img60'] 		= "artworkUrl60";
					$tags['object_img100'] 		= "artworkUrl100";
					$tags['object_price'] 		= "collectionPrice";
					$tags['object_currency'] 	= "currency";
					
					$tags['artist_id'] 			= "artistId";
					$tags['artist_name'] 		= "artistName";
					
				break;
						
				//---------------
				case "TRACK":
				//---------------
					$tags['object_type'] 		= "kind";
					$tags['object_id'] 			= "trackId";
					$tags['object_name'] 		= "trackName";
					$tags['object_track_num'] 	= "trackNumber";
					$tags['object_disc_num'] 	= "discNumber";
					$tags['object_time'] 		= "trackTimeMillis";
					$tags['object_url'] 		= "trackViewUrl";
					$tags['object_prev_url'] 	= "previewUrl";
					$tags['object_radio_url'] 	= "radioStationUrl";
					$tags['object_country'] 	= "country";
					$tags['object_genre'] 		= "primaryGenreName";
					$tags['object_price'] 		= "trackPrice";
					$tags['object_currency'] 	= "currency";
					
					$tags['artist_id'] 			= "artistId";
					$tags['artist_name'] 		= "artistName";
					$tags['artist_url'] 		= "artistViewUrl";
					$tags['album_id'] 			= "collectionId";
					
					$tags['album_name'] 		= "collectionName";
					$tags['album_tracks_num'] 	= "trackCount";
					$tags['object_img60'] 		= "artworkUrl60";
					$tags['object_img100'] 		= "artworkUrl100";					
					
				break;
				
				//---------------
				case "ARTIST":
				//---------------
					$tags['object_type'] 		= "wrapperType";
					$tags['object_id'] 			= "artistId";
					$tags['object_name'] 		= "artistName";
					$tags['object_url'] 		= "artistLinkUrl";
					$tags['object_radio_url'] 	= "radioStationUrl";
						
				break;				
			}
			//---------------------------
		
			if(count($tags) > 0) {
				//---------------------------
				foreach($tags as $key => $val) {
					$result[$key] = !empty($result_arr[$val]) ? trim((string)$result_arr[$val]) : "";
				}
				//---------------------------

				$result['object_url_short'] = $this->get_short_url($result['object_url']);
				//$result['match_percent'] = 100;
				
				//detect country
				$country = $this->extract_country_from_url($result['object_url']);
					if(count($country) > 0) {
						$result['country_code'] = $country['country_code'];
						$result['country_id'] = $country['country_id'];
					}
				
				
				//change 'formatted price'
				if(!empty($result['object_price'])) {
					if(strlen($result['object_price']) > 0 && strlen($result['object_currency']) > 0) {
			
						if((float)$result['object_price'] > 0) {
							if($result['object_currency'] == 'GBP')
								$result['object_price_formatted'] = "£".$result['object_price'];
							elseif($result['object_currency'] == 'USD')
								$result['object_price_formatted'] = "$".$result['object_price'];
							elseif($result['object_currency'] == 'CAD')
								$result['object_price_formatted'] = "CAD $".$result['object_price'];
							else
								$result['object_price_formatted'] = $result['object_currency']." ".$result['object_price'];
								
								
							$result['object_price'] = (float)$result['object_price'] * 100;
						}
					}
				}
				//--------------------------
					
			}//if(count($tags) > 0)
		}
		
		return $result;		
	}
	
	
	/**
	 * Lookup Item ID in all countries
	 * 
	 * @param string $item_id
	 * @param int $request_interval (milliseconds, default=0)
	 * @return array
	 */
	public function lookup_item_id($item_id, $request_interval=0) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();		
		$index = 0;
		
		if($item_id) {
			foreach($this->COUNTRY as $country) {
				$arr = $this->get_item_info($item_id, $country['code']);
				
				if($arr[0] == "FAILED") {
					$result[0] = "FAILED";
					break;
				}
				
				if($arr[0] == "FOUND" && count($arr[1]) > 0) {
					$result[0] = "FOUND";
					$result[1][$index] = $arr[1];
					$index ++;
				}
				
					if($request_interval > 0)
						usleep($request_interval);
			}
		}
		
		return $result;
	}
	

	/**
	 * Lookup Item URL in all countries
	 *
	 * @param string $url
	 * @param int $request_interval (milliseconds, default=0)
	 * @return array
	 */	
	public function lookup_item_url($url, $request_interval=0) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		$search_item = "";
		$index = 0;
	
		if($url) {
			$item_arr = $this->parse_item_url($url);
			$item_country_code = $item_arr['country_code'];
			
			if(count($item_arr) > 0 && !empty($item_arr['object_type'])) {
				switch(strtoupper($item_arr['object_type'])) {
					//------------------
					case 'ALBUM':
					//------------------
						$search_item = "ALBUM";
						$term1 = !empty($item_arr['object_name']) ? $item_arr['object_name'] : "";
						$term2 = !empty($item_arr['artist_name']) ? $item_arr['artist_name'] : "";
						$term3 = !empty($item_arr['object_tracks_num']) ? $item_arr['object_tracks_num'] : "";
					break;

					//------------------
					case 'SONG':
					//------------------
						$search_item = "TRACK";
						$term1 = !empty($item_arr['object_name']) ? $item_arr['object_name'] : "";
						$term2 = !empty($item_arr['artist_name']) ? $item_arr['artist_name'] : "";
						$term3 = "";
					break;

					//------------------
					case 'ARTIST':
					//------------------
						$search_item = "ARTIST";
						$term1 = !empty($item_arr['object_name']) ? $item_arr['object_name'] : "";
						$term2 = "";
						$term3 = "";					
					break;	
				}
				
				
				if($search_item) {
					//first search item by id
					$items_arr = $this->lookup_item_id($item_arr['object_id'], $request_interval);
						
						if($items_arr[0] == "FOUND" && count($items_arr[1]) > 0) {
							foreach($items_arr[1] as $arr) {
								$result[0] = "FOUND";
								$result[1][$index] = $arr;
								$index ++;
							}
						}
					
					
					//now search by names if some item was not found in some countries	
					foreach($this->COUNTRY as $country) {
						$item_already_found = false;
						
							foreach($result[1] as $arr) {
								if($arr['country_code'] == $country['code']) {
									$item_already_found = true;
									break;
								}
							}
						
							
						if(!$item_already_found) {
	 						$arr = $this->search_item($search_item, $term1, $term2, $term3, 200, $country['code']);
						
							if($arr[0] == "FAILED") {
								$result[0] = "FAILED";
								break;
							}
						
							if($arr[0] == "FOUND" && count($arr[1]) > 0) {
								$result[0] = "FOUND";
								$result[1][$index] = $arr[1][0];
								$index ++;
							}
						
								if($request_interval > 0)
									usleep($request_interval);
						}
					}//foreach					
				}//if($search_item)
				
			}
			

		}
	
		return $result;
	}	
	

	
	/**
	 * Get Short URL of the Item
	 * 
	 * @param string $url
	 * @return string
	 */
	public function get_short_url($url) {
		$result = "";
		$arr = parse_url($url);
		
			if(!empty($arr['path'])) {
				$temp = explode("/", $arr['path']);
				
				$result = !empty($arr['scheme']) ? $arr['scheme']."://" : "";
				$result .= !empty($arr['host']) ? $arr['host']."/" : "";
				$result .= !empty($temp[1]) ? $temp[1]."/" : "";
				$result .= !empty($temp[2]) ? $temp[2]."/" : "";
				$result .= $temp[count($temp)-1];
				
					if(!empty($arr['query'])) {
						$temp = explode("&", $arr['query']);

							if(!empty($temp[0]) && substr($temp[0], 0, 2) == "i=")
								$result .= "?".$temp[0];
							elseif(substr($arr['query'], 0, 2) == "i=")
								$result .= "?".$arr['query'];
					}
			}
		
		return $result;
	}
	
	
	/**
	 * Parse Item URL
	 * 
	 * @param string $url
	 * @return array
	 */
	public function parse_item_url($url) {
		$item_id = "";
		$result = array();
	
		if($url) {
			$arr = parse_url($url);
			$country = $this->extract_country_from_url($url);
			
			if(!empty($arr['path'])) {
				$temp = explode("/", $arr['path']);
				
				if(count($temp) > 0) {
					$temp_id = $temp[count($temp)-1];
					
						if(substr($temp_id, 0, 2) == "id")
							$item_id = substr($temp_id, 2, strlen($temp_id)-2);
				
						if(!empty($arr['query'])) {
							$temp2 = explode("&", $arr['query']);
				
							if(!empty($temp2[0]) && substr($temp2[0], 0, 2) == "i=")
								$item_id = substr($temp2[0], 2, strlen($temp2[0])-2);
							elseif(substr($arr['query'], 0, 2) == "i=")
								$item_id = substr($arr['query'], 2, strlen($arr['query'])-2);
						}
						
							if($item_id) {
								$temp_arr = $this->get_item_info($item_id, $country['country_code']);
									if($temp_arr[0] == "FOUND" && count($temp_arr[1]) > 0) {
										$result = $temp_arr[1];
										
											if(count($country) > 0) {
												$result['country_code'] = $country['country_code'];
												$result['country_id'] = $country['country_id'];
											}										
									}
							}
				}
			}
		}
	
		return $result;
	}

	
	/**
	 * Exstract Country from URL
	 * 
	 * @param string $url
	 * @return array
	 */
	public function extract_country_from_url($url) {
		$result = array();
		
		if($url) {
			$arr = parse_url($url);
				if(!empty($arr['path'])) {
					$temp = explode("/", $arr['path']);
						if(!empty($temp[1]) && strlen($temp[1]) == 2) {
							$result['country_code'] = strtoupper($temp[1]);
							$result['country_id'] = $this->get_country_id($temp[1]);
						}
				}
		}
		
		return $result;
	}
	
	/**
	 * Search Item by Term
	 * 
	 * @param string $media
	 * @param string $term
	 * @param string $entity
	 * @param string $attribute
	 * @param string $country_code (optional)
	 * @param int $limit (default=10)
	 * @return string
	 */	
	public function search_item_info($media, $term, $entity, $attribute, $country_code="", $limit=10) {
		$url = $this->URL_SEARCH."?media=".$media."&limit=".$limit."&term=".urlencode($term)."&entity=".$entity."&attribute=".$attribute;
		$url .= ($country_code) ? "&country=".strtoupper($country_code) : "";
		
		return file_get_contents($url);
	}
	
	
	/**
	 * Search Item
	 * 
	 * @param string $search_item
	 * @param string $term1
	 * @param string $term2 (optional)
	 * @param string $term3 (optional)
	 * @param string $term4 (optional)
	 * @param number $limit (default=10)
	 * @param string $country_code (optional)
	 * @param array $filters_arr (additional filters array, optional)	 
	 * @return array
	 */	
	public function search_item($search_item, $term1, $term2="", $term3="", $term4="", $limit=10, $country_code="", $filters_arr=array()) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		$index = 0;
		
		switch($search_item) {
			//----------------
			case "ALBUM":
			//----------------
				$media 		= $this->GV['media'][2]['type']; //music
				$entity 	= $this->GV['media'][2]['entity'][2]; //album
				$attribute 	= $this->GV['media'][2]['attr'][2]; //albumTerm
			break;

			//----------------
			case "TRACK":
			//----------------	
				$media 		= $this->GV['media'][2]['type']; //music
				$entity 	= $this->GV['media'][2]['entity'][1]; //musicTrack
				$attribute 	= $this->GV['media'][2]['attr'][1]; //songTerm
			break;
			
			//----------------
			case "ARTIST":
			//----------------
				$media 		= $this->GV['media'][2]['type']; //music
				$entity 	= $this->GV['media'][2]['entity'][0]; //musicArtist
				$attribute 	= $this->GV['media'][2]['attr'][0]; //artistTerm
			break;			

			
			default:
				$media 		= "";
				$entity 	= "";
				$attribute 	= "";
			break;				
		}
		
		//---------------------------------
		if($media && $entity && $attribute) {
			foreach($this->COUNTRY as $country) {
				if($country_code) {
					if(strtoupper($country_code) == $country['code']) {
						$result_json = $this->search_item_info($media, $term1, $entity, $attribute, $country_code, $limit);
							if(!$result_json) {
								$result[0] = "FAILED";
							}
							else {
								$arr = json_decode($result_json, true);
									if(!count($arr)) {
										$result[0] = "FAILED";
									}
									else {
										$arr2 = $this->parse_search_result($search_item, $arr, $term1, $term2, $term3, $term4, $filters_arr);
											if(count($arr2) > 0) {
												$result[0] = "FOUND";
												$result[1][0] = $arr2;
											}										
									}								
							}						
						break;
					}
				}
				elseif(count($this->COUNTRY) > count($result[1])) {
					
					//check if item already found in current country
					$item_already_found = false;
						if(!empty($result[1][0]['country_code'])) {
							foreach($result[1] as $r) {
								if($r['country_code'] == $country['code']) {
									$item_already_found = true;
									break;
								}
							}
						}
					//---------------------------
					
					if(!$item_already_found) {					
						$result_json = $this->search_item_info($media, $term1, $entity, $attribute, $country['code'], $limit);
	
							if(!$result_json) {
								$result[0] = "FAILED";
								break;
							}
							else {
								$arr = json_decode($result_json, true);
									if(!count($arr)) {
										$result[0] = "FAILED";
										break;
									}
									else {
										//echo PHP_EOL."Start Search: ".$country['code'].PHP_EOL;
										$arr2 = $this->parse_search_result($search_item, $arr, $term1, $term2, $term3, $term4, $filters_arr);
											if(count($arr2) > 0) {
												$result[0] = "FOUND";
												$result[1][$index] = $arr2;
												$index ++;
												
													//lookup founded item id in all countries
													if($arr2['match_percent'] >= 99) {
														$arr3 = $this->lookup_item_id($arr2['object_id']);
															if($arr3[0] == "FOUND" && count($arr3[1]) > 1) {
																foreach($arr3[1] as $item_info) {
																	$link_already_found = false;
																	
																		foreach($result[1] as $r) {
																			if($item_info['country_code'] == $r['country_code']) {
																				$link_already_found = true;
																				break;
																			}
																		}
																		
																		if(!$link_already_found) {
																			$result[1][$index] = $item_info;
																			$result[1][$index]['match_percent'] = $arr2['match_percent'];
																			
																				if(!empty($arr2['match_step']))
																					$result[1][$index]['match_step'] = $arr2['match_step'];
																			
																			$index ++;
																		}
																}
															}
													}
													//--------------------------------
												
											}
									}//else
							}//else											
						
					}//if(!$item_already_found)
				}//elseif(count($this->COUNTRY) > count($result[1]))
				else 
					break;
			}
		}
		
		return $result;
	}
	
	
	/**
	 * Get Tracks of Album
	 * 
	 * @param string $item_id (id of the Album)
	 * @param string $country_code (optional)
	 * @param number $limit (default=200)
	 * @return array
	 */
	public function get_album_tracks($item_id, $country_code="", $limit=200) {
		$result = array();
		$result[0] = "NOT FOUND";
		$result[1] = array();
		
		$result_json = $this->lookup_item_info($item_id, $country_code, "song", $limit);
		
		
			if(!$result_json) {
				$result[0] = "FAILED";
				return $result;
			}
		
		$result_arr = json_decode($result_json, true);
		
			if(!count($result_arr)) {
				$result[0] = "FAILED";
				return $result;
			}
		
		
			if(!empty($result_arr['results']) && count($result_arr['results']) > 1) {
				$index = 0;
				$result[0] = "FOUND";
				
					for($i=1; $i<count($result_arr['results']); $i++) {//strat from 1, because first (0) element contains Album info, we need just Tracks
						$temp = $this->parse_item_info($result_arr['results'][$i]);
							if(count($temp) > 0) {
								$result[1][$index] = $temp;
								$index ++;
							}
					}
			}

		return $result;		
	}
	
	
	
	/**
	 * Parse Search Result
	 * 
	 * @param string $search_item
	 * @param array $result_arr
	 * @param string $term1
	 * @param string $term2 (optional)
	 * @param string $term3 (optional)
	 * @param string $term4 (optional)
	 * @param array $filters_arr (additional filters array, optional)
	 * @return array
	 */
	private function parse_search_result($search_item, $result_arr, $term1, $term2="", $term3="", $term4="", $filters_arr=array()) {
		$result = array();
		$sort_arr = array();
		$index = 0;
		
		switch($search_item) {
			//--------------------
			case 'ALBUM':
			//--------------------
				$total_tracks = ($term3) ? $term3 : 0;
				$release_year = ($term4) ? $term4 : 0;
				
				if(!empty($result_arr['results'])) {
					foreach($result_arr['results'] as $key => $album) {
						$album_artist = !empty($album['artistName']) ? $album['artistName'] : "";
						$trackCount = !empty($album['trackCount']) ? $album['trackCount'] : 0;
						
						$album_artist = $this->replace_special_chars($album_artist);
						$term2 = $this->replace_special_chars($term2);
						
						$album_artist = $this->remove_special_chars($album_artist, " \t");
						$term2 = $this->remove_special_chars($term2, " \t");
											
						if(strtoupper($album_artist) == strtoupper($term2)) {
							$album_title = !empty($album['collectionName']) ? $album['collectionName'] : "";
							
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
									$sort_arr[$index]['album_id'] = !empty($album['collectionId']) ? $album['collectionId'] : "";
									$sort_arr[$index]['total_tracks'] = !empty($album['trackCount']) ? $album['trackCount'] : 0;
									$sort_arr[$index]['copyright'] = !empty($album['copyright']) ? $album['copyright'] : "";
									$sort_arr[$index]['rel_year'] = !empty($album['releaseDate']) ? $this->extract_rel_year($album['releaseDate']) : 0;
									
									$country = !empty($album['collectionViewUrl']) ? $this->extract_country_from_url($album['collectionViewUrl']) : array();
									$sort_arr[$index]['country_code'] = !empty($country['country_code']) ? $country['country_code'] : "";
									
									$index ++;
								}
						}
							
						
					}
				}
					
				
				//------------
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
								$high_result = $result_arr['results'][$hight_index];
									
								$result = $this->parse_item_info($high_result);
								$result['match_percent'] = $hight_perc;
								$result['match_step'] = $match_step;
									
								$matched_album_content = 1;
							}							
														
						}
					
					
					if(count($filters_arr) > 0 && !$album_content_is_matched) {
						
						foreach($sort_arr as $arr) {
							$matched_tracks = 0;
														
							$tracks = $this->get_album_tracks($arr['album_id'], $arr['country_code'], 200);
															
								if($tracks[0] == "FOUND" && count($tracks[1]) == count($filters_arr)) {									
									$index = 0;
									foreach($tracks[1] as $track) {
										$track_title = $filters_arr[$index]['title'];
										$track_title .= !empty($filters_arr[$index]['part']) ? ": ".$filters_arr[$index]['part'] : "";
										$track_title .= (empty($filters_arr[$index]['part']) && !empty($filters_arr[$index]['version'])) ? ": ".$filters_arr[$index]['version'] : "";
										
										$track_title = $this->replace_special_chars($track_title);
										$object_name = $this->replace_special_chars($track['object_name']);
										
										$track_title = $this->remove_special_chars($track_title, " \t’,':-_()[]„\"");
										$object_name = $this->remove_special_chars($track['object_name'], " \t’,':-_()[]„\"");
										
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
									}//foreach
									
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
											$high_result = $result_arr['results'][$hight_index];
											
											$result = $this->parse_item_info($high_result);
											$result['match_percent'] = $hight_perc;
											$result['match_step'] = $match_step;
											
											$matched_album_content ++;
		
											//break;
										}
								}
								
						}//foreach
					}
					
					
					//Check Copyright (step 5)			
					$copyright_found = false;
					if($matched_album_content != 1) {//if there is no matched album or more then one matched album
						
						$arr = $this->check_copyright($result_arr, $sort_arr, $release_year);
							if(count($arr) == 0) {
								$arr = $this->check_copyright($result_arr, $sort_arr, 0);
								$match_step = 51;
							}
							else
								$match_step = 50;
							

						if(count($arr) > 0) {
							$copyright_found = true;
							
							$high_result = $result_arr['results'][$arr['high_index']];
							$result = $this->parse_item_info($high_result);
							$result['match_percent'] = $arr['match_percent'];
							$result['match_step'] = $match_step;
						}
						
					}
					//------------------

					//Choose Album with highest similarity (step 6)
					if($matched_album_content != 1 && !$copyright_found) {
						$hight_index = $sort_arr[0]['item_index'];
						$hight_perc = $sort_arr[0]['perc'];	
						$high_result = $result_arr['results'][$hight_index];
														
						$result = $this->parse_item_info($high_result);
						$result['match_percent'] = $hight_perc;
						$result['match_step'] = 60;
					}		

				}
					
			break;
		
			
			//--------------------
			case 'TRACK':
			//--------------------
				$sort_arr = $this->choose_tracks($result_arr, $term1, $term2, $term3); //choose tracks by track artist and album title
					
				if(!count($sort_arr))
					$sort_arr = $this->choose_tracks($result_arr, $term1, $term2, ""); //choose tracks by track artist only
		
				//------------
				if(count($sort_arr) > 0) {
					$this->array_sort_by_column($sort_arr, 'perc', SORT_DESC);
					$hight_index = $sort_arr[0]['item_index'];
					$hight_perc = $sort_arr[0]['perc'];
						
						if($hight_perc > 70) {
							$high_result = $result_arr['results'][$hight_index];
			
								if(!empty($high_result['trackViewUrl'])) {									
									$result = $this->parse_item_info($high_result);
									$result['match_percent'] = $hight_perc;
								}
						}
				}
		
			break;
			
			//--------------------
			case 'ARTIST':
			//--------------------
				if(!empty($result_arr['results'])) {
					foreach($result_arr['results'] as $artist) {
						if(!empty($artist['wrapperType']) && $artist['wrapperType'] == "artist") {
							$artist_name = !empty($artist['artistName']) ? $artist['artistName'] : "";
							
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
				}		
			
			break;			
		}
		
		return $result;		
	}
	
	
	/**
	 * Choose Tracks from result array and sort
	 * 
	 * @param array $result_arr
	 * @param string $term1
	 * @param string $term2
	 * @param string $term3
	 * @return array
	 */	
	private function choose_tracks($result_arr, $term1, $term2, $term3) {
		$sort_arr = array();
		$index = 0;
		
			if(!empty($result_arr['results'])) {			
				foreach($result_arr['results'] as $track) {
					if(!empty($track['kind']) && $track['kind'] == "song") {
						if(!empty($term2) && !empty($term3)) {//comparing by Track Artist and Album Title
							$track_artist = !empty($track['artistName']) ? $track['artistName'] : "";
							$album_title = !empty($track['collectionCensoredName']) ? $track['collectionCensoredName'] : "";
								
							if(strtoupper($track_artist) == strtoupper($term2) && strtoupper($album_title) == strtoupper($term3)) {
								$track_title = !empty($track['trackName']) ? $track['trackName'] : "";
								similar_text(strtoupper($term1), strtoupper($track_title), $perc);
									
								$sort_arr[$index]['item_index'] = $index;
								$sort_arr[$index]['perc'] = $perc;
							}
						}
						elseif(!empty($term2)) {//comparing by Track Artist only
							$track_artist = !empty($track['artistName']) ? $track['artistName'] : "";
								
							if(strtoupper($track_artist) == strtoupper($term2)) {
								$track_title = !empty($track['trackName']) ? $track['trackName'] : "";
								similar_text(strtoupper($term1), strtoupper($track_title), $perc);
									
								$sort_arr[$index]['item_index'] = $index;
								$sort_arr[$index]['perc'] = $perc;
							}
						}
							
					}
					$index ++;
				}
			}
		
		return $sort_arr;		
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
	 * Get Country ID by Country Code
	 * 
	 * @param string $country_code
	 * @return int
	 */
	public function get_country_id($country_code) {
		$country_id = 0;
		
			foreach($this->COUNTRY as $country) {
				if($country['code'] == strtoupper($country_code)) {
					$country_id = $country['id'];
					break;
				}
			}
		
		return $country_id;
	}
	
	
	/**
	 * Get Country Code by Country ID
	 *
	 * @param int $country_id
	 * @return string
	 */
	public function get_country_code($country_id) {
		$country_code = "";
	
		foreach($this->COUNTRY as $country) {
			if($country['id'] == $country_id) {
				$country_code = $country['code'];
				break;
			}
		}
	
		return $country_code;
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
	 * Check Copyright
	 * 
	 * @param array $result_arr
	 * @param array $sort_arr
	 * @param int $release_year
	 * @return array
	 */
	private function check_copyright($result_arr, $sort_arr, $release_year) {		
		$result = array();
		$copyright_found = false;
		
		foreach($sort_arr as $arr_perc) {
			$hight_perc = $arr_perc['perc'];
				
			foreach($this->COPYRIGHTS as $copyright) {
				foreach($sort_arr as $arr) {
					if(stripos($arr['copyright'], $copyright) !== false && $arr['perc'] == $hight_perc) {
						
						//check release year
						if($release_year && $arr['rel_year']) {
							if((int)$release_year == (int)$arr['rel_year'])
								$copyright_found = true;
							else
								$copyright_found = false;
						}
						else
							$copyright_found = true;
						//-----------------------------
		
						if($copyright_found) {
							
							$high_index = $arr['item_index'];

							$result['high_index'] = $high_index;
							$result['match_percent'] = $arr['perc'];							
						}
					}
						
					if($copyright_found)
						break;
				}
				if($copyright_found)
					break;
			}
			if($copyright_found)
				break;
		}
		
		return $result;		
	}
}
?>