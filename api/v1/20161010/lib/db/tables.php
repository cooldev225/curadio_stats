<?PHP

//DATABASES
//**************************************
$DB = array();
$DB['curadio_cache'] = "curadio_cache"; //Main Cache Database
$DB['archive_db_prefix'] = "curadio_cache_"; //Archive Cache DB Prefix


//TABLES
//**************************************
$Tables = array();
$Tables['country'] 					= "country_list";
$Tables['country_ip'] 				= "country_ip";
$Tables['city'] 					= "country_city_list";
$Tables['dbip']                     = "dbip_lookup";

//Temp Tables Suffix
$temp_table_suffix = "_tmp";



//Configuration Tables
$Tables['process']       = "_process_list";
$Tables['cache_table']   = "_cache_tables_list";
$Tables['data_table']    = "_data_tables_list";
$Tables['update_status'] = "cr_update_status";


// Amount Tables
$Tables['amount_coefficient']       = "amount_coefficient";
$Tables['amount_price_composer']    = "amount_price_composer";
$Tables['amount_price_artist']      = "amount_price_artist";


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

$Tables['track_played_total__this_year']                  = "track_played_total__this_year";
$Tables['track_played_by_continent__this_year']           = "track_played_by_continent__this_year";
$Tables['track_played_by_country__this_year']             = "track_played_by_country__this_year";
$Tables['track_played_by_subdivision__this_year']         = "track_played_by_subdivision__this_year";
$Tables['track_played_by_city__this_year']                = "track_played_by_city__this_year";
$Tables['track_played_by_station__this_year']             = "track_played_by_station__this_year";
$Tables['track_played_by_daytime__this_year']             = "track_played_by_daytime__this_year";
$Tables['track_played_by_daytime_continent__this_year']   = "track_played_by_daytime_continent__this_year";
$Tables['track_played_by_daytime_country__this_year']     = "track_played_by_daytime_country__this_year";
$Tables['track_played_by_daytime_subdivision__this_year'] = "track_played_by_daytime_subdivision__this_year";
$Tables['track_played_by_daytime_city__this_year']        = "track_played_by_daytime_city__this_year";
$Tables['track_played_by_daytime_station__this_year']     = "track_played_by_daytime_station__this_year";
$Tables['track_played_by_artist__this_year']              = "track_played_by_artist__this_year";


// by ARTIST
$Tables['artist_played_total__last_7_days']                  = "artist_played_total__last_7_days";
$Tables['artist_played_by_continent__last_7_days']           = "artist_played_by_continent__last_7_days";
$Tables['artist_played_by_country__last_7_days']             = "artist_played_by_country__last_7_days";
$Tables['artist_played_by_subdivision__last_7_days']         = "artist_played_by_subdivision__last_7_days";
$Tables['artist_played_by_city__last_7_days']                = "artist_played_by_city__last_7_days";
$Tables['artist_played_by_station__last_7_days']             = "artist_played_by_station__last_7_days";
$Tables['artist_played_by_daytime__last_7_days']             = "artist_played_by_daytime__last_7_days";
$Tables['artist_played_by_daytime_continent__last_7_days']   = "artist_played_by_daytime_continent__last_7_days";
$Tables['artist_played_by_daytime_country__last_7_days']     = "artist_played_by_daytime_country__last_7_days";
$Tables['artist_played_by_daytime_subdivision__last_7_days'] = "artist_played_by_daytime_subdivision__last_7_days";
$Tables['artist_played_by_daytime_city__last_7_days']        = "artist_played_by_daytime_city__last_7_days";
$Tables['artist_played_by_daytime_station__last_7_days']     = "artist_played_by_daytime_station__last_7_days";
$Tables['artist_played_by_artist__last_7_days']              = "artist_played_by_artist__last_7_days";

