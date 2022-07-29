<?PHP
/**
 * Convert Milliseconds to Time
 * 
 * Format1: HH:MM:SS:ss
 * Format2: MM:SS
 * @param integer
 * @param integer (1 or 2, default is 1)
 * @return string
 */
function cug_mseconds_to_time($milliseconds=0, $format=1) 
{
$seconds = floor($milliseconds / 1000);
$minutes = floor($seconds / 60);
$hours = floor($minutes / 60);
$milliseconds = $milliseconds % 1000;
$seconds = $seconds % 60;
$minutes = $minutes % 60;

	if($format == 1) {// 00:03:55.937
		$format = '%02u:%02u:%02u.%03u';
		$time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
		return $time;
		//return rtrim($time, '0');
	}
	elseif($format == 2) {// 03:55
		$format = '%02u:%02u';
		$time = sprintf($format, $minutes, $seconds);
		return $time;
	}
	elseif($format == 3) {// 00:03:55
		$format = '%02u:%02u:%02u';
		$time = sprintf($format, $hours, $minutes, $seconds);
		return $time;
		//return rtrim($time, '0');
	}	
}


//---------------------------
function cug_parse_date_for_mysql($date, $delimiter="-") {
	$result = "";

	if($date) {
		$arr = explode($delimiter, $date);

		if(count($arr) > 0) {
			if(strlen($arr[0]) == 4) {
				$result = $arr[0];

				if(!empty($arr[1]) && strlen($arr[1]) == 2 && ((int)$arr[1] >=1 && (int)$arr[1] <= 12)) {
					$result .= "-".$arr[1];
						
					if(!empty($arr[2]) && strlen($arr[1]) == 2 && ((int)$arr[2] >=1 && (int)$arr[1] <= 31)) {
						$result .= "-".$arr[2];
					}
					else {
						$result .= "-00";
					}
				}
				else {
					$result .= "-00-00";
				}
			}
		}
	}

	return $result;
}


//---------------------------
function cug_parse_date_from_mysql($date, $delimiter="-") {
	$result = "";

	if($date) {
		$arr = explode($delimiter, $date);

		if(count($arr) == 3 && (int)$arr[0] > 0) {
			$result .= $arr[0];
			
			if($arr[1] != "00")
				$result .= "-".$arr[1];
			
			if($arr[2] != "00")
				$result .= "-".$arr[2];
		
		}
	}

	return $result;
}


/**
 * Validate datetime string (Gregorian date)
 * 
 * @param string $str
 * @param bool $time (Optional, 'false' - when $str is date [YYYY-MM-DD]; 'true' - when $str is datetime [YYYY-MM-DD HH:MM:SS])
 * @return boolean
 */
function cug_validate_datetime_str($str, $time=false) {
	$result = false;
	
	if(!$time) {//date, '2016-05-19'
		$pattern = "/^\d{4}-\d{2}-\d{2}$/";
		
		if(preg_match($pattern, $str)) {
			$arr = explode("-", $str);
			
			$year = $arr[0];
			$month = $arr[1];
			$day = $arr[2];

			if(checkdate($month, $day, $year)) {
				$result = true;
			}

		}
	}
	else {//date and time, '2016-05-19 21:38:00'
		$pattern = "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/";
		
		if(preg_match($pattern, $str)) {
			$temp_arr = $arr = explode(" ", $str);
			
			$arr = explode("-", $temp_arr[0]);		
			$year = $arr[0];
			$month = $arr[1];
			$day = $arr[2];
			
			$arr = explode(":", $temp_arr[1]);		
			$hour = $arr[0];
			$min = $arr[1];
			$sec = $arr[2];

			if(checkdate($month, $day, $year)) {
				if((int)$hour >= 0 && (int)$hour <= 23) {
					if((int)$min >= 0 && (int)$min <= 59) {
						if((int)$sec >= 0 && (int)$sec <= 59) {
							$result = true;
						}
					}
				}
			}
		}		
	}

	return $result;   
}
?>