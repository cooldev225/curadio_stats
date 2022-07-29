<?PHP
/**
 * Get JSON last Error Code
 * 
 * @return number
 */
function cug_json_last_error() {
	global $ERRORS;
	
	switch(json_last_error()) {
		case JSON_ERROR_NONE:
			return 0;
		break;
		//-----------------------
		case JSON_ERROR_DEPTH:
			return $ERRORS['JSON_ERROR_DEPTH'];
		break;
		//-----------------------
		case JSON_ERROR_STATE_MISMATCH:
			return $ERRORS['JSON_ERROR_STATE_MISMATCH'];
		break;
		//-----------------------
		case JSON_ERROR_CTRL_CHAR:
			return $ERRORS['JSON_ERROR_CTRL_CHAR'];
		break;
		//-----------------------
		case JSON_ERROR_SYNTAX:
			return $ERRORS['JSON_ERROR_SYNTAX'];
		break;
		//-----------------------
		case JSON_ERROR_UTF8:
			return $ERRORS['JSON_ERROR_UTF8'];
		break;
		//-----------------------
		default:
			return $ERRORS['JSON_ERROR_UNKNOWN'];
		break;
	}
		
}

?>