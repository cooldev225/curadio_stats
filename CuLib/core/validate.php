<?PHP

/**
 * Validate ProductCode Type
 * 
 * @param string $str
 * @return boolean
 */
function cug_validate_productcode_type($str) {
    $arr = array("EAN", "UPC");
    
    if(in_array(strtoupper($str), $arr))
        return true;
    else 
        return false;
}

/**
 * Validate Product Type ID
 *
 * @param string $id
 * @return boolean
 */
function cug_validate_product_type_id($id) {
    global $mysqli, $Tables;
    
    $arr = $mysqli->get_field_val($Tables['album_type'], "id", "id=".$mysqli->escape_str($id));

    if(count($arr) > 0)
        return true;
    else
        return false;
}


/**
 * Validate Product Format ID
 *
 * @param string $id
 * @return boolean
 */
function cug_validate_product_format_id($id) {
    global $mysqli, $Tables;
    
    $arr = $mysqli->get_field_val($Tables['album_format'], "id", "id=".$mysqli->escape_str($id));

    if(count($arr) > 0)
        return true;
    else
        return false;
}


/**
 * Validate Gapless Playing
 *
 * @param string $str
 * @return boolean
 */
function cug_validate_gapless_playing($str) {
    $arr = array("YES", "NO");

    if(in_array(strtoupper($str), $arr))
        return true;
    else
        return false;
}


/**
 * Validate Explicit Content
 *
 * @param string $str
 * @return boolean
 */
function cug_validate_explicit_content($str) {
    $arr = array("YES", "NO");

    if(in_array(strtoupper($str), $arr))
        return true;
    else
        return false;
}


/**
 * Validate Member's Role ID
 *
 * @param string $id
 * @return boolean
 */
function cug_validate_member_role_id($id) {
    global $mysqli, $Tables;

    $arr = $mysqli->get_field_val($Tables['member_role'], "id", "id=".$mysqli->escape_str($id));

    if(count($arr) > 0)
        return true;
    else
        return false;
}


/**
 * Validate Genre and SubGenre ID
 *
 * @param string $id
 * @return boolean
 */
function cug_validate_genre_id($id) {
    global $mysqli, $Tables;

    $arr = $mysqli->get_field_val($Tables['genre'], "id", "id=".$mysqli->escape_str($id));

    if(count($arr) > 0)
        return true;
    else
        return false;
}


/**
 * Validate Country Code
 *
 * @param string $str
 * @return boolean
 */
function cug_validate_country_code($str) {
    global $mysqli, $Tables;

    if(strtoupper($str) == "WW")
        return true;
    else {
        $arr = $mysqli->get_field_val($Tables['country'], "id", "code_alpha2='".$mysqli->escape_str($str)."'");
        
        if(count($arr) > 0)
            return true;
        else
            return false;
    }
}


/**
 * Validate Country Codes Sequence
 *
 * @param string $str
 * @param char $delimiter
 * @return boolean
 */
function cug_validate_country_codes_sequence($str, $delimiter) {
    global $mysqli, $Tables;
    $result = false;
    
    $arr = explode($delimiter, $str);
    
    if(count($arr) > 0) {
        foreach($arr as $val) {
            if(cug_validate_country_code(trim($val)))
                $result = true;
            else {
                $result = false;
                break;
            }
        }
    }
    
    return $result;
}

/**
 * Validate Language Details Code
 *
 * @param string $id
 * @return boolean
 */
function cug_validate_lang_details_code($str) {
    global $mysqli, $Tables;

    $arr = $mysqli->get_field_val($Tables['lang_details'], "id", "code_represent='".$mysqli->escape_str($str)."'");

    if(count($arr) > 0)
        return true;
    else
        return false;
}


/**
 * Validate Price Code
 *
 * @param string $str
 * @return boolean
 */
function cug_validate_price_code($str) {
    global $mysqli, $Tables;

    $arr = $mysqli->get_field_val($Tables['price_code'], "id", "id=".$mysqli->escape_str($str));

    if(count($arr) > 0)
        return true;
    else
        return false;
}
?>