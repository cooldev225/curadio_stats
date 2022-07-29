<?php
$result['start_date'] = date("Y-01-01", strtotime('now'));
$result['start_time'] = $result['start_date']." 00:00:00";
$result['start_timestamp'] = strtotime($result['start_time']) * 1000;

$result['end_date'] = date("Y-m-d", strtotime('-1 day'));
$result['end_time'] = $result['end_date']." 23:59:59";
$result['end_timestamp'] = strtotime($result['end_time']) * 1000;


var_dump($result);