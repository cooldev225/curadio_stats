<?PHP

/**
 * Get Cugate RMS Area List (Continent->Country->Subdivision->City->Station)
 * 
 * @return array|number
 */
function cug_rms_stat_get_area_list() {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    $result = array();
    
    //get unique continents
    $query = "SELECT DISTINCT(continent_code), continent_name FROM {$Tables['station']} ORDER BY continent_code";
    $r = $mysqli_rms_cache->query($query);
    
    if($r && $r->num_rows) {
        $continent_index = 0;
        
        while($row = $r->fetch_assoc()) {
            $continent_code = $row['continent_code'];
            $continent_name = $row['continent_name'];
            
            $result[$continent_index][0] = $continent_code;
            $result[$continent_index][1] = $continent_name;
            $result[$continent_index][2] = array();
            
            //get unique countries in current continent
            $query = "SELECT DISTINCT(country_code), country_name FROM {$Tables['station']} WHERE continent_code='$continent_code' ORDER BY country_code";
            $r1 = $mysqli_rms_cache->query($query);
            
            if($r1 && $r1->num_rows) {
                $country_index = 0;
                
                while($row1 = $r1->fetch_assoc()) {
                    $country_code = $row1['country_code'];
                    $country_name = $row1['country_name'];
                    
                    $result[$continent_index][2][$country_index][0] = $country_code;
                    $result[$continent_index][2][$country_index][1] = $country_name;
                    $result[$continent_index][2][$country_index][2] = array();
                    
                    //get unique subdivisions in current country
                    $query = "SELECT DISTINCT(subdivision_code), subdivision_name FROM {$Tables['station']} WHERE country_code='$country_code' ORDER BY subdivision_code";
                    $r2 = $mysqli_rms_cache->query($query);
                    
                    if($r2 && $r2->num_rows) {
                        $subdiv_index = 0;
                        
                        while($row2 = $r2->fetch_assoc()) {
                            $subdivision_code = $row2['subdivision_code'];
                            $subdivision_name = $row2['subdivision_name'];
                            
                            $result[$continent_index][2][$country_index][2][$subdiv_index][0] = $subdivision_code;
                            $result[$continent_index][2][$country_index][2][$subdiv_index][1] = $subdivision_name;
                            /*
                            $result[$continent_index][2][$country_index][2][$subdiv_index][2] = array();
                            
                            //get unique cities in current subdivision and country
                            $query = "SELECT DISTINCT(city) FROM {$Tables['station']} WHERE country_code='$country_code' AND subdivision_code='$subdivision_code' ORDER BY city";
                            $r3 = $mysqli_rms_cache->query($query);
                            
                            if($r3 && $r3->num_rows) {
                                $city_index = 0;
                                
                                while($row3 = $r3->fetch_assoc()) {
                                    $city = $row3['city'];
                                    
                                    $result[$continent_index][2][$country_index][2][$subdiv_index][2][$city_index][0] = $city;
                                    $result[$continent_index][2][$country_index][2][$subdiv_index][2][$city_index][1] = array();
                                    
                                    //get stations in current city, subdivision and country
                                    $query = "SELECT id, station_name FROM {$Tables['station']} WHERE country_code='$country_code' AND subdivision_code='$subdivision_code' AND city='".$mysqli_rms_cache->escape_str($city)."' ORDER BY city";
                                    $r4 = $mysqli_rms_cache->query($query);
                                    
                                    if($r4 && $r4->num_rows) {
                                        $station_index = 0;
                                        
                                        while($row4 = $r4->fetch_assoc()) {
                                            $station_id = $row4['id'];
                                            $station_name = $row4['station_name'];
                                            
                                            $result[$continent_index][2][$country_index][2][$subdiv_index][2][$city_index][1][$station_index][0] = $station_id;
                                            $result[$continent_index][2][$country_index][2][$subdiv_index][2][$city_index][1][$station_index][1] = $station_name;
                                            
                                            $station_index ++;
                                        }//while station
                                    }
                                    
                                    $city_index ++;
                                }//while city
                            }
                            */
                            $subdiv_index ++;
                        }//while subdivision
                    }
                    
                    $country_index ++;
                }//while country
            }
            
            $continent_index ++;
        }//while continent
    }
    else 
        return $ERRORS['NO_AREA_DATA'];
    
    return $result;
}


/**
 * Get Stations List
 *
 * @return array
 */
