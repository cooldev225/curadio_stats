<?PHP

function multiRequest($data, $options = array()) {

	// array of curl handles
	$curly = array();
	// data to be returned
	$result = array();

	// multi handle
	$mh = curl_multi_init();

	// loop through $data and create curl handles
	// then add them to the multi-handle
	foreach ($data as $id => $d) {

		$curly[$id] = curl_init();

		$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
		curl_setopt($curly[$id], CURLOPT_URL,            $url);
		curl_setopt($curly[$id], CURLOPT_HEADER,         0);
		curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

		// post?
		if (is_array($d)) {
			if (!empty($d['post'])) {
				curl_setopt($curly[$id], CURLOPT_POST,       1);
				curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
			}
		}

		// extra options?
		if (!empty($options)) {
			curl_setopt_array($curly[$id], $options);
		}

		curl_multi_add_handle($mh, $curly[$id]);
	}

	// execute the handles
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);


	// get content and remove handles
	foreach($curly as $id => $c) {
		$result[$id] = curl_multi_getcontent($c);
		curl_multi_remove_handle($mh, $c);
	}

	// all done
	curl_multi_close($mh);

	return $result;
}


//---------------------
function cug_http_request($url, $url_params, $post_method=true, $timeout=30, $session_id="", $return_header=false, $ssl_verify=false, $accept_encoding="gzip") {
    
    if($ch = curl_init()) {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, ($timeout*1000)/2);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout*1000);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $url_params);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
        curl_setopt($ch, CURLOPT_POST, $post_method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);        

        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        //curl_setopt($ch, CURLOPT_CAINFO, "cugate.pem");

        if($session_id) curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . $session_id . '; path=/');
        if($accept_encoding) curl_setopt($ch, CURLOPT_ENCODING, $accept_encoding);

        curl_setopt($ch, CURLOPT_HEADER, $return_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }
    else {
        return false;
    }
}
?>