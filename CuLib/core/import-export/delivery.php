<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Import-Export
 * @package     Products Delivery
 * @subpackage  Log
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2016
 * @version		1.0
 */

// ------------------------------------------------------------------------

/**
 * Get Log (Delivered Packages)
 * 
 * @param int $client_id
 * @param int $package_log_id (Optional)
 * @param string $package_name (Optional)
 * @return array
 */
function cug_get_delivered_package_log($client_id, $package_log_id=0, $package_name="") {
    global $mysqli, $Tables;
    $result = array();

    if($client_id > 0 || $package_log_id > 0 || $package_name) {
        $query = "SELECT * FROM {$Tables['delivery_package_log']} WHERE";
        
        if($package_log_id > 0)
            $query .= " id=".$mysqli->escape_str($package_log_id)." AND";
        else {
            $query .= ($client_id > 0) ? " client_id=".$mysqli->escape_str($client_id)." AND" : "";
            $query .= ($package_name) ? " package_name='".$mysqli->escape_str($client_id)."' AND" : "";
        }
        $query = rtrim($query, "AND");

        $r = $mysqli->query($query);
        if($r->num_rows) {
            while($row = $r->fetch_assoc()) {
                $result[] = $row;
            }
        }
    }

    return $result;
}


/**
 * Get Log (Delivered Product)
 *
 * @param int $id
 * @param int $package_log_id (Optional)
 * @param string $product_name (Optional)
 * @param int $album_id (Optional)
 * @return array
 */
function cug_get_delivered_product_log($id, $package_log_id=0, $product_name="", $album_id=0) {
    global $mysqli, $Tables;
    $result = array();

    if($id > 0 || $package_log_id > 0 || $product_name) {
        $query = "SELECT * FROM {$Tables['delivery_product_log']} WHERE";

        if($id > 0)
            $query .= " id=".$mysqli->escape_str($id)." AND";
        else {
            $query .= ($package_log_id > 0) ? " package_log_id=".$mysqli->escape_str($package_log_id)." AND" : "";
            $query .= ($product_name) ? " product_name='".$mysqli->escape_str($product_name)."' AND" : "";
            $query .= ($album_id > 0) ? " album_id=".$mysqli->escape_str($album_id)." AND" : "";
        }
            $query = rtrim($query, "AND");

            $r = $mysqli->query($query);
            if($r->num_rows) {
                while($row = $r->fetch_assoc()) {
                    $result[] = $row;
                }
            }
    }

    return $result;
}

/**
 * Register Delivered Package Log
 * 
 * @param int $client_id
 * @param string $package_name
 * @return number
 */
function cug_reg_delivered_package_log($client_id, $package_name) {
    global $mysqli, $Tables;
    $result = 0;
    
    if($client_id > 0 && $package_name) {
        $query = "INSERT INTO {$Tables['delivery_package_log']} (client_id, package_name, process_start_time, status) VALUES(";
        $query .= "$client_id,'".$mysqli->escape_str($package_name)."','".@date("Y-m-d H:i:s")."',1)";
        
        if($mysqli->query($query))
            $result = $mysqli->insert_id;
        else
            $result = -1; //error
        
    }
        
    return $result;
}


/**
 * Update Delivered Package Log
 * 
 * @param int $id
 * @param string $process_end_time (Optional)
 * @param number $status (Optional)
 * @param number $error_code (Optional)
 * @param number $email_sent (Optional)
 * @param string $email_msg (Optional)
 * @return boolean
 */
function cug_update_delivered_package_log($id, $process_end_time="", $status=0, $error_code=0, $email_sent=0, $email_msg="") {
    global $mysqli, $Tables;
    $result = false;
    
    if($id > 0) {
        $query = "UPDATE {$Tables['delivery_package_log']} SET ";
        $fields = "";
        
        $fields .= ($process_end_time) ? "process_end_time='$process_end_time'," : "process_end_time='".@date("Y-m-d H:i:s")."',";
        $fields .= ($status != 0) ? "status=$status," : "";
        $fields .= ($error_code != 0) ? "error_code=$error_code," : "";
        $fields .= ($email_sent != 0) ? "email_sent=$email_sent," : "";
        $fields .= ($email_msg) ? "email_msg='".$mysqli->escape_str($email_msg)."'," : "";
        
        if($fields) {
            $fields = rtrim($fields, ",");
            $query .= $fields." WHERE id=$id";
            
            if($mysqli->query($query))
                $result = true;
        }
    }
    
    return $result;
}


/**
 * Delete Delivered Package Log
 * 
 * @param int $id
 * @param number $client_id (Optional)
 * @param string $package_name (Optional)
 * @return boolean
 */
function cug_del_delivered_package_log($id, $client_id=0, $package_name="") {
    global $mysqli, $Tables;
    $result = false;   
    $where = "";
    
    if($id > 0) {
        $where = "id=$id AND";
    }
    else {
        if($client_id > 0) {
            $where = " client_id=$client_id AND";
            $where .= ($package_name) ? " package_name='".$mysqli->escape_str($package_name)."' AND" : "";
        }
    }
    //--------------------
    
    
    if($where) {
        $where = rtrim($where, "AND");
        $query = "DELETE FROM {$Tables['delivery_package_log']} WHERE ".$where;
        
        if($mysqli->query($query))
            $result = true;
    }
    
    return $result;
}