$Tables['artist_played_total__last_30_days']                  = "artist_played_total__last_30_days";
$Tables['artist_played_by_continent__last_30_days']           = "artist_played_by_continent__last_30_days";
$Tables['artist_played_by_country__last_30_days']             = "artist_played_by_country__last_30_days";
$Tables['artist_played_by_subdivision__last_30_days']         = "artist_played_by_subdivision__last_30_days";
$Tables['artist_played_by_city__last_30_days']                = "artist_played_by_city__last_30_days";
$Tables['artist_played_by_station__last_30_days']             = "artist_played_by_station__last_30_days";
$Tables['artist_played_by_daytime__last_30_days']             = "artist_played_by_daytime__last_30_days";
$Tables['artist_played_by_daytime_continent__last_30_days']   = "artist_played_by_daytime_continent__last_30_days";
$Tables['artist_played_by_daytime_country__last_30_days']     = "artist_played_by_daytime_country__last_30_days";
$Tables['artist_played_by_daytime_subdivision__last_30_days'] = "artist_played_by_daytime_subdivision__last_30_days";
$Tables['artist_played_by_daytime_city__last_30_days']        = "artist_played_by_daytime_city__last_30_days";
$Tables['artist_played_by_daytime_station__last_30_days']     = "artist_played_by_daytime_station__last_30_days";
$Tables['artist_played_by_artist__last_30_days']              = "artist_played_by_artist__last_30_days";

$Tables['artist_played_total__last_365_days']                  = "artist_played_total__last_365_days";
$Tables['artist_played_by_continent__last_365_days']           = "artist_played_by_continent__last_365_days";
$Tables['artist_played_by_country__last_365_days']             = "artist_played_by_country__last_365_days";
$Tables['artist_played_by_subdivision__last_365_days']         = "artist_played_by_subdivision__last_365_days";
$Tables['artist_played_by_city__last_365_days']                = "artist_played_by_city__last_365_days";
$Tables['artist_played_by_station__last_365_days']             = "artist_played_by_station__last_365_days";
$Tables['artist_played_by_daytime__last_365_days']             = "artist_played_by_daytime__last_365_days";
$Tables['artist_played_by_daytime_continent__last_365_days']   = "artist_played_by_daytime_continent__last_365_days";
$Tables['artist_played_by_daytime_country__last_365_days']     = "artist_played_by_daytime_country__last_365_days";
$Tables['artist_played_by_daytime_subdivision__last_365_days'] = "artist_played_by_daytime_subdivision__last_365_days";
$Tables['artist_played_by_daytime_city__last_365_days']        = "artist_played_by_daytime_city__last_365_days";
$Tables['artist_played_by_daytime_station__last_365_days']     = "artist_played_by_daytime_station__last_365_days";
$Tables['artist_played_by_artist__last_365_days']              = "artist_played_by_artist__last_365_days";

$Tables['artist_played_total__last_year']                  = "artist_played_total__last_year";
$Tables['artist_played_by_continent__last_year']           = "artist_played_by_continent__last_year";
$Tables['artist_played_by_country__last_year']             = "artist_played_by_country__last_year";
$Tables['artist_played_by_subdivision__last_year']         = "artist_played_by_subdivision__last_year";
$Tables['artist_played_by_city__last_year']                = "artist_played_by_city__last_year";
$Tables['artist_played_by_station__last_year']             = "artist_played_by_station__last_year";
$Tables['artist_played_by_daytime__last_year']             = "artist_played_by_daytime__last_year";
$Tables['artist_played_by_daytime_continent__last_year']   = "artist_played_by_daytime_continent__last_year";
$Tables['artist_played_by_daytime_country__last_year']     = "artist_played_by_daytime_country__last_year";
$Tables['artist_played_by_daytime_subdivision__last_year'] = "artist_played_by_daytime_subdivision__last_year";
$Tables['artist_played_by_daytime_city__last_year']        = "artist_played_by_daytime_city__last_year";
$Tables['artist_played_by_daytime_station__last_year']     = "artist_played_by_daytime_station__last_year";
$Tables['artist_played_by_artist__last_year']              = "artist_played_by_artist__last_year";

