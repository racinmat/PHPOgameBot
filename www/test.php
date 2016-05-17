<?php

$hours = 1 + 24/60 + 38/3600;
$minutes = ($hours - (int) $hours) * 60;
$seconds = ($minutes - (int) $minutes) * 60;
$hours = (int) $hours;
$minutes = (int) $minutes;
$seconds = (int) $seconds;
echo "{$hours}:$minutes:$seconds";
