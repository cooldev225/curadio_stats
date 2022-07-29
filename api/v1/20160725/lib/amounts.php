<?PHP
/**
 * Get Amounts
 * 
 * @param int $cugate_track_id
 * @param int $shenzhen_track_id
 * @param string $time_period
 * @param bool $is_daytime (calculate amounts from daytime table or not)
 * @param bool $amount_sum (calculate summirized amounts or not summarized)
 * @param string $object ('artist' or 'composer')
 * @param string $country_code
 * @param string $subdivision_code
 * @param string $city
 * @param int $station_id
 * @return array
 */
function cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime, $amount_sum, $object, $country_code, $subdivision_code="", $city="", $station_id=0) {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    $result = array();
    
    //get Track ID Info
    $track_id_info = cug_rms_stat_get_track_id_info($cugate_track_id, $shenzhen_track_id);
    if($track_id_info['track_id'] == 0)
        return $ERRORS['NO_TRACK_ID'];
    else {
        $track_id = $track_id_info['track_id'];
        $track_id_field = $track_id_info['track_id_field'];
    }
    
    //define dates for prices and coefficients
    $arr = cug_rms_get_time_periods($time_period);
    $start_date = $arr['start_date'];
    $end_date = $arr['end_date'];
    
    $start_year = date("Y", strtotime($start_date));
    $end_year = date("Y", strtotime($end_date));
    $max_year = max($start_year, $end_year);
    
    $start_date = $max_year."-01-01";
    $end_date = $max_year."-12-31";
    
    //define cache table
    $index = ($is_daytime) ? "track_played_by_daytime_station__" . strtolower($time_period) : "track_played_by_station__" . strtolower($time_period);
    $cache_table = $Tables[$index];
    
    //define price table
    $index = "amount_price_" . strtolower($object);
    $price_table = $Tables[$index];

    //generate SQL query
    $query = cug_rms_stat_gen_query_amount($track_id_field, $track_id, $start_date, $end_date, $cache_table, $price_table, $is_daytime, $amount_sum, $object, $country_code, $subdivision_code, $city, $station_id);
    
    //echo PHP_EOL.$query.PHP_EOL;
    
    //execute query
    $r = $mysqli_rms_cache->query($query);
    if($r && $r->num_rows) {
        $result = $r->fetch_assoc();
    }
    
    return $result;
}


/**
 * Generate amount calculation query
 * 
 * @param string $track_id_field
 * @param int $track_id
 * @param string $start_date (start valid date of prices and coefficients)
 * @param string $end_date (end valid date of prices and coefficients)
 * @param string $cache_table
 * @param string $price_table
 * @param bool $is_daytime (calculate amounts from daytime table or not)
 * @param bool $amount_sum (calculate summirized amounts or not summarized)
 * @param string $object ('artist' or 'composer')
 * @param string $country_code
 * @param string $subdivision_code
 * @param string $city
 * @param int $station_id
 * @return string
 */
function cug_rms_stat_gen_query_amount($track_id_field, $track_id, $start_date, $end_date, $cache_table, $price_table, $is_daytime, $amount_sum, $object, $country_code, $subdivision_code="", $city="", $station_id=0) {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    
    //generate SQL query
    $query_start = "SELECT ";
    
    //fields
    $query_fields = "p.currency_code, ";
    if($is_daytime) {
        for($i=0; $i<24; $i++) {
            $airtime_field = ($i < 10) ? "r.airtime_0$i" : "r.airtime_$i";
            $amount_field = ($i < 10) ? "amount_0$i" : "amount_$i";
            
            $query_formula = cug_rms_stat_gen_query_formula($object, $country_code, $airtime_field);
            $query_formula = ($amount_sum) ? "SUM((".$query_formula."))" : "(".$query_formula.")";
            $query_fields .= $query_formula." AS $amount_field,";
        }
        $query_fields = rtrim($query_fields, ",")." ";
    }
    else {
        $query_formula = cug_rms_stat_gen_query_formula($object, $country_code, "r.airtime");
        $query_formula = ($amount_sum) ? "SUM((".$query_formula."))" : "(".$query_formula.")";
        $query_fields .= $query_formula." AS amount ";
    }
    
    
    //from
    $query_from = "FROM $cache_table AS r ";
    
    //joins
    $query_joins = "LEFT JOIN $price_table AS p ON r.country_code=p.country_code AND p.start_date <= '$start_date' AND p.end_date >= '$end_date' ";
    $query_joins .= "LEFT JOIN {$Tables['amount_coefficient']} AS c ON r.station_id=c.station_id AND c.start_date <= '$start_date' AND c.end_date >= '$end_date' ";
    
    //where
    $query_where = "WHERE r.$track_id_field=$track_id AND r.country_code='$country_code' ";
    $query_where .= ($subdivision_code) ? "AND r.subdivision_code='$subdivision_code' " : "";
    $query_where .= ($city) ? "AND r.city='$city' " : "";
    $query_where .= ($station_id > 0) ? "AND r.station_id=$station_id " : "";
    
    //query
    $query = $query_start . $query_fields . $query_from . $query_joins . $query_where;
    
    return $query;
}



/**
 * Generate calculation formula for SQL query
 * 
 * @param string $object ('artist' or 'composer')
 * @param string $country_code
 * @param string $airtime_field
 * @return string
 */
function cug_rms_stat_gen_query_formula($object, $country_code, $airtime_field) {
    global $AMOUNT_FORMULA;
    $result = "";
    
    switch($country_code) {
        case 'DE':
            $variables = array(
                '{culture_factor}' => 'c.culture_factor',
                '{broadcaster_coefficient}' => 'c.broadcaster_coefficient',
                '{airtime}' => $airtime_field,
                '{time_interval_sec}' => 'p.time_interval_sec',
                '{amount}' => 'p.amount'
            );
            
        break;
        //-------------------------------
        default:
            $variables = array();
        break;
    }
    
    //-----------------------
    if(count($variables) > 0)
        $result = strtr($AMOUNT_FORMULA[$object][$country_code], $variables);
    
        
    return $result;
}

?>