$Tables['artist_played_total__last_month']                  = "artist_played_total__last_month";
$Tables['artist_played_by_continent__last_month']           = "artist_played_by_continent__last_month";
$Tables['artist_played_by_country__last_month']             = "artist_played_by_country__last_month";
$Tables['artist_played_by_subdivision__last_month']         = "artist_played_by_subdivision__last_month";
$Tables['artist_played_by_city__last_month']                = "artist_played_by_city__last_month";
$Tables['artist_played_by_station__last_month']             = "artist_played_by_station__last_month";
$Tables['artist_played_by_daytime__last_month']             = "artist_played_by_daytime__last_month";
$Tables['artist_played_by_daytime_continent__last_month']   = "artist_played_by_daytime_continent__last_month";
$Tables['artist_played_by_daytime_country__last_month']     = "artist_played_by_daytime_country__last_month";
$Tables['artist_played_by_daytime_subdivision__last_month'] = "artist_played_by_daytime_subdivision__last_month";
$Tables['artist_played_by_daytime_city__last_month']        = "artist_played_by_daytime_city__last_month";
$Tables['artist_played_by_daytime_station__last_month']     = "artist_played_by_daytime_station__last_month";
$Tables['artist_played_by_artist__last_month']              = "artist_played_by_artist__last_month";

$Tables['artist_played_total__last_week']                  = "artist_played_total__last_week";
$Tables['artist_played_by_continent__last_week']           = "artist_played_by_continent__last_week";
$Tables['artist_played_by_country__last_week']             = "artist_played_by_country__last_week";
$Tables['artist_played_by_subdivision__last_week']         = "artist_played_by_subdivision__last_week";
$Tables['artist_played_by_city__last_week']                = "artist_played_by_city__last_week";
$Tables['artist_played_by_station__last_week']             = "artist_played_by_station__last_week";
$Tables['artist_played_by_daytime__last_week']             = "artist_played_by_daytime__last_week";
$Tables['artist_played_by_daytime_continent__last_week']   = "artist_played_by_daytime_continent__last_week";
$Tables['artist_played_by_daytime_country__last_week']     = "artist_played_by_daytime_country__last_week";
$Tables['artist_played_by_daytime_subdivision__last_week'] = "artist_played_by_daytime_subdivision__last_week";
$Tables['artist_played_by_daytime_city__last_week']        = "artist_played_by_daytime_city__last_week";
$Tables['artist_played_by_daytime_station__last_week']     = "artist_played_by_daytime_station__last_week";
$Tables['artist_played_by_artist__last_week']              = "artist_played_by_artist__last_week";

$Tables['artist_played_total__this_year']                  = "artist_played_total__this_year";
$Tables['artist_played_by_continent__this_year']           = "artist_played_by_continent__this_year";
$Tables['artist_played_by_country__this_year']             = "artist_played_by_country__this_year";
$Tables['artist_played_by_subdivision__this_year']         = "artist_played_by_subdivision__this_year";
$Tables['artist_played_by_city__this_year']                = "artist_played_by_city__this_year";
$Tables['artist_played_by_station__this_year']             = "artist_played_by_station__this_year";
$Tables['artist_played_by_daytime__this_year']             = "artist_played_by_daytime__this_year";
$Tables['artist_played_by_daytime_continent__this_year']   = "artist_played_by_daytime_continent__this_year";
$Tables['artist_played_by_daytime_country__this_year']     = "artist_played_by_daytime_country__this_year";
$Tables['artist_played_by_daytime_subdivision__this_year'] = "artist_played_by_daytime_subdivision__this_year";
$Tables['artist_played_by_daytime_city__this_year']        = "artist_played_by_daytime_city__this_year";
$Tables['artist_played_by_daytime_station__this_year']     = "artist_played_by_daytime_station__this_year";
$Tables['artist_played_by_artist__this_year']              = "artist_played_by_artist__this_year";

