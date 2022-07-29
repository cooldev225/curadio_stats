<?PHP

// GLOBALS
$F_LEVEL = 5; //Folders Level Depth
$F_DIGITS_NUM = 3; // Num of digits in Folder Name
$F_TOTAL_DIGITS = ($F_LEVEL * $F_DIGITS_NUM) + $F_DIGITS_NUM; // Total digits in File Name



/**
 * Copy Object Image
 *
 * Used in 'cug_copy_album()' function
 * Note: Works only if all images and this script are located on the same server
 * 
 * @param int $source_obj_id
 * @param int $new_obj_id
 * @param string $obj_type
 * @param string $subfolder
 * @return boolean
 */
function cug_copy_obj_img($source_obj_id, $new_obj_id, $obj_type, $subfolder) {
	global $slash, $TEMP_UPLOAD_PATH, $ftp_server, $ftp_user, $ftp_password, $ftp_port;
	
	$source_file_info = cug_get_obj_file_info($source_obj_id, $obj_type, $subfolder, $domain="");
	$new_file_info = cug_get_obj_file_info($new_obj_id, $obj_type, $subfolder, $domain="");
	
	$source_file = $source_file_info['dir_tree_u']."/".$source_file_info['basename'];
	$temp_file = $TEMP_UPLOAD_PATH.$source_file_info['basename'];
	$dest_file = $new_file_info['dir_tree_u']."/".$new_file_info['basename'];
	
	$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);
	
		if($ftp_conn) {
			$ftp_conn->make_dir($new_file_info['dir_tree_u']);
			
				if($ftp_conn->copy_file($source_file, $temp_file, $dest_file, $delete_temp_file=true))
					return true;
				else 
					return false;
		}
		
	return false;	
}


/**
 * Get Object File Info
 *
 * @param integer $obj_id
 * @param string $obj_type ('TRACK', 'ALBUM', 'DISC', 'MEMBER', 'USER', 'EXTRALINK', 'CUBE')
 * @param string $subfolder
 * @param string $domain (Optional)
 * @param string $obj_cat (Optional)
 * @return array
 */
 function cug_get_obj_file_info($obj_id, $obj_type, $subfolder, $domain="", $obj_cat="")
{
global $slash, $F_LEVEL, $F_DIGITS_NUM, $IMG_EXT;
$result = array();


	switch(strtoupper($obj_type)) {
		
		//--------------
		case 'TRACK':
			$main_folder = "music".$slash.$subfolder;
			$main_folder_url = "music/".$subfolder;
			$file_name_suffix = "";
			$file_extention = $subfolder;
		break;
		
		
		//-------------	
		case 'ALBUM':
			$main_folder = "img".$slash."album".$slash.$subfolder;
			$main_folder_url = "img/album/".$subfolder;
			$file_name_suffix = "_a_".$subfolder;
			$file_extention = $IMG_EXT['album'];
		break;
		
		
		//-------------
		case 'DISC':
			$main_folder = "img".$slash."disc".$slash.$subfolder;
			$main_folder_url = "img/disc/".$subfolder;
			$file_name_suffix = "_d_".$subfolder;
			$file_extention = $IMG_EXT['disc'];
		break;
		
		
		//-------------
		case 'MEMBER':
			$main_folder = "img".$slash."member".$slash.$subfolder;
			$main_folder_url = "img/member/".$subfolder;
			$file_name_suffix = "_m_".$subfolder;
			$file_extention = $IMG_EXT['member'];
		break;

		//-------------
		case 'USER':
			$main_folder = "img".$slash."user".$slash.$subfolder;
			$main_folder_url = "img/user/".$subfolder;
			$file_name_suffix = "_u_".$subfolder;
			$file_extention = $IMG_EXT['user'];
		break;
		
		//-------------
		case 'CLIENT':
			$main_folder = "img".$slash."client".$slash.$subfolder;
			$main_folder_url = "img/client/".$subfolder;
			$file_name_suffix = "_c_".$subfolder;
			$file_extention = $IMG_EXT['client'];
		break;
		//-------------
		case 'EXTRALINK':
		    $main_folder = "img".$slash."link".$slash."extra".$slash.$subfolder;
		    $main_folder_url = "img/link/extra/".$subfolder;
		    $file_name_suffix = "_le_".$subfolder;
		    $file_extention = $IMG_EXT['extra_ink'];
		break;
		//-------------
		case 'CUBE':
		    $main_folder = "img".$slash."link".$slash."cube".$slash.$subfolder;
		    $main_folder_url = "img/link/cube/".$subfolder;
		    $file_name_suffix = "_lc_".$obj_cat."_".$subfolder;
		    $file_extention = $IMG_EXT['cube_link'];
	    break;
	}
	
	
	
	$file_name = cug_get_obj_file_name($obj_id);
	$file_path_p = cug_get_obj_file_path_l($file_name);
	$file_path_u = cug_get_obj_file_path_u($file_name);
		
	$result['filename'] = $file_name.$file_name_suffix;
	$result['basename'] = $result['filename'].".".$file_extention;
	$result['dir_tree_l'] = $main_folder.$slash.$file_path_p;
	$result['dir_tree_u'] = $main_folder_url."/".$file_path_u;
	$result['url'] = ($domain != "") ? $domain."/".$main_folder_url."/".$file_path_u."/".$result['basename'] : "";

	
	
return $result;	
}	


