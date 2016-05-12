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
 * Class Research
 * @package App\Enum
 * @method static _(string $value) @return Research
 */
class Research extends Upgradable
{

	const
		ESPIONAGE_TECHNOLOGY = 'espionage technology',
		COMPUTER_TECHNOLOGY = 'computer technology',
		WEAPON_TECHNOLOGY = 'weapon technology',
		SHIELDING_TECHNOLOGY = 'shielding technology',
		ARMOUR_TECHNOLOGY = 'armour technology',
		ENERGY_TECHNOLOGY = 'energy technology',
		HYPERSPACE_TECHNOLOGY = 'hyperspace technology',
		COMBUSTION_DRIVE = 'combustion drive',
		IMPULSE_DRIVE = 'impulse drive',
		HYPERSPACE_DRIVE = 'hyperspace drive',
		LASER_TECHNOLOGY = 'laser technology',
		ION_TECHNOLOGY = 'ion technology',
		PLASMA_TECHNOLOGY = 'plasma technology',
		INTERGALACTIC_RESEARCH_NETWORK = 'intergalactic research network',
		ASTROPHYSICS = 'astrophysics',
		GRAVITON_TECHNOLOGY = 'graviton technology'
	;

	protected function getClassSelector() : string
	{
		switch ($this->getValue()) {
			case static::ESPIONAGE_TECHNOLOGY: return '.research106';
			case static::COMPUTER_TECHNOLOGY: return '.research108';
			case static::WEAPON_TECHNOLOGY: return '.research109';
			case static::SHIELDING_TECHNOLOGY: return '.research110';
			case static::ARMOUR_TECHNOLOGY: return '.research111';
			case static::ENERGY_TECHNOLOGY: return '.research113';
			case static::HYPERSPACE_TECHNOLOGY: return '.research114';
			case static::COMBUSTION_DRIVE: return '.research115';
			case static::IMPULSE_DRIVE: return '.research117';
			case static::HYPERSPACE_DRIVE: return '.research118';
			case static::LASER_TECHNOLOGY: return '.research120';
			case static::ION_TECHNOLOGY: return '.research121';
			case static::PLASMA_TECHNOLOGY: return '.research122';
			case static::INTERGALACTIC_RESEARCH_NETWORK: return '.research123';
			case static::ASTROPHYSICS: return '.research124';
			case static::GRAVITON_TECHNOLOGY: return '.research199';
		}
	}

	/**
	 * @return MenuItem
	 */
	public function getMenuLocation() : MenuItem
	{
		return MenuItem::_(MenuItem::RESEARCH);
	}

	protected function getNextLevelPriceConstant() : float
	{
		switch ($this->getValue()) {
			case static::ASTROPHYSICS: return 1.75;
			default: return 2;
		}
	}

	protected function getBasePrice() : Resources
	{
		switch ($this->getValue()) {
			case static::ESPIONAGE_TECHNOLOGY:
				return new Resources(200, 1000, 200);
			case static::COMPUTER_TECHNOLOGY:
				return new Resources(0, 400, 600);
			case static::WEAPON_TECHNOLOGY:
				return new Resources(800, 200, 0);
			case static::SHIELDING_TECHNOLOGY:
				return new Resources(200, 600, 0);
			case static::ARMOUR_TECHNOLOGY:
				return new Resources(1000, 0, 0);
			case static::ENERGY_TECHNOLOGY:
				return new Resources(0, 800, 400);
			case static::HYPERSPACE_TECHNOLOGY:
				return new Resources(0, 4000, 2000);
			case static::COMBUSTION_DRIVE:
				return new Resources(400, 0, 600);
			case static::IMPULSE_DRIVE:
				return new Resources(2000, 4000, 600);
			case static::HYPERSPACE_DRIVE:
				return new Resources(10000, 20000, 6000);
			case static::LASER_TECHNOLOGY:
				return new Resources(200, 100, 0);
			case static::ION_TECHNOLOGY:
				return new Resources(1000, 300, 100);
			case static::PLASMA_TECHNOLOGY:
				return new Resources(2000, 4000, 1000);
			case static::INTERGALACTIC_RESEARCH_NETWORK:
				return new Resources(240000, 400000, 160000);
			case static::ASTROPHYSICS:
				return new Resources(4000, 8000, 4000);
			case static::GRAVITON_TECHNOLOGY:
				return new Resources(0, 0, 0);
		}
	}

	public function getCurrentLevel(Planet $planet) : int 
	{
		switch ($this->getValue()) {
			case static::ESPIONAGE_TECHNOLOGY:
				return $planet->getEspionageTechnologyLevel();
			case static::COMPUTER_TECHNOLOGY:
				return $planet->getComputerTechnologyLevel();
			case static::WEAPON_TECHNOLOGY:
				return $planet->getWeaponTechnologyLevel();
			case static::SHIELDING_TECHNOLOGY:
				return $planet->getShieldingTechnologyLevel();
			case static::ARMOUR_TECHNOLOGY:
				return $planet->getArmourTechnologyLevel();
			case static::ENERGY_TECHNOLOGY:
				return $planet->getEnergyTechnologyLevel();
			case static::HYPERSPACE_TECHNOLOGY:
				return $planet->getHyperspaceTechnologyLevel();
			case static::COMBUSTION_DRIVE:
				return $planet->getCombustionDriveLevel();
			case static::IMPULSE_DRIVE:
				return $planet->getImpulseDriveLevel();
			case static::HYPERSPACE_DRIVE:
				return $planet->getHyperspaceDriveLevel();
			case static::LASER_TECHNOLOGY:
				return $planet->getLaserTechnologyLevel();
			case static::ION_TECHNOLOGY:
				return $planet->getIonTechnologyLevel();
			case static::PLASMA_TECHNOLOGY:
				return $planet->getPlasmaTechnologyLevel();
			case static::INTERGALACTIC_RESEARCH_NETWORK:
				return $planet->getIntergalacticResearchNetworkLevel();
			case static::ASTROPHYSICS:
				return $planet->getAstrophysicsLevel();
			case static::GRAVITON_TECHNOLOGY:
				return $planet->getGravitonTechnologyLevel();
		}
	}

	public function getFreeToEnhanceText() : string
	{
		return 'V tuto chvíli neprobíhá žádný výzkum.';
	}

	public function getEnhanceStatusSelector() : string
	{
		return '#overviewBottom > div:nth-child(2) table.construction.active';
	}
}