function cug_rms_stat_get_station_list() {
    global $mysqli_rms_cache, $Tables;
    $result = array();

    $query = "SELECT id, station_name FROM {$Tables['station']} ORDER BY id";
    $r = $mysqli_rms_cache->query($query);

    if($r && $r->num_rows) {
        $station_index = 0;

        while($row = $r->fetch_assoc()) {
            $station_id = $row['id'];
            $station_name = $row['station_name'];

            $result[$station_index][0] = $station_id;
            $result[$station_index][1] = $station_name;

            $station_index ++;
        }
    }
     
    return $result;
}


/**
 * Get Statistical Data by Track ID
 * 
 * @param int $cugate_track_id
 * @param int $shenzhen_track_id
 * @param string $time_period
 * @param int $limit
 * @return array|number
 */
function cug_rms_stat_get_data_by_track($cugate_track_id, $shenzhen_track_id, $time_period, $limit) {
    global $mysqli_rms_cache, $ERRORS;
    $result = array();
    $results_count = 0;
    
    //check if it is time to access cache tables
    $status = cug_rms_stat_is_time_to_access_cache_tables($mysqli_rms_cache, "status");
    
    if(!$status)
        return $ERRORS['SERVER_IS_BUSY_TRY_LATER'];
    //---------------------------------------
    
        
    if($time_period) {
        $arr = cug_rms_stat_get_data_by_track_timeperiod($cugate_track_id, $shenzhen_track_id, $time_period, $limit);
        
        if($arr > 0) {
            $result[$time_period] = $arr;
            $results_count ++;
        }
    }
    else {
        $time_periods = array();
        $time_periods[0] = "last_7_days";
        $time_periods[1] = "last_week";
        $time_periods[2] = "last_month";
        
        foreach($time_periods as $val) {
            $arr = cug_rms_stat_get_data_by_track_timeperiod($cugate_track_id, $shenzhen_track_id, $val, $limit);
            
            if($arr > 0) {
                $result[$val] = $arr;
                $results_count ++;
            }
        }
    }
    //-----------------------
    
    if($results_count > 0)
        return $result;
    else
        return $arr;    
}


/**
 * Get Statistical Data by Track ID and Time Period
 * 
 * @param int $cugate_track_id
 * @param int $shenzhen_track_id
 * @param string $time_period
 * @param int $limit
 * @return array|number
 */
