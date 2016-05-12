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
 * Class Building
 * @package App\Enum
 * @method static _(string $value) @return Building
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
		FUSION_REACTOR = 'fusion reactor',
		METAL_STORAGE = 'metal storage',
		CRYSTAL_STORAGE = 'crystal storage',
		DEUTERIUM_TANK = 'deuterium tank'
	;

	public function getSelector() : string 
	{
		return $this->getClassSelector() . ' > div:nth-child(1) > a.detail_button';
	}

	private function getClassSelector() : string 
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return '.supply1';
			case static::CRYSTAL_MINE: return '.supply2';
			case static::DEUTERIUM_MINE: return '.supply3';
			case static::SOLAR_POWER_PLANT: return '.supply4';
			case static::ROBOTIC_FACTORY: return '.station14';
			case static::SHIPYARD: return '.station21';
			case static::FUSION_REACTOR: return '.supply12';
			case static::METAL_STORAGE: return '.supply22';
			case static::CRYSTAL_STORAGE: return '.supply23';
			case static::DEUTERIUM_TANK: return '.supply24';
		}
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation() : MenuItem
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::CRYSTAL_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::DEUTERIUM_MINE: return MenuItem::_(MenuItem::RESOURCES);
			case static::SOLAR_POWER_PLANT: return MenuItem::_(MenuItem::RESOURCES);
			case static::ROBOTIC_FACTORY: return MenuItem::_(MenuItem::STATION);
			case static::SHIPYARD: return MenuItem::_(MenuItem::STATION);
			case static::FUSION_REACTOR: return MenuItem::_(MenuItem::RESOURCES);
			case static::METAL_STORAGE: return MenuItem::_(MenuItem::RESOURCES);
			case static::CRYSTAL_STORAGE: return MenuItem::_(MenuItem::RESOURCES);
			case static::DEUTERIUM_TANK: return MenuItem::_(MenuItem::RESOURCES);
		}
	}

	public function getUpgradeButtonSelector() : string
	{
		return '.build-it > span:nth-child(1)';
	}

	public function getNextLevelPriceConstant() : float
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
		return $this->getBasePrice()->multiplyScalar(pow($this->getNextLevelPriceConstant(), $currentLevel));
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
			case static::METAL_STORAGE:
				return new Resources(1000, 0, 0);
			case static::CRYSTAL_STORAGE:
				return new Resources(1000, 500, 0);
			case static::DEUTERIUM_TANK:
				return new Resources(1000, 1000, 0);
		}
	}

	public function getCurrentLevel(Planet $planet) : int 
	{
		switch ($this->getValue()) {
			case static::METAL_MINE:
				return $planet->getMetalMineLevel();
			case static::CRYSTAL_MINE:
				return $planet->getCrystalMineLevel();
			case static::DEUTERIUM_MINE:
				return $planet->getDeuteriumMineLevel();
			case static::SOLAR_POWER_PLANT:
				return $planet->getSolarPowerPlantLevel();
			case static::ROBOTIC_FACTORY:
				return $planet->getRoboticFactoryLevel();
			case static::SHIPYARD:
				return $planet->getShipyardLevel();
			case static::FUSION_REACTOR:
				return $planet->getFusionReactorLevel();
			case static::METAL_STORAGE:
				return $planet->getMetalStorageLevel();
			case static::CRYSTAL_STORAGE:
				return $planet->getCrystalStorageLevel();
			case static::DEUTERIUM_TANK:
				return $planet->getDeuteriumTankLevel();
		}
	}
	
}