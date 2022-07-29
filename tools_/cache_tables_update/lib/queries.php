<?PHP



//----------------------------
function cug_rms_query_get_distinct_track_ids($table, $start_time=0, $end_time=0) {
    global $Tables;
    
    $query = "SELECT DISTINCT(r.cugate_track_id) FROM $table AS r ";
    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
    $query .= "WHERE r.cugate_track_id IS NOT NULL ";
    $query .= "AND duration>0 ";
    $query .= ($start_time) ? "AND played_date >= $start_time " : "";
    $query .= ($end_time) ? "AND played_date < $end_time" : "";    
    //$query .= ($start_time) ? "AND FROM_UNIXTIME(played_date / 1000, '%Y-%m-%d') >= '$start_time' " : "";
    //$query .= ($end_time) ? "AND FROM_UNIXTIME(played_date / 1000, '%Y-%m-%d') < '$end_time'" : "";
    
    return $query;
}


//----------------------------
function cug_rms_query_get_distinct_member_ids($table, $start_time=0, $end_time=0) {
    global $Tables;

    $query = "SELECT DISTINCT(r.cugate_member_id) FROM $table AS r ";
    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
    $query .= "WHERE r.cugate_member_id IS NOT NULL ";
    $query .= "AND duration>0 ";
    $query .= ($start_time) ? "AND played_date >= $start_time " : "";
    $query .= ($end_time) ? "AND played_date < $end_time" : "";

    return $query;
}


//----------------------------
function cug_rms_query_get_distinct_label_ids($table, $start_time=0, $end_time=0) {
    global $Tables;

    $query = "SELECT DISTINCT(r.cugate_label_id) FROM $table AS r ";
    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
    $query .= "WHERE r.cugate_label_id IS NOT NULL AND r.cugate_label_id<>0 ";
    $query .= "AND duration>0 ";
    $query .= ($start_time) ? "AND played_date >= $start_time " : "";
    $query .= ($end_time) ? "AND played_date < $end_time" : "";

    return $query;
}

//----------------------------
function cug_rms_query_get_distinct_publisher_ids($table, $start_time=0, $end_time=0) {
    global $Tables;

    $query = "SELECT DISTINCT(r.cugate_publisher_id) FROM $table AS r ";
    $query .= "INNER JOIN {$Tables['station']} AS s ON r.station_id=s.id ";
    $query .= "WHERE r.cugate_publisher_id IS NOT NULL AND r.cugate_publisher_id<>0 ";
    $query .= "AND duration>0 ";
    $query .= ($start_time) ? "AND played_date >= $start_time " : "";
    $query .= ($end_time) ? "AND played_date < $end_time" : "";

    return $query;
}

//create table query array
//**************************************************
$RMS_QUERY_MAKE_TABLE = array();


// 'stations'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id int(11) NOT NULL, ";
$query .= "station_name varchar(50) NOT NULL, ";
$query .= "country_name varchar(50) NOT NULL, ";
$query .= "country_code char(2) DEFAULT NULL, ";
$query .= "city varchar(50) NOT NULL, ";
$query .= "subdivision_code char(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(50) DEFAULT NULL, ";
$query .= "continent_code char(2) DEFAULT NULL, ";
$query .= "continent_name varchar(13) DEFAULT NULL, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY country_code (country_code), ";
$query .= "KEY subdivision_code (subdivision_code), ";
$query .= "KEY continent_code (continent_code), ";
$query .= "KEY city (city) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['stations'] = $query;