function cug_rms_stat_get_data_by_track_timeperiod($cugate_track_id, $shenzhen_track_id, $time_period, $limit) {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    $result = array();
     
    $time_period = strtolower($time_period);

    //get Track ID Info
    $track_id_info = cug_rms_stat_get_track_id_info($cugate_track_id, $shenzhen_track_id);
    if($track_id_info['track_id'] == 0)
        return $ERRORS['NO_TRACK_ID'];
    else {
        $track_id = $track_id_info['track_id'];
        $track_id_field = $track_id_info['track_id_field'];
    }

    //track played total
    $table_index = "track_played_total__".$time_period;
    
    $played_num_total = 0;
    $query = "SELECT played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id";
    $r = $mysqli_rms_cache->query($query);
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $played_num_total = $row['played_num'];
        $rank_num_total = $row['rank_num'];
    }
    //---------------------------------
    if($played_num_total > 0) {
        $result['total'][0] = (int)$played_num_total;
        $result['total'][1] = (int)$rank_num_total;

        //track played by daytimes, totall
        $table_index = "track_played_by_daytime__".$time_period;       
           
        $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id";
        $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
        
        if(count($arr) > 0)
            $result['total']['daytime'] = $arr;

        
        //track played by continent
        $table_index = "track_played_by_continent__".$time_period;
        
        $query = "SELECT continent_code, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id ORDER BY played_num DESC LIMIT $limit";
        $r = $mysqli_rms_cache->query($query);
        
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $continent_code = $row['continent_code'];
                $played_num_continent = $row['played_num'];
                $rank_num_continent = $row['rank_num'];
                
                $result['continent'][$continent_code][0] = (int)$played_num_continent;
                $result['continent'][$continent_code][1] = (int)$rank_num_continent;
                
                //track played by daytime, continent
                $table_index = "track_played_by_daytime_continent__".$time_period;
                
                $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND continent_code='$continent_code'";
                $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
                
                if(count($arr) > 0)
                    $result['continent'][$continent_code]['daytime'] = $arr;
                
                //track played by country
                $table_index = "track_played_by_country__".$time_period;
                
                $query = "SELECT country_code, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND continent_code='$continent_code' ORDER BY played_num DESC LIMIT $limit";
                $r1 = $mysqli_rms_cache->query($query);
        
                if($r1 && $r1->num_rows) {
                    while($row1 = $r1->fetch_assoc()) {
                        $country_code = $row1['country_code'];
                        $played_num_country = $row1['played_num'];
                        $rank_num_country = $row1['rank_num'];
        
                        $result['continent'][$continent_code]['country'][$country_code][0] = (int)$played_num_country;
                        $result['continent'][$continent_code]['country'][$country_code][1] = (int)$rank_num_country;
        
                        //track played by daytime, country
                        $table_index = "track_played_by_daytime_country__".$time_period;
                        $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code'";
                        $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
                        
                        if(count($arr) > 0)
                            $result['continent'][$continent_code]['country'][$country_code]['daytime'] = $arr;
                        
                        
                        //AMOUNTS - Composer 
                        $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="composer", $country_code, $subdivision_code="", $city="", $station_id=0);
                        if(count($amounts) > 0) {
                            $str_daytime = $amounts['currency_code'].",";
                            $str_total = $amounts['currency_code'].",";
                            $total_amount = 0;
                        
                            for($i=0; $i<24; $i++) {
                                $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                
                                $total_amount += $amount;
                            }
                            $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                            $str_total .= $total_amount;
                        
                            if($total_amount > 0) {
                                $result['continent'][$continent_code]['country'][$country_code]['amount_composer'] = $str_total;
                                $result['continent'][$continent_code]['country'][$country_code]['amount_composer_daytime'] = $str_daytime;
                            }
                        }
                        //-----------------------------
                        
                        //AMOUNTS - Artist
                        $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="artist", $country_code, $subdivision_code="", $city="", $station_id=0);
                        if(count($amounts) > 0) {
                            $str_daytime = $amounts['currency_code'].",";
                            $str_total = $amounts['currency_code'].",";
                            $total_amount = 0;
                        
                            for($i=0; $i<24; $i++) {
                                $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                $str_daytime .= ($amount > 0) ? $amount."," : ",";
                        
                                $total_amount += $amount;
                            }
                            $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                            $str_total .= $total_amount;
                        
                            if($total_amount > 0) {
                                $result['continent'][$continent_code]['country'][$country_code]['amount_artist'] = $str_total;
                                $result['continent'][$continent_code]['country'][$country_code]['amount_artist_daytime'] = $str_daytime;
                            }
                        }
                        //-----------------------------                        
                        
                        
                        //track played by subdivision
                        $table_index = "track_played_by_subdivision__".$time_period;
                        $query = "SELECT subdivision_code, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' ORDER BY played_num DESC LIMIT $limit";
                        $r2 = $mysqli_rms_cache->query($query);
        
                        if($r2 && $r2->num_rows) {
                            while($row2 = $r2->fetch_assoc()) {
                                $subdivision_code = $row2['subdivision_code'];
                                $played_num_subdivision = $row2['played_num'];
                                $rank_num_subdivision = $row2['rank_num'];
        
                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code][0] = (int)$played_num_subdivision;
                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code][1] = (int)$rank_num_subdivision;
        
                                //track played by daytime, subdivision
                                $table_index = "track_played_by_daytime_subdivision__".$time_period;
                                $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND subdivision_code='$subdivision_code' AND country_code='$country_code'";
                                $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
                                
                                if(count($arr) > 0)
                                    $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['daytime'] = $arr;
                                
                                
                                //AMOUNTS - Compsoer
                                $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="composer", $country_code, $subdivision_code, $city="", $station_id=0);
                                if(count($amounts) > 0) {
                                    $str_daytime = $amounts['currency_code'].",";
                                    $str_total = $amounts['currency_code'].",";
                                    $total_amount = 0;
                                
                                    for($i=0; $i<24; $i++) {
                                        $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                        $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                        $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                
                                        $total_amount += $amount;
                                    }
                                    $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                    $str_total .= $total_amount;
                                
                                    if($total_amount > 0) {
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['amount_composer'] = $str_total;
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['amount_composer_daytime'] = $str_daytime;
                                    }
                                }
                                //-----------------------------   
                                
                                //AMOUNTS - Artist
                                $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="artist", $country_code, $subdivision_code, $city="", $station_id=0);
                                if(count($amounts) > 0) {
                                    $str_daytime = $amounts['currency_code'].",";
                                    $str_total = $amounts['currency_code'].",";
                                    $total_amount = 0;
                                
                                    for($i=0; $i<24; $i++) {
                                        $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                        $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                        $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                
                                        $total_amount += $amount;
                                    }
                                    $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                    $str_total .= $total_amount;
                                
                                    if($total_amount > 0) {
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['amount_artist'] = $str_total;
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['amount_artist_daytime'] = $str_daytime;
                                    }
                                }
                                //-----------------------------                                
                                
                                    
                                //track played by city
                                $table_index = "track_played_by_city__".$time_period;
                                $query = "SELECT city, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' ORDER BY played_num DESC LIMIT $limit";
                                $r3 = $mysqli_rms_cache->query($query);
        
                                if($r3 && $r3->num_rows) {
                                    while($row3 = $r3->fetch_assoc()) {
                                        $city = $row3['city'];
                                        $played_num_city = $row3['played_num'];
                                        $rank_num_city = $row3['rank_num'];
        
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city][0] = (int)$played_num_city;
                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city][1] = (int)$rank_num_city;
        
                                        //track played by daytime, city
                                        $table_index = "track_played_by_daytime_city__".$time_period;
                                        $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND city='".$mysqli_rms_cache->escape_str($city)."' AND country_code='$country_code' AND subdivision_code='$subdivision_code'";
                                        $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
                                        
                                        if(count($arr) > 0)
                                            $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['daytime'] = $arr;
                                        
                                                                               
                                        //AMOUNTS - Composer
                                        $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="composer", $country_code, $subdivision_code, $city, $station_id=0);
                                        if(count($amounts) > 0) {
                                            $str_daytime = $amounts['currency_code'].",";
                                            $str_total = $amounts['currency_code'].",";
                                            $total_amount = 0;
                                        
                                            for($i=0; $i<24; $i++) {
                                                $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                                $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                                $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                        
                                                $total_amount += $amount;
                                            }
                                            $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                            $str_total .= $total_amount;
                                        
                                            if($total_amount > 0) {
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['amount_composer'] = $str_total;
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['amount_composer_daytime'] = $str_daytime;
                                            }
                                        }
                                        //-----------------------------
                                        
                                        //AMOUNTS - Artist
                                        $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="artist", $country_code, $subdivision_code, $city, $station_id=0);
                                        if(count($amounts) > 0) {
                                            $str_daytime = $amounts['currency_code'].",";
                                            $str_total = $amounts['currency_code'].",";
                                            $total_amount = 0;
                                        
                                            for($i=0; $i<24; $i++) {
                                                $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                                $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                                $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                        
                                                $total_amount += $amount;
                                            }
                                            $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                            $str_total .= $total_amount;
                                        
                                            if($total_amount > 0) {
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['amount_artist'] = $str_total;
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['amount_artist_daytime'] = $str_daytime;
                                            }
                                        }
                                        //-----------------------------                                        
                                                                                    
                                            
                                        //track played by stations
                                        $table_index = "track_played_by_station__".$time_period;
                                        $query = "SELECT station_id, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' AND city='".$mysqli_rms_cache->escape_str($city)."' ORDER BY played_num DESC LIMIT $limit";
                                        $r4 = $mysqli_rms_cache->query($query);
        
                                        if($r4 && $r4->num_rows) {
                                            while($row4 = $r4->fetch_assoc()) {
                                                $station_id = $row4['station_id'];
                                                $played_num_station = $row4['played_num'];
                                                $rank_num_station = $row4['rank_num'];
        
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id][0] = (int)$played_num_station;
                                                $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id][1] = (int)$rank_num_station;
                                                
                                                //track played by daytime, station
                                                $table_index = "track_played_by_daytime_station__".$time_period;
                                                $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND station_id=$station_id";
                                                $arr = cug_rms_stat_track_played_by_daytime($mysqli_rms_cache, $query);
                                                
                                                if(count($arr) > 0)
                                                    $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['daytime'] = $arr;
                                                
                                                    
                                                
                                                //AMOUNTS - Composer
                                                $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="composer", $country_code, $subdivision_code, $city, $station_id);
                                                if(count($amounts) > 0) {
                                                    $str_daytime = $amounts['currency_code'].",";
                                                    $str_total = $amounts['currency_code'].",";
                                                    $total_amount = 0;
                                                
                                                    for($i=0; $i<24; $i++) {
                                                        $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                                        $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                                        $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                                
                                                        $total_amount += $amount;
                                                    }
                                                    $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                                    $str_total .= $total_amount;
                                                
                                                    if($total_amount > 0) {
                                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['amount_composer'] = $str_total;
                                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['amount_composer_daytime'] = $str_daytime;
                                                    }
                                                }
                                                //-----------------------------   
                                                
                                                //AMOUNTS - Artist
                                                $amounts = cug_rms_stat_get_amounts($cugate_track_id, $shenzhen_track_id, $time_period, $is_daytime=true, $amount_sum=true, $object="artist", $country_code, $subdivision_code, $city, $station_id);
                                                if(count($amounts) > 0) {
                                                    $str_daytime = $amounts['currency_code'].",";
                                                    $str_total = $amounts['currency_code'].",";
                                                    $total_amount = 0;
                                                
                                                    for($i=0; $i<24; $i++) {
                                                        $index = ($i < 10) ? "amount_0$i" : "amount_$i";
                                                        $amount = ($amounts[$index] > 0) ? round($amounts[$index]) : 0;
                                                        $str_daytime .= ($amount > 0) ? $amount."," : ",";
                                                
                                                        $total_amount += $amount;
                                                    }
                                                    $str_daytime = substr($str_daytime, 0, strlen($str_daytime)-1);
                                                    $str_total .= $total_amount;
                                                
                                                    if($total_amount > 0) {
                                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['amount_artist'] = $str_total;
                                                        $result['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['amount_artist_daytime'] = $str_daytime;
                                                    }
                                                }
                                                //-----------------------------                                                
                                                                                                                                            
                                            }
                                        }
                                    }
                                }
        
                            }
                        }
                    }
                }
            }
        }
    }
    else
        return $ERRORS['NO_STAT_DATA'];


   return $result;
}




















