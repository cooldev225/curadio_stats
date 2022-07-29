<?PHP

// ERROR CODES
//***************
$ERRORS = array();
$ERRORS['UNKNOWN_ACTION_ID'] 			= -100;
$ERRORS['NO_TRACK_ID']                  = -102;
$ERRORS['NO_STAT_DATA']                 = -103;
$ERRORS['SERVER_IS_BUSY_TRY_LATER']     = -104; //when tables renaming process is running or for other reasons
$ERRORS['UNKNOWN_TABLE_INDEX']          = -105; //when FILTER or TIME_PERIOD is wrong or there is no relevant index in $Tables array
$ERRORS['UNKNOWN_DB_ERROR']             = -106;
$ERRORS['NO_AREA_DATA']                 = -107;

$ERRORS['JSON_ERROR_DEPTH']				= -160;
$ERRORS['JSON_ERROR_STATE_MISMATCH']	= -161;
$ERRORS['JSON_ERROR_CTRL_CHAR']			= -162;
$ERRORS['JSON_ERROR_SYNTAX']			= -163;
$ERRORS['JSON_ERROR_UTF8']				= -164;
$ERRORS['JSON_ERROR_UNKNOWN']			= -165;

//For internal usage
$ERRORS['CACHE_TABLE_IS_DISABLED']      = -300;
?>