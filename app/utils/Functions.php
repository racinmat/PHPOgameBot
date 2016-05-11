<?php

namespace App\Utils;

use app\model\command\ArraySerializable;
use App\Model\Command\ICommand;
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

	public static function toArray()
	{
		return function (ArraySerializable $data) {
			return $data->toArray();
		};
	}
}