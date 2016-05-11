<?php

namespace App\Utils;

use Nette\Object;

class Functions extends Object
{

	public static function isGreaterThanZero() : callable
	{
		return function(int $e) : bool {
			return $e > 0;
		};
	}

	public static function isZero()
	{
		return function(int $e) : bool {
			return $e === 0;
		};
	}

}