function test_old($cugate_track_id, $shenzhen_track_id, $time_period, $limit) {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    $result = array();
   
    $time_period = strtolower($time_period);
    
    //get Track ID Info
    $track_id_info = cug_rms_stat_get_track_id_info($cugate_track_id, $shenzhen_track_id);
    if($track_id_info['track_id'] == 0)
        return $ERRORS['NO_TRACK_ID'];
    else {
        $track_id = $track_id_info['track_id'];
        $track_id_field = $track_id_info['track_id_field'];
    }
    
    //track played totall
    $played_num_total = 0;
    
    $table_index = "track_played_total__".$time_period;
    $query = "SELECT played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id";
    $r = $mysqli_rms_cache->query($query);
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
        $played_num_total = $row['played_num'];
        $rank_num_total = $row['rank_num'];
    }
    //---------------------------------
    if($played_num_total > 0) { 
        $result[$time_period] = array();
        $result[$time_period]['total']['played_num'] = $played_num_total;
        $result[$time_period]['total']['rank_num'] = $rank_num_total;
        
        //track played by country
        $table_index = "track_played_by_country__".$time_period;
        $query = "SELECT country_code, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id ORDER BY played_num DESC LIMIT $limit";
        $r = $mysqli_rms_cache->query($query);
        
        if($r && $r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $country_code = $row['country_code'];
                $played_num_country = $row['played_num'];
                $rank_num_country = $row['rank_num'];
                
                $result[$time_period]['country'][$country_code]['played_num'] = $played_num_country;
                $result[$time_period]['country'][$country_code]['rank_num'] = $rank_num_country;
                
                //track played by subdivision
                $table_index = "track_played_by_subdivision__".$time_period;
                $query = "SELECT subdivision_code, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' ORDER BY played_num DESC LIMIT $limit";
                $r1 = $mysqli_rms_cache->query($query);
                
                if($r1 && $r1->num_rows) {
                    while($row1 = $r1->fetch_assoc()) {
                        $subdivision_code = $row1['subdivision_code'];
                        $played_num_subdivision = $row1['played_num'];
                        $rank_num_subdivision = $row1['rank_num'];
                        
                        $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['played_num'] = $played_num_subdivision;
                        $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['rank_num'] = $rank_num_subdivision;
                        
                        //track played by city
                        $table_index = "track_played_by_city__".$time_period;
                        $query = "SELECT city, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' ORDER BY played_num DESC LIMIT $limit";
                        $r2 = $mysqli_rms_cache->query($query);
                        
                        if($r2 && $r2->num_rows) {
                            while($row2 = $r2->fetch_assoc()) {
                                $city = $row2['city'];
                                $played_num_city = $row2['played_num'];
                                $rank_num_city = $row2['rank_num'];
                                
                                $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['played_num'] = $played_num_city;
                                $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['rank_num'] = $rank_num_city;
                                
                                //track played by stations
                                $table_index = "track_played_by_station__".$time_period;
                                $query = "SELECT station_id, played_num, rank_num FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' AND city='".$mysqli_rms_cache->escape_str($city)."' ORDER BY played_num DESC LIMIT $limit";
                                $r3 = $mysqli_rms_cache->query($query);
                                
                                if($r3 && $r3->num_rows) {
                                    while($row3 = $r3->fetch_assoc()) {
                                        $station_id = $row3['station_id'];
                                        $played_num_station = $row3['played_num'];
                                        $rank_num_station = $row3['rank_num'];
                                        
                                        $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['played_num'] = $played_num_station;
                                        $result[$time_period]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['rank_num'] = $rank_num_station;
                                    }
                                }
                            }
                        }
                        
                    }
                }
            }
        }
        
        //*************************************************************************
        //*************************************************************************
        
        //track played by daytimes
        $table_index = "track_played_by_daytime__".$time_period;
        $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id";
        $r = $mysqli_rms_cache->query($query);
        if($r && $r->num_rows) {
            $row = $r->fetch_assoc();
            
            for($i=0; $i<24; $i++) {
                //define field names
                if($i<10) {
                    $played_num_field = "played_num_0$i";
                    $percent_field = "percent_0$i";
                }
                else {
                    $played_num_field = "played_num_$i";
                    $percent_field = "percent_$i";
                }
                //----------------------
                
                $played_num = $row[$played_num_field];
                $percent = $row[$percent_field];
                
                $result[$time_period]['daytime']['total']['played_num'][$i] = $played_num;
                $result[$time_period]['daytime']['total']['rank_num'][$i] = $percent;
            }
            
            //track played by daytime, continent
            $table_index = "track_played_by_daytime_continent__".$time_period;
            $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id ORDER BY played_num_00 DESC LIMIT $limit";
            $r1 = $mysqli_rms_cache->query($query);
            
            if($r1 && $r1->num_rows) {
                while($row1 = $r1->fetch_assoc()) {
                    $continent_code = $row1['continent_code'];

                    for($i=0; $i<24; $i++) {
                        //define field names
                        if($i<10) {
                            $played_num_field = "played_num_0$i";
                            $percent_field = "percent_0$i";
                        }
                        else {
                            $played_num_field = "played_num_$i";
                            $percent_field = "percent_$i";
                        }
                        //----------------------
                    
                        $played_num = $row1[$played_num_field];
                        $percent = $row1[$percent_field];
                    
                        $result[$time_period]['daytime']['continent'][$continent_code]['played_num'][$i] = $played_num;
                        $result[$time_period]['daytime']['continent'][$continent_code]['rank_num'][$i] = $percent;
                    }
                    
                    //track played by daytime, country
                    $table_index = "track_played_by_daytime_country__".$time_period;
                    $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND continent_code='$continent_code' ORDER BY played_num_00 DESC LIMIT $limit";
                    $r2 = $mysqli_rms_cache->query($query);
                    
                    if($r2 && $r2->num_rows) {
                        while($row2 = $r2->fetch_assoc()) {
                            $country_code = $row2['country_code'];
                            
                            for($i=0; $i<24; $i++) {
                                //define field names
                                if($i<10) {
                                    $played_num_field = "played_num_0$i";
                                    $percent_field = "percent_0$i";
                                }
                                else {
                                    $played_num_field = "played_num_$i";
                                    $percent_field = "percent_$i";
                                }
                                //----------------------
                    
                                $played_num = $row2[$played_num_field];
                                $percent = $row2[$percent_field];

                                $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['played_num'][$i] = $played_num;
                                $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['rank_num'][$i] = $percent;
                            }
                            
                            //track played by daytime, subdivision
                            $table_index = "track_played_by_daytime_subdivision__".$time_period;
                            $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' ORDER BY played_num_00 DESC LIMIT $limit";
                            $r3 = $mysqli_rms_cache->query($query);
                            
                            if($r3 && $r3->num_rows) {
                                while($row3 = $r3->fetch_assoc()) {
                                    $subdivision_code = $row3['subdivision_code'];
                            
                                    for($i=0; $i<24; $i++) {
                                        //define field names
                                        if($i<10) {
                                            $played_num_field = "played_num_0$i";
                                            $percent_field = "percent_0$i";
                                        }
                                        else {
                                            $played_num_field = "played_num_$i";
                                            $percent_field = "percent_$i";
                                        }
                                        //----------------------
                            
                                        $played_num = $row3[$played_num_field];
                                        $percent = $row3[$percent_field];
                            
                                        $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['played_num'][$i] = $played_num;
                                        $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['rank_num'][$i] = $percent;
                                    }
                                    
                                    //track played by daytime, city
                                    $table_index = "track_played_by_daytime_city__".$time_period;
                                    $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' ORDER BY played_num_00 DESC LIMIT $limit";
                                    $r4 = $mysqli_rms_cache->query($query);
                                    
                                    if($r4 && $r4->num_rows) {
                                        while($row4 = $r4->fetch_assoc()) {
                                            $city = $row4['city'];
                                    
                                            for($i=0; $i<24; $i++) {
                                                //define field names
                                                if($i<10) {
                                                    $played_num_field = "played_num_0$i";
                                                    $percent_field = "percent_0$i";
                                                }
                                                else {
                                                    $played_num_field = "played_num_$i";
                                                    $percent_field = "percent_$i";
                                                }
                                                //----------------------
                                    
                                                $played_num = $row4[$played_num_field];
                                                $percent = $row4[$percent_field];
                                    
                                                $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['played_num'][$i] = $played_num;
                                                $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['rank_num'][$i] = $percent;
                                            }
                                    
                                            //track played by daytime, station
                                            $table_index = "track_played_by_daytime_station__".$time_period;
                                            $query = "SELECT * FROM $Tables[$table_index] WHERE $track_id_field=$track_id AND country_code='$country_code' AND subdivision_code='$subdivision_code' AND city='".$mysqli_rms_cache->escape_str($city)."' ORDER BY played_num_00 DESC LIMIT $limit";
                                            $r5 = $mysqli_rms_cache->query($query);
                                    
                                            if($r5 && $r5->num_rows) {
                                                while($row5 = $r5->fetch_assoc()) {
                                                    $station_id = $row5['station_id'];
                                    
                                                    for($i=0; $i<24; $i++) {
                                                        //define field names
                                                        if($i<10) {
                                                            $played_num_field = "played_num_0$i";
                                                            $percent_field = "percent_0$i";
                                                        }
                                                        else {
                                                            $played_num_field = "played_num_$i";
                                                            $percent_field = "percent_$i";
                                                        }
                                                        //----------------------
                                    
                                                        $played_num = $row5[$played_num_field];
                                                        $percent = $row5[$percent_field];
                                    
                                                        $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['played_num'][$i] = $played_num;
                                                        $result[$time_period]['daytime']['continent'][$continent_code]['country'][$country_code]['subdivision'][$subdivision_code]['city'][$city]['station'][$station_id]['rank_num'][$i] = $percent;
                                                    }
                                                }
                                            }
                                        }
                                    }                                    
                                }
                            } 
                        }
                    }
                }
            }
            
        }
    }
    else
        return $ERRORS['NO_STAT_DATA'];
        
    
    return $result;
}




