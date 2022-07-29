<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	Core Library
 * @category	Links
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2016
 * @version		1.0
 */

// ------------------------------------------------------------------------



class cug__object_link {
    public 
    $id,
    $object_id,
    $object_item_id,
    $object_parent_item_id,
    $link_type_id,
    $link_num,
    $has_link_img,
    $link_img_url,
    $link_url,
    $link_title,
    $link_comment,
    $client_id,
    $user_id,
    $link_is_active,
    $register_date,
    $update_time;
    
    private $mysqli, $Tables;
    
    
    /**
     * Constructor
     * 
     * @param object $mysqli (link of db connection)
     * @param array $Tables
     */
    public function __construct($mysqli, $Tables) {
        $this->mysqli = $mysqli;
        $this->Tables = $Tables;
        
        $this->id = 0;
        $this->object_id = 0;
        $this->object_item_id = 0;
        $this->object_parent_item_id = 0;
        $this->link_type_id = 0;
        $this->link_num = 0;
        $this->has_link_img = 0;
        $this->link_img_url = "";
        $this->link_url = "";
        $this->link_title = 0;
        $this->link_comment = 0;
        $this->client_id = 0;
        $this->user_id = 0;
        $this->link_is_active = 1;
        $this->register_date = "";
        $this->update_time = "";
    }
    
    
    
