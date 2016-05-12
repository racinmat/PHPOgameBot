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
class Fleet extends Enum
{

	const
		SMALL_CARGO_SHIP = 'small cargo ship',
		LARGE_CARGO_SHIP = 'large cargo ship',
		LIGHT_FIGHTER = 'light fighter',
		HEAVY_FIGHTER = 'heavy fighter',
		CRUISER = 'cruiser',
		BATTLESHIP = 'battleship',
		BATTLECRUISER = 'battlecruiser',
		DESTROYER = 'destroyer',
		DEATHSTAR = 'deathstar',
		BOMBER = 'bomber',
		RECYCLER = 'recycler',
		ESPIONAGE_PROBE = 'espionage probe',
		SOLAR_SATELLITE = 'solar satellite',
		COLONY_SHIP = 'colony ship'
	;

	public function getSelector() : string
	{
		return $this->getClassSelector() . ' > div:nth-child(1) > a.detail_button';
	}

	private function getClassSelector() : string
	{
		//todo: upravit
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP: return '.defense401';
			case static::LARGE_CARGO_SHIP: return '.defense402';
			case static::LIGHT_FIGHTER: return '.defense403';
			case static::HEAVY_FIGHTER: return '.defense404';
			case static::CRUISER: return '.defense405';
			case static::BATTLESHIP: return '.defense406';
			case static::BATTLECRUISER: return '.defense407';
			case static::DESTROYER: return '.defense408';
			case static::DEATHSTAR: return '.defense502';
			case static::BOMBER: return '.defense503';
			case static::RECYCLER: return '.defense503';
			case static::ESPIONAGE_PROBE: return '.defense503';
			case static::SOLAR_SATELLITE: return '.defense503';
			case static::COLONY_SHIP: return '.defense503';
		}
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation() : MenuItem
	{
		return MenuItem::_(MenuItem::FLEET);
	}

	public function getBuildButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

	public function getPrice() : Resources
	{
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP:
				return new Resources(2000, 2000, 0);
			case static::LARGE_CARGO_SHIP:
				return new Resources(6000, 6000, 0);
			case static::LIGHT_FIGHTER:
				return new Resources(3000, 1000, 0);
			case static::HEAVY_FIGHTER:
				return new Resources(6000, 4000, 0);
			case static::CRUISER:
				return new Resources(20000, 7000, 2000);
			case static::BATTLESHIP:
				return new Resources(45000, 15000, 0);
			case static::BATTLECRUISER:
				return new Resources(30000, 40000, 15000);
			case static::DESTROYER:
				return new Resources(60000, 50000, 15000);
			case static::DEATHSTAR:
				return new Resources(5000000, 4000000, 1000000);
			case static::BOMBER:
				return new Resources(50000, 25000, 15000);
			case static::RECYCLER:
				return new Resources(10000, 6000, 2000);
			case static::ESPIONAGE_PROBE:
				return new Resources(0, 1000, 0);
			case static::SOLAR_SATELLITE:
				return new Resources(0, 2000, 0);
			case static::COLONY_SHIP:
				return new Resources(10000, 20000, 10000);
		}
	}

}