/**
 * Create Sub Folders
 *
 * @param string
 * @param string
 * @return bool
 */
 function cug_create_subfolders($volume, $subfolders)
{
global $slash;
$folders_arr = explode($slash, $subfolders);
$folder = $volume;
$result = TRUE;

	for($i=0; $i<count($folders_arr); $i++) {
		$folder .= $slash.$folders_arr[$i];

			if(!file_exists($folder)) {
				if(!mkdir($folder)) {
					$result = FALSE;
					break;
				}
			}
	}

return $result;	
}



/**
 * Get Object File Path (Local)
 *
 * @param string
 * @return string
 */
function cug_get_obj_file_path_l($file_name)
{
global $slash, $F_LEVEL, $F_DIGITS_NUM;	
$path = "";

	for($i=0; $i<$F_LEVEL; $i++) {
		$chunk = substr($file_name, $i*$F_DIGITS_NUM, $F_DIGITS_NUM);
		$path .= $chunk.$slash;
	}
	
//$chunk = substr($file_name, $F_LEVEL*$F_DIGITS_NUM, $F_DIGITS_NUM);
//$path .= $chunk;
$path = substr( $path, 0, strlen($path) - strlen($slash) );
	
return $path;
}


/**
 * Get Object File Path (URL)
 *
 * @param string
 * @return string
 */
function cug_get_obj_file_path_u($file_name)
{
global $F_LEVEL, $F_DIGITS_NUM;
$path = "";

	for($i=0; $i<$F_LEVEL; $i++) {
		$chunk = substr($file_name, $i*$F_DIGITS_NUM, $F_DIGITS_NUM);
		$path .= $chunk."/";
	}

$path = substr( $path, 0, strlen($path) - 1 );

return $path;
}


/**
 * Get Object File Name
 *
 * @param integer
 * @return string
 */
 function cug_get_obj_file_name($obj_id)
{
global $F_TOTAL_DIGITS;	
$zeros = "";

	for($i=0; $i<($F_TOTAL_DIGITS - strlen($obj_id)); $i++) {
		$zeros .= "0";
	}
	
return $zeros.$obj_id;
}


/**
 * Upload Object's Image
 *
 * @param string $obj_type ('ALBUM', 'DISC', 'MEMBER', 'USER', 'CLIENT', 'EXTRALINK', 'CUBE')
 * @param integer $obj_id
 * @param string $img_file
 * @param bool $delete_original_file (Optional, default is TRUE)
 * @param string $server (Optional)
 * @param string $obj_cat (Optional)
 * @return array ([0] - result code; [1] - results array;)
 */
