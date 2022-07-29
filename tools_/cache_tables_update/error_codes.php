<?PHP

// ERROR CODES
//***************
$ERRORS = array();

$ERRORS['UNABLE_TO_CREATE_TABLE']       = -100;
$ERRORS['UNABLE_TO_CREATE_TEMP_TABLE']  = -101;
$ERRORS['UNABLE_TO_RENAME_TEMP_TABLE']  = -102;
$ERRORS['UNABLE_TO_DROP_TEMP_TABLE']    = -103;
$ERRORS['UNABLE_TO_DROP_TABLE']         = -104;
$ERRORS['UNKNOWN_TABLE_INDEX']          = -105; //when FILTER or TIME_PERIOD is wrong or there is no relevant index in $Tables array
$ERRORS['UNABLE_TO_COLLECT_UNIQUE_COUNTRY_CODES']   = -106;
$ERRORS['UNABLE_TO_EXTRACT_DATA']       = -107;
$ERRORS['UNABLE_TO_COLLECT_UNIQUE_CONTINENT_CODES']   = -108;
$ERRORS['UNABLE_TO_COLLECT_UNIQUE_SUBDIVISION_CODES'] = -109;
$ERRORS['UNABLE_TO_COLLECT_UNIQUE_CITY_NAMES']        = -110;
$ERRORS['UNABLE_TO_COLLECT_UNIQUE_STATION_IDS']       = -111;
$ERRORS['NO_TIME_YET_TO_ARCHIVE_YEAR']      = -112; 
$ERRORS['NOT_ENOUGH_ARCHIVE_TABLES']        = -113; //not all archive tables are created, 'archive year' process will be interrupted
$ERRORS['ARCHIVE_YEAR_DONE_PARTIALLY']      = -114; //not all cache tables were copied to archive db
$ERRORS['UNABLE_TO_CREATE_ARCHIVE_DB']      = -115;
$ERRORS['UNABLE_TO_COPY_TABLE_DATA']        = -116;

$ERRORS['UNABLE_TO_GET_TABLE_CREATE_TIME']          = -200;
$ERRORS['NO_TIME_YET_TO_UPDATE_CACHE_TABLE']        = -201;
$ERRORS['MAIN_STAT_DATA_TABLE_NOT_EXISTS']          = -202; //when main statistics data table does not exists
$ERRORS['FILTERED_STAT_DATA_TABLE_NOT_EXISTS']      = -203; //when filtered (by time period) statistics data table does not exists
$ERRORS['PROCESS_IS_NOT_READY_TO_START']            = -204; //when status != 1 in '_process_list' table 
$ERRORS['NO_NEW_DATA_TABLES_UPDATES']               = -205; //when there is no new update for data tables 'cr_detect_results', 'cr_stations'
$ERRORS['PROCESS_WAS_INTERRUPTED']                  = -206; //Process was interrupted if it was running longer than $PROCESS_MAX_DURATION
$ERRORS['PROCESS_IS_ALREADY_RUNNING']               = -207;
$ERRORS['PROCESS_IS_ALREADY_FINISHED']              = -208;
$ERRORS['TABLE_UPDATE_IS_ALREADY_RUNNING']          = -209;
$ERRORS['TABLE_UPDATE_IS_ALREADY_FINISHED']         = -210;
$ERRORS['NOT_ENOUGH_DATA_FOR_CURR_TIME_PERIOD']     = -211;
$ERRORS['NO_TIME_YET_TO_EXTRACT_DATA']              = -212;
$ERRORS['NO_TIME_YET_TO_EXTRACT_DATA']              = -212;
$ERRORS['CACHE_TABLE_IS_DISABLED']                  = -213; //calculation for cache table is disabled in '_cache_tables_list' table
?>