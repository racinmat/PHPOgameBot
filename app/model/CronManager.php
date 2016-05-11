<?php

namespace App\Model;
 
use Carbon\Carbon;
use Nette;
 
class CronManager extends Nette\Object
{

	public function __construct()
	{
	
	}

	public function setNextStart(Carbon $datetime)
	{
		$input = "schtasks /change /tn OgameBot /st {$datetime->format('H:i')}";
		$output = shell_exec($input);
		echo $input . PHP_EOL;
		echo $output . PHP_EOL;
	}
	
}