function cug_upload_obj_img($obj_type, $obj_id, $img_file, $delete_original_file=TRUE, $server="", $obj_cat="") {
    global $TEMP_UPLOAD_PATH, $ftp_server, $ftp_user, $ftp_password, $ftp_port, $CUG_MAX_IMG_SIZE, $FILE_SERVER_URL;
    $uploaded_files_num = 0;
    $uploaded_files = array();
    $result = array();

	if(!empty($img_file) && $obj_id > 0) {
			
		$img_obj = new cug__img_ffmpeg($img_file);

		if($img_obj->mimetype) {
			if($img_obj->width >= 64) {
				if(!$server) $server = $FILE_SERVER_URL;
				
				$resized_img_num = 0;
				
				//Resize Image
				//--------------------
				//34s
				$file_info_34 = cug_get_obj_file_info($obj_id, $obj_type, '34', $server, $obj_cat);
				$img_34_file_name = $file_info_34['basename'];
				$img_34_file = $TEMP_UPLOAD_PATH.$img_34_file_name;
				//if($img_obj->width >= 34) {
					$img_obj->resize(34, -1, $img_34_file);
					
					if(file_exists($img_34_file) && filesize($img_34_file) > 0)
						$resized_img_num ++;
				//}
	
				//64s
				$file_info_64 = cug_get_obj_file_info($obj_id, $obj_type, '64', $server, $obj_cat);
				$img_64_file_name = $file_info_64['basename'];
				$img_64_file = $TEMP_UPLOAD_PATH.$img_64_file_name;
				//if($img_obj->width >= 64) {
					$img_obj->resize(64, -1, $img_64_file);
					
					if(file_exists($img_64_file) && filesize($img_64_file) > 0)
						$resized_img_num ++;
				//}
	
				//174s
				$file_info_174 = cug_get_obj_file_info($obj_id, $obj_type, '174', $server, $obj_cat);
				$img_174_file_name = $file_info_174['basename'];
				$img_174_file = $TEMP_UPLOAD_PATH.$img_174_file_name;
				//if($img_obj->width >= 174) {
					$img_obj->resize(174, -1, $img_174_file);
					
					if(file_exists($img_174_file) && filesize($img_174_file) > 0)
						$resized_img_num ++;
				//}
	
				//300s
				$file_info_300 = cug_get_obj_file_info($obj_id, $obj_type, '300', $server, $obj_cat);
				$img_300_file_name =  $file_info_300['basename'];
				$img_300_file = $TEMP_UPLOAD_PATH.$img_300_file_name;
				//if($img_obj->width >= 300) {
					$img_obj->resize(300, -1, $img_300_file);
					
					if(file_exists($img_300_file) && filesize($img_300_file) > 0)
						$resized_img_num ++;
				//}
	
				//600s
				$file_info_600 = cug_get_obj_file_info($obj_id, $obj_type, '600', $server, $obj_cat);
				$img_600_file_name = $file_info_600['basename'];
				$img_600_file = $TEMP_UPLOAD_PATH.$img_600_file_name;
				//if($img_obj->width >= 600) {
					$img_obj->resize(600, -1, $img_600_file);
						
					if(file_exists($img_600_file) && filesize($img_600_file) > 0) {
						$resized_img_num ++;
						$img_obj_temp = new cug__img_ffmpeg($img_600_file);
	
						if(!file_exists($img_34_file)) $img_obj_temp->resize(34, -1, $img_34_file);
						if(!file_exists($img_64_file)) $img_obj_temp->resize(64, -1, $img_64_file);
						if(!file_exists($img_174_file)) $img_obj_temp->resize(174, -1, $img_174_file);
						if(!file_exists($img_300_file)) $img_obj_temp->resize(300, -1, $img_300_file);
					}
				//}
	
				//original
				$file_info_orgn = cug_get_obj_file_info($obj_id, $obj_type, 'mega', $server, $obj_cat);
				$img_orgn_file_name = $file_info_orgn['basename'];
				$img_orgn_file = $TEMP_UPLOAD_PATH.$img_orgn_file_name;
				
				if($img_obj->width > $CUG_MAX_IMG_SIZE) { // otherwise 'ffmpeg' can not convert image
					$img_obj->resize($CUG_MAX_IMG_SIZE, -1, $img_orgn_file);
					$img_obj->width = $CUG_MAX_IMG_SIZE;
				}
				else {
					$img_obj->convert($img_orgn_file);
				}
				
					if(file_exists($img_orgn_file) && filesize($img_orgn_file) > 0)
						$resized_img_num ++;
				//---------------------------------	
	
				
					
				if($resized_img_num > 0) {						
					
					//Upload Images on FILE Server
					$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);
		
					if($ftp_conn) {
		
						// 34
						//--------------------------------
						if(file_exists($img_34_file) && filesize($img_34_file)>0) {
							$ftp_conn->make_dir($file_info_34['dir_tree_u']);
							$ftp_conn->dir_change($file_info_34['dir_tree_u']);
		
							if($ftp_conn->send_file($img_34_file_name, $img_34_file)) {
								$uploaded_files_num ++;
								$uploaded_files[0] = 1;
							}
							else {
								$uploaded_files[0] = 0;
							}
		
							@unlink($img_34_file);
						}
						else {
							$uploaded_files[0] = 0;
						}
		
		
		
						// 64
						//--------------------------------
						if(file_exists($img_64_file) && filesize($img_64_file)>0) {
							$ftp_conn->dir_change("/");
							$ftp_conn->make_dir($file_info_64['dir_tree_u']);
							$ftp_conn->dir_change($file_info_64['dir_tree_u']);
		
							if($ftp_conn->send_file($img_64_file_name, $img_64_file)) {
								$uploaded_files_num ++;
								$uploaded_files[1] = 1;
							}
							else {
								$uploaded_files[1] = 0;
							}
		
							@unlink($img_64_file);
						}
						else {
							$uploaded_files[1] = 0;
						}
		
		
						// 174
						//--------------------------------
						if(file_exists($img_174_file) && filesize($img_174_file)>0) {
							$ftp_conn->dir_change("/");
							$ftp_conn->make_dir($file_info_174['dir_tree_u']);
							$ftp_conn->dir_change($file_info_174['dir_tree_u']);
		
							if($ftp_conn->send_file($img_174_file_name, $img_174_file)) {
								$uploaded_files_num ++;
								$uploaded_files[2] = 1;
							}
							else {
								$uploaded_files[2] = 0;
							}
		
							@unlink($img_174_file);
						}
						else {
							$uploaded_files[2] = 0;
						}
		
		
						// 300
						//--------------------------------
						if(file_exists($img_300_file) && filesize($img_300_file)>0) {
							$ftp_conn->dir_change("/");
							$ftp_conn->make_dir($file_info_300['dir_tree_u']);
							$ftp_conn->dir_change($file_info_300['dir_tree_u']);
		
							if($ftp_conn->send_file($img_300_file_name, $img_300_file)) {
								$uploaded_files_num ++;
								$uploaded_files[3] = 1;
							}
							else {
								$uploaded_files[3] = 0;
							}
		
							@unlink($img_300_file);
						}
						else {
							$uploaded_files[3] = 0;
						}
		
		
						// 600
						//--------------------------------
						if(file_exists($img_600_file) && filesize($img_600_file)>0) {
							$ftp_conn->dir_change("/");
							$ftp_conn->make_dir($file_info_600['dir_tree_u']);
							$ftp_conn->dir_change($file_info_600['dir_tree_u']);
		
							if($ftp_conn->send_file($img_600_file_name, $img_600_file)) {
								$uploaded_files_num ++;
								$uploaded_files[4] = 1;
							}
							else {
								$uploaded_files[4] = 0;
							}
		
							@unlink($img_600_file);
						}
						else {
							$uploaded_files[4] = 0;
						}
		
		
						// orgn
						//--------------------------------
						if(file_exists($img_orgn_file) && filesize($img_orgn_file)>0) {
							$ftp_conn->dir_change("/");
							$ftp_conn->make_dir($file_info_orgn['dir_tree_u']);
							$ftp_conn->dir_change($file_info_orgn['dir_tree_u']);
		
							if($ftp_conn->send_file($img_orgn_file_name, $img_orgn_file)) {
								$uploaded_files_num ++;
								$uploaded_files[5] = $img_obj->width;
							}
							else {
								$uploaded_files[5] = 0;
							}
		
							@unlink($img_orgn_file);
						}
						else {
							$uploaded_files[5] = 0;
						}
						//--------------------------------
		
						$ftp_conn->disconnect();
		
		
						if($uploaded_files_num > 0) {
							$result[0] = 1; //OK
							$result[1] = $uploaded_files;
						}
						else {
							$result[0] = -4;//Can't upload Images
						}
					}
					else {
						@unlink($img_34_file);
						@unlink($img_64_file);
						@unlink($img_174_file);
						@unlink($img_300_file);
						@unlink($img_600_file);
						@unlink($img_orgn_file);
						$result[0] = -3; //Can't connect to FTP
					}
				}
				else {
					$result[0] = -5; //Cannot resize Images
				}
			}
			else {
				$result[0] = -3; //Image Width is less then 64px
			}	
		}
		else {
			$result[0] = -2; //Image Error
		}
	}
	else {
		$result[0] = -1; //No Image File or No Member ID
	}


	if($delete_original_file) {
		@unlink($img_file);
	}

