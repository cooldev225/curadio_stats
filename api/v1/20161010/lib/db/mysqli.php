<?PHP
include "tables.php";


/**
 * CUGATE
 *
 * @package		CuLib
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * MySQLi Database Class
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Database
 * @author		Khvicha Chikhladze
 */

 class cug__mysqli extends mysqli {

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct($db_host, $db_user, $db_password, $db_name, $server_port=NULL, $db_ssl=false, $server_key=NULL, $server_cert=NULL, $ca_cert=NULL) {	
		$port = (!empty($server_port)) ? $server_port : 3306;
		
		stream_context_set_default(array(
			'ssl'   => array(
			'peer_name' => 'Cugate Ltd',
			'verify_peer' => FALSE,
			'verify_peer_name' => FALSE,
			'allow_self_signed' => TRUE,
			),
		));
		
		self::init();

			if($db_ssl) { //SSL
				if(!empty($server_key) && !empty($server_cert) && !empty($ca_cert))
					self::ssl_set($server_key, $server_cert, $ca_cert, NULL, NULL);
				
				if(!self::real_connect($db_host, $db_user, $db_password, $db_name, $port, NULL, MYSQLI_CLIENT_SSL)) 
					die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());			
			}
			 else {		
				if(!self::real_connect($db_host, $db_user, $db_password, $db_name, $port))
					die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());				
			}

		self::set_charset("utf8");
	}
	
	
	
	/**
	 * Escapes special characters in a string
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	-> whether or not the string will be used in a LIKE condition, default value is FALSE
	 * @return	string
	 */	
	public function escape_str($str, $LIKE=FALSE) {
		$result = self::real_escape_string($str);
		
			// escape LIKE condition wildcards
			if($LIKE == TRUE)
				$result = str_replace(array('%', '_'), array('\\%', '\\_'), $result);
			
		return $result;	
	}

	
	/**
	 * Get Field Value
	 *
	 * @access	public
	 * @param	string	-> Table Name
	 * @param	string	-> Field Name
	 * @param	string	-> 'WHERE' Condition
	 * @return	array
	 */
	public function get_field_val($table, $get_field, $where) {
		$result = array();
		$index = 0;
		$query = "SELECT $get_field FROM $table WHERE $where";
			
			$r = self::query($query);
			
				if($r->num_rows) {
					
					while($arr = $r->fetch_array()){
						$result[$index] = $arr;
						$index ++;
					}
				}
		
		return $result;
	}
	
	/**
	 * Get Table Modify Time
	 * 
	 * @param string $table - Table Name
	 * @param string $time_format - Optional, 'datetime' or 'date', default is 'datetime'
	 * @return string (like: '2016-07-01 15:34:21' or '2016-07-01') 
	 */
	public function get_table_update_time($table, $time_format='datetime') {
	    $result = "";
	    
	    $time_format = (strtoupper($time_format) == 'DATETIME') ? "UPDATE_TIME" : "DATE_FORMAT(UPDATE_TIME, '%Y-%m-%d')";
        $query = "SELECT $time_format AS update_time FROM INFORMATION_SCHEMA.TABLES ";
        $query .= "WHERE TABLE_NAME ='$table'"; 
	    	
	    $r = self::query($query);
	    	
	    if($r->num_rows) {
	        $row = $r->fetch_assoc();
	        $result = $row['update_time'];
	    }
	
	    return $result;
	}
	
	
	/**
	 * Get Table Create Time
	 *
	 * @param string $table - Table Name
	 * @param string $time_format - Optional, 'datetime' or 'date', default is 'datetime'
	 * @return string (like: '2016-07-01 15:34:21' or '2016-07-01')
	 */
	public function get_table_create_time($table, $time_format='datetime') {
	    $result = "";
	     
	    $time_format = (strtoupper($time_format) == 'DATETIME') ? "CREATE_TIME" : "DATE_FORMAT(CREATE_TIME, '%Y-%m-%d')";
	    $query = "SELECT $time_format AS create_time FROM INFORMATION_SCHEMA.TABLES ";
	    $query .= "WHERE TABLE_NAME ='$table'";
	
	    $r = self::query($query);
	
	    if($r->num_rows) {
	        $row = $r->fetch_assoc();
	        $result = $row['create_time'];
	    }
	
	    return $result;
	}
	
	
	/**
	 * Rename Table
	 *
	 * @param string $table - Table Name to be renamed
	 * @param string $table_to - Table Name to be renamed to
	 * @return bool
	 */
	public function rename_table($table, $table_to) {

	    $query = "ALTER TABLE $table RENAME TO $table_to";	
	
	    if(self::query($query))
	        return true;
	    else 
	        return false;
	}
	
	
	/**
	 * Drop Table
	 *
	 * @param string $table - Table Name to be droped
	 * @return bool
	 */
	public function drop_table($table) {
	
	    $query = "DROP TABLE IF EXISTS $table";
	
	    if(self::query($query))
	        return true;
	    else
	        return false;
	}
	
	
	/**
	 * Check if Table exists
	 *
	 * @param string $table - Table Name to be checked
	 * @return bool
	 */
	public function table_exists($table) {
	
	    $query = "SHOW TABLES LIKE '$table'";
	    $r = self::query($query);
	
	    if($r && $r->num_rows)
	        return true;
	    else
	        return false;
	    
	}
	
	
	/**
	 * Check if Table exists in Database
	 * 
	 * @param string $db
	 * @param string $table
	 * @return boolean
	 */
	public function table_exists_in_db($db, $table) {	
	    $query = "SELECT * FROM information_schema.TABLES";
	    $query .= " WHERE (TABLE_SCHEMA = '$db') AND (TABLE_NAME = '$table')";
	    $r = self::query($query);
	
	    if($r && $r->num_rows)
	        return true;
	    else
	        return false;            
	}
	
	
	/**
	 * Get Number of rows from Table
	 * 
	 * @param string $table
	 * @return int
	 */
	public function get_table_num_rows($table) {
	    $result = 0;
	    
	    $query = "SELECT COUNT(*) FROM $table";	    	
	    $r = self::query($query);
	    	
	    if($r && $r->num_rows) {	        	
	        $row = $r->fetch_array();
	        $result = $row[0];
	    }
	
	    return $result;
	}
	
	
	/**
	 * Copy Table
	 * 
	 * @param string $table_source
	 * @param string $table_dest
	 * @return boolean
	 */
	public function copy_table($table_source, $table_dest) {
	    $result = false;
	    
	    $query = "CREATE TABLE IF NOT EXISTS $table_dest LIKE $table_source";
	
	    if(self::query($query)) {
	        $query = "ALTER TABLE $table_dest DISABLE KEYS";
	        
	        if(self::query($query)) {
	            $query = "INSERT INTO $table_dest SELECT * FROM $table_source";
	            
	            if(self::query($query)) {
	                $query = "ALTER TABLE $table_dest ENABLE KEYS";
	                
	                if(self::query($query)) 
	                    $result = true;
	            }
	        }
	    }

	    return $result;
	}
	
	
	/**
	 * Create Database
	 * 
	 * @param string $db_name
	 * @param string $char_set (Optional, default: 'utf8')
	 * @param string $collation (Optional, default: 'utf8_general_ci')
	 * @return boolean
	 */
	public function create_db($db_name, $char_set="utf8", $collation="utf8_general_ci") {
	    $query = "CREATE DATABASE $db_name CHARACTER SET $char_set COLLATE $collation";
	    
	    if(self::query($query)) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	
	/**
	 * Check if Database exists
	 * 
	 * @param string $db
	 * @return boolean
	 */
	public function db_exists($db) {	
	    $query = "SHOW DATABASES LIKE '$db'";
	    $r = self::query($query);
	
	    if($r && $r->num_rows)
	        return true;
	    else
	        return false;
	             
	}
}
?>