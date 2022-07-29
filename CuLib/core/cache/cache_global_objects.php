<?PHP


/**
 * Update 'cache_country' table
 *
 * @return bool
 */
function cug_cache_update_country()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_country'])) { //empty 'cache_country' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['country']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_country']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."','".$mysqli->escape_str($arr['code_alpha2'])."','".$mysqli->escape_str($arr['code_alpha3'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);
				
			if($mysqli->query($query)) { //fill 'cache_country' table
				if(cug_cache_update_global_object("country")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_gender' table
 *
 * @return bool
 */
function cug_cache_update_gender()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_gender'])) { //empty 'cache_gender' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['gender']." ORDER BY id");
		
		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_gender']." VALUES";
				while($arr = $r->fetch_array()) {
					$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
				}

			$query = substr($query, 0, strlen($query) - 1);
			
				if($mysqli->query($query)) { //fill 'cache_gender' table
					if(cug_cache_update_global_object("gender")) {
						return true;
					}
					else {
						return false;
					}
				}
				else {
					return false;
				}	
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}		

}


/**
 * Update 'cache_role' table
 *
 * @return bool
 */
function cug_cache_update_role()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_role'])) { //empty 'cache_role' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['member_role']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_role']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);
				
			if($mysqli->query($query)) { //fill 'cache_role' table
				if(cug_cache_update_global_object("role")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_mood' table
 *
 * @return bool
 */
function cug_cache_update_mood()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_mood'])) { //empty 'cache_mood' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['mood']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_mood']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);

			if($mysqli->query($query)) { //fill 'cache_mood' table
				if(cug_cache_update_global_object("mood")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_tempo' table
 *
 * @return bool
 */
function cug_cache_update_tempo()
{
	global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_tempo'])) { //empty 'cache_tempo' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['tempo']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_tempo']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);

			if($mysqli->query($query)) { //fill 'cache_tempo' table
				if(cug_cache_update_global_object("tempo")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_tag_status' table
 *
 * @return bool
 */
function cug_cache_update_tag_status()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_tag_status'])) { //empty 'cache_tag_status' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['tag_status']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_tag_status']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);

			if($mysqli->query($query)) { //fill 'cache_tag_status' table
				if(cug_cache_update_global_object("tag_status")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_member_type' table
 *
 * @return bool
 */
function cug_cache_update_member_type()
{
global $mysqli, $Tables;

	if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_member_type'])) { //empty 'cache_member_type' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['member_type']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_member_type']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);

			if($mysqli->query($query)) { //fill 'cache_member_type' table
				if(cug_cache_update_global_object("member_type")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}


/**
 * Update 'cache_genre' table
 *
 * @return bool
 */
function cug_cache_update_genre() {
	global $mysqli, $mysqli_cache, $Tables;

	if($mysqli_cache->query("TRUNCATE TABLE ".$Tables['cache_genre'])) { //empty 'cache_genre' table
		$r = $mysqli->query("SELECT * FROM ".$Tables['genre']." ORDER BY id");

		if($r->num_rows) {
			//generate sql insert query
			$query = "INSERT INTO ".$Tables['cache_genre']." VALUES";
			while($arr = $r->fetch_array()) {
				$query .= "(".$arr['id'].",'".$mysqli->escape_str($arr['title'])."',".$arr['parent_id'].",'".$mysqli->escape_str($arr['icon_url'])."'),";
			}

			$query = substr($query, 0, strlen($query) - 1);

			if($mysqli_cache->query($query)) { //fill 'cache_genre' table
				if(cug_cache_update_global_object("genre")) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}

}

/**
 * Update timestamp for given object name in 'cache_objects' table
 *
 * @param string ('genre', 'role', 'mood', 'tempo', 'tag_status', 'member_type', 'gender')
 * @return bool
 */
function cug_cache_update_global_object($object_name)
{
global $mysqli, $Tables;

$timestamp = time();
	
	if($mysqli->query("UPDATE ".$Tables['cache_global_objects']." SET timestamp=$timestamp WHERE title='".$mysqli->escape_str($object_name)."'")) {
		
		//calculate hash value for all global objects
		$r = $mysqli->query("SELECT timestamp FROM ".$Tables['cache_global_objects']." ORDER BY id");
		
		if($r->num_rows) {
			$str = "";
				while($arr = $r->fetch_array()) {
					$str .= $arr['timestamp'];
				}
			
			$global_hash_value = hash("sha256", $str, false);
		}
		else {
			return false;
		}
		//----------------------------
		
			//update 'cache_objects_hash' table
			if(cug_cache_update_global_objects_hash($global_hash_value)) {
				return true;
			}
			else {
				return false;
			}
	}
	else {
		return false;
	}
	
}


/**
 * Calculate hash value from concatinated string of timestamps from 'cache_objects' table and update 'cache_objects_hash' table
 *
 * @param string
 * @return bool
 */
function cug_cache_update_global_objects_hash($hash_value)
{
global $mysqli, $Tables;

	if($hash_value) {
		if($mysqli->query("TRUNCATE TABLE ".$Tables['cache_global_objects_hash'])) { 
			if($mysqli->query("INSERT INTO ".$Tables['cache_global_objects_hash']." VALUES(1,'$hash_value')")) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

}

?>