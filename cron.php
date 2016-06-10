<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 11. 5. 2016
 * Time: 19:11
 */
$interval = 60; //in seconds
while (true) {
	sleep($interval);
	$string = file_get_contents('www/cron.txt');
	if ($string == '') {
		continue;
	}

	$locked = file_get_contents('www/running.txt');
	if ($locked != '') {
		continue;
	}

	if ( ! isConnectedToInternet()) {
		continue;
	}
	
	$datetime = new DateTime($string);
	$difference = (new DateTime())->getTimestamp() - $datetime->getTimestamp();    //in seconds
	echo $difference . PHP_EOL;
	if ($difference < 0) {
		continue;
	}

	$output = shell_exec('php www/index.php bot:queue --debug-mode');
	echo $output . PHP_EOL;
}

function isConnectedToInternet() {

	$connected = @fsockopen('www.google.com', 80);
	if ($connected) {
		fclose($connected);
		return true;
	}
	return false;
}