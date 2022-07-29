<?PHP

/**
 * Get Time Periods (StartTime and EndTime)
 *
 * @param string $ACTION - Like: 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_WEEK', 'LAST_MONTH', ...
 * @return array
 */
function cug_rms_get_time_periods($ACTION) {
    $result = array();


    switch(strtoupper($ACTION)) {
        //---------------------
        case 'LAST_7_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-7 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;

            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
            break;

        //---------------------
        case 'LAST_30_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-30 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;

            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));//-1
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
            break;

        //---------------------
        case 'LAST_365_DAYS':
        //---------------------
            $result['start_date'] = date("Y-m-d", strtotime('-365 days'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;

            $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
            break;
             
        //---------------------
        case 'LAST_YEAR':
        //---------------------
            $result['start_date'] = date("Y-01-01", strtotime('last year'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;

            $result['end_date'] = date("Y-12-31", strtotime('last year'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
            break;

        //---------------------
        case 'LAST_MONTH':
        //---------------------
            $result['start_date'] = date("Y-m-01", strtotime('last month'));
            $result['start_time'] = $result['start_date']." 00:00:00";
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;

            $result['end_date'] = date("Y-m-t", strtotime('last month'));
            $result['end_time'] = $result['end_date']." 23:59:59";
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
            break;
             
        //---------------------
        case 'LAST_WEEK':
        //---------------------
            /*
            //PHP v5.5.9
            //check this, because strtotime('last week monday') returns Monday of the current week (not of the last week) if today is Sunday
            if(strtoupper(date('l', strtotime('now'))) == "SUNDAY") {
                $result['start_date'] = date("Y-m-d", strtotime('last week monday -7 day'));
                $result['start_time'] = $result['start_date']." 00:00:00";
                 
                $result['end_date'] = date("Y-m-d", strtotime('last week sunday -7 day'));
                $result['end_time'] = $result['end_date']." 23:59:59";
            }
            else {
                $result['start_date'] = date("Y-m-d", strtotime('last week monday'));
                $result['start_time'] = $result['start_date']." 00:00:00";
                 
                $result['end_date'] = date("Y-m-d", strtotime('last week sunday'));
                $result['end_time'] = $result['end_date']." 23:59:59";
            }
            */
            
            //PHP v5.6.30
            $result['start_date'] = date("Y-m-d", strtotime('last week monday'));
            $result['start_time'] = $result['start_date']." 00:00:00";
             
            $result['end_date'] = date("Y-m-d", strtotime('last week sunday'));
            $result['end_time'] = $result['end_date']." 23:59:59";            
            //------------------------
             
            $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
       break;
       
       //---------------------
       case 'THIS_YEAR':
       //---------------------
           $result['start_date'] = date("Y-01-01", strtotime('now'));
           $result['start_time'] = $result['start_date']." 00:00:00";
           $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
            
           $result['end_date'] = date("Y-m-d", strtotime('-1 day'));
           $result['end_time'] = $result['end_date']." 23:59:59";
           $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
       break;
       
       //---------------------
       default: //other
       //---------------------
           $arr = explode("_", $ACTION);
           //year, month
           if(count($arr) >= 2) {
               //year
               if(strtolower($arr[0]) == "year") {
                   $year = $arr[1];
                   
                   $result['start_date'] = "$year-01-01";
                   $result['start_time'] = $result['start_date']." 00:00:00";
                   $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
                   
                   $result['end_date'] = "$year-12-31";
                   $result['end_time'] = $result['end_date']." 23:59:59";
                   $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
               }
               //month
               elseif(strtolower($arr[0]) == "month") {
                   $month = ((int)$arr[1] < 10) ? "0".(int)$arr[1] : (int)$arr[1];
                   $year = $arr[2];
                   
                   $result['start_date'] = $year."-".$month."-01";
                   $result['start_time'] = $result['start_date']." 00:00:00";
                   $result['start_timestamp'] = strtotime($result['start_time']) * 1000;
                    
                   $result['end_date'] = $year."-".$month.cal_days_in_month(CAL_GREGORIAN, (int)$arr[1], $year);
                   $result['end_time'] = $result['end_date']." 23:59:59";
                   $result['end_timestamp'] = strtotime($result['end_time']) * 1000;
               }
           }
       break;
       
    }

    return $result;
}
?>