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
		ROBOTIC_FACTORY = 'robotic factory',
		SHIPYARD = 'shipyard',
		FUSION_REACTOR = 'fusion reactor'
	;

	/**
	 * @return string
	 */
	public function getSelector()
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return '.supply1 > div:nth-child(1) > a.detail_button';
			case static::CRYSTAL_MINE: return '.supply2 > div:nth-child(1) > a.detail_button';
			case static::DEUTERIUM_MINE: return '.supply3 > div:nth-child(1) > a.detail_button';
			case static::SOLAR_POWER_PLANT: return '.supply4 > div:nth-child(1) > a.detail_button';
			case static::ROBOTIC_FACTORY: return '.station14 > div:nth-child(1) > a.detail_button';
			case static::SHIPYARD: return '.station21 > div:nth-child(1) > a.detail_button';
			case static::FUSION_REACTOR: return '.supply12 > div:nth-child(1) > a.detail_button';
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
			case static::ROBOTIC_FACTORY: return MenuItem::_(MenuItem::STATION);
			case static::SHIPYARD: return MenuItem::_(MenuItem::STATION);
			case static::FUSION_REACTOR: return MenuItem::_(MenuItem::RESOURCES);
		}
	}

	public function getBuildButtonSelector()
	{
		return '.build-it > span:nth-child(1)';
	}

	public function getNextLevelPriceConstant()
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return 1.5;
			case static::CRYSTAL_MINE: return 1.6;
			case static::DEUTERIUM_MINE: return 1.5;
			case static::SOLAR_POWER_PLANT: return 1.5;
			case static::FUSION_REACTOR: return 1.8;
			default: return 2;
		}
	}

	public function getPriceToNextLevel($currentLevel) : Resources
	{
		return $this->getBasePrice()->multiply(pow($this->getNextLevelPriceConstant(), $currentLevel));
	}

	public function getBasePrice() : Resources
	{
		switch ($this->getValue()) {
			case static::METAL_MINE:
				return new Resources(60, 15, 0);
			case static::CRYSTAL_MINE:
				return new Resources(48, 24, 0);
			case static::DEUTERIUM_MINE:
				return new Resources(225, 75, 0);
			case static::SOLAR_POWER_PLANT:
				return new Resources(75, 30, 0);
			case static::ROBOTIC_FACTORY:
				return new Resources(400, 120, 200);
			case static::SHIPYARD:
				return new Resources(200, 400, 200);
			case static::FUSION_REACTOR:
				return new Resources(900, 360, 180);
		}
	}

}