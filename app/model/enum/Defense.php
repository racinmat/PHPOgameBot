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
 * @method static _(string $value) @return Defense
 */
class Defense extends Buildable
{

	const
		ROCKET_LAUNCHER = 'Rocket Launcher',
		LIGHT_LASER = 'Light Laser',
		HEAVY_LASER = 'Heavy Laser',
		ION_CANNON = 'Ion Cannon',
		GAUSS_CANNON = 'Gauss Cannon',
		PLASMA_TURRET = 'Plasma Turret',
		SMALL_SHIELD_DOME = 'Small Shield Dome',
		LARGE_SHIELD_DOME = 'Large Shield Dome',
		ANTI_BALLISTIC_MISSILE = 'Anti-Ballistic Missile',
		INTERPLANETARY_MISSILE = 'Interplanetary Missile'
	;

	public function getSelector() : string
	{
		return $this->getClassSelector() . ' > div:nth-child(1) > a.detail_button';
	}

	private function getClassSelector() : string
	{
		switch ($this->getValue()) {
			case static::ROCKET_LAUNCHER: return '.defense401';
			case static::LIGHT_LASER: return '.defense402';
			case static::HEAVY_LASER: return '.defense403';
			case static::ION_CANNON: return '.defense404';
			case static::GAUSS_CANNON: return '.defense405';
			case static::PLASMA_TURRET: return '.defense406';
			case static::SMALL_SHIELD_DOME: return '.defense407';
			case static::LARGE_SHIELD_DOME: return '.defense408';
			case static::ANTI_BALLISTIC_MISSILE: return '.defense502';
			case static::INTERPLANETARY_MISSILE: return '.defense503';
		}
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation() : MenuItem
	{
		return MenuItem::_(MenuItem::DEFENSE);
	}

	public function getBuildButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

	public function getPrice() : Resources
	{
		switch ($this->getValue()) {
			case static::ROCKET_LAUNCHER:
				return new Resources(2000, 0, 0);
			case static::LIGHT_LASER:
				return new Resources(1500, 500, 0);
			case static::HEAVY_LASER:
				return new Resources(6000, 2000, 0);
			case static::ION_CANNON:
				return new Resources(20000, 15000, 2000);
			case static::GAUSS_CANNON:
				return new Resources(2000, 6000, 0);
			case static::PLASMA_TURRET:
				return new Resources(50000, 50000, 30000);
			case static::SMALL_SHIELD_DOME:
				return new Resources(10000, 10000, 0);
			case static::LARGE_SHIELD_DOME:
				return new Resources(50000, 50000, 0);
			case static::ANTI_BALLISTIC_MISSILE:
				return new Resources(8000, 0, 2000);
			case static::INTERPLANETARY_MISSILE:
				return new Resources(12500, 2500, 10000);
		}
	}

}