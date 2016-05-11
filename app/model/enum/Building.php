<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;

/**
 * Class MenuItem
 * @package App\Enum
 * @method static _(string $value) @return MenuItem
 */
class Building extends Enum
{

	const
		METAL_MINE = 'metal mine',
		CRYSTAL_MINE = 'crystal mine',
		DEUTERIUM_MINE = 'deuterium mine',
		SOLAR_POWER_PLANT = 'solar power plant',
		ROBOT_FACTORY = 'robot factory',
		HANGAR = 'hangar'
	;

	/**
	 * @return string
	 */
	public function getSelector()
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return '.supply1 > div:nth-child(1)';
			case static::CRYSTAL_MINE: return '.supply2 > div:nth-child(1)';
			case static::DEUTERIUM_MINE: return '.supply3 > div:nth-child(1)';
			case static::SOLAR_POWER_PLANT: return '.supply4 > div:nth-child(1)';
			case static::ROBOT_FACTORY: return '.station14 > div:nth-child(1)';
			case static::HANGAR: return '.station21 > div:nth-child(1)';
		}
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation()
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::CRYSTAL_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::DEUTERIUM_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::SOLAR_POWER_PLANT: return MenuItem::_(MenuItem::RESOURCES);
			case static::ROBOT_FACTORY: return MenuItem::_(MenuItem::STATION);
			case static::HANGAR: return MenuItem::_(MenuItem::STATION);
		}
	}

	public function getBuildButtonSelector()
	{
		return '.build-it > span:nth-child(1)';
	}
}