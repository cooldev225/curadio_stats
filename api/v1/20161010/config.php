<?PHP
//------------------------------------
// DB Configuration
//------------------------------------

//CUGATE
$cug_db_host		= '85.214.64.18';
$cug_db_name		= 'cugate_v3';
$cug_db_user		= 'khvicha';
$cug_db_password	= '8qU213@aJwGt';
$cug_db_server_port	= 3306;
$cug_db_ssl         = false;

$cug_db_server_key		= "";
$cug_db_server_cert		= "";
$cug_db_ca_cert			= "";


//RMS STATISTICS CACHE
$rms_cache_db_host		= '192.168.2.23';
$rms_cache_db_name		= 'curadio_cache';
$rms_cache_db_user		= 'root';
$rms_cache_db_password	= 'mazzrazz';
$rms_cache_db_server_port	= 3306;
$rms_cache_db_ssl         = false;

$rms_cache_db_user_global       = 'root'; //access to all db
$rms_cache_db_password_global   = 'mazzrazz';

$rms_cache_db_server_key		= "";
$rms_cache_db_server_cert		= "";
$rms_cache_db_ca_cert			= "";



// CuLib Path
//------------------------------------
$CuLib_PATH = dirname(dirname(dirname(dirname(__FILE__))))."/CuLib/";
?>