/**
 * Registered Delivered Product Log
 * 
 * @param int $package_log_id
 * @param string $product_name
 * @return int
 */
function cug_reg_delivered_product_log($package_log_id, $product_name) {
    global $mysqli, $Tables;
    $result = 0;

    if($package_log_id > 0 && $product_name) {
        $query = "INSERT INTO {$Tables['delivery_product_log']} (package_log_id, product_name, process_start_time, status) VALUES(";
        $query .= "$package_log_id,'".$mysqli->escape_str($product_name)."','".@date("Y-m-d H:i:s")."',1)";

        if($mysqli->query($query))
            $result = $mysqli->insert_id;
        else
            $result = -1; //error

    }

    return $result;
}


/**
 * Update Delivered Product Log
 * 
 * @param int $id
 * @param int $action_id (Optional)
 * @param string $xml_version (Optional)
 * @param string $error_msg (Optional)
 * @param int $album_id (Optional)
 * @param string $process_end_time (Optional)
 * @param number $status (Optional)
 * @param number $error_code (Optional)
 * @return boolean
 */
function cug_update_delivered_product_log($id, $action_id=0, $xml_version="", $error_msg="", $album_id=0, $process_end_time="", $status=0, $error_code=0) {
    global $mysqli, $Tables;
    $result = false;

    if($id > 0) {
        $query = "UPDATE {$Tables['delivery_product_log']} SET ";
        $fields = "";

        $fields .= ($action_id != 0) ? "action_id=$action_id," : "";
        $fields .= ($album_id != 0) ? "album_id=$album_id," : "";
        $fields .= ($xml_version) ? "xml_version='$xml_version'," : "";
        $fields .= ($error_msg) ? "error_msg='".$mysqli->escape_str($error_msg)."'," : "";
        $fields .= ($process_end_time) ? "process_end_time='$process_end_time'," : "process_end_time='".@date("Y-m-d H:i:s")."',";
        $fields .= ($status != 0) ? "status=$status," : "";
        $fields .= ($error_code != 0) ? "error_code=$error_code," : "";

        if($fields) {
            $fields = rtrim($fields, ",");
            $query .= $fields." WHERE id=$id";

            if($mysqli->query($query))
                $result = true;
        }
    }

    return $result;
}



/**
 * Delete Delivered Product Log
 * 
 * @param int $id
 * @param int $package_log_id (Optional)
 * @param string $product_name (Optional)
 * @return boolean
 */
function cug_del_delivered_product_log($id, $package_log_id=0, $product_name="") {
    global $mysqli, $Tables;
    $result = false;
    $where = "";

    if($id > 0) {
        $where = "id=$id AND";
    }
    else {
        if($package_log_id > 0) {
            $where = " package_log_id=$package_log_id AND";
            $where .= ($product_name) ? " product_name='".$mysqli->escape_str($product_name)."' AND" : "";
        }
    }
    //--------------------


    if($where) {
        $where = rtrim($where, "AND");
        $query = "DELETE FROM {$Tables['delivery_product_log']} WHERE ".$where;

        if($mysqli->query($query))
            $result = true;
    }

    return $result;
}


/**
 * Update Delivered Object Log
 * 
 * @param string $object
 * @param int $object_log_id
 * @param string $process_end_time (Optional)
 * @param number $status (Optional)
 * @param number $error_code (Optional)
 * @param number $action_id (Optional)
 * @param string $xml_version (Optional)
 * @param string $error_msg (Optional)
 * @param int $album_id (Optional)
 * @param number $email_sent (Optional)
 * @param string $email_msg (Optional)
 * @return boolean
 */
function cug_update_delivered_object_log($object, $object_log_id, $process_end_time="", $status=0, $error_code=0, $action_id=0, $xml_version="", $error_msg="", $album_id=0, $email_sent=0, $email_msg="") {
    $result = false;
    
    switch(strtoupper($object)) {
        case 'PACKAGE':
            $result = cug_update_delivered_package_log($object_log_id, $process_end_time, $status, $error_code, $email_sent, $email_msg);
        break;
        //--------------------
        case 'PRODUCT':
            $result = cug_update_delivered_product_log($object_log_id, $action_id, $xml_version, $error_msg, $album_id, $process_end_time, $status, $error_code);
        break;    
    }
    
    return $result;
}


/**
 * Get Delivery Actions List
 * 
 * @return array
 */
function cug_get_delivery_actions() {
    global $mysqli, $Tables;
    $result = array();
    
    $r = $mysqli->query("SELECT id, title FROM {$Tables['delivery_action_list']} ORDER BY id");
    if($r->num_rows) {
        while($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
    }
    
    return $result;
}


/**
 * Get Deliver Error
 *
 * @param int $error_id
 * @return array
 */
function cug_get_delivery_error($error_id) {
    global $mysqli, $Tables;
    $result = array();

    if($error_id > 0) {
        $r = $mysqli->query("SELECT * FROM {$Tables['delivery_error_list']} WHERE id=$error_id");
        if($r->num_rows) {
            $row = $r->fetch_assoc();
            $result = $row; 
        }
    }

    return $result;
}
?>