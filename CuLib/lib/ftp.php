<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

/**
 * FTP Class
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	FTP
 * @author		Khvicha Chikhladze
 */

class cug__ftp {
	private $ftp_server;
	private $ftp_user_name;
	private $ftp_user_pass;
	private $port;
	private $conn_id;
	private $login_result;
	public $curr_dir;
 

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	string
	 */
	  public function __construct($ftp_server, $ftp_user_name, $ftp_user_pass, $port=21) {
		  if ($ftp_server!="" && $ftp_user_name!="" && $ftp_user_pass!="") {
		  	
		   	$this->ftp_server = $ftp_server;
		   	$this->ftp_user_name = $ftp_user_name;
		   	$this->ftp_user_pass = $ftp_user_pass;
		   	$this->port = $port;
		 }
		  else {
		  	return FALSE;
		 }

		   
		 if (!$this->connect())
		 	return FALSE;
		 else 
		 	ftp_pasv($this->conn_id, TRUE);
	 }

	 
	//----------------------------
	private function connect() {
		$this->conn_id = @ftp_connect($this->ftp_server, $this->port);
		$this->login_result = @ftp_login($this->conn_id, $this->ftp_user_name, $this->ftp_user_pass);
	 
	 	if((!$this->conn_id) || (!$this->login_result))
	 		return FALSE;
	 	else
	 		return TRUE;
	}
	
	//----------------------------
	public function disconnect() {
		if($this->conn_id)
			ftp_close($this->conn_id);
	}	

	 
	/**
	 * Change Directory
	 *
	 * @param string
	 * @access	public
	 * @return	bool
	 */
	public function dir_change($dst_dir) {
		if(!ftp_chdir($this->conn_id, $dst_dir))
			return FALSE;
		else {
			$this->curr_dir = ftp_pwd($this->conn_id);
			return TRUE;
		}
			
	}

	
	/**
	 * Up one Level
	 *
	 * @access	public
	 * @return	bool
	 */
	public function dir_up() {
		if(!ftp_cdup($this->conn_id))
			return FALSE;
		else {
			$this->curr_dir = ftp_pwd($this->conn_id);
			return TRUE;
		}
			
	}	


	/**
	 * Send File
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function send_file($remote_file, $file, $mode=FTP_BINARY) {
		if(ftp_put($this->conn_id, $remote_file, $file, $mode))
			return TRUE;
		else 
			return FALSE;
	}
	
	
	/**
	 * Copy File on FTP Server
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public function copy_file($source_file, $temp_file, $dest_file, $delete_temp_file=true, $mode=FTP_BINARY) {
		$result = false;
		
		if(ftp_get($this->conn_id, $temp_file, $source_file, $mode)) {
			
			if(ftp_put($this->conn_id, $dest_file, $temp_file, $mode))
				$result = true;
			else 
				$result = false;
			//-------------------------
			
				if($delete_temp_file)
					@unlink($temp_file);
		}
		
		return $result;
	}	
	
	
	/**
	 * Make Directory
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function make_dir($dirname) {
		if(@ftp_mkdir($this->conn_id, $dirname))
			return TRUE;
		else
			return FALSE;
	}
	
	
	/**
	 * Delete File
	 *
	 * @access	public
	 * @return	bool
	 */
	public function delete_file($remote_file) {
		if(@ftp_delete($this->conn_id, $remote_file))
			return TRUE;
		else
			return FALSE;
	}
 }
?> 