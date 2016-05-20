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
		switch ($this->getValue()) {
			case static::EXPEDITION: return '#missionButton15';
			case static::COLONIZATION: return '#missionButton7';
			case static::HARVESTING: return '#missionButton8';
			case static::TRANSPORT: return '#missionButton3';
			case static::DEPLOYMENT: return '#missionButton4';
			case static::ESPIONAGE: return '#missionButton6';
			case static::ATTACKING: return '#missionButton1';
			case static::DESTROY: return '#missionButton9';
		}
	}
}