/**
 * Get Track ID Info
 * 
 * @param int $cugate_track_id
 * @param int $shenzhen_track_id
 * @return array
 */
function cug_rms_stat_get_track_id_info($cugate_track_id, $shenzhen_track_id) {
    $result = array();
    
    if($cugate_track_id > 0) {
        $result['track_id'] = $cugate_track_id;
        $result['track_id_field'] = "cugate_track_id";
    }
    elseif($shenzhen_track_id) {
        $result['track_id'] = $shenzhen_track_id;
        $result['track_id_field'] = "shenzhen_track_id";
    }
    else {
        $result['track_id'] = 0;
        $result['track_id_field'] = "";
    }
    
    return $result;
}


/**
 * Capture Track ID parameter
 * 
 * @param array $params
 * @return array
 */
function cug_rms_stat_capture_track_id($params) {
    $result = array();
    
    $result['cugate_track_id'] = !empty($params['cugate_track_id']) ? $params['cugate_track_id'] : 0;
    $result['shenzhen_track_id'] = !empty($params['shenzhen_track_id']) ? $params['shenzhen_track_id'] : 0;
    
    return $result;
}


/**
 * Get Track Played Numbers by daytimes with spesific query
 * 
 * @param object $mysqli - instance of db connection
 * @param string $query
 * @return array
 */