return $result;
}


/**
 * Update Object's Image Status
 *
 * @param string ('ALBUM', 'DISC', 'MEMBER', 'USER')
 * @param integer
 * @param array
 * @return bool
 */
function cug_update_obj_img_status($obj_type, $obj_id, $status_arr) {
    global $mysqli, $Tables;
    $update_subquery = "";

	if($obj_id > 0 && count($status_arr) > 0 && !empty($obj_type)) {
		$update_subquery .= ($status_arr[0] == 1) ? "img_34=1," : "img_34=null,";
		$update_subquery .= ($status_arr[1] == 1) ? "img_64=1," : "img_64=null,";
		$update_subquery .= ($status_arr[2] == 1) ? "img_174=1," : "img_174=null,";
		$update_subquery .= ($status_arr[3] == 1) ? "img_300=1," : "img_300=null,";
		$update_subquery .= ($status_arr[4] == 1) ? "img_600=1," : "img_600=null,";
		$update_subquery .= ($status_arr[5] > 0) ? "img_orgn=".$status_arr[5] : "img_orgn=null";

			switch(strtoupper($obj_type)) {
				
				case 'ALBUM':
					$table_name = $Tables['album'];
				break;
				//-------------
				case 'DISC':
					$table_name = $Tables['album_disc'];
				break;
				//-------------
				case 'MEMBER':
					$table_name = $Tables['member'];
				break;
				//-------------
				case 'USER':
					$table_name = $Tables['user'];
				break;
				//-------------
				case 'CLIENT':
					$table_name = $Tables['client'];
				break;
				//-------------	
				case 'EXTRALINK':
				case 'CUBE':
				    $table_name = $Tables['object_link'];
				break;
				//-------------			
				default:
					$table_name = "";
				break;
			}
		
				if($table_name) {
				    if($table_name == $Tables['object_link'])
				        $query = "UPDATE ".$table_name." SET has_link_img=1 WHERE id=".$obj_id;
				    else
					    $query = "UPDATE ".$table_name." SET ".$update_subquery." WHERE id=".$obj_id;
					
					   
						if($mysqli->query($query))
							return true;
						else
							return false;	
				}
				else {
					return false;
				}
	}
	else
		return false;
}


