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
 * @method static Building _(string $value)
 */
class Building extends Upgradable
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
		DEUTERIUM_TANK = 'deuterium tank',
		RESEARCH_LAB = 'research lab',
		ALLIANCE_DEPOT = 'alliance depot',
		MISSILE_SILO = 'missile silo',
		NANITE_FACTORY = 'nanite factory',
		TERRAFORMER = 'terraformer'
	;

	public function getClassSelector() : string
	{
		switch ($this->getCategory()) {
			case MenuItem::RESOURCES: $class = '.supply'; break;
			case MenuItem::STATION: $class = '.station'; break;
			default: throw new \Exception('A wild unnown category appeared.');
		}
		return $class . $this->getNumber();
	}


	/**
	 * @return static[]
	 */
	public static function getEnumsSortedByCategory()
	{
		$enums = static::getEnums();
		usort($enums, function(Building $a, Building $b) {return $a->getCategory() != $b->getCategory();});
		return $enums;
	}

	private function getNumber() : string
	{
		switch ($this->getValue()) {
			case static::METAL_MINE: return '1';
			case static::CRYSTAL_MINE: return '2';
			case static::DEUTERIUM_MINE: return '3';
			case static::SOLAR_POWER_PLANT: return '4';
			case static::ROBOTIC_FACTORY: return '14';
			case static::SHIPYARD: return '21';
			case static::FUSION_REACTOR: return '12';
			case static::METAL_STORAGE: return '22';
			case static::CRYSTAL_STORAGE: return '23';
			case static::DEUTERIUM_TANK: return '24';
			case static::RESEARCH_LAB: return '31';
			case static::ALLIANCE_DEPOT: return '34';
			case static::MISSILE_SILO: return '44';
			case static::NANITE_FACTORY: return '15';
			case static::TERRAFORMER: return '33';
		}
	} 
	
	private function getCategory() : string
	{
		return $this->getMenuLocation()->getValue();
	}

	public static function getFromTranslatedName(string $name) : string
	{
		switch ($name) {
			case 'Důl na Kov': return static::METAL_MINE;
			case 'Důl na krystaly': return static::CRYSTAL_MINE;
			case 'Syntetizér deuteria': return static::DEUTERIUM_MINE;
			case 'Solární elektrárna': return static::SOLAR_POWER_PLANT;
			case 'Fúzní reaktor': return static::FUSION_REACTOR;
			case 'Sklad kovu': return static::METAL_STORAGE;
			case 'Sklad krystalu': return static::CRYSTAL_STORAGE;
			case 'Nádrž na deuterium': return static::DEUTERIUM_TANK;
			case 'Továrna na roboty': return static::ROBOTIC_FACTORY;
			case 'Hangár': return static::SHIPYARD;
			case 'Výzkumná laboratoř': return static::RESEARCH_LAB;
			case 'Alianční sklad': return static::ALLIANCE_DEPOT;
			case 'Raketové silo': return static::MISSILE_SILO;
			case 'Továrna s nanoboty': return static::NANITE_FACTORY;
			case 'Terraformer': return static::TERRAFORMER;
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
			case static::RESEARCH_LAB: return MenuItem::_(MenuItem::STATION);
			case static::ALLIANCE_DEPOT: return MenuItem::_(MenuItem::STATION);
			case static::MISSILE_SILO: return MenuItem::_(MenuItem::STATION);
			case static::NANITE_FACTORY: return MenuItem::_(MenuItem::STATION);
			case static::TERRAFORMER: return MenuItem::_(MenuItem::STATION);
		}
	}

	protected function getNextLevelPriceConstant() : float
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

	protected function getBasePrice() : Resources
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
				return new Resources(400, 200, 100);
			case static::FUSION_REACTOR:
				return new Resources(900, 360, 180);
			case static::METAL_STORAGE:
				return new Resources(1000, 0, 0);
			case static::CRYSTAL_STORAGE:
				return new Resources(1000, 500, 0);
			case static::DEUTERIUM_TANK:
				return new Resources(1000, 1000, 0);
			case static::RESEARCH_LAB:
				return new Resources(200, 400, 200);
			case static::ALLIANCE_DEPOT:
				return new Resources(20000, 40000, 0);
			case static::MISSILE_SILO:
				return new Resources(20000, 20000, 1000);
			case static::NANITE_FACTORY:
				return new Resources(1000000, 500000, 100000);
			case static::TERRAFORMER:
				return new Resources(0, 50000, 100000);
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
			case static::RESEARCH_LAB:
				return $planet->getResearchLabLevel();
			case static::ALLIANCE_DEPOT:
				return $planet->getAllianceDepotLevel();
			case static::MISSILE_SILO:
				return $planet->getMissileSiloLevel();
			case static::NANITE_FACTORY:
				return $planet->getNaniteFactoryLevel();
			case static::TERRAFORMER:
				return $planet->getTerraformerLevel();
		}
	}

	public function getFreeToEnhanceText() : string
	{
		return 'Nestaví se žádné budovy.';
	}

	public function getEnhanceStatusSelector() : string
	{
		return '#overviewBottom > div:nth-child(1) table.construction.active';
	}

	public function setCurrentLevel(Planet $planet, int $currentLevel)
	{
		switch ($this->getValue()) {
			case static::METAL_MINE:
				return $planet->setMetalMineLevel($currentLevel);
			case static::CRYSTAL_MINE:
				return $planet->setCrystalMineLevel($currentLevel);
			case static::DEUTERIUM_MINE:
				return $planet->setDeuteriumMineLevel($currentLevel);
			case static::SOLAR_POWER_PLANT:
				return $planet->setSolarPowerPlantLevel($currentLevel);
			case static::ROBOTIC_FACTORY:
				return $planet->setRoboticFactoryLevel($currentLevel);
			case static::SHIPYARD:
				return $planet->setShipyardLevel($currentLevel);
			case static::FUSION_REACTOR:
				return $planet->setFusionReactorLevel($currentLevel);
			case static::METAL_STORAGE:
				return $planet->setMetalStorageLevel($currentLevel);
			case static::CRYSTAL_STORAGE:
				return $planet->setCrystalStorageLevel($currentLevel);
			case static::DEUTERIUM_TANK:
				return $planet->setDeuteriumTankLevel($currentLevel);
			case static::RESEARCH_LAB:
				return $planet->setResearchLabLevel($currentLevel);
			case static::ALLIANCE_DEPOT:
				return $planet->setAllianceDepotLevel($currentLevel);
			case static::MISSILE_SILO:
				return $planet->setMissileSiloLevel($currentLevel);
			case static::NANITE_FACTORY:
				return $planet->setNaniteFactoryLevel($currentLevel);
			case static::TERRAFORMER:
				return $planet->setTerraformerLevel($currentLevel);
		}
	}

	public function getEnhanceCountdownSelector() : string
	{
		return '#Countdown';
	}

}