    /**
     * Get Link(s)
     * 
     * @param int $id
     * @param number $object_id (Optional)
     * @param number $object_item_id (Optional)
     * @param number $object_parent_item_id (Optional)
     * @param number $link_type_id (Optional)
     * @param number $link_is_active (Optional, 1 - only active links; 0 - only inactive links; -1 - all links;)
     * @param number $link_num (Optional)
     * @param number $client_id (Optional)
     * @param number $user_id (Optional)
     * @return array
     */
    public function get_link($id, $object_id=0, $object_item_id=0, $object_parent_item_id=0, $link_type_id=0, $link_is_active=-1, $link_num=0, $client_id=0, $user_id=0) {
        $result = array();
        
        //generate query
        $query = "SELECT * FROM {$this->Tables['object_link']} WHERE";
        $where = "";
        
        if($id > 0)
            $where .= " id=$id AND";
        else {
            if($object_id > 0)
                $where .= " object_id=$object_id AND";
            if($object_item_id > 0)
                $where .= " object_item_id=$object_item_id AND";
            if($object_parent_item_id > 0)
                $where .= " object_parent_item_id=$object_parent_item_id AND";
            if($link_type_id > 0)
                $where .= " link_type_id=$link_type_id AND";
            if($link_num > 0)
                $where .= " link_num=$link_num AND";            
            if($client_id > 0)
                $where .= " client_id=$client_id AND";
            if($user_id > 0)
                $where .= " user_id=$user_id AND";            
            if($link_is_active >= 0) {
                $where .= ($link_is_active) ? " link_is_active=1 AND" : " link_is_active IS NULL AND";
            }
        }
        
        
        if($where) {
            $where = rtrim($where, "AND");
            $query .= $where." ORDER BY link_num";
            
            $r = $this->mysqli->query($query);
            if($r) {
                while($row = $r->fetch_assoc()) {
                    $result[] = $row;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Register New Link
     * 
     * @return int (0 - not enough fields; -1 - link already exists; -2 - error registering link; ID of the new link - success;)
     */
    public function reg_link() {
        
        if($this->object_id && $this->object_item_id && $this->link_type_id && $this->link_num && $this->link_url && $this->client_id && $this->user_id) {
            if($this->object_id == 1 && !$this->object_parent_item_id) // if object is Audio Tracks ('id' from 'object_list' table) then object_parent_item_id also must be provided
                return 0;
            
            //check for existing link
            $arr = $this->get_link($id=0, $this->object_id, $this->object_item_id, $this->object_parent_item_id, $this->link_type_id, $this->link_num, $link_is_active=-1, $client_id=0, $user_id=0);
            if(count($arr) > 0)
                return -1; //already exists
            
            //generate INSERT query
            $fields = "";
            $values = "";
            
            $fields .= "(object_id,object_item_id,";
            $fields .= ($this->object_parent_item_id) ? "object_parent_item_id," : "";
            $fields .= "link_type_id,link_num,";
            $fields .= ($this->has_link_img) ? "has_link_img," : "";
            $fields .= ($this->link_img_url) ? "link_img_url," : "";
            $fields .= ($this->link_url) ? "link_url," : "";
            $fields .= ($this->link_title) ? "link_title," : "";
            $fields .= ($this->link_comment) ? "link_comment," : "";
            $fields .= "client_id,user_id,";
            $fields .= ($this->link_is_active) ? "link_is_active," : "";
            $fields .= "register_date,";
            
            $values .= "VALUES($this->object_id,$this->object_item_id,";
            $values .= ($this->object_parent_item_id) ? "$this->object_parent_item_id," : "";
            $values .= "$this->link_type_id,$this->link_num,";
            $values .= ($this->has_link_img) ? "1," : "";
            $values .= ($this->link_img_url) ? "'".$this->mysqli->escape_str($this->link_img_url)."'," : "";
            $values .= ($this->link_url) ? "'".$this->mysqli->escape_str($this->link_url)."'," : "";
            $values .= ($this->link_title) ? "'".$this->mysqli->escape_str($this->link_title)."'," : "";
            $values .= ($this->link_comment) ? "'".$this->mysqli->escape_str($this->link_comment)."'," : "";
            $values .= "$this->client_id,$this->user_id,";
            $values .= ($this->link_is_active) ? "1," : "";
            $values .= ($this->register_date) ? "'".$this->mysqli->escape_str($this->register_date)."'," : "NOW(),";
                        
            $fields = rtrim($fields, ",").")";
            $values = rtrim($values, ",").")";
            
            $query = "INSERT INTO {$this->Tables['object_link']} $fields $values";

            //execute query
            if($this->mysqli->query($query))
                return $this->mysqli->insert_id;
            else 
                return -2; //error
        }
        else 
            return 0;
    }
    
    
    /**
     * Delete Link(s)
     * 
     * Delete links by $id only or by $object_id and $object_item_id or by ($object_id and $object_item_id) and ($object_parent_item_id or/and $link_type_id) 
     * 
     * @param int $id
     * @param number $object_id (Optional)
     * @param number $object_item_id (Optional)
     * @param number $object_parent_item_id (Optional)
     * @param number $link_type_id (Optional)
     * @return boolean (true - if sql query was sent to db server; false - not enough parameters)
     */
    public function del_link($id, $object_id=0, $object_item_id=0, $object_parent_item_id=0, $link_type_id=0) {
    
        //generate query
        $query = "DELETE FROM {$this->Tables['object_link']} WHERE";
        $where = "";
    
        if($id > 0)
            $where .= " id=$id AND";
        else {
            if($object_id > 0) {
                if($object_item_id > 0 && $object_parent_item_id > 0)
                    $where .= " object_id=$object_id AND object_item_id=$object_item_id AND object_parent_item_id=$object_parent_item_id AND";
                elseif($object_item_id > 0)
                    $where .= " object_id=$object_id AND object_item_id=$object_item_id AND";
                
                if($where){
                    if($link_type_id > 0)
                        $where .= " link_type_id=$link_type_id AND";
                }
            }
        }
    
    
        if($where) {
            $where = rtrim($where, "AND");
            $query .= $where;
            
            if($this->mysqli->query($query))
                return true;
            else 
                return false;
        }
        else 
            return false;

    }
    
    
    /**
     * Edit Link
     * 
     * @param int $id
     * @param boolean $update_empty_fields (Optional, default: false)
     * @return boolean (true - if sql query was sent to db server; false - not enough fields or no $id)
     */
    public function edit_link($id, $update_empty_fields=false) {
        $result = false;
        
        if($id > 0) {
            $fields = "";
            
            $fields .= ($this->object_id > 0 ) ? "object_id=$this->object_id," : "";
            $fields .= ($this->object_item_id > 0 ) ? "object_item_id=$this->object_item_id," : "";
            $fields .= ($this->object_parent_item_id > 0 ) ? "object_parent_item_id=$this->object_parent_item_id," : "";
            $fields .= ($this->link_type_id > 0 ) ? "link_type_id=$this->link_type_id," : "";
            $fields .= ($this->link_num > 0 ) ? "link_num=$this->link_num," : "";
            $fields .= ($this->has_link_img >= 0) ? (($this->has_link_img > 0) ? "has_link_img=1," : "has_link_img=null,") : "";
            $fields .= ($this->link_img_url) ? "link_img_url='".$this->mysqli->escape_str($this->link_img_url)."'," : "";
            $fields .= ($this->link_url) ? "link_url='".$this->mysqli->escape_str($this->link_url)."'," : "";
            
            $fields .= ($this->link_title) ? "link_title='".$this->mysqli->escape_str($this->link_title)."'," : (($update_empty_fields) ? "link_title=null," : "");
            
            $fields .= ($this->link_comment) ? "link_comment='".$this->mysqli->escape_str($this->link_comment)."'," : (($update_empty_fields) ? "link_comment=null," : "");
                       
            $fields .= ($this->client_id > 0 ) ? "client_id=$this->client_id," : "";
            $fields .= ($this->user_id > 0 ) ? "user_id=$this->user_id," : "";
            $fields .= ($this->link_is_active >= 0) ? (($this->link_is_active > 0) ? "link_is_active=1," : "link_is_active=null,") : "";
            $fields .= ($this->register_date) ? "register_date='".$this->mysqli->escape_str($this->register_date)."'," : "";
            
            if($fields) {
                $fields = rtrim($fields, ",");
                $query = "UPDATE {$this->Tables['object_link']} SET $fields WHERE id=$id";
                
                if($this->mysqli->query($query))
                    $result = true;
            }
        }
        
        return $result;
    }
    
    
    /**
     * Search Links
     * 
     * @param string $object_name ('ALBUM, 'TRACK', 'MEMBER')
     * @param int $object_item_id
     * @param int $object_parent_item_id
     * @param int $link_type_id
     * @param string $LINK_IMG_URL (for generating link image url)
     * @return array
     */
    public function search_links($object_name, $object_item_id, $object_parent_item_id, $link_type_id, $LINK_IMG_URL) {
        global $mysqli, $Tables;
        $result = array();
    
        $object_id = 0;
        switch(strtoupper($object_name)) {
            case "ALBUM":
                $object_id = 2;
            break;
            //--------------
            case "TRACK":
                $object_id = 1;
            break;
            //--------------
            case "MEMBER":
                $object_id = 4;
            break;
        }
    
        $arr = $this->get_link($id=0, $object_id, $object_item_id, $object_parent_item_id, $link_type_id, $link_is_active=1);
    
        //if TRACK has not some links then fullfill these links from it's ALBUM
        if($object_id == 1 && $object_parent_item_id > 0) {
            //get links from ALBUM
            $arr2 = $this->get_link($id=0, $object_id=2, $object_parent_item_id, 0, $link_type_id, $link_is_active=1);
    
            foreach($arr2 as $key2 => $val2) {
                $link_found = false;
    
                foreach($arr as $key => $val) {
                    if($val2['link_num'] == $val['link_num']) {
                        $link_found = true;
                        break;
                    }
                }
                //-------------------
                if(!$link_found) {
                    $index = count($arr);
                    $arr[$index] = $val2;
                }
            }
        }
        //------------------------------
    
        //sort array by 'link_num'
        cug_sort_array($arr, "link_num");
    
        //parse links result and construct final $result array
        foreach($arr as $key => $val) {
            $result[$key]['link_type_id'] = $val['link_type_id'];
            $result[$key]['link_num'] = $val['link_num'];
            $result[$key]['link_url'] = $val['link_url'];
            $result[$key]['link_title'] = $val['link_title'];
    
            $link_type_name = ($link_type_id == 1) ? "extralink" : "cube";
            $result[$key]['has_link_img'] = ($val['has_link_img']) ? $val['has_link_img'] : 0;
            $result[$key]['link_img_url_64'] = ($val['has_link_img']) ? $LINK_IMG_URL."/?o=$link_type_name&i={$val['id']}&s=64&in={$val['link_num']}" : $LINK_IMG_URL."/?o=$link_type_name&i=-1&s=64&in={$val['link_num']}";
            $result[$key]['link_img_url_174'] = ($val['has_link_img']) ? $LINK_IMG_URL."/?o=$link_type_name&i={$val['id']}&s=174&in={$val['link_num']}" : $LINK_IMG_URL."/?o=$link_type_name&i=-1&s=174&in={$val['link_num']}";
            $result[$key]['link_img_url_300'] = ($val['has_link_img']) ? $LINK_IMG_URL."/?o=$link_type_name&i={$val['id']}&s=300&in={$val['link_num']}" : $LINK_IMG_URL."/?o=$link_type_name&i=-1&s=300&in={$val['link_num']}";
        }
    
        return $result;
    }    
    
    
}
?>