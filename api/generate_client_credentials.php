<?PHP
$client_id =  substr(number_format(time() * mt_rand(),0,'',''), 0, 20);
echo "<b>client id: </b>".$client_id."</br>";

$random_data = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
$client_secret = substr(hash('sha512', $random_data), 0, 32);
echo "<b>client_secret:</b> ".$client_secret;
?>