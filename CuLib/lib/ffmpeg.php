<?PHP

class cug__ffmpeg {

	private $ffmpeg_tool;
	
	/**
	 * Constructor
	 *
	 * @param string $var
	 */
	public function __construct($ffmpeg_tool){
		$this->ffmpeg_tool = $ffmpeg_tool;
	}
	
	
	/**
	 * Convert Files
	 * 
	 * @param string $input_file
	 * @param string $output_file
	 * @param string $params (Optional)
	 * @param int $min_bytes (Optional, Minimal size of the output file, in bytes)
	 * @param boolean $loglevel (Optional, default: true)
	 * @return boolean
	 */
	public function convert_file($input_file, $output_file, $params="", $min_bytes=0, $loglevel=true) {
		if($input_file && $output_file) {
			$command = $this->ffmpeg_tool;
			$command .= ($loglevel) ? " -loglevel quiet" : "";
			$command .= " -i \"".$input_file."\"";
			$command .= ($params) ? " ".$params : "";
			$command .= " \"".$output_file."\"";
			//echo PHP_EOL.$command.PHP_EOL;
			exec($command, $buffer);
			
				if(file_exists($output_file) && @filesize($output_file) > $min_bytes)
					return true;
				else 
					@unlink($output_file);
		}
		
		return false;
		
	}
}
?>