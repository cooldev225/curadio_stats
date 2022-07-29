<?PHP
/**
 * CUGATE
 *
 * @package		CuLib
 * @subpackage	External Library
 * @category	XML
 * @author		Khvicha Chikhladze
 * @copyright	Copyright (c) 2013
 * @version		1.0
 */

// ------------------------------------------------------------------------


class cug__xml_encode {

	/**
	 * Encode an object as XML string
	 *
	 * @param Object $obj
	 * @param string $root_node
	 * @return string $xml
	 */
	public function encodeObj($obj, $root_node = 'cugate') {
		$xml = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
		$xml .= self::encode($obj, $root_node, $depth = 0);
		return $xml;
	}


	/**
	 * Encode an object as XML string
	 *
	 * @param Object|array $data
	 * @param string $root_node
	 * @param int $depth Used for indentation
	 * @return string $xml
	 */
	private function encode($data, $node, $depth) {
		$xml = str_repeat("\t", $depth);

		if(is_array($data)) {

			$xml .= "<{$node}>" . PHP_EOL;

			foreach($data as $key => $val) {
			  if(is_array($val) || is_object($val)) {
			  	if(!empty($data[$key]['attributes']))
			  		$xml .= self::encode_attr($val, $key, ($depth + 1));
			  	else
			  		$xml .= self::encode($val, $key, ($depth + 1));
			  }
			  else {
			  	$xml .= str_repeat("\t", ($depth + 1));
			  	$xml .= "<{$key}>" . htmlspecialchars($val) . "</{$key}>" . PHP_EOL;
			  }
			}

			$xml .= str_repeat("\t", $depth);
			$xml .= "</{$node}>" . PHP_EOL;
			return $xml;
		}
		else {
			$xml .= "<{$node}>" . htmlspecialchars($data) . "</{$node}>" . PHP_EOL;
			return $xml;
		}
	}

	
	/**
	 * Encode an object with attribute as XML string
	 *
	 * @param array $data
	 * @param string $node
	 * @param int $depth Used for indentation
	 * @return string $xml
	 */
	private function encode_attr($data, $node, $depth) {
		$xml = str_repeat("\t", $depth);

		foreach($data as $key => $val) {
			if($key == "attributes") {
				$xml .= "<{$node}";
					foreach($data['attributes'] as $key1 => $val1) {
						$xml .= " {$key1}=\"".htmlspecialchars($val1)."\"";
					}
				$xml .= ">" . PHP_EOL;
			}
			elseif(!empty($data[$key]['attributes'])) {
				$xml .= self::encode_attr($val, $key, ($depth + 1));
			}
			else {
				$xml .= self::encode($val, $key, ($depth + 1));
			}
		}
			
		$xml .= str_repeat("\t", $depth);
		$xml .= "</{$node}>" . PHP_EOL;

		return $xml;
	}

	
/*
 How to use:
 -------------
$arr['data']['attributes'] = array('a1' => 11, 'a2' => 22);
$arr['data']['a'] = "Soko";
$arr['data']['b'] = 1;
$arr['data']['c']['attributes'] = array('attr1' => 11, 'attr2' => 22);
$arr['data']['c']['id'] = 123;
$arr['data']['c']['data'] = "Data here";
$arr['data']['c']['array']['attributes'] = array('attr11' => 11, 'attr22' => 22);
$arr['data']['c']['array']['arr1'] = 111;
$arr['data']['c']['array']['arr2']['attributes'] = array('attr33' => 31, 'attr44' => 42);
$arr['data']['c']['array']['arr2']['lola'] = 222;
  
$xml = new cug__xml_encode();
echo $xml->encodeObj($arr, $root_node = 'cugate'); 
 
 */	
	
}




/**
 * Write XML Header
 *
 * @param string -> default is '1.0'
 * @param string -> default is 'UTF-8'
 * @param string -> optional
 * @return string
 */
function cug_xml_header($version="1.0", $encoding="UTF-8", $suffix="")
{
return "<?xml version=\"$version\" encoding=\"$encoding\"?>$suffix";
}


/**
 * Write Start Tag
 *
 * @param string
 * @param array
 * @param string
 * @param string
 * @param integer
 * @return string
 */
function cug_xml_start_tag($tag_name, $attributes=array(), $prefix="", $suffix="", $close_tag=0)
{
$output = "";
	
	 if($tag_name)
	{
	$output .= $prefix."<".$tag_name;
	
		 if( count($attributes) )
		{
		$keys_arr = array_keys($attributes);
		
			 for($i=0; $i<count($keys_arr); $i++)
			{
				$output .= " ".$keys_arr[$i]."="."\"".cug_xml_entities($attributes[$keys_arr[$i]])."\"";
			}
			
		}
	
		 
		 if($close_tag > 0)
		$output .= "/>".$suffix;
		 else		
		$output .= ">".$suffix;
	}

return $output;	
}


/**
 * Write Element
 *
 * @param string
 * @return string
 */
function cug_xml_element($element)
{
	 if($element)
	return cug_xml_entities($element);
}


/**
 * Write End Tag
 *
 * @param string
 * @param string
 * @param string
 * @return string
 */
function cug_xml_end_tag($tag_name, $prefix="", $suffix="")
{
	 if($tag_name)
	return $prefix."</".$tag_name.">".$suffix;
}



/**
 * Change special characters
 *
 * @param string
 * @return string
 */
 function cug_xml_entities($string) 
{
return str_replace(array("&", "<", ">", "\"", "'"), array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $string);
}



/**
 * Convert XML String to Array
 *
 * @param string
 * @return array
 */
function cug_xmlstr_to_array($xmlstr)
{
$doc = new DOMDocument();
$doc->loadXML($xmlstr);
return cug_domnode_to_array($doc->documentElement);
}



/**
 * Convert DOMNode to Array
 *
 * @param string
 * @return array
 */
function cug_domnode_to_array($node)
{
$output = array();

	switch ($node->nodeType)
	{
		case XML_CDATA_SECTION_NODE:
		case XML_TEXT_NODE:
			$output = trim($node->textContent);
			break;

		case XML_ELEMENT_NODE:
		 for ($i=0, $m=$node->childNodes->length; $i<$m; $i++)
		 {
		 	$child = $node->childNodes->item($i);
		 	$v = cug_domnode_to_array($child);
		 		
			 if(isset($child->tagName))
			 {
			 	$t = $child->tagName;

				 if(!isset($output[$t]))
				 {
				 	$output[$t] = array();
				 }

				 $output[$t][] = $v;
			 }
			 elseif($v)
			 {
			 	$output = (string) $v;
			 }
		 }
		 	
		 if(is_array($output))
			{
				if($node->attributes->length)
				{
					$a = array();

					foreach($node->attributes as $attrName => $attrNode)
					{
						$a[$attrName] = (string) $attrNode->value;
					}
						
					$output['@attributes'] = $a;
				}

				foreach ($output as $t => $v)
				{
					if(is_array($v) && count($v)==1 && $t!='@attributes')
					{
						$output[$t] = $v[0];
					}
				}
			}
			break;
	}

return $output;
}
?>