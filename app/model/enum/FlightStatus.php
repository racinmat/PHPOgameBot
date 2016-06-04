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
		ENEMY = 'hostile'
	;


	public function getSelector() : string
	{
		switch ($this->getValue()) {
			case static::MINE: return '.friendly';
			case static::FRIENDLY: return '.neutral';
			case static::ENEMY: return '.hostile';
		}
	}

}