<?PHP

//DATABASES
//**************************************
$DB = array();
$DB['curadio_cache'] = "curadio_cache"; //current DB
$DB['curadio_cache_2016'] = "curadio_cache_2016"; // Year 2016
$DB['curadio_cache_2017'] = "curadio_cache_2017"; // Year 2017
$DB['curadio_cache_2018'] = "curadio_cache_2018"; // Year 2018


//TABLES
//**************************************
$Tables = array();

//Temp Tables Suffix
$temp_table_suffix = "_tmp";



//Configuration Tables
$Tables['process']       = "_process_list";
$Tables['cache_table']   = "_cache_tables_list";
$Tables['data_table']    = "_data_tables_list";
$Tables['update_status'] = "cr_update_status";


// Data Tables
$stat_table_index = "stat_data";
$Tables[$stat_table_index]                      = "cr_detect_results";
$Tables[$stat_table_index.'__last_7_days']      = "cr_detect_results__last_7_days";
$Tables[$stat_table_index.'__last_30_days']     = "cr_detect_results__last_30_days";
$Tables[$stat_table_index.'__last_365_days']    = "cr_detect_results__last_365_days";
$Tables[$stat_table_index.'__last_year']        = "cr_detect_results__last_year";
$Tables[$stat_table_index.'__last_month']       = "cr_detect_results__last_month";
$Tables[$stat_table_index.'__last_week']        = "cr_detect_results__last_week";

$Tables['station'] = "cr_stations";


// Cache Tables
// by TRACK
$Tables['track_played_total__last_7_days']                  = "track_played_total__last_7_days";
$Tables['track_played_by_continent__last_7_days']           = "track_played_by_continent__last_7_days";
$Tables['track_played_by_country__last_7_days']             = "track_played_by_country__last_7_days";
$Tables['track_played_by_subdivision__last_7_days']         = "track_played_by_subdivision__last_7_days";
$Tables['track_played_by_city__last_7_days']                = "track_played_by_city__last_7_days";
$Tables['track_played_by_station__last_7_days']             = "track_played_by_station__last_7_days";
$Tables['track_played_by_daytime__last_7_days']             = "track_played_by_daytime__last_7_days";
$Tables['track_played_by_daytime_continent__last_7_days']   = "track_played_by_daytime_continent__last_7_days";
$Tables['track_played_by_daytime_country__last_7_days']     = "track_played_by_daytime_country__last_7_days";
$Tables['track_played_by_daytime_subdivision__last_7_days'] = "track_played_by_daytime_subdivision__last_7_days";
$Tables['track_played_by_daytime_city__last_7_days']        = "track_played_by_daytime_city__last_7_days";
$Tables['track_played_by_daytime_station__last_7_days']     = "track_played_by_daytime_station__last_7_days";
$Tables['track_played_by_artist__last_7_days']              = "track_played_by_artist__last_7_days";

$Tables['track_played_total__last_30_days']                  = "track_played_total__last_30_days";
$Tables['track_played_by_continent__last_30_days']           = "track_played_by_continent__last_30_days";
$Tables['track_played_by_country__last_30_days']             = "track_played_by_country__last_30_days";
$Tables['track_played_by_subdivision__last_30_days']         = "track_played_by_subdivision__last_30_days";
$Tables['track_played_by_city__last_30_days']                = "track_played_by_city__last_30_days";
$Tables['track_played_by_station__last_30_days']             = "track_played_by_station__last_30_days";
$Tables['track_played_by_daytime__last_30_days']             = "track_played_by_daytime__last_30_days";
$Tables['track_played_by_daytime_continent__last_30_days']   = "track_played_by_daytime_continent__last_30_days";
$Tables['track_played_by_daytime_country__last_30_days']     = "track_played_by_daytime_country__last_30_days";
$Tables['track_played_by_daytime_subdivision__last_30_days'] = "track_played_by_daytime_subdivision__last_30_days";
$Tables['track_played_by_daytime_city__last_30_days']        = "track_played_by_daytime_city__last_30_days";
$Tables['track_played_by_daytime_station__last_30_days']     = "track_played_by_daytime_station__last_30_days";
$Tables['track_played_by_artist__last_30_days']              = "track_played_by_artist__last_30_days";

