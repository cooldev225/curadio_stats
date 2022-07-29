<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	URL
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------


/**
 * Get full URL of the current page
 */
function cug_get_full_url()
{
return cug_get_url_protocol() . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
//return "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Get URL Protocol (http or https)
 * @return string
 */
function cug_get_url_protocol()
{
$protocol = stripos(@$_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https' : 'http';	
return $protocol;
}


// check HUA for Spiders, Bots, Crawlers
function is_bot($hua)
{
$bot_found = false;

$bots_arr = array(
		"google",
		"spider",
		"bot",
		"crawler",
		"facebook",
		"yahoo",
		"bing",
		"microsoft",
		"scoutjet",
		"msn",
		"mail.ru",
		"rambler",
		"test",
		"validator",
		"soso",
		"find",
		"visit",
		"java"
);

$bots_count = count($bots_arr);

	if(!$hua) {
		$bot_found = true;
	}
	else {
		for($i=0; $i<$bots_count; $i++) {
			if( stripos($hua, $bots_arr[$i]) !== false) {
				$bot_found = true;
				break;
			}
		}
	}

return $bot_found;
}
?>
