<?php

namespace App\Utils;

use App\Model\Entity\Planet;
use App\Model\Queue\Command\ArraySerializable;
use App\Model\ValueObject\Coordinates;
use Carbon\Carbon;
use Nette\Object;

class Functions extends Object
{

	public static function isGreaterThanZero() : callable
	{
		return function(int $e) : bool {
			return $e > 0;
		};
	}

	public static function isZero() : callable
	{
		return function(int $e) : bool {
			return $e === 0;
		};
	}

	public static function toArray() : callable
	{
		return function (ArraySerializable $data) {
			return $data->toArray();
		};
	}

	public static function textCoordinatesToValueObject() : callable
	{
		return function (string $text) : Coordinates {
			return OgameParser::parseOgameCoordinates($text);
		};
	}

	public static function valueObjectCoordinatesToText() : callable
	{
		return function (Coordinates $coordinates) : string {
			return $coordinates->__toString();
		};
	}

	public static function planetToNameAndTextCoordinates()
	{
		return function (Planet $planet) : string {
			return $planet->getName() . ' ' . $planet->getCoordinates()->toValueObject()->__toString();
		};
	}


	public static function planetToCoordinates()
	{
		return function (Planet $planet) : Coordinates {
			return $planet->getCoordinates()->toValueObject();
		};
	}

	public static function compareCarbonDateTimes()
	{
		return function (Carbon $a, Carbon $b) {
			return $a->lt($b) ? -1 : 1;
		};
	}

	public static function equalCoordinates(Coordinates $coordinates)
	{
		return function (Coordinates $c) use ($coordinates) : bool {
			return $c->equals($coordinates);
		};
	}
}