$Tables['track_played_total__last_365_days']                  = "track_played_total__last_365_days";
$Tables['track_played_by_continent__last_365_days']           = "track_played_by_continent__last_365_days";
$Tables['track_played_by_country__last_365_days']             = "track_played_by_country__last_365_days";
$Tables['track_played_by_subdivision__last_365_days']         = "track_played_by_subdivision__last_365_days";
$Tables['track_played_by_city__last_365_days']                = "track_played_by_city__last_365_days";
$Tables['track_played_by_station__last_365_days']             = "track_played_by_station__last_365_days";
$Tables['track_played_by_daytime__last_365_days']             = "track_played_by_daytime__last_365_days";
$Tables['track_played_by_daytime_continent__last_365_days']   = "track_played_by_daytime_continent__last_365_days";
$Tables['track_played_by_daytime_country__last_365_days']     = "track_played_by_daytime_country__last_365_days";
$Tables['track_played_by_daytime_subdivision__last_365_days'] = "track_played_by_daytime_subdivision__last_365_days";
$Tables['track_played_by_daytime_city__last_365_days']        = "track_played_by_daytime_city__last_365_days";
$Tables['track_played_by_daytime_station__last_365_days']     = "track_played_by_daytime_station__last_365_days";
$Tables['track_played_by_artist__last_365_days']              = "track_played_by_artist__last_365_days";

$Tables['track_played_total__last_year']                  = "track_played_total__last_year";
$Tables['track_played_by_continent__last_year']           = "track_played_by_continent__last_year";
$Tables['track_played_by_country__last_year']             = "track_played_by_country__last_year";
$Tables['track_played_by_subdivision__last_year']         = "track_played_by_subdivision__last_year";
$Tables['track_played_by_city__last_year']                = "track_played_by_city__last_year";
$Tables['track_played_by_station__last_year']             = "track_played_by_station__last_year";
$Tables['track_played_by_daytime__last_year']             = "track_played_by_daytime__last_year";
$Tables['track_played_by_daytime_continent__last_year']   = "track_played_by_daytime_continent__last_year";
$Tables['track_played_by_daytime_country__last_year']     = "track_played_by_daytime_country__last_year";
$Tables['track_played_by_daytime_subdivision__last_year'] = "track_played_by_daytime_subdivision__last_year";
$Tables['track_played_by_daytime_city__last_year']        = "track_played_by_daytime_city__last_year";
$Tables['track_played_by_daytime_station__last_year']     = "track_played_by_daytime_station__last_year";
$Tables['track_played_by_artist__last_year']              = "track_played_by_artist__last_year";

$Tables['track_played_total__last_month']                  = "track_played_total__last_month";
$Tables['track_played_by_continent__last_month']           = "track_played_by_continent__last_month";
$Tables['track_played_by_country__last_month']             = "track_played_by_country__last_month";
$Tables['track_played_by_subdivision__last_month']         = "track_played_by_subdivision__last_month";
$Tables['track_played_by_city__last_month']                = "track_played_by_city__last_month";
$Tables['track_played_by_station__last_month']             = "track_played_by_station__last_month";
$Tables['track_played_by_daytime__last_month']             = "track_played_by_daytime__last_month";
$Tables['track_played_by_daytime_continent__last_month']   = "track_played_by_daytime_continent__last_month";
$Tables['track_played_by_daytime_country__last_month']     = "track_played_by_daytime_country__last_month";
$Tables['track_played_by_daytime_subdivision__last_month'] = "track_played_by_daytime_subdivision__last_month";
$Tables['track_played_by_daytime_city__last_month']        = "track_played_by_daytime_city__last_month";
$Tables['track_played_by_daytime_station__last_month']     = "track_played_by_daytime_station__last_month";
$Tables['track_played_by_artist__last_month']              = "track_played_by_artist__last_month";

$Tables['track_played_total__last_week']                  = "track_played_total__last_week";
$Tables['track_played_by_continent__last_week']           = "track_played_by_continent__last_week";
$Tables['track_played_by_country__last_week']             = "track_played_by_country__last_week";
$Tables['track_played_by_subdivision__last_week']         = "track_played_by_subdivision__last_week";
$Tables['track_played_by_city__last_week']                = "track_played_by_city__last_week";
$Tables['track_played_by_station__last_week']             = "track_played_by_station__last_week";
$Tables['track_played_by_daytime__last_week']             = "track_played_by_daytime__last_week";
$Tables['track_played_by_daytime_continent__last_week']   = "track_played_by_daytime_continent__last_week";
$Tables['track_played_by_daytime_country__last_week']     = "track_played_by_daytime_country__last_week";
$Tables['track_played_by_daytime_subdivision__last_week'] = "track_played_by_daytime_subdivision__last_week";
$Tables['track_played_by_daytime_city__last_week']        = "track_played_by_daytime_city__last_week";
$Tables['track_played_by_daytime_station__last_week']     = "track_played_by_daytime_station__last_week";
$Tables['track_played_by_artist__last_week']              = "track_played_by_artist__last_week";


// by ARTIST

?>