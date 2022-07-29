<?PHP

/**
 * Upload Image
 * 
 * @param string $file_input (Name of of the 'file' input tag in HTML)
 * @param string $obj_type ('ALBUM', 'DISC', 'MEMBER', 'USER', 'CLIENT', 'EXTRALINK', 'CUBE')
 * @param int $obj_id
 * @param boolean $delete_original_file (Optional, default: true)
 * @param number $file_size_limit (Optional, Number in bytes, default: 8388608 (8MB))
 * @param string $server (Optional)
 * @param string $obj_cat (Optional)
 * @return number (1 - OK;  Negative numbers on error;)
 */
function cug_upload_img($file_input, $obj_type, $obj_id, $delete_original_file=true, $file_size_limit=8388608, $server="", $obj_cat="") {
    global $TEMP_UPLOAD_PATH, $CUG_MAX_IMG_SIZE, $slash;

    if($obj_type && $obj_id > 0 && !empty($_FILES[$file_input])) {
        	
        if($_FILES[$file_input]["size"] <= $file_size_limit) {
            if(cug_img_validate($_FILES[$file_input]['tmp_name'], array('jpg', 'jpeg', 'png', 'tif'))) {
                	
                $path_parts = pathinfo($_FILES[$file_input]['name']);
                $session_id = (session_status() === PHP_SESSION_ACTIVE) ? session_id() : uniqid();
                $temp_file = $TEMP_UPLOAD_PATH.$obj_type."_".$session_id."_".uniqid().".".$path_parts['extension'];
                	
                if(move_uploaded_file($_FILES[$file_input]['tmp_name'], $temp_file)) {
                    $result = cug_upload_obj_img($obj_type, $obj_id, $temp_file, $delete_original_file, $server, $obj_cat);

                    if($result[0] == 1) {//OK
                        cug_update_obj_img_status($obj_type, $obj_id, $result[1]);
                        return 1; //OK
                    }
                    elseif($result[0] == -3) {
                        return -6; //File is too small, it is less than 64px
                    }
                    else {
                        return -5; //Can not resize image
                    }
                }
                else {
                    return -4; //A problem occurred during file upload
                }
            }
            else {
                return -3; //Not allowed file type
            }
        }
        else {
            return -2; //File is too big
        }
    }
    else {
        return -1; //Not enough parameters
    }
}
?>