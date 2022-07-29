<?PHP

class cug__wm {

	private $wm_tool;
	private $slash;

	/**
	 * Constructor
	 *
	 * @param string $var
	 */
	public function __construct($wm_tool, $slash){
		$this->wm_tool = $wm_tool;
		$this->slash = $slash;
	}


	/**
	 * Check Audio File for Watermarks
	 * 
	 * @param string $audio_file
	 * @param string $markers (Optional, default: 1[First Watermak])
	 * @param string $log_file (Optional)
	 * @param string $delete_log_file (Optional, default: true)
	 * @return array
	 */
	public function check_wm_v034($audio_file, $markers="1", $log_file="", $delete_log_file=true) {
		$result = array(0,0,0,0);

		if($audio_file && $markers) {
				
			if(!$log_file) {
				$path_parts = pathinfo($audio_file);
				$log_file = $path_parts['dirname'].$this->slash.$path_parts['filename'].".wm";
			}

			//execute tool
			$command = $this->wm_tool." \"$audio_file\" -S:$markers -L:\"$log_file\"";
			exec($command, $buffer);
			
			if(@file_exists($log_file) && @filesize($log_file) > 0) {
				if($f = fopen($log_file, 'r')) {
					$line = fgets($f);
					$arr = explode("\t", $line);
					
					if(count($arr) > 4) {
						for($i=0; $i<4; $i++) {
							$wm_num = (int)$arr[$i];
							$result[$i] = ($wm_num > 0) ? $wm_num : 0;
						}
					}
					
					fclose($f);
				}
					
			}
				
				//---------------------
				if($delete_log_file) {
					@unlink($log_file);
				}
		}
		
		
		return $result;
	}
}
?>