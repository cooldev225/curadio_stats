<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	General
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Get Gender List
 *
 * @param string -> 'TITLE' or 'ID', default is 'ID'
 * @param string -> 'ASC' or 'DESC', default is 'ASC'
 * @return array
 */
function cug_get_gender_list($sort_by="ID", $sort_type="ASC")
{
global $mysqli, $Tables;
$result = array();
$field = "";
$index = 0;

	if($sort_by == "TITLE")
		$field = "title";
	else
		$field = "id";


	$r = $mysqli->query("SELECT * FROM ".$Tables['gender']." ORDER BY ".$field." ".$sort_type);

	if($r->num_rows) {
		while($arr = $r->fetch_array()) {
			$result[$index] = $arr;
			$index ++;
		}
	}

return $result;
}


/**
 * Get Genre ID and FileUnder ID by Genre Title
 * 
 * @param string $genre
 * @return array
 */
function cug_get_genre_ids($genre) {
	global $mysqli, $Tables;
	$result = array();

	$r = $mysqli->query("SELECT * FROM {$Tables['genre']} WHERE title='".$mysqli->escape_str($genre)."'");
	if($r->num_rows) {
		$row = $r->fetch_array();
		if($row['parent_id'] > 0) {
			$result['genre_id'] = $row['parent_id'];
			$result['fileunder_id'] = $row['id'];
		}
		else {
			$result['genre_id'] = $row['id'];
			$result['fileunder_id'] = 0;
		}
	}

	return $result;
}
?>