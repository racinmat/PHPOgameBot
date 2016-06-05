<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;

use App\Model\ValueObject\Resources;

/**
 * Class Defense
 * @package App\Enum
 * @method static _(string $value) @return FleetMission
 */
class FlightStatus extends Enum
{

	const
		MINE = 'mine',
		FRIENDLY = 'friendly',
		ENEMY = 'enemy'
	;

	static private $classToStatus = [
			'friendly' => self::MINE,
			'neutral' => self::FRIENDLY,
			'hostile' => self::ENEMY
	];

	public function getSelector() : string
	{
		return '.' . array_flip(static::$classToStatus)[$this->getValue()];
	}

	public static function fromClass(string $class) : FlightStatus
	{
		return static::newInstance(static::$classToStatus[$class]);
	}

}