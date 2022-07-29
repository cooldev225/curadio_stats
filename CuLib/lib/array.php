<?PHP

function cug_sort_array($arr, $sort_by_column, $sort_type=SORT_ASC) {
	if(count($arr) > 0 && $sort_by_column) {
		// Obtain a list of columns
		foreach ($arr as $key => $row) {
			$culumns[$key]  = $row[$sort_by_column];
		}
		//--------------------
		
		array_multisort($culumns, $sort_type, $arr);
	}
	
	return $arr;
}



function cug_search_in_array($arr, $colums_val_arr) {
	$columns_num = count($colums_val_arr);
	$result = false;
	
	foreach($arr as $val) {
		$matched_columns = 0;
		
		foreach($colums_val_arr as $key => $val2) {
			if($val[$key] == $val2) {
				$matched_columns ++;
			}
		}
		
		if($matched_columns == $columns_num) {
			$result = true;
			break;
		}
	}
	
	return $result;
}
?>