<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 13. 5. 2016
 * Time: 11:56
 */

namespace App\Utils;


class Random
{

	public static function microseconds(float $secondsFrom, float $secondsTo)
	{
		return random_int($secondsFrom * 1000000, $secondsTo * 1000000);
	}

}