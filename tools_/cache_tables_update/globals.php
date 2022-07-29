<?PHP

//$PROCESS_MAX_DURATION = 5; //for testing
$PROCESS_MAX_DURATION = 24 * 3600; // 24 hours

//------------------------------------
// Log FILE
//------------------------------------
$LOG_FILE_DIR = __DIR__."/_log";

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


//------------------------------------
// EMAIL OBJECT
//------------------------------------
$RMS_STAT_EMAIL = new PHPMailer;

$RMS_STAT_EMAIL->IsSMTP();
$RMS_STAT_EMAIL->Host = 'smtp.strato.de';
$RMS_STAT_EMAIL->SMTPAuth = true;
$RMS_STAT_EMAIL->SMTPSecure = 'tls'; //ssl, tls
$RMS_STAT_EMAIL->Port = 587; //465, 587
$RMS_STAT_EMAIL->isHTML(true);
$RMS_STAT_EMAIL->CharSet = "UTF-8";
//$RMS_STAT_EMAIL->SMTPDebug = 2; //see detailed erros

$RMS_STAT_EMAIL->Username = 'rms-stat@cugate.com';
$RMS_STAT_EMAIL->Password = 'Ma567Ra110';

$RMS_STAT_EMAIL->setFrom('rms-stat@cugate.com', 'CUGATE');


//------------------------------------
// EMAIL ADDRESSES WHO SHOULD RECEIVE NOTIFICATION EMAILS
//------------------------------------
$RMS_EMAIL_ADDR = array();
$RMS_EMAIL_ADDR[0] = "marios.georgiou@cugate.com";
//$RMS_EMAIL_ADDR[1] = "David.Ruettinger@cugate.com";
?>
