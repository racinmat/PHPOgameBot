<?php

namespace App\Utils;

use App\Model\Queue\Command\ArraySerializable;
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

	public static function coordinatesToValueObject()
	{
		return function (string $text) {
			return OgameParser::parseOgameCoordinates($text);
		};
	}
}