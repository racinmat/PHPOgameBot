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
 * @method static _(string $value) @return PlayerStatus
 */
class PlayerStatus extends Enum
{

	const
		STATUS_INACTIVE = 'inactive',
		STATUS_LONG_INACTIVE = 'long inactive',
		STATUS_NOOB = 'noob',
		STATUS_HONORABLE_TARGET = 'honorable target',
		STATUS_ACTIVE = 'active',
		STATUS_VACATION = 'vacation',
		STATUS_STRONG = 'strong',
		STATUS_ADMIN = 'admin',
		STATUS_OUTLAW = 'outlaw',
		STATUS_BANNED = 'banned'
	;

	public static function fromClass(string $class) : PlayerStatus
	{

		$classToStatus = [
			'status_abbr_noob' => static::STATUS_NOOB,
			'status_abbr_active' => static::STATUS_NOOB,
			'status_abbr_honorableTarget' => static::STATUS_NOOB,
			'status_abbr_vacation' => static::STATUS_VACATION,
			'status_abbr_inactive' => static::STATUS_INACTIVE,
			'status_abbr_strong' => static::STATUS_STRONG,
			'status_abbr_longinactive' => static::STATUS_LONG_INACTIVE,
			'status_abbr_admin' => static::STATUS_ADMIN,
			'status_abbr_outlaw' => static::STATUS_OUTLAW,
			'status_abbr_banned' => static::STATUS_BANNED
		];
		return static::newInstance($classToStatus[$class]);
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
		}
	}

	public function getEnhanceCountdownSelector() : string
	{
		return '#Countdown';
	}

}