// -- stat data tables --
//---------------------------
// 'stat_data'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL, ";
$query .= "station_id int(11) NOT NULL, ";
$query .= "played_date bigint(20) NOT NULL, ";
$query .= "member varchar(255) DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) DEFAULT NULL, ";
$query .= "cugate_member_id bigint(20) DEFAULT NULL, ";
$query .= "track varchar(255) DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) DEFAULT NULL, ";
$query .= "cugate_track_id bigint(20) DEFAULT NULL, ";
$query .= "mood_id int(11) DEFAULT NULL, ";
$query .= "genre_id int(11) DEFAULT NULL, ";
$query .= "cugate_genre_id int(11) DEFAULT NULL, ";
$query .= "cugate_subgenre_id int(11) DEFAULT NULL, ";
$query .= "duration int(11) DEFAULT NULL, ";
$query .= "cugate_label_id int(11) DEFAULT NULL, ";
$query .= "shenzhen_label_id int(11) DEFAULT NULL, ";
$query .= "cugate_publisher_id int(11) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id int(11) DEFAULT NULL, ";
$query .= "played_time time DEFAULT NULL, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY played_date (played_date), ";
$query .= "KEY station_id (station_id), ";
$query .= "KEY mood_id (mood_id), ";
$query .= "KEY genre_id (genre_id), ";
$query .= "KEY cugate_genre_id (cugate_genre_id), ";
$query .= "KEY cugate_subgenre_id (cugate_subgenre_id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY played_time (played_time) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['stat_data'] = $query;


// -- cache tables --
//---------------------------
// BY TRACK
//---------------------------
// 'track_played_total'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_total'] = $query;


// 'track_played_by_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY continent_code (continent_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_continent'] = $query;


// 'track_played_by_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY country_code (country_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_country'] = $query;


// 'track_played_by_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY subdivision_code (subdivision_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_subdivision'] = $query;


// 'track_played_by_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY city (city) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_city'] = $query;


// 'track_played_by_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY station_id (station_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_station'] = $query;


// 'track_played_by_daytime'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";

    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
    }
    //------------------------
    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
    }
    //------------------------
    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
    }    

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime'] = $query;



// 'track_played_by_daytime_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY continent_code (continent_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime_continent'] = $query;



// 'track_played_by_daytime_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY country_code (country_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime_country'] = $query;


// 'track_played_by_daytime_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY subdivision_code (subdivision_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime_subdivision'] = $query;


// 'track_played_by_daytime_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY city (city), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime_city'] = $query;


// 'track_played_by_daytime_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_track_id (cugate_track_id), ";
$query .= "KEY shenzhen_track_id (shenzhen_track_id), ";
$query .= "KEY station_id (station_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_daytime_station'] = $query;


// 'track_played_by_artist'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "cugate_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_track_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['track_played_by_artist'] = $query;







// BY ARTIST
//---------------------------
// 'artist_played_total'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_total'] = $query;


// 'artist_played_by_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY continent_code (continent_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_continent'] = $query;


// 'artist_played_by_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY country_code (country_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_country'] = $query;


// 'artist_played_by_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY subdivision_code (subdivision_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_subdivision'] = $query;


// 'artist_played_by_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY city (city) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_city'] = $query;


// 'artist_played_by_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY station_id (station_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_station'] = $query;


// 'artist_played_by_daytime'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";

    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
    }
    //------------------------
    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
    }
    //------------------------
    for($i=0; $i<24; $i++) {
        $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
    }    

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime'] = $query;



// 'artist_played_by_daytime_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY continent_code (continent_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime_continent'] = $query;



// 'artist_played_by_daytime_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY country_code (country_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime_country'] = $query;


// 'artist_played_by_daytime_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY subdivision_code (subdivision_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime_subdivision'] = $query;


// 'artist_played_by_daytime_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY city (city), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime_city'] = $query;


// 'artist_played_by_daytime_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "shenzhen_member_id bigint(20) unsigned DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_member_id (cugate_member_id), ";
$query .= "KEY shenzhen_member_id (shenzhen_member_id), ";
$query .= "KEY station_id (station_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['artist_played_by_daytime_station'] = $query;



//---------------------------
// BY LABEL
//---------------------------
// 'label_played_total'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_total'] = $query;


// 'label_played_by_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY continent_code (continent_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_continent'] = $query;


// 'label_played_by_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY country_code (country_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_country'] = $query;