// by LABEL
$Tables['label_played_total__last_7_days']                  = "label_played_total__last_7_days";
$Tables['label_played_by_continent__last_7_days']           = "label_played_by_continent__last_7_days";
$Tables['label_played_by_country__last_7_days']             = "label_played_by_country__last_7_days";
$Tables['label_played_by_subdivision__last_7_days']         = "label_played_by_subdivision__last_7_days";
$Tables['label_played_by_city__last_7_days']                = "label_played_by_city__last_7_days";
$Tables['label_played_by_station__last_7_days']             = "label_played_by_station__last_7_days";
$Tables['label_played_by_daytime__last_7_days']             = "label_played_by_daytime__last_7_days";
$Tables['label_played_by_daytime_continent__last_7_days']   = "label_played_by_daytime_continent__last_7_days";
$Tables['label_played_by_daytime_country__last_7_days']     = "label_played_by_daytime_country__last_7_days";
$Tables['label_played_by_daytime_subdivision__last_7_days'] = "label_played_by_daytime_subdivision__last_7_days";
$Tables['label_played_by_daytime_city__last_7_days']        = "label_played_by_daytime_city__last_7_days";
$Tables['label_played_by_daytime_station__last_7_days']     = "label_played_by_daytime_station__last_7_days";

$Tables['label_played_total__last_30_days']                  = "label_played_total__last_30_days";
$Tables['label_played_by_continent__last_30_days']           = "label_played_by_continent__last_30_days";
$Tables['label_played_by_country__last_30_days']             = "label_played_by_country__last_30_days";
$Tables['label_played_by_subdivision__last_30_days']         = "label_played_by_subdivision__last_30_days";
$Tables['label_played_by_city__last_30_days']                = "label_played_by_city__last_30_days";
$Tables['label_played_by_station__last_30_days']             = "label_played_by_station__last_30_days";
$Tables['label_played_by_daytime__last_30_days']             = "label_played_by_daytime__last_30_days";
$Tables['label_played_by_daytime_continent__last_30_days']   = "label_played_by_daytime_continent__last_30_days";
$Tables['label_played_by_daytime_country__last_30_days']     = "label_played_by_daytime_country__last_30_days";
$Tables['label_played_by_daytime_subdivision__last_30_days'] = "label_played_by_daytime_subdivision__last_30_days";
$Tables['label_played_by_daytime_city__last_30_days']        = "label_played_by_daytime_city__last_30_days";
$Tables['label_played_by_daytime_station__last_30_days']     = "label_played_by_daytime_station__last_30_days";

$Tables['label_played_total__last_365_days']                  = "label_played_total__last_365_days";
$Tables['label_played_by_continent__last_365_days']           = "label_played_by_continent__last_365_days";
$Tables['label_played_by_country__last_365_days']             = "label_played_by_country__last_365_days";
$Tables['label_played_by_subdivision__last_365_days']         = "label_played_by_subdivision__last_365_days";
$Tables['label_played_by_city__last_365_days']                = "label_played_by_city__last_365_days";
$Tables['label_played_by_station__last_365_days']             = "label_played_by_station__last_365_days";
$Tables['label_played_by_daytime__last_365_days']             = "label_played_by_daytime__last_365_days";
$Tables['label_played_by_daytime_continent__last_365_days']   = "label_played_by_daytime_continent__last_365_days";
$Tables['label_played_by_daytime_country__last_365_days']     = "label_played_by_daytime_country__last_365_days";
$Tables['label_played_by_daytime_subdivision__last_365_days'] = "label_played_by_daytime_subdivision__last_365_days";
$Tables['label_played_by_daytime_city__last_365_days']        = "label_played_by_daytime_city__last_365_days";
$Tables['label_played_by_daytime_station__last_365_days']     = "label_played_by_daytime_station__last_365_days";

