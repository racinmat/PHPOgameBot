<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;



/**
 * Class Defense
 * @package App\Enum
 * @method static _(string $value) @return FleetMission
 */
class FleetMission extends Enum
{

	const
		EXPEDITION = 'expedition',
		COLONIZATION = 'colonization',
		HARVESTING = 'harvesting',
		TRANSPORT = 'transport',
		DEPLOYMENT = 'deployment',
		ESPIONAGE = 'espionage',
		ATTACKING = 'attacking',
		DESTROY = 'destroy'
	;

	private static $numberToMission = [
		'15' => self::EXPEDITION,
		'7' => self::COLONIZATION,
		'8' => self::HARVESTING,
		'3' => self::TRANSPORT,
		'4' => self::DEPLOYMENT,
		'6' => self::ESPIONAGE,
		'1' => self::ATTACKING,
		'9' => self::DESTROY
	];

	public function getMissionSelector() : string 
	{
		return $this->getId() . $this->getNumber();
	}

	private function getId() : string
	{
		return '#missionButton';
	}

	public function getNumber() : string
	{
		return array_flip(static::$numberToMission)[$this->getValue()];
	}

	public static function fromNumber(string $number) : FleetMission
	{
		return static::newInstance(static::$numberToMission[$number]);
	}
}