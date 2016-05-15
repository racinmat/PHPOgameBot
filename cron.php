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
	$difference = (new DateTime())->getTimestamp() - $datetime->getTimestamp();    //in seconds
	if ($difference < $interval && $difference > 0) {
		file_put_contents('www/cron.txt', '');
		$output = shell_exec('php www/index.php bot:queue --debug-mode');
		echo $output . PHP_EOL;
	}
	sleep($interval);
}
