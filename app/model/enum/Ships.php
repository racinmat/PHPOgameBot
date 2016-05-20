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
 * @method static Ships _(string $value)
 */
class Ships extends Buildable
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

	private function getNumber() : string
	{
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP: return '202';
			case static::LARGE_CARGO_SHIP: return '203';
			case static::LIGHT_FIGHTER: return '204';
			case static::HEAVY_FIGHTER: return '205';
			case static::CRUISER: return '206';
			case static::BATTLESHIP: return '207';
			case static::BATTLECRUISER: return '215';
			case static::DESTROYER: return '213';
			case static::DEATHSTAR: return '214';
			case static::BOMBER: return '211';
			case static::RECYCLER: return '209';
			case static::ESPIONAGE_PROBE: return '210';
			case static::SOLAR_SATELLITE: return '212';
			case static::COLONY_SHIP: return '208';
		}
	}

	public function getClassSelector() : string
	{
		return '.' . $this->getType() . $this->getNumber();
	}
	
	public function getType() : string
	{
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP: return 'civil';
			case static::LARGE_CARGO_SHIP: return 'civil';
			case static::LIGHT_FIGHTER: return 'military';
			case static::HEAVY_FIGHTER: return 'military';
			case static::CRUISER: return 'military';
			case static::BATTLESHIP: return 'military';
			case static::BATTLECRUISER: return 'military';
			case static::DESTROYER: return 'military';
			case static::DEATHSTAR: return 'military';
			case static::BOMBER: return 'military';
			case static::RECYCLER: return 'civil';
			case static::ESPIONAGE_PROBE: return 'civil';
			case static::SOLAR_SATELLITE: return 'civil';
			case static::COLONY_SHIP: return 'civil';
		}
	}
	
	public function getFleetInputSelector() : string
	{
		return '#ship_' . $this->getNumber();
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation() : MenuItem
	{
		return MenuItem::_(MenuItem::SHIPYARD);
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

	public function getFreeToEnhanceText() : string
	{
		return 'Žádné lodě/obrany se nyní nestaví.';
	}

	public function getEnhanceStatusSelector() : string
	{
		return '#overviewBottom > div:nth-child(3) table.construction.active';
	}

	public function getEnhanceCountdownSelector() : string
	{
		return '#shipAllCountdown7';
	}

}