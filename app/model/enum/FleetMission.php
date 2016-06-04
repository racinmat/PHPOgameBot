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
		switch ($this->getValue()) {
			case static::EXPEDITION: return '15';
			case static::COLONIZATION: return '7';
			case static::HARVESTING: return '8';
			case static::TRANSPORT: return '3';
			case static::DEPLOYMENT: return '4';
			case static::ESPIONAGE: return '6';
			case static::ATTACKING: return '1';
			case static::DESTROY: return '9';
		}
	}

	public static function fromNumber(string $number) : FleetMission
	{
		switch ($number) {
			case '15': return static::_(static::EXPEDITION);
			case '7': return static::_(static::COLONIZATION);
			case '8': return static::_(static::HARVESTING);
			case '3': return static::_(static::TRANSPORT);
			case '4': return static::_(static::DEPLOYMENT);
			case '6': return static::_(static::ESPIONAGE);
			case '1': return static::_(static::ATTACKING);
			case '9': return static::_(static::DESTROY);
		}

	}
}