// 'label_played_by_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY subdivision_code (subdivision_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_subdivision'] = $query;


// 'label_played_by_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY city (city) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_city'] = $query;


// 'label_played_by_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY station_id (station_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_station'] = $query;


// 'label_played_by_daytime'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime'] = $query;


// 'label_played_by_daytime_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY continent_code (continent_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime_continent'] = $query;


// 'label_played_by_daytime_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY country_code (country_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime_country'] = $query;


// 'label_played_by_daytime_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY subdivision_code (subdivision_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime_subdivision'] = $query;


// 'label_played_by_daytime_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY city (city), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime_city'] = $query;


// 'label_played_by_daytime_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_label_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_label_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_label_id (cugate_label_id), ";
$query .= "KEY shenzhen_label_id (shenzhen_label_id), ";
$query .= "KEY station_id (station_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['label_played_by_daytime_station'] = $query;



//---------------------------
// BY PUBLISHER
//---------------------------
// 'publisher_played_total'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_total'] = $query;


// 'publisher_played_by_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY continent_code (continent_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_continent'] = $query;


// 'publisher_played_by_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY country_code (country_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_country'] = $query;


// 'publisher_played_by_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY subdivision_code (subdivision_code) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_subdivision'] = $query;


// 'publisher_played_by_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY city (city) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_city'] = $query;


// 'publisher_played_by_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";
$query .= "played_num bigint(20) unsigned DEFAULT NULL, ";
$query .= "rank_num bigint(20) DEFAULT NULL, ";
$query .= "airtime bigint(20) DEFAULT NULL COMMENT 'Total played time in air, based on ''played_num'' and duration of the track (in seconds)', ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY station_id (station_id) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_station'] = $query;


// 'publisher_played_by_daytime'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime'] = $query;


// 'publisher_played_by_daytime_continent'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "continent_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY continent_code (continent_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime_continent'] = $query;


// 'publisher_played_by_daytime_country'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "country_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY country_code (country_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime_country'] = $query;


// 'publisher_played_by_daytime_subdivision'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "subdivision_name varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY subdivision_code (subdivision_code), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime_subdivision'] = $query;


// 'publisher_played_by_daytime_city'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY city (city), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime_city'] = $query;


// 'publisher_played_by_daytime_station'
////////////////////////////////////////////////////////
$query = "(";
$query .= "id bigint(20) unsigned NOT NULL AUTO_INCREMENT, ";
$query .= "cugate_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "shenzhen_publisher_id bigint(20) DEFAULT NULL, ";
$query .= "continent_code varchar(2) DEFAULT NULL, ";
$query .= "country_code varchar(2) DEFAULT NULL, ";
$query .= "subdivision_code varchar(3) DEFAULT NULL, ";
$query .= "city varchar(64) DEFAULT NULL, ";
$query .= "station_id bigint(20) DEFAULT NULL, ";

for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "played_num_0$i bigint(20) DEFAULT NULL, " : "played_num_$i bigint(20) DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "percent_0$i float DEFAULT NULL, " : "percent_$i float DEFAULT NULL, ";
}
//------------------------
for($i=0; $i<24; $i++) {
    $query .= ($i < 10) ? "airtime_0$i bigint DEFAULT NULL, " : "airtime_$i bigint DEFAULT NULL, ";
}

$query .= "played_num_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "airtime_total bigint(20) unsigned DEFAULT NULL, ";
$query .= "update_time timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, ";
$query .= "PRIMARY KEY (id), ";
$query .= "KEY cugate_publisher_id (cugate_publisher_id), ";
$query .= "KEY shenzhen_publisher_id (shenzhen_publisher_id), ";
$query .= "KEY station_id (station_id), ";
$query .= "KEY played_num_total (played_num_total) ";
$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";

$RMS_QUERY_MAKE_TABLE['publisher_played_by_daytime_station'] = $query;
?>