$Tables['label_played_total__last_year']                  = "label_played_total__last_year";
$Tables['label_played_by_continent__last_year']           = "label_played_by_continent__last_year";
$Tables['label_played_by_country__last_year']             = "label_played_by_country__last_year";
$Tables['label_played_by_subdivision__last_year']         = "label_played_by_subdivision__last_year";
$Tables['label_played_by_city__last_year']                = "label_played_by_city__last_year";
$Tables['label_played_by_station__last_year']             = "label_played_by_station__last_year";
$Tables['label_played_by_daytime__last_year']             = "label_played_by_daytime__last_year";
$Tables['label_played_by_daytime_continent__last_year']   = "label_played_by_daytime_continent__last_year";
$Tables['label_played_by_daytime_country__last_year']     = "label_played_by_daytime_country__last_year";
$Tables['label_played_by_daytime_subdivision__last_year'] = "label_played_by_daytime_subdivision__last_year";
$Tables['label_played_by_daytime_city__last_year']        = "label_played_by_daytime_city__last_year";
$Tables['label_played_by_daytime_station__last_year']     = "label_played_by_daytime_station__last_year";

$Tables['label_played_total__last_month']                  = "label_played_total__last_month";
$Tables['label_played_by_continent__last_month']           = "label_played_by_continent__last_month";
$Tables['label_played_by_country__last_month']             = "label_played_by_country__last_month";
$Tables['label_played_by_subdivision__last_month']         = "label_played_by_subdivision__last_month";
$Tables['label_played_by_city__last_month']                = "label_played_by_city__last_month";
$Tables['label_played_by_station__last_month']             = "label_played_by_station__last_month";
$Tables['label_played_by_daytime__last_month']             = "label_played_by_daytime__last_month";
$Tables['label_played_by_daytime_continent__last_month']   = "label_played_by_daytime_continent__last_month";
$Tables['label_played_by_daytime_country__last_month']     = "label_played_by_daytime_country__last_month";
$Tables['label_played_by_daytime_subdivision__last_month'] = "label_played_by_daytime_subdivision__last_month";
$Tables['label_played_by_daytime_city__last_month']        = "label_played_by_daytime_city__last_month";
$Tables['label_played_by_daytime_station__last_month']     = "label_played_by_daytime_station__last_month";

$Tables['label_played_total__last_week']                  = "label_played_total__last_week";
$Tables['label_played_by_continent__last_week']           = "label_played_by_continent__last_week";
$Tables['label_played_by_country__last_week']             = "label_played_by_country__last_week";
$Tables['label_played_by_subdivision__last_week']         = "label_played_by_subdivision__last_week";
$Tables['label_played_by_city__last_week']                = "label_played_by_city__last_week";
$Tables['label_played_by_station__last_week']             = "label_played_by_station__last_week";
$Tables['label_played_by_daytime__last_week']             = "label_played_by_daytime__last_week";
$Tables['label_played_by_daytime_continent__last_week']   = "label_played_by_daytime_continent__last_week";
$Tables['label_played_by_daytime_country__last_week']     = "label_played_by_daytime_country__last_week";
$Tables['label_played_by_daytime_subdivision__last_week'] = "label_played_by_daytime_subdivision__last_week";
$Tables['label_played_by_daytime_city__last_week']        = "label_played_by_daytime_city__last_week";
$Tables['label_played_by_daytime_station__last_week']     = "label_played_by_daytime_station__last_week";

$Tables['label_played_total__this_year']                  = "label_played_total__this_year";
$Tables['label_played_by_continent__this_year']           = "label_played_by_continent__this_year";
$Tables['label_played_by_country__this_year']             = "label_played_by_country__this_year";
$Tables['label_played_by_subdivision__this_year']         = "label_played_by_subdivision__this_year";
$Tables['label_played_by_city__this_year']                = "label_played_by_city__this_year";
$Tables['label_played_by_station__this_year']             = "label_played_by_station__this_year";
$Tables['label_played_by_daytime__this_year']             = "label_played_by_daytime__this_year";
$Tables['label_played_by_daytime_continent__this_year']   = "label_played_by_daytime_continent__this_year";
$Tables['label_played_by_daytime_country__this_year']     = "label_played_by_daytime_country__this_year";
$Tables['label_played_by_daytime_subdivision__this_year'] = "label_played_by_daytime_subdivision__this_year";
$Tables['label_played_by_daytime_city__this_year']        = "label_played_by_daytime_city__this_year";
$Tables['label_played_by_daytime_station__this_year']     = "label_played_by_daytime_station__this_year";

