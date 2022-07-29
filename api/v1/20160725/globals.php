<?PHP

$TEMP_UPLOAD_PATH = "/temp_upload/";
$slash = "/";


$TABLE_CHECK_TIMEOUT = 1; //seconds
$TABLE_CHECK_LOOP_NUM = 2; //number of try


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