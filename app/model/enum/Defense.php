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
 * @method static Defense _(string $value)
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

	public static function getFromTranslatedName(string $name) : string
	{
		switch ($name) {
			case 'Raketomet': return static::ROCKET_LAUNCHER;
			case 'Lehký laser': return static::LIGHT_LASER;
			case 'Těžký laser': return static::HEAVY_LASER;
			case 'Iontový kanón': return static::ION_CANNON;
			case 'Gaussův kanón': return static::GAUSS_CANNON;
			case 'Plasmová věž': return static::PLASMA_TURRET;
			case 'Malý planetární štít': return static::SMALL_SHIELD_DOME;
			case 'Velký planetární štít': return static::LARGE_SHIELD_DOME;
			case 'Antibalistické rakety': return static::ANTI_BALLISTIC_MISSILE;
			case 'Meziplanetární rakety': return static::INTERPLANETARY_MISSILE;
		}
	}

	public function setAmount(Planet $planet, int $amount)
	{
		switch ($this->getValue()) {
			case static::ROCKET_LAUNCHER:
				return $planet->setRocketLauncherAmount($amount);
			case static::LIGHT_LASER:
				return $planet->setLightLaserAmount($amount);
			case static::HEAVY_LASER:
				return $planet->setHeavyLaserAmount($amount);
			case static::ION_CANNON:
				return $planet->setIonCannonAmount($amount);
			case static::GAUSS_CANNON:
				return $planet->setGaussCannonAmount($amount);
			case static::PLASMA_TURRET:
				return $planet->setPlasmaTurretAmount($amount);
			case static::SMALL_SHIELD_DOME:
				return $planet->setSmallShieldDomeAmount($amount);
			case static::LARGE_SHIELD_DOME:
				return $planet->setLargeShieldDomeAmount($amount);
			case static::ANTI_BALLISTIC_MISSILE:
				return $planet->setAntiBallisticMissileAmount($amount);
			case static::INTERPLANETARY_MISSILE:
				return $planet->setInterplanetaryMissileAmount($amount);
		}
	}

	public function getClassSelector() : string
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