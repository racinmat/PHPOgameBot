<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 30. 7. 2015
 * Time: 17:14
 */

namespace App\Enum;
use App\Model\Entity\Planet;
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

	public static function getFromTranslatedName(string $name) : string
	{
		switch ($name) {
			case 'Malý transportér': return static::SMALL_CARGO_SHIP;
			case 'Velký transportér': return static::LARGE_CARGO_SHIP;
			case 'Lehký stíhač': return static::LIGHT_FIGHTER;
			case 'Těžký stíhač': return static::HEAVY_FIGHTER;
			case 'Křižník': return static::CRUISER;
			case 'Bitevní loď': return static::BATTLESHIP;
			case 'Bitevní křižník': return static::BATTLECRUISER;
			case 'Ničitel': return static::DESTROYER;
			case 'Hvězda smrti': return static::DEATHSTAR;
			case 'Bombardér': return static::BOMBER;
			case 'Recyklátor': return static::RECYCLER;
			case 'Špionážní sonda': return static::ESPIONAGE_PROBE;
			case 'Solární satelit': return static::SOLAR_SATELLITE;
			case 'Kolonizační loď': return static::COLONY_SHIP;
		}
	}

	public function setAmount(Planet $planet, int $amount)
	{
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP:
				return $planet->setSmallCargoShipAmount($amount);
			case static::LARGE_CARGO_SHIP:
				return $planet->setLargeCargoShipAmount($amount);
			case static::LIGHT_FIGHTER:
				return $planet->setLightFighterAmount($amount);
			case static::HEAVY_FIGHTER:
				return $planet->setHeavyFighterAmount($amount);
			case static::CRUISER:
				return $planet->setCruiserAmount($amount);
			case static::BATTLESHIP:
				return $planet->setBattleshipAmount($amount);
			case static::BATTLECRUISER:
				return $planet->setBattlecruiserAmount($amount);
			case static::DESTROYER:
				return $planet->setDestroyerAmount($amount);
			case static::DEATHSTAR:
				return $planet->setDeathstarAmount($amount);
			case static::BOMBER:
				return $planet->setBomberAmount($amount);
			case static::RECYCLER:
				return $planet->setRecyclerAmount($amount);
			case static::ESPIONAGE_PROBE:
				return $planet->setEspionageProbeAmount($amount);
			case static::SOLAR_SATELLITE:
				return $planet->setSolarSatelliteAmount($amount);
			case static::COLONY_SHIP:
				return $planet->setColonyShipAmount($amount);
		}
	}

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

	public function getCurrentAmountSelector() : string 
	{
		return '#button' . $this->getNumber() . 'a.tooltip > .ecke > .level';
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

	public function getCapacity() : int
	{
		switch ($this->getValue()) {
			case static::SMALL_CARGO_SHIP:
				return 5000;
			case static::LARGE_CARGO_SHIP:
				return 25000;
			case static::LIGHT_FIGHTER:
				return 50;
			case static::HEAVY_FIGHTER:
				return 100;
			case static::CRUISER:
				return 800;
			case static::BATTLESHIP:
				return 1500;
			case static::BATTLECRUISER:
				return 750;
			case static::DESTROYER:
				return 2000;
			case static::DEATHSTAR:
				return 1000000;
			case static::BOMBER:
				return 500;
			case static::RECYCLER:
				return 20000;
			case static::ESPIONAGE_PROBE:
				return 5;
			case static::SOLAR_SATELLITE:
				return 0;
			case static::COLONY_SHIP:
				return 7500;
		}
	}

}