<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 11. 5. 2016
 * Time: 19:11
 */
$interval = 60; //in seconds
while (true) {
	$string = file_get_contents('www/cron.txt');
	$datetime = new DateTime($string);
	$difference = abs($datetime->getTimestamp() - (new DateTime("2016-05-11 21:06:10"))->getTimestamp());    //in seconds
	if ($difference < $interval) {
		file_put_contents('www/cron.txt', '');
		shell_exec('php www/index.php bot:queue');
	}
	sleep($interval);
}
