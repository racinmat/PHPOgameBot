<?php

require_once __DIR__ . '/../vendor/autoload.php';

$hours = 1 + 24/60 + 38/3600;
$minutes = ($hours - (int) $hours) * 60;
$seconds = ($minutes - (int) $minutes) * 60;
$hours = (int) $hours;
$minutes = (int) $minutes;
$seconds = (int) $seconds;
echo "{$hours}:$minutes:$seconds";

$interval = "26min";
$params = \Nette\Utils\Strings::match($interval, '~((?<weeks>\d{1,2})t)? ?((?<days>\d{1,2})d)? ?((?<hours>\d{1,2})hod)? ?((?<minutes>\d{1,2})min)? ?((?<seconds>\d{1,2})s)?~');
var_dump($params);
echo $params['seconds'] ?? 0;