function cug_rms_stat_track_played_by_daytime($mysqli, $query) {
    $result = array();
    $played_nums = "";
    
    $r = $mysqli->query($query);
    if($r && $r->num_rows) {
        $row = $r->fetch_assoc();
    
        for($i=0; $i<24; $i++) {
            //define field names
            if($i<10) {
                $played_num_field = "played_num_0$i";
                //$percent_field = "percent_0$i";
            }
            else {
                $played_num_field = "played_num_$i";
                //$percent_field = "percent_$i";
            }
            //----------------------
    
            $played_num = $row[$played_num_field];
            //$percent = $row[$percent_field];
    
            $played_nums .= ($played_num > 0) ? $played_num."," : ",";
            //$result[0][$i] = (int)$played_num;
            //$result[1][$i] = $percent;
        }
        
        $played_nums = substr($played_nums, 0, strlen($played_nums)-1);
        $result = $played_nums;
    }
    
    return $result;
}


/**
 * Check if it is time to access Cache Tables
 * 
 * @param object $mysqli - instance of db connection
 * @param string $status_field
 * @return bool
 */
function cug_rms_stat_is_time_to_access_cache_tables($mysqli, $status_field) {
    global $Tables, $TABLE_CHECK_TIMEOUT, $TABLE_CHECK_LOOP_NUM, $ERRORS;
    $result = false;

    for($i=0; $i<=$TABLE_CHECK_LOOP_NUM; $i++) {
       $status = cug_rms_check_cache_tables_statuses($mysqli, $status_field);
            
        if($status == 1) {
            $result = true; //OK
            break;   
        }
        else {
            if($i < $TABLE_CHECK_LOOP_NUM)
                sleep($TABLE_CHECK_TIMEOUT);
        }

    }
    //------------------------------------------------
    
    return $result;
}


/**
 * Check Cache Tables Statuses
 *
 * @param object $mysqli - instance of db connection
 * @param string $status_field
 * @return number (1 - if all tables statuses are 1, otherwise - 0)
 */
function cug_rms_check_cache_tables_statuses($mysqli, $status_field) {
    global $Tables;
    $result = 0;

    $query = "SELECT COUNT(id) FROM {$Tables['cache_table']} WHERE enabled=1";
    $r = $mysqli->query($query);

    if($r && $r->num_rows) {
        $row = $r->fetch_array();
        $total_cache_tables = $row[0];

        $query = "SELECT COUNT(id) FROM {$Tables['cache_table']} WHERE $status_field=1 AND enabled=1";
        $r1 = $mysqli->query($query);

        if($r1 && $r1->num_rows) {
            $row1 = $r1->fetch_array();
            $updated_cache_tables = $row1[0];

            if($total_cache_tables == $updated_cache_tables)
                $result = 1;
        }
    }

    return $result;
}


/**
 * Check Track if there is any statistical data for last 365 days
 * 
 * @param int $cugate_track_id
 * @param int $shenzhen_track_id
 * @return number
 */
function cug_rms_stat_check_track($cugate_track_id, $shenzhen_track_id) {
    global $mysqli_rms_cache, $Tables, $ERRORS;
    
    //get Track ID Info
    $track_id_info = cug_rms_stat_get_track_id_info($cugate_track_id, $shenzhen_track_id);
    if($track_id_info['track_id'] == 0)
        return $ERRORS['NO_TRACK_ID'];
    else {
        $track_id = $track_id_info['track_id'];
        $track_id_field = $track_id_info['track_id_field'];
    }
    
    
    //check track
    $query = "SELECT id FROM {$Tables['track_played_total__last_365_days'] } WHERE $track_id_field=$track_id LIMIT 1";
    $r = $mysqli_rms_cache->query($query);
    
    if($r && $r->num_rows) {
        return 1;
    }
    else {
        return $ERRORS['NO_STAT_DATA'];
    }
}
?>