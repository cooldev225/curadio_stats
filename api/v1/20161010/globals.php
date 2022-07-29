<?PHP

$TEMP_UPLOAD_PATH = "/temp_upload/";
$slash = "/";


$TABLE_CHECK_TIMEOUT = 1; //seconds
$TABLE_CHECK_LOOP_NUM = 2; //number of try


//------------------------------------
// Time Periods
//------------------------------------
$TIME_PERIODS = array();
//do not change order of indexes!
$TIME_PERIODS[0]    = "LAST_7_DAYS";
$TIME_PERIODS[1]    = "LAST_30_DAYS";
$TIME_PERIODS[2]    = "LAST_365_DAYS";
$TIME_PERIODS[3]    = "LAST_YEAR";
$TIME_PERIODS[4]    = "LAST_MONTH";
$TIME_PERIODS[5]    = "LAST_WEEK";
$TIME_PERIODS[6]    = "THIS_YEAR";
$TIME_PERIODS[7]    = "THIS_MONTH";




//AMOUNT CALCULATION FORMULAS by objects and countries
$AMOUNT_FORMULA = array();
$AMOUNT_FORMULA['composer']['DE'] = "{culture_factor} * {broadcaster_coefficient} * ({airtime} / {time_interval_sec}) * {amount}";
$AMOUNT_FORMULA['artist']['DE'] = "({airtime} / {time_interval_sec}) * {amount}";


$CULINK = array();

//ITUNES
//**************************
$CULINK['ITUNES']['TAG']['MAIN'] = "1l3vri7"; 

//iOS APP (Les)
$CULINK['ITUNES']['TAG']['177439484866400192']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['177439484866400192']['VAL'] = "CuPlay_iOS";

//Android App (Chris)
$CULINK['ITUNES']['TAG']['1428368035964209152']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['1428368035964209152']['VAL'] = "CuPlay_android";

//Android App (Matvey)
$CULINK['ITUNES']['TAG']['101121642910772528']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['101121642910772528']['VAL'] = "synclicensor";

//Android App (Tony)
$CULINK['ITUNES']['TAG']['654960855822794112']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['654960855822794112']['VAL'] = "tony";

//PC WIdget
$CULINK['ITUNES']['TAG']['2305812337175728896']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['2305812337175728896']['VAL'] = "CuPlay_pcwidget";

//CuRadio Application - iOS (Tony)
$CULINK['ITUNES']['TAG']['1846586657208370176']['KEY'] = "ct";
$CULINK['ITUNES']['TAG']['1846586657208370176']['VAL'] = "CuRadio_iOS_APP";


?>