$Tables['label_played_total__this_month']                  = "label_played_total__this_month";
$Tables['label_played_by_continent__this_month']           = "label_played_by_continent__this_month";
$Tables['label_played_by_country__this_month']             = "label_played_by_country__this_month";
$Tables['label_played_by_subdivision__this_month']         = "label_played_by_subdivision__this_month";
$Tables['label_played_by_city__this_month']                = "label_played_by_city__this_month";
$Tables['label_played_by_station__this_month']             = "label_played_by_station__this_month";
$Tables['label_played_by_daytime__this_month']             = "label_played_by_daytime__this_month";
$Tables['label_played_by_daytime_continent__this_month']   = "label_played_by_daytime_continent__this_month";
$Tables['label_played_by_daytime_country__this_month']     = "label_played_by_daytime_country__this_month";
$Tables['label_played_by_daytime_subdivision__this_month'] = "label_played_by_daytime_subdivision__this_month";
$Tables['label_played_by_daytime_city__this_month']        = "label_played_by_daytime_city__this_month";
$Tables['label_played_by_daytime_station__this_month']     = "label_played_by_daytime_station__this_month";


// by PUBLISHER
$Tables['publisher_played_total__last_7_days']                  = "publisher_played_total__last_7_days";
$Tables['publisher_played_by_continent__last_7_days']           = "publisher_played_by_continent__last_7_days";
$Tables['publisher_played_by_country__last_7_days']             = "publisher_played_by_country__last_7_days";
$Tables['publisher_played_by_subdivision__last_7_days']         = "publisher_played_by_subdivision__last_7_days";
$Tables['publisher_played_by_city__last_7_days']                = "publisher_played_by_city__last_7_days";
$Tables['publisher_played_by_station__last_7_days']             = "publisher_played_by_station__last_7_days";
$Tables['publisher_played_by_daytime__last_7_days']             = "publisher_played_by_daytime__last_7_days";
$Tables['publisher_played_by_daytime_continent__last_7_days']   = "publisher_played_by_daytime_continent__last_7_days";
$Tables['publisher_played_by_daytime_country__last_7_days']     = "publisher_played_by_daytime_country__last_7_days";
$Tables['publisher_played_by_daytime_subdivision__last_7_days'] = "publisher_played_by_daytime_subdivision__last_7_days";
$Tables['publisher_played_by_daytime_city__last_7_days']        = "publisher_played_by_daytime_city__last_7_days";
$Tables['publisher_played_by_daytime_station__last_7_days']     = "publisher_played_by_daytime_station__last_7_days";

$Tables['publisher_played_total__last_30_days']                  = "publisher_played_total__last_30_days";
$Tables['publisher_played_by_continent__last_30_days']           = "publisher_played_by_continent__last_30_days";
$Tables['publisher_played_by_country__last_30_days']             = "publisher_played_by_country__last_30_days";
$Tables['publisher_played_by_subdivision__last_30_days']         = "publisher_played_by_subdivision__last_30_days";
$Tables['publisher_played_by_city__last_30_days']                = "publisher_played_by_city__last_30_days";
$Tables['publisher_played_by_station__last_30_days']             = "publisher_played_by_station__last_30_days";
$Tables['publisher_played_by_daytime__last_30_days']             = "publisher_played_by_daytime__last_30_days";
$Tables['publisher_played_by_daytime_continent__last_30_days']   = "publisher_played_by_daytime_continent__last_30_days";
$Tables['publisher_played_by_daytime_country__last_30_days']     = "publisher_played_by_daytime_country__last_30_days";
$Tables['publisher_played_by_daytime_subdivision__last_30_days'] = "publisher_played_by_daytime_subdivision__last_30_days";
$Tables['publisher_played_by_daytime_city__last_30_days']        = "publisher_played_by_daytime_city__last_30_days";
$Tables['publisher_played_by_daytime_station__last_30_days']     = "publisher_played_by_daytime_station__last_30_days";