/**
 * Delete Track File
 *
 * @param integer (ID of File)
 * @return bool
 */
function cug_delete_track_file($file_id)
{
global $ftp_server, $ftp_user, $ftp_password, $ftp_port;
$files_deleted = 0;

$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);

	if($ftp_conn) {
	    //wav
	    $file_info = cug_get_obj_file_info($file_id, "TRACK", "wav");
	    $remote_file = $file_info['dir_tree_u']."/".$file_info['basename'];	    
	    if($ftp_conn->delete_file($remote_file)) $files_deleted ++;
	    
	    //mp3
		$file_info = cug_get_obj_file_info($file_id, "TRACK", "mp3");
		$remote_file = $file_info['dir_tree_u']."/".$file_info['basename'];
		if($ftp_conn->delete_file($remote_file)) $files_deleted ++;
		
		//wma
		$file_info = cug_get_obj_file_info($file_id, "TRACK", "wma");
		$remote_file = $file_info['dir_tree_u']."/".$file_info['basename'];
		if($ftp_conn->delete_file($remote_file)) $files_deleted ++;
		
		if($files_deleted > 0) {
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
 * Delete Object's Image File
 * 
 * @param int $obj_id
 * @param string $obj_type
 * @param string $domain
 * @param string $obj_cat (Default)
 */
function cug_delete_obj_img($obj_id, $obj_type, $domain, $obj_cat="") {
    global $ftp_server, $ftp_user, $ftp_password, $ftp_port;

	$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);

	if($ftp_conn) {
		
		switch(strtoupper($obj_type)) {
			//----------------
			case 'MEMBER':
				$file_info_34 = cug_get_obj_file_info($obj_id, "MEMBER", "34");
				$file_info_64 = cug_get_obj_file_info($obj_id, "MEMBER", "64");
				$file_info_174 = cug_get_obj_file_info($obj_id, "MEMBER", "174");
				$file_info_300 = cug_get_obj_file_info($obj_id, "MEMBER", "300");
				$file_info_600 = cug_get_obj_file_info($obj_id, "MEMBER", "600");
				$file_info_mega = cug_get_obj_file_info($obj_id, "MEMBER", "mega");
			break;
			
			//----------------
			case 'ALBUM':
				$file_info_34 = cug_get_obj_file_info($obj_id, "ALBUM", "34");
				$file_info_64 = cug_get_obj_file_info($obj_id, "ALBUM", "64");
				$file_info_174 = cug_get_obj_file_info($obj_id, "ALBUM", "174");
				$file_info_300 = cug_get_obj_file_info($obj_id, "ALBUM", "300");
				$file_info_600 = cug_get_obj_file_info($obj_id, "ALBUM", "600");
				$file_info_mega = cug_get_obj_file_info($obj_id, "ALBUM", "mega");
			break;
			
			//----------------
			case 'DISC':
				$file_info_34 = cug_get_obj_file_info($obj_id, "DISC", "34");
				$file_info_64 = cug_get_obj_file_info($obj_id, "DISC", "64");
				$file_info_174 = cug_get_obj_file_info($obj_id, "DISC", "174");
				$file_info_300 = cug_get_obj_file_info($obj_id, "DISC", "300");
				$file_info_600 = cug_get_obj_file_info($obj_id, "DISC", "600");
				$file_info_mega = cug_get_obj_file_info($obj_id, "DISC", "mega");
			break;	
			//----------------
			case 'EXTRALINK':
			    $file_info_34 = cug_get_obj_file_info($obj_id, "EXTRALINK", "34");
			    $file_info_64 = cug_get_obj_file_info($obj_id, "EXTRALINK", "64");
			    $file_info_174 = cug_get_obj_file_info($obj_id, "EXTRALINK", "174");
			    $file_info_300 = cug_get_obj_file_info($obj_id, "EXTRALINK", "300");
			    $file_info_600 = cug_get_obj_file_info($obj_id, "EXTRALINK", "600");
			    $file_info_mega = cug_get_obj_file_info($obj_id, "EXTRALINK", "mega");
			break;
			//----------------
			case 'CUBE':
			    $file_info_34 = cug_get_obj_file_info($obj_id, "CUBE", "34", $domain, $obj_cat);
			    $file_info_64 = cug_get_obj_file_info($obj_id, "CUBE", "64, $domain, $obj_cat");
			    $file_info_174 = cug_get_obj_file_info($obj_id, "CUBE", "174", $domain, $obj_cat);
			    $file_info_300 = cug_get_obj_file_info($obj_id, "CUBE", "300", $domain, $obj_cat);
			    $file_info_600 = cug_get_obj_file_info($obj_id, "CUBE", "600", $domain, $obj_cat);
			    $file_info_mega = cug_get_obj_file_info($obj_id, "CUBE", "mega", $domain, $obj_cat);
			break;			
		}
		
		
		$remote_file_34 = $file_info_34['dir_tree_u']."/".$file_info_34['basename'];
		$remote_file_64 = $file_info_64['dir_tree_u']."/".$file_info_64['basename'];
		$remote_file_174 = $file_info_174['dir_tree_u']."/".$file_info_174['basename'];
		$remote_file_300 = $file_info_300['dir_tree_u']."/".$file_info_300['basename'];
		$remote_file_600 = $file_info_600['dir_tree_u']."/".$file_info_600['basename'];
		$remote_file_mega = $file_info_mega['dir_tree_u']."/".$file_info_mega['basename'];

		
			if($ftp_conn->delete_file($remote_file_34) &&
				$ftp_conn->delete_file($remote_file_64) &&
				$ftp_conn->delete_file($remote_file_174) &&
				$ftp_conn->delete_file($remote_file_300) &&
				$ftp_conn->delete_file($remote_file_600) &&
				$ftp_conn->delete_file($remote_file_mega)) {
				
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
 * Upload Audio File
 *
 * @param string ('mp3', 'wav', default is 'mp3')
 * @param integer
 * @param string
 * @param bool (default is FALSE)
 * @return int
 */
function cug_upload_audio_file($file_type='mp3', $obj_id, $audio_file, $delete_original_file=false, $server="") {
	global $ftp_server, $ftp_user, $ftp_password, $ftp_port, $MUSIC_SERVER_URL;
	$result = 0;

	if(!empty($audio_file) && $obj_id > 0) {
		if(file_exists($audio_file) && filesize($audio_file)>0) {
			if(!$server) $server = $MUSIC_SERVER_URL;
			
			$file_info = cug_get_obj_file_info($obj_id, $obj_type="TRACK", $file_type, $server);
			$file_name = $file_info['basename'];
			
			//Upload Audio File
			$ftp_conn = new cug__ftp($ftp_server, $ftp_user, $ftp_password, $ftp_port);
			
				if($ftp_conn) {
					$ftp_conn->make_dir($file_info['dir_tree_u']);
					$ftp_conn->dir_change($file_info['dir_tree_u']);
			
						if($ftp_conn->send_file($file_name, $audio_file)) {
							$result = 1; //OK
						}
						else {
							$result = -1; //Can't upload file
						}		

					$ftp_conn->disconnect();
				}
				else {
					$result = -2; //Can't connect to FTP
				}
			//--------------------	
				

				if($delete_original_file) {
					@unlink($audio_file);
				}
	
		}
		else {
			$result = -3; //File does not exists
		}
	}
	else {
		$result = -4; //No file or file id
	}
	
	
	return $result;
}
?>