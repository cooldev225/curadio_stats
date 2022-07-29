<?PHP

class cug__portal_youtube {
	
	private $youtubedl_tool;
	private $cookie_name;
	
	/**
	 * Constructor
	 * 
	 * @param string $var
	 * @param string $cookie_name (Optional)
	 */
	public function __construct($youtubedl_tool="", $cookie_name=""){
		$this->youtubedl_tool = $youtubedl_tool;
		$this->cookie_name = ($cookie_name) ? $cookie_name : "";
	}
	
	
	/**
	 * Get Video ID from URL
	 * 
	 * @param string $url (Youtube Video URL)
	 * @return string
	 */
	public function get_videoid_from_url($url) {
		$video_id = "";
		
		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$video_id = $match[1];
		}
		
		return 	$video_id;		
	}
	
	
	/**
	 * Get Start Time from URL
	 * 
	 * @param string $url
	 * @return int
	 */
	public function get_start_time_from_url($url) {
		$start_time_sec = 0;
		$hour = 0;
		$min = 0;
		$sec = 0;
		
		$arr = parse_url($url);
		
		if(!empty($arr['query'])) {
			parse_str($arr['query'], $query_arr);
			
			if(!empty($query_arr['t'])) {
				$arr2 = explode("h", $query_arr['t']);
				
				if(count($arr2) == 2) {
					$hour = (int)$arr2[0];
					$arr3 = explode("m", $arr2[1]);
					
					if(count($arr3) == 2) {
						$min = (int)$arr3[0];
						$arr4 = explode("s", $arr3[1]);
						$sec  = !empty($arr4[0]) ? (int)$arr4[0] : 0;
					}
				}
				else {
					$arr3 = explode("m", $query_arr['t']);
						
					if(count($arr3) == 2) {
						$hour = 0;
						$min = (int)$arr3[0];
						$arr4 = explode("s", $arr3[1]);
						$sec  = !empty($arr4[0]) ? (int)$arr4[0] : 0;
					}
					else {
						$hour = 0;
						$min = 0;
						$arr4 = explode("s", $query_arr['t']);
						$sec  = !empty($arr4[0]) ? (int)$arr4[0] : 0;					
					}					
				}
			}
		}
		
		$start_time_sec = ($hour * 3600) + ($min * 60) + $sec;
		
		return $start_time_sec;
	}
	
	/**
	 * Download File from Youtube (using 'youtube-dl' tool)
	 * 
	 * @param string $url
	 * @param string $output_folder
	 * @param array $itags (Array of itags)
	 * @return array
	 */
	public function download_file($url, $output_folder, $itags) {
		$file_info = array();
		$file_info['status'] = 0;
		$file_info['msg'] = "Even if technical errors are very rare some has occurred. :-/ Please try some other video while we are working on solving this issue.";
		$file_info['msg'] .= isset($_COOKIE[$this->cookie_name]) ? "<br>Not Enough Parameters for Downloading Video" : "";
	
		if($url && $output_folder && count($itags) > 0) {
			foreach($itags as $itag) {
				$file_info['file_tag'] = $itag;
					
				switch($itag) {
					// Audio Only: m4a, 44.1 KHz, 48 Kbps, AAC [DASH audio]
					case 139:
					//--------------------
						$file_info['file_type'] 	= 1; // Audio Only (ID from 'track_file_type_list')
						$file_info['file_format'] 	= 'AAC';
						$file_info['file_srate'] 	= '44.1'; // Audio Sampling Rate
						$file_info['file_brate'] 	= '48';   // Audio BitRate
						$file_info['file_ext'] 		= 'm4a';  // File Extention
					break;
							
					// Audio Only: m4a, 44.1 KHz, 128 Kbps, AAC [DASH audio]
					case 140:
					//--------------------
						$file_info['file_type'] 	= 1; //Audio Only
						$file_info['file_format'] 	= 'AAC';
						$file_info['file_srate'] 	= '44.1';
						$file_info['file_brate'] 	= '128';
						$file_info['file_ext'] 		= 'm4a';
					break;
							
					// Audio Only: m4a, 44.1 KHz, 256 Kbps, AAC [DASH audio]
					case 141:
					//--------------------
						$file_info['file_type'] 	= 1; //Audio Only
						$file_info['file_format'] 	= 'AAC';
						$file_info['file_srate'] 	= '44.1';
						$file_info['file_brate'] 	= '256';
						$file_info['file_ext'] 		= 'm4a';
					break;
							
					// Audio Only: webm, 44.1 KHz, 128 Kbps, AAC [DASH audio]
					case 171:
					//--------------------
						$file_info['file_type'] 	= 1; //Audio Only
						$file_info['file_format'] 	= 'AAC';
						$file_info['file_srate'] 	= '44.1';
						$file_info['file_brate'] 	= '128';
						$file_info['file_ext'] 		= 'webm';
					break;
							
					// Audio Only: webm, 44.1 KHz, 256 Kbps, AAC [DASH audio]
					case 172:
					//--------------------
						$file_info['file_type'] 	= 1; //Audio Only
						$file_info['file_format'] 	= 'AAC';
						$file_info['file_srate'] 	= '44.1';
						$file_info['file_brate'] 	= '256';
						$file_info['file_ext'] 		= 'webm';
					break;
							
					default:
						$file_info['file_type'] 	= '';
						$file_info['file_format'] 	= '';
						$file_info['file_srate'] 	= '';
						$file_info['file_brate'] 	= '';
						$file_info['file_ext'] 		= '';
						break;
				}
					
					
					
				if($file_info['file_type']) {//try to download file
					$video_id = $this->get_videoid_from_url($url);
					$file_info['file_id'] = "";
	
					if($video_id) {
						$file_info['file_id'] = $video_id;
						$output_file = $output_folder.$video_id;
							
						$command = $this->youtubedl_tool." -f $itag ".$url." -o \"$output_file"."_%(duration)s\"";
						exec($command, $buffer);
						//print_r($buffer).PHP_EOL;
						//unset($buffer);
							
						if(count($buffer) > 2) {
							$destination_file = "";
	
							foreach($buffer as $val) {
								$arr = explode($video_id."_", $val);
									
								if(!empty($arr[1])) {
									$arr2 = explode(" ", $arr[1]);
									$duration = trim($arr2[0]);
	
									$destination_file = $output_folder.$video_id."_".$duration;
	
									if(file_exists($destination_file) && filesize($destination_file) > 0) {
										@chmod($destination_file, 0774);
										$path_parts = pathinfo($destination_file);
											
										$file_info['file'] = $destination_file;
										$file_info['file_duration'] = $duration;
										$file_info['status'] = 1; // OK
										$file_info['file_size'] = filesize($destination_file);
										return $file_info;
									}
								}
							}
	
							unset($buffer);
	
							if(!$destination_file) {
								$file_info['status'] = -2; // Unable to Parse Data
								$file_info['msg'] = "Even if technical errors are very rare some has occurred. :-/ Please try some other video while we are working on solving this issue.";
								$file_info['msg'] .= isset($_COOKIE[$this->cookie_name]) ? "<br>Unable to Parse youtube-dl Data" : "";
							}
	
						}
						else {
							$file_info['status'] = -1; // Unable to Download
							$file_info['msg'] = "It was not possible to download the video. :-/ Most probably this video is blocked in some countries. Please try some other video while we are working on solving this issue.";
							$file_info['msg'] .= isset($_COOKIE[$this->cookie_name]) ? "<br>Unable to Download Video" : "";
						}
					}
					else {
						$file_info['status'] = -3; // Unable to extract video_id
						$file_info['msg'] = "Even if technical errors are very rare some has occurred. :-/ Please try some other video while we are working on solving this issue.";
						$file_info['msg'] .= isset($_COOKIE[$this->cookie_name]) ? "<br>Unable to extract video_id" : "";
					}
	
				}
			}
		}
	
		return $file_info;
	}	
}
?>