$Tables['publisher_played_total__last_365_days']                  = "publisher_played_total__last_365_days";
$Tables['publisher_played_by_continent__last_365_days']           = "publisher_played_by_continent__last_365_days";
$Tables['publisher_played_by_country__last_365_days']             = "publisher_played_by_country__last_365_days";
$Tables['publisher_played_by_subdivision__last_365_days']         = "publisher_played_by_subdivision__last_365_days";
$Tables['publisher_played_by_city__last_365_days']                = "publisher_played_by_city__last_365_days";
$Tables['publisher_played_by_station__last_365_days']             = "publisher_played_by_station__last_365_days";
$Tables['publisher_played_by_daytime__last_365_days']             = "publisher_played_by_daytime__last_365_days";
$Tables['publisher_played_by_daytime_continent__last_365_days']   = "publisher_played_by_daytime_continent__last_365_days";
$Tables['publisher_played_by_daytime_country__last_365_days']     = "publisher_played_by_daytime_country__last_365_days";
$Tables['publisher_played_by_daytime_subdivision__last_365_days'] = "publisher_played_by_daytime_subdivision__last_365_days";
$Tables['publisher_played_by_daytime_city__last_365_days']        = "publisher_played_by_daytime_city__last_365_days";
$Tables['publisher_played_by_daytime_station__last_365_days']     = "publisher_played_by_daytime_station__last_365_days";

$Tables['publisher_played_total__last_year']                  = "publisher_played_total__last_year";
$Tables['publisher_played_by_continent__last_year']           = "publisher_played_by_continent__last_year";
$Tables['publisher_played_by_country__last_year']             = "publisher_played_by_country__last_year";
$Tables['publisher_played_by_subdivision__last_year']         = "publisher_played_by_subdivision__last_year";
$Tables['publisher_played_by_city__last_year']                = "publisher_played_by_city__last_year";
$Tables['publisher_played_by_station__last_year']             = "publisher_played_by_station__last_year";
$Tables['publisher_played_by_daytime__last_year']             = "publisher_played_by_daytime__last_year";
$Tables['publisher_played_by_daytime_continent__last_year']   = "publisher_played_by_daytime_continent__last_year";
$Tables['publisher_played_by_daytime_country__last_year']     = "publisher_played_by_daytime_country__last_year";
$Tables['publisher_played_by_daytime_subdivision__last_year'] = "publisher_played_by_daytime_subdivision__last_year";
$Tables['publisher_played_by_daytime_city__last_year']        = "publisher_played_by_daytime_city__last_year";
$Tables['publisher_played_by_daytime_station__last_year']     = "publisher_played_by_daytime_station__last_year";

$Tables['publisher_played_total__last_month']                  = "publisher_played_total__last_month";
$Tables['publisher_played_by_continent__last_month']           = "publisher_played_by_continent__last_month";
$Tables['publisher_played_by_country__last_month']             = "publisher_played_by_country__last_month";
$Tables['publisher_played_by_subdivision__last_month']         = "publisher_played_by_subdivision__last_month";
$Tables['publisher_played_by_city__last_month']                = "publisher_played_by_city__last_month";
$Tables['publisher_played_by_station__last_month']             = "publisher_played_by_station__last_month";
$Tables['publisher_played_by_daytime__last_month']             = "publisher_played_by_daytime__last_month";
$Tables['publisher_played_by_daytime_continent__last_month']   = "publisher_played_by_daytime_continent__last_month";
$Tables['publisher_played_by_daytime_country__last_month']     = "publisher_played_by_daytime_country__last_month";
$Tables['publisher_played_by_daytime_subdivision__last_month'] = "publisher_played_by_daytime_subdivision__last_month";
$Tables['publisher_played_by_daytime_city__last_month']        = "publisher_played_by_daytime_city__last_month";
$Tables['publisher_played_by_daytime_station__last_month']     = "publisher_played_by_daytime_station__last_month";

