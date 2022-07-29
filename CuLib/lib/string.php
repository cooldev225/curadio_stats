<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	STRING
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2015
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Converts all accent characters to ASCII characters.
 * If there are no accent characters, then the string given is just returned.
 * 
 * @param string $str
 * @return string
 */
function cug_convert_accent($str) {

	$chars_arr = array(
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ā'=>'A', 'Æ'=>'AE', 'Ą'=>'A',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', '±'=>'a',
			'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Đ'=>'Dj','Š'=>'S', 'Ŝ'=>'S', 'Ş'=>'S', 'Ģ'=>'G', 'Ĝ'=>'G',
			'ç'=>'c', 'č'=>'c', 'ć'=>'c', '¹'=>'dj','š'=>'s', 'ŝ'=>'s', 'ş'=>'s', 'ģ'=>'g', 'ĝ'=>'g',
			'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', '¢'=>'E', 'Ė'=>'E',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ē'=>'e', 'ė'=>'e',
			'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', '¤'=>'I', 'Į'=>'I',
			'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ī'=>'i', 'į'=>'i',
			'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 			'Ø'=>'O', 'Ō'=>'O', 'Œ'=>'OE',
			'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o',	'ø'=>'o', 'ō'=>'o', 'œ'=>'oe',
			'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', '×'=>'U', 'Ü'=>'U', 'Ū'=>'U', 'Ÿ'=>'Y',
			'ù'=>'u', 'ú'=>'u', 'û'=>'u', '÷'=>'u', 'ü'=>'u', 'ū'=>'u', 'ÿ'=>'y',
			'Ķ'=>'K', '¨'=>'L', 'Ł'=>'L', 'Ņ'=>'N', 'Ń'=>'N', 'Ž'=>'Z', 'Ż'=>'Z', 'Ź'=>'Z', 'Ț'=>'T', 'Þ'=>'B',
			'ķ'=>'k', '¸'=>'l', '³'=>'l', 'ņ'=>'n', 'ń'=>'n', 'ž'=>'z', 'ż'=>'z',			'ț'=>'t', 'þ'=>'b',
			'’'=>"'", 'ß'=>'Ss'
	);


		foreach($chars_arr as $key=>$val) {
			$str = str_replace($key, $val, $str);
		}

	return $str;
}
?>