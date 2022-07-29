<?PHP

/**
 * Output Data (in XML or JSON format)
 *
 * @param int $action
 * @param string $data
 * @return void
 */
function cugapi_output($action, $data)
{
global $API_OUTPUT_FORMAT, $OUTPUT_FORMAT, $ACTIONS, $ROOT_SUCCESS_NODE, $ROOT_ERROR_NODE;

echo ($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) ? cug_xml_header($version="1.0", $encoding="UTF-8", PHP_EOL) : "";

	
	switch($action) {
		
		// =1=
		//************************************************
		case $ACTIONS['INIT_SESSION'] :
		//************************************************
			if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['xml'])) {
				
				echo cug_xml_start_tag($ROOT_SUCCESS_NODE, $data[$ROOT_SUCCESS_NODE]['attributes'], $prefix="", PHP_EOL);
				
				echo cug_xml_start_tag("session_id", array(), tabnum(1));
				echo cug_xml_entities($data[$ROOT_SUCCESS_NODE]['session_id']);
				echo cug_xml_end_tag("session_id", "", PHP_EOL);
				
				echo cug_xml_end_tag($ROOT_SUCCESS_NODE);
			}
			else if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['json'])) {
				echo json_encode($data);
			}
		break;

			
		// =2=
		//************************************************
		case $ACTIONS['GET_STAT_DATA'] :
		//************************************************
			if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['xml'])) {
			
				if(!empty($data[$ROOT_SUCCESS_NODE]['result'])){ //OK
				    echo cug_xml_start_tag($ROOT_SUCCESS_NODE, $data[$ROOT_SUCCESS_NODE]['attributes'], $prefix="", PHP_EOL);
				    $results = $data[$ROOT_SUCCESS_NODE]['result'];
				    
    				foreach($results as $time_period => $result) {
    				    //time period
        				echo cug_xml_start_tag($time_period, array(), tabnum(1), PHP_EOL);
        				
        				//total
        				echo cug_xml_start_tag("total", array("played_num" => $result['total'][0], "rank_num" => $result['total'][1]), tabnum(2), PHP_EOL);
    				    //daytime
    				    $daytime = $result['total']['daytime'];
        				//echo cug_xml_start_tag("daytime", array(), tabnum(3), PHP_EOL);
        				echo cug_xml_start_tag("daytime", array(), tabnum(3), "");
        				    echo cug_xml_entities($daytime);
    				        /*
    				        //played_num
        				    echo cug_xml_start_tag("played_num", array(), tabnum(4), PHP_EOL);
        				        foreach($daytime[0] as $key => $val) {
        				            echo cug_xml_start_tag("item", array(), tabnum(5));
        				            echo cug_xml_entities($val);
        				            echo cug_xml_end_tag("item", "", PHP_EOL);
        				        }
        				    echo cug_xml_end_tag("played_num", tabnum(4), PHP_EOL);
        				    //------------------
        				    
        				    //rank_num
        				    echo cug_xml_start_tag("rank_num", array(), tabnum(4), PHP_EOL);
        				    foreach($daytime[1] as $key => $val) {
        				        echo cug_xml_start_tag("item", array(), tabnum(5));
        				        echo cug_xml_entities($val);
        				        echo cug_xml_end_tag("item", "", PHP_EOL);
        				    }
        				    echo cug_xml_end_tag("rank_num", tabnum(4), PHP_EOL);
        				    //-------------------
        				    */
        				    
    				    echo cug_xml_end_tag("daytime", "", PHP_EOL);
        				//-----------------------    
        				echo cug_xml_end_tag("total", tabnum(2), PHP_EOL);
        				//------------------------
        				//------------------------
        				
        				//continent
        				//----------------
        				echo cug_xml_start_tag("continents", array(), tabnum(2), PHP_EOL);
        				$continents = $result['continent'];
        				
        				foreach($continents as $continent_code => $continent) {
        				    echo cug_xml_start_tag("continent", array("code" => $continent_code, "played_num" => $continent[0], "rank_num" => $continent[1]), tabnum(3), PHP_EOL);
        				    //daytime
        				    $daytime = $continent['daytime'];
        				    //echo cug_xml_start_tag("daytime", array(), tabnum(4), PHP_EOL);
        				    echo cug_xml_start_tag("daytime", array(), tabnum(4), "");
        				        echo cug_xml_entities($daytime);
            				    /*
            				    //played_num
            				    echo cug_xml_start_tag("played_num", array(), tabnum(5), PHP_EOL);
            				    foreach($daytime[0] as $key => $val) {
            				        echo cug_xml_start_tag("item", array(), tabnum(6));
            				        echo cug_xml_entities($val);
            				        echo cug_xml_end_tag("item", "", PHP_EOL);
            				    }
            				    echo cug_xml_end_tag("played_num", tabnum(5), PHP_EOL);
            				    //------------------
            				    
            				    //rank_num
            				    echo cug_xml_start_tag("rank_num", array(), tabnum(5), PHP_EOL);
            				    foreach($daytime[1] as $key => $val) {
            				        echo cug_xml_start_tag("item", array(), tabnum(6));
            				        echo cug_xml_entities($val);
            				        echo cug_xml_end_tag("item", "", PHP_EOL);
            				    }
            				    echo cug_xml_end_tag("rank_num", tabnum(5), PHP_EOL);
            				    //-------------------
            				    */
            				echo cug_xml_end_tag("daytime", "", PHP_EOL);
            				//-----------------------
        				    
            				//country
            				//----------------
            				echo cug_xml_start_tag("countries", array(), tabnum(4), PHP_EOL);
            				$countries = $continent['country'];
            				
            				foreach($countries as $country_code => $country) {
            				    echo cug_xml_start_tag("country", array("code" => $country_code, "played_num" => $country[0], "rank_num" => $country[1]), tabnum(5), PHP_EOL);
            				    //daytime
            				    $daytime = $country['daytime'];
            				    //echo cug_xml_start_tag("daytime", array(), tabnum(6), PHP_EOL);
            				    echo cug_xml_start_tag("daytime", array(), tabnum(6), "");
            				        echo cug_xml_entities($daytime);
            				    /*
            				    //played_num
            				    echo cug_xml_start_tag("played_num", array(), tabnum(7), PHP_EOL);
            				    foreach($daytime[0] as $key => $val) {
            				        echo cug_xml_start_tag("item", array(), tabnum(8));
            				        echo cug_xml_entities($val);
            				        echo cug_xml_end_tag("item", "", PHP_EOL);
            				    }
            				    echo cug_xml_end_tag("played_num", tabnum(7), PHP_EOL);
            				    //------------------
            				    
            				    //rank_num
            				    echo cug_xml_start_tag("rank_num", array(), tabnum(7), PHP_EOL);
            				    foreach($daytime[1] as $key => $val) {
            				        echo cug_xml_start_tag("item", array(), tabnum(8));
            				        echo cug_xml_entities($val);
            				        echo cug_xml_end_tag("item", "", PHP_EOL);
            				    }
            				    echo cug_xml_end_tag("rank_num", tabnum(7), PHP_EOL);
            				    //-------------------
            				    */
            				    echo cug_xml_end_tag("daytime", "", PHP_EOL);
            				    //-----------------------            		
            				    
            				    //amounts
            				    if(!empty($country['amount_composer'])) {
            				        //total
            				        echo cug_xml_start_tag("amount_composer", array(), tabnum(6), "");
            				            echo cug_xml_entities($country['amount_composer']);
            				        echo cug_xml_end_tag("amount_composer", "", PHP_EOL);
            				        
            				        //daytime
            				        if(!empty($country['amount_composer_daytime'])) {
                				        echo cug_xml_start_tag("amount_composer_daytime", array(), tabnum(6), "");
                				            echo cug_xml_entities($country['amount_composer_daytime']);
                				        echo cug_xml_end_tag("amount_composer_daytime", "", PHP_EOL);
            				        }
            				    } 
            				    //----------------
            				    if(!empty($country['amount_artist'])) {
            				        //total
            				        echo cug_xml_start_tag("amount_artist", array(), tabnum(6), "");
            				        echo cug_xml_entities($country['amount_artist']);
            				        echo cug_xml_end_tag("amount_artist", "", PHP_EOL);
            				    
            				        //daytime
            				        if(!empty($country['amount_artist_daytime'])) {
            				            echo cug_xml_start_tag("amount_artist_daytime", array(), tabnum(6), "");
            				            echo cug_xml_entities($country['amount_artist_daytime']);
            				            echo cug_xml_end_tag("amount_artist_daytime", "", PHP_EOL);
            				        }
            				    }
            				    
            				    
            				    //subdivision
            				    //----------------
            				    echo cug_xml_start_tag("subdivisions", array(), tabnum(5), PHP_EOL);
            				    $subdivisions = $country['subdivision'];
            				    
            				    foreach($subdivisions as $subdivision_code => $subdivision) {
            				        echo cug_xml_start_tag("subdivision", array("code" => $subdivision_code, "played_num" => $subdivision[0], "rank_num" => $subdivision[1]), tabnum(6), PHP_EOL);
            				        //daytime
            				        $daytime = $subdivision['daytime'];
            				        //echo cug_xml_start_tag("daytime", array(), tabnum(7), PHP_EOL);
            				        echo cug_xml_start_tag("daytime", array(), tabnum(7), "");
            				            echo cug_xml_entities($daytime);
            				        /*
            				        //played_num
            				        echo cug_xml_start_tag("played_num", array(), tabnum(8), PHP_EOL);
            				        foreach($daytime[0] as $key => $val) {
            				            echo cug_xml_start_tag("item", array(), tabnum(9));
            				            echo cug_xml_entities($val);
            				            echo cug_xml_end_tag("item", "", PHP_EOL);
            				        }
            				        echo cug_xml_end_tag("played_num", tabnum(8), PHP_EOL);
            				        //------------------
            				    
            				        //rank_num
            				        echo cug_xml_start_tag("rank_num", array(), tabnum(8), PHP_EOL);
            				        foreach($daytime[1] as $key => $val) {
            				            echo cug_xml_start_tag("item", array(), tabnum(9));
            				            echo cug_xml_entities($val);
            				            echo cug_xml_end_tag("item", "", PHP_EOL);
            				        }
            				        echo cug_xml_end_tag("rank_num", tabnum(8), PHP_EOL);
            				        //-------------------
            				        */
            				    
            				        echo cug_xml_end_tag("daytime", "", PHP_EOL);
            				        //-----------------------
            				    
            				        //amounts
            				        if(!empty($subdivision['amount_composer'])) {
            				            //total
            				            echo cug_xml_start_tag("amount_composer", array(), tabnum(7), "");
            				            echo cug_xml_entities($subdivision['amount_composer']);
            				            echo cug_xml_end_tag("amount_composer", "", PHP_EOL);
            				        
            				            //daytime
            				            if(!empty($subdivision['amount_composer_daytime'])) {
            				                echo cug_xml_start_tag("amount_composer_daytime", array(), tabnum(7), "");
            				                echo cug_xml_entities($subdivision['amount_composer_daytime']);
            				                echo cug_xml_end_tag("amount_composer_daytime", "", PHP_EOL);
            				            }
            				        } 
            				        //---------------------
            				        if(!empty($subdivision['amount_artist'])) {
            				            //total
            				            echo cug_xml_start_tag("amount_artist", array(), tabnum(7), "");
            				            echo cug_xml_entities($subdivision['amount_artist']);
            				            echo cug_xml_end_tag("amount_artist", "", PHP_EOL);
            				        
            				            //daytime
            				            if(!empty($subdivision['amount_artist_daytime'])) {
            				                echo cug_xml_start_tag("amount_artist_daytime", array(), tabnum(7), "");
            				                echo cug_xml_entities($subdivision['amount_artist_daytime']);
            				                echo cug_xml_end_tag("amount_artist_daytime", "", PHP_EOL);
            				            }
            				        }
            				        
            				        
            				        //city
            				        //--------------------
            				        echo cug_xml_start_tag("cities", array(), tabnum(6), PHP_EOL);
            				        $cities = $subdivision['city'];
            				        
            				        foreach($cities as $city_name => $city) {
            				            echo cug_xml_start_tag("city", array("code" => $city_name, "played_num" => $city[0], "rank_num" => $city[1]), tabnum(7), PHP_EOL);
            				            //daytime
            				            $daytime = $city['daytime'];
            				            //echo cug_xml_start_tag("daytime", array(), tabnum(8), PHP_EOL);
            				            echo cug_xml_start_tag("daytime", array(), tabnum(8), "");
            				                echo cug_xml_entities($daytime);
            				            /*
            				            //played_num
            				            echo cug_xml_start_tag("played_num", array(), tabnum(9), PHP_EOL);
            				            foreach($daytime[0] as $key => $val) {
            				                echo cug_xml_start_tag("item", array(), tabnum(10));
            				                echo cug_xml_entities($val);
            				                echo cug_xml_end_tag("item", "", PHP_EOL);
            				            }
            				            echo cug_xml_end_tag("played_num", tabnum(9), PHP_EOL);
            				            //------------------
            				        
            				            //rank_num
            				            echo cug_xml_start_tag("rank_num", array(), tabnum(9), PHP_EOL);
            				            foreach($daytime[1] as $key => $val) {
            				                echo cug_xml_start_tag("item", array(), tabnum(10));
            				                echo cug_xml_entities($val);
            				                echo cug_xml_end_tag("item", "", PHP_EOL);
            				            }
            				            echo cug_xml_end_tag("rank_num", tabnum(9), PHP_EOL);
            				            //-------------------
            				            */
            				            echo cug_xml_end_tag("daytime", "", PHP_EOL);
            				            //-----------------------
            				        
            				            
            				            //amounts
            				            if(!empty($city['amount_composer'])) {
            				                //total
            				                echo cug_xml_start_tag("amount_composer", array(), tabnum(8), "");
            				                echo cug_xml_entities($city['amount_composer']);
            				                echo cug_xml_end_tag("amount_composer", "", PHP_EOL);
            				            
            				                //daytime
            				                if(!empty($city['amount_composer_daytime'])) {
            				                    echo cug_xml_start_tag("amount_composer_daytime", array(), tabnum(8), "");
            				                    echo cug_xml_entities($city['amount_composer_daytime']);
            				                    echo cug_xml_end_tag("amount_composer_daytime", "", PHP_EOL);
            				                }
            				            }
            				            //--------------------
            				            if(!empty($city['amount_artist'])) {
            				                //total
            				                echo cug_xml_start_tag("amount_artist", array(), tabnum(8), "");
            				                echo cug_xml_entities($city['amount_artist']);
            				                echo cug_xml_end_tag("amount_artist", "", PHP_EOL);
            				            
            				                //daytime
            				                if(!empty($city['amount_artist_daytime'])) {
            				                    echo cug_xml_start_tag("amount_artist_daytime", array(), tabnum(8), "");
            				                    echo cug_xml_entities($city['amount_artist_daytime']);
            				                    echo cug_xml_end_tag("amount_artist_daytime", "", PHP_EOL);
            				                }
            				            }            				            
            				            
            				            
            				            
            				            //station
            				            //-------------------
            				            echo cug_xml_start_tag("stations", array(), tabnum(7), PHP_EOL);
            				            $stations = $city['station'];
            				            
            				            foreach($stations as $station_id => $station) {
            				                echo cug_xml_start_tag("station", array("code" => $station_id, "played_num" => $station[0], "rank_num" => $station[1]), tabnum(8), PHP_EOL);
            				                //daytime
            				                $daytime = $station['daytime'];
            				                //echo cug_xml_start_tag("daytime", array(), tabnum(9), PHP_EOL);
            				                echo cug_xml_start_tag("daytime", array(), tabnum(9), "");
            				                    echo cug_xml_entities($daytime);
            				                /*
            				                //played_num
            				                echo cug_xml_start_tag("played_num", array(), tabnum(10), PHP_EOL);
            				                
            				                foreach($daytime[0] as $key => $val) {
            				                    echo cug_xml_start_tag("item", array(), tabnum(11));
            				                    echo cug_xml_entities($val);
            				                    echo cug_xml_end_tag("item", "", PHP_EOL);
            				                }
            				                
            				                echo cug_xml_end_tag("played_num", tabnum(10), PHP_EOL);
            				                //------------------
            				            
            				                //rank_num
            				                echo cug_xml_start_tag("rank_num", array(), tabnum(10), PHP_EOL);
            				                foreach($daytime[1] as $key => $val) {
            				                    echo cug_xml_start_tag("item", array(), tabnum(11));
            				                    echo cug_xml_entities($val);
            				                    echo cug_xml_end_tag("item", "", PHP_EOL);
            				                }
            				                echo cug_xml_end_tag("rank_num", tabnum(10), PHP_EOL);
            				                //-------------------
            				                */
            				                echo cug_xml_end_tag("daytime", "", PHP_EOL);
            				                //-----------------------
            				            
            				                //amount
            				                if(!empty($station['amount_composer'])) {
            				                    //total
            				                    echo cug_xml_start_tag("amount_composer", array(), tabnum(9), "");
            				                    echo cug_xml_entities($station['amount_composer']);
            				                    echo cug_xml_end_tag("amount_composer", "", PHP_EOL);
            				                
            				                    //daytime
            				                    if(!empty($station['amount_composer_daytime'])) {
            				                        echo cug_xml_start_tag("amount_composer_daytime", array(), tabnum(9), "");
            				                        echo cug_xml_entities($station['amount_composer_daytime']);
            				                        echo cug_xml_end_tag("amount_composer_daytime", "", PHP_EOL);
            				                    }
            				                }
            				                //-----------------------
            				                if(!empty($station['amount_artist'])) {
            				                    //total
            				                    echo cug_xml_start_tag("amount_artist", array(), tabnum(9), "");
            				                    echo cug_xml_entities($station['amount_artist']);
            				                    echo cug_xml_end_tag("amount_artist", "", PHP_EOL);
            				                
            				                    //daytime
            				                    if(!empty($station['amount_artist_daytime'])) {
            				                        echo cug_xml_start_tag("amount_artist_daytime", array(), tabnum(9), "");
            				                        echo cug_xml_entities($station['amount_artist_daytime']);
            				                        echo cug_xml_end_tag("amount_artist_daytime", "", PHP_EOL);
            				                    }
            				                }
            				                
            				                
            				                
            				                echo cug_xml_end_tag("station", tabnum(8), PHP_EOL);
            				                //--------------------------
            				            }//foreach station
            				            
            				            echo cug_xml_end_tag("stations", tabnum(7), PHP_EOL);
            				            //------------------------            				            
            				            
            				            echo cug_xml_end_tag("city", tabnum(7), PHP_EOL);
            				            //--------------------------
            				        }//foreach city
            				        
            				        echo cug_xml_end_tag("cities", tabnum(6), PHP_EOL);
            				        //------------------------            				        
            				        
            				        echo cug_xml_end_tag("subdivision", tabnum(6), PHP_EOL);
            				        //--------------------------
            				    }//foreach subdivision
            				    
            				    echo cug_xml_end_tag("subdivisions", tabnum(5), PHP_EOL);
            				    //------------------------
            				    
            				    echo cug_xml_end_tag("country", tabnum(5), PHP_EOL);
            				    //--------------------------
            				}//foreach country
            				
            				echo cug_xml_end_tag("countries", tabnum(4), PHP_EOL);
            				//------------------------
            				
        				    echo cug_xml_end_tag("continent", tabnum(3), PHP_EOL);
        				    //--------------------------   
        				}//foreach continent

        				echo cug_xml_end_tag("continents", tabnum(2), PHP_EOL);
        				//------------------------
        				
        				echo cug_xml_end_tag($time_period, tabnum(1), PHP_EOL);
        				//------------------------
    				}//foreach time period
    				
    				echo cug_xml_end_tag($ROOT_SUCCESS_NODE);
    				//----------------------------
				}
				else { //error
				    cugapi_output_error_xml($ROOT_ERROR_NODE, $data[$ROOT_ERROR_NODE]['code'], $data[$ROOT_ERROR_NODE]['msg']);
				}
			}
			else if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['json'])) {
				echo json_encode($data);
			}
			else if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['web'])) {
			    if(!empty($data[$ROOT_SUCCESS_NODE]['result'])) { //OK
			        $output = "";
			        //$output .= "["; //START
			        
			        $results = $data[$ROOT_SUCCESS_NODE]['result'];
			        
			        //TIME PERIOD
			        foreach($results as $time_period => $result) {
			            $output .= "[[";
			            
			            //total
			            $output .= "\"$time_period\", {$result['total'][0]}, {$result['total'][1]}, ";
			             //daytime
			             $daytime = $result['total']['daytime'];
			             $daytime_str = "";
			             $output .= "[";
			                 foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
			                 $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
			                 $output .= $daytime_str;
			             $output .= "]],"; //end daytime
			             //--------------------------			            
			            
			            //CONTINENT
			            $continents = $result['continent'];
    		            foreach($continents as $continent_code => $continent) {
    		                $output .= "[";
    		                
    			            //total
    			            $output .= "\"$continent_code\", {$continent[0]}, {$continent[1]}, ";
    			             //daytime
    			             $daytime = $continent['daytime'];
    			             $daytime_str = "";
    			             $output .= "[";
    			                 foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
    			                 $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
    			                 $output .= $daytime_str;
    			             $output .= "],"; //end daytime
    			             //--------------------------
			             
    			            //COUNTRY
    			            $output .= "[";
    			            $countries = $continent['country'];
    			            foreach($countries as $country_code => $country) {
    			                $output .= "[";
    			                
    			                //total
    			                $output .= "\"$country_code\", {$country[0]}, {$country[1]}, ";
    			                //daytime
    			                $daytime = $country['daytime'];
    			                $daytime_str = "";
    			                $output .= "[";
        			                foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
        			                $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
        			                $output .= $daytime_str;
    			                $output .= "],"; //end daytime
    			                //-------------------------- 
    			                
    			                //SUBDIVISION
    			                $output .= "[";
    			                $subdivisions = $country['subdivision'];
    			                foreach($subdivisions as $subdivision_code => $subdivision) {
    			                    $output .= "[";
    			                    
    			                    //total
    			                    $output .= "\"$subdivision_code\", {$subdivision[0]}, {$subdivision[1]}, ";
    			                    //daytime
    			                    $daytime = $subdivision['daytime'];
    			                    $daytime_str = "";
    			                    $output .= "[";
        			                    foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
        			                    $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
        			                    $output .= $daytime_str;
    			                    $output .= "],"; //end daytime
    			                    //--------------------------
    			                    
    			                    //CITY
    			                    $output .= "[";
    			                    $cities = $subdivision['city'];
    			                    foreach($cities as $city_name => $city) {
    			                        $output .= "[";
    			                        
    			                        //total
    			                        $output .= "\"$city_name\", {$city[0]}, {$city[1]}, ";
    			                        //daytime
    			                        $daytime = $city['daytime'];
    			                        $daytime_str = "";
    			                        $output .= "[";
        			                        foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
        			                        $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
        			                        $output .= $daytime_str;
    			                        $output .= "],"; //end daytime
    			                        //--------------------------
    			                        
    			                        //STATION
    			                        $output .= "[";
    			                        $stations = $city['station'];
    			                        foreach($stations as $station_id => $station) {
    			                            $output .= "[";
    			                            
    			                            //total
    			                            $output .= "$station_id, {$station[0]}, {$station[1]}, ";
    			                            //daytime
    			                            $daytime = $station['daytime'];
    			                            $daytime_str = "";
    			                            $output .= "[";
        			                            foreach($daytime[0] as $val) { $daytime_str .= ($val > 0) ? "$val," : ","; }
        			                            //$daytime_str = rtrim($daytime_str, ",");
        			                            $daytime_str = substr($daytime_str, 0, strlen($daytime_str));
        			                            $output .= $daytime_str;
    			                            $output .= "]"; //end daytime
    			                            //--------------------------    			                            
    			                            
    			                            $output .= "],"; //end station
    			                        }
    			                        $output = rtrim($output, ",");
    			                        $output .= "]"; //end stations
    			                        
    			                        $output .= "],"; //end city
    			                    }
    			                    $output = rtrim($output, ",");
    			                    $output .= "]"; //end cities
    			                    
    			                    $output .= "],"; //end subdivision
    			                }
    			                $output = rtrim($output, ",");
    			                $output .= "]"; //end subdivisions
    			                
    			                $output .= "],"; //end country
    			            }
    			            $output = rtrim($output, ",");
    			            $output .= "]"; //end countries
    			            
    		                $output .= "],"; //end continent
    		            }// foreach continent			             
    		            $output = rtrim($output, ",");
    		            
			            $output .= "],"; //end time period
			        } // foreach time period
			        
			        $output = rtrim($output, ",");			        
			        //$output .= "]"; //END
			        
			        echo $output;
			    }
			    else {
			        echo "[[\"error\", \"{$data[$ROOT_ERROR_NODE]['code']}\", \"{$data[$ROOT_ERROR_NODE]['msg']}\"]]";
			    }
			    
			}
			
		break;
		
		
		// =3=
		//************************************************
		case $ACTIONS['GET_ARE_LIST'] :
		//************************************************			
		    if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
		        
		    }
		    else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
		        echo json_encode($data);
		    }
		    else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['web']) {
		        if(!empty($data[$ROOT_SUCCESS_NODE]['result'])) {//OK
		            echo serialize($data[$ROOT_SUCCESS_NODE]['result']);
		        }
		        else {
		            echo "[[\"error\", \"{$data[$ROOT_ERROR_NODE]['code']}\", \"{$data[$ROOT_ERROR_NODE]['msg']}\"]]";
		        }
		    }		
		break;
		
		
		
		// =4=
		//************************************************
		case $ACTIONS['CHECK_TRACK'] :
		//************************************************
		    if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['xml'])) {		        	
		        if(!empty($data[$ROOT_SUCCESS_NODE]['result'])){
		            echo cug_xml_start_tag($ROOT_SUCCESS_NODE, $data[$ROOT_SUCCESS_NODE]['attributes'], $prefix="", PHP_EOL);
		            
		            echo cug_xml_start_tag("result", array(), tabnum(1));
		            echo cug_xml_entities($data[$ROOT_SUCCESS_NODE]['result']);
		            echo cug_xml_end_tag("result", "", PHP_EOL);
		            
		            echo cug_xml_end_tag($ROOT_SUCCESS_NODE);
		        }
		        else { //error
				    cugapi_output_error_xml($ROOT_ERROR_NODE, $data[$ROOT_ERROR_NODE]['code'], $data[$ROOT_ERROR_NODE]['msg']);
				}
		    }
		    else if($API_OUTPUT_FORMAT == strtolower($OUTPUT_FORMAT['json'])) {
		        echo json_encode($data);
		    }
		    
		break;    
		
		// =default=
		//************************************************
		default:
	   //************************************************
			if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['xml']) {
				cugapi_output_error_xml($ROOT_ERROR_NODE, $data[$ROOT_ERROR_NODE]['code'], $data[$ROOT_ERROR_NODE]['msg']);
			}
			else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['json']) {
				echo json_encode($data);
			}
			else if($API_OUTPUT_FORMAT == $OUTPUT_FORMAT['web']) {
			    echo "[[\"error\", \"{$data[$ROOT_ERROR_NODE]['code']}\", \"{$data[$ROOT_ERROR_NODE]['msg']}\"]]";
			}			
		
		break;		
					
	}
}


/**
 * Output Error in XML format
 *
 * @param string $node
 * @param int $err_code (negative numbers)
 * @param string $err_msg
 * @return void
 */
function cugapi_output_error_xml($node, $err_code, $err_msg)
{
	echo cug_xml_start_tag($node, array(), $prefix="", PHP_EOL);
		
	echo cug_xml_start_tag("code", array(), "\t");
	echo cug_xml_entities($err_code);
	echo cug_xml_end_tag("code", "", PHP_EOL);
	
	echo cug_xml_start_tag("msg", array(), "\t");
	echo cug_xml_entities($err_msg);
	echo cug_xml_end_tag("msg", "", PHP_EOL);
		
	echo cug_xml_end_tag($node);	
}


/**
 * Get number of TABs
 * 
 * @param int $num
 * @return string
 */
function tabnum($num) {
    $result = "";
    
    for($i=1; $i<=$num; $i++)
        $result .= "\t";
    
    return $result;
}
?>