$Tables['publisher_played_total__last_week']                  = "publisher_played_total__last_week";
$Tables['publisher_played_by_continent__last_week']           = "publisher_played_by_continent__last_week";
$Tables['publisher_played_by_country__last_week']             = "publisher_played_by_country__last_week";
$Tables['publisher_played_by_subdivision__last_week']         = "publisher_played_by_subdivision__last_week";
$Tables['publisher_played_by_city__last_week']                = "publisher_played_by_city__last_week";
$Tables['publisher_played_by_station__last_week']             = "publisher_played_by_station__last_week";
$Tables['publisher_played_by_daytime__last_week']             = "publisher_played_by_daytime__last_week";
$Tables['publisher_played_by_daytime_continent__last_week']   = "publisher_played_by_daytime_continent__last_week";
$Tables['publisher_played_by_daytime_country__last_week']     = "publisher_played_by_daytime_country__last_week";
$Tables['publisher_played_by_daytime_subdivision__last_week'] = "publisher_played_by_daytime_subdivision__last_week";
$Tables['publisher_played_by_daytime_city__last_week']        = "publisher_played_by_daytime_city__last_week";
$Tables['publisher_played_by_daytime_station__last_week']     = "publisher_played_by_daytime_station__last_week";

$Tables['publisher_played_total__this_year']                  = "publisher_played_total__this_year";
$Tables['publisher_played_by_continent__this_year']           = "publisher_played_by_continent__this_year";
$Tables['publisher_played_by_country__this_year']             = "publisher_played_by_country__this_year";
$Tables['publisher_played_by_subdivision__this_year']         = "publisher_played_by_subdivision__this_year";
$Tables['publisher_played_by_city__this_year']                = "publisher_played_by_city__this_year";
$Tables['publisher_played_by_station__this_year']             = "publisher_played_by_station__this_year";
$Tables['publisher_played_by_daytime__this_year']             = "publisher_played_by_daytime__this_year";
$Tables['publisher_played_by_daytime_continent__this_year']   = "publisher_played_by_daytime_continent__this_year";
$Tables['publisher_played_by_daytime_country__this_year']     = "publisher_played_by_daytime_country__this_year";
$Tables['publisher_played_by_daytime_subdivision__this_year'] = "publisher_played_by_daytime_subdivision__this_year";
$Tables['publisher_played_by_daytime_city__this_year']        = "publisher_played_by_daytime_city__this_year";
$Tables['publisher_played_by_daytime_station__this_year']     = "publisher_played_by_daytime_station__this_year";

$Tables['publisher_played_total__this_month']                  = "publisher_played_total__this_month";
$Tables['publisher_played_by_continent__this_month']           = "publisher_played_by_continent__this_month";
$Tables['publisher_played_by_country__this_month']             = "publisher_played_by_country__this_month";
$Tables['publisher_played_by_subdivision__this_month']         = "publisher_played_by_subdivision__this_month";
$Tables['publisher_played_by_city__this_month']                = "publisher_played_by_city__this_month";
$Tables['publisher_played_by_station__this_month']             = "publisher_played_by_station__this_month";
$Tables['publisher_played_by_daytime__this_month']             = "publisher_played_by_daytime__this_month";
$Tables['publisher_played_by_daytime_continent__this_month']   = "publisher_played_by_daytime_continent__this_month";
$Tables['publisher_played_by_daytime_country__this_month']     = "publisher_played_by_daytime_country__this_month";
$Tables['publisher_played_by_daytime_subdivision__this_month'] = "publisher_played_by_daytime_subdivision__this_month";
$Tables['publisher_played_by_daytime_city__this_month']        = "publisher_played_by_daytime_city__this_month";
$Tables['publisher_played_by_daytime_station__this_month']     = "publisher_played_by_daytime_station__this_month";
?>