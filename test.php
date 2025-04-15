<?php

date_default_timezone_set("Asia/Manila");

$last_time = strtotime(date('h:i:s A'));
$time_last = strtotime('10:09:00 AM');

$sec = abs($last_time - $time_last)/3600;
$sec = $sec * 60;
$sec = $sec * 60;

echo date('h:i:s A',$last_time).'<br>';
echo date('h:i:s A',$time_last).'<br>';
echo round($sec);