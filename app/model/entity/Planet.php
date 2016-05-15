<?php
 
namespace App\Model\Entity;

use App\Enum\Building;
use App\Model\ValueObject;
use App\Model\ValueObject\Resources;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity
 */
class Planet extends Object
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\OneToOne(targetEntity="Coordinates", cascade={"persist", "remove"})
	 * @var Coordinates
	 */
	private $coordinates;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $metalMineLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $crystalMineLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $deuteriumMineLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $solarPowerPlantLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $roboticFactoryLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $shipyardLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $fusionReactorLevel;

	/**
	 * @ORM\Column(type="boolean")
	 * @var boolean
	 */
	private $my;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $metal;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $crystal;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $deuterium;

	/**
	 * @ORM\Column(type="carbon")
	 * @var Carbon
	 */
	private $lastVisited;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var integer
	 */
	private $minimalTemperature;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var integer
	 */
	private $maximalTemperature;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $metalStorageLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $crystalStorageLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $deuteriumTankLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $espionageTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $computerTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $weaponTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $shieldingTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $armourTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $energyTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $hyperspaceTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $combustionDriveLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $impulseDriveLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $hyperspaceDriveLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $laserTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $ionTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $plasmaTechnologyLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $intergalacticResearchNetworkLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $astrophysicsLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
 	 */
	private $gravitonTechnologyLevel;

	/**
	 * Planet constructor.
	 * @param string $name
	 * @param Coordinates $coordinates
	 * @param bool $my
	 */
	public function __construct($name, ValueObject\Coordinates $coordinates, $my)
	{
		$this->name = $name;
		$this->coordinates = new ValueObject\Coordinates($coordinates->getGalaxy(), $coordinates->getSystem(), $coordinates->getPlanet());
		$this->my = $my;
	}

	/**
	 * @return int
	 */
	public function getMetalMineLevel()
	{
		return $this->metalMineLevel;
	}

	/**
	 * @param int $metalMineLevel
	 */
	public function setMetalMineLevel($metalMineLevel)
	{
		$this->metalMineLevel = $metalMineLevel;
	}

	/**
	 * @return int
	 */
	public function getCrystalMineLevel()
	{
		return $this->crystalMineLevel;
	}

	/**
	 * @param int $crystalMineLevel
	 */
	public function setCrystalMineLevel($crystalMineLevel)
	{
		$this->crystalMineLevel = $crystalMineLevel;
	}

	/**
	 * @return int
	 */
	public function getDeuteriumMineLevel()
	{
		return $this->deuteriumMineLevel;
	}

	/**
	 * @param int $deuteriumMineLevel
	 */
	public function setDeuteriumMineLevel($deuteriumMineLevel)
	{
		$this->deuteriumMineLevel = $deuteriumMineLevel;
	}

	/**
	 * @return int
	 */
	public function getSolarPowerPlantLevel()
	{
		return $this->solarPowerPlantLevel;
	}

	/**
	 * @param int $solarPowerPlantLevel
	 */
	public function setSolarPowerPlantLevel($solarPowerPlantLevel)
	{
		$this->solarPowerPlantLevel = $solarPowerPlantLevel;
	}

	/**
	 * @return int
	 */
	public function getRoboticFactoryLevel()
	{
		return $this->roboticFactoryLevel;
	}

	/**
	 * @param int $roboticFactoryLevel
	 */
	public function setRoboticFactoryLevel($roboticFactoryLevel)
	{
		$this->roboticFactoryLevel = $roboticFactoryLevel;
	}

	/**
	 * @return int
	 */
	public function getShipyardLevel()
	{
		return $this->shipyardLevel;
	}

	/**
	 * @param int $shipyardLevel
	 */
	public function setShipyardLevel($shipyardLevel)
	{
		$this->shipyardLevel = $shipyardLevel;
	}

	/**
	 * @return int
	 */
	public function getMetal()
	{
		return $this->metal;
	}

	/**
	 * @param int $metal
	 */
	public function setMetal($metal)
	{
		$this->metal = $metal;
	}

	/**
	 * @return int
	 */
	public function getCrystal()
	{
		return $this->crystal;
	}

	/**
	 * @param int $crystal
	 */
	public function setCrystal($crystal)
	{
		$this->crystal = $crystal;
	}

	/**
	 * @return int
	 */
	public function getDeuterium()
	{
		return $this->deuterium;
	}

	/**
	 * @param int $deuterium
	 */
	public function setDeuterium($deuterium)
	{
		$this->deuterium = $deuterium;
	}

	/**
	 * @return Carbon
	 */
	public function getLastVisited()
	{
		return $this->lastVisited;
	}

	/**
	 * @param Carbon $lastVisited
	 */
	public function setLastVisited(Carbon $lastVisited)
	{
		$this->lastVisited = $lastVisited;
	}

	/**
	 * @return int
	 */
	public function getMinimalTemperature()
	{
		return $this->minimalTemperature;
	}

	/**
	 * @return int
	 */
	public function getMaximalTemperature()
	{
		return $this->maximalTemperature;
	}

	/**
	 * @return float
	 */
	public function getAverageTemperature()
	{
		return ($this->minimalTemperature + $this->maximalTemperature) / 2;
	}

	public function getResources() : Resources
	{
		return new Resources($this->metal, $this->crystal, $this->deuterium);
	}

	public function getCurrentLevel(Building $building) : int
	{
		return $building->getCurrentLevel($this);
	}

	/**
	 * @return int
	 */
	public function getFusionReactorLevel()
	{
		return $this->fusionReactorLevel;
	}

	/**
	 * @param int $fusionReactorLevel
	 */
	public function setFusionReactorLevel($fusionReactorLevel)
	{
		$this->fusionReactorLevel = $fusionReactorLevel;
	}

	/**
	 * @return int
	 */
	public function getMetalStorageLevel()
	{
		return $this->metalStorageLevel;
	}

	/**
	 * @param int $metalStorageLevel
	 */
	public function setMetalStorageLevel($metalStorageLevel)
	{
		$this->metalStorageLevel = $metalStorageLevel;
	}

	/**
	 * @return int
	 */
	public function getCrystalStorageLevel()
	{
		return $this->crystalStorageLevel;
	}

	/**
	 * @param int $crystalStorageLevel
	 */
	public function setCrystalStorageLevel($crystalStorageLevel)
	{
		$this->crystalStorageLevel = $crystalStorageLevel;
	}

	/**
	 * @return int
	 */
	public function getDeuteriumTankLevel()
	{
		return $this->deuteriumTankLevel;
	}

	/**
	 * @param int $deuteriumTankLevel
	 */
	public function setDeuteriumTankLevel($deuteriumTankLevel)
	{
		$this->deuteriumTankLevel = $deuteriumTankLevel;
	}

	/**
	 * @return int
	 */
	public function getEspionageTechnologyLevel()
	{
		return $this->espionageTechnologyLevel;
	}

	/**
	 * @param int $espionageTechnologyLevel
	 */
	public function setEspionageTechnologyLevel($espionageTechnologyLevel)
	{
		$this->espionageTechnologyLevel = $espionageTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getComputerTechnologyLevel()
	{
		return $this->computerTechnologyLevel;
	}

	/**
	 * @param int $computerTechnologyLevel
	 */
	public function setComputerTechnologyLevel($computerTechnologyLevel)
	{
		$this->computerTechnologyLevel = $computerTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getWeaponTechnologyLevel()
	{
		return $this->weaponTechnologyLevel;
	}

	/**
	 * @param int $weaponTechnologyLevel
	 */
	public function setWeaponTechnologyLevel($weaponTechnologyLevel)
	{
		$this->weaponTechnologyLevel = $weaponTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getShieldingTechnologyLevel()
	{
		return $this->shieldingTechnologyLevel;
	}

	/**
	 * @param int $shieldingTechnologyLevel
	 */
	public function setShieldingTechnologyLevel($shieldingTechnologyLevel)
	{
		$this->shieldingTechnologyLevel = $shieldingTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getArmourTechnologyLevel()
	{
		return $this->armourTechnologyLevel;
	}

	/**
	 * @param int $armourTechnologyLevel
	 */
	public function setArmourTechnologyLevel($armourTechnologyLevel)
	{
		$this->armourTechnologyLevel = $armourTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getEnergyTechnologyLevel()
	{
		return $this->energyTechnologyLevel;
	}

	/**
	 * @param int $energyTechnologyLevel
	 */
	public function setEnergyTechnologyLevel($energyTechnologyLevel)
	{
		$this->energyTechnologyLevel = $energyTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getHyperspaceTechnologyLevel()
	{
		return $this->hyperspaceTechnologyLevel;
	}

	/**
	 * @param int $hyperspaceTechnologyLevel
	 */
	public function setHyperspaceTechnologyLevel($hyperspaceTechnologyLevel)
	{
		$this->hyperspaceTechnologyLevel = $hyperspaceTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getCombustionDriveLevel()
	{
		return $this->combustionDriveLevel;
	}

	/**
	 * @param int $combustionDriveLevel
	 */
	public function setCombustionDriveLevel($combustionDriveLevel)
	{
		$this->combustionDriveLevel = $combustionDriveLevel;
	}

	/**
	 * @return int
	 */
	public function getImpulseDriveLevel()
	{
		return $this->impulseDriveLevel;
	}

	/**
	 * @param int $impulseDriveLevel
	 */
	public function setImpulseDriveLevel($impulseDriveLevel)
	{
		$this->impulseDriveLevel = $impulseDriveLevel;
	}

	/**
	 * @return int
	 */
	public function getHyperspaceDriveLevel()
	{
		return $this->hyperspaceDriveLevel;
	}

	/**
	 * @param int $hyperspaceDriveLevel
	 */
	public function setHyperspaceDriveLevel($hyperspaceDriveLevel)
	{
		$this->hyperspaceDriveLevel = $hyperspaceDriveLevel;
	}

	/**
	 * @return int
	 */
	public function getLaserTechnologyLevel()
	{
		return $this->laserTechnologyLevel;
	}

	/**
	 * @param int $laserTechnologyLevel
	 */
	public function setLaserTechnologyLevel($laserTechnologyLevel)
	{
		$this->laserTechnologyLevel = $laserTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getIonTechnologyLevel()
	{
		return $this->ionTechnologyLevel;
	}

	/**
	 * @param int $ionTechnologyLevel
	 */
	public function setIonTechnologyLevel($ionTechnologyLevel)
	{
		$this->ionTechnologyLevel = $ionTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getPlasmaTechnologyLevel()
	{
		return $this->plasmaTechnologyLevel;
	}

	/**
	 * @param int $plasmaTechnologyLevel
	 */
	public function setPlasmaTechnologyLevel($plasmaTechnologyLevel)
	{
		$this->plasmaTechnologyLevel = $plasmaTechnologyLevel;
	}

	/**
	 * @return int
	 */
	public function getIntergalacticResearchNetworkLevel()
	{
		return $this->intergalacticResearchNetworkLevel;
	}

	/**
	 * @param int $intergalacticResearchNetworkLevel
	 */
	public function setIntergalacticResearchNetworkLevel($intergalacticResearchNetworkLevel)
	{
		$this->intergalacticResearchNetworkLevel = $intergalacticResearchNetworkLevel;
	}

	/**
	 * @return int
	 */
	public function getAstrophysicsLevel()
	{
		return $this->astrophysicsLevel;
	}

	/**
	 * @param int $astrophysicsLevel
	 */
	public function setAstrophysicsLevel($astrophysicsLevel)
	{
		$this->astrophysicsLevel = $astrophysicsLevel;
	}

	/**
	 * @return int
	 */
	public function getGravitonTechnologyLevel()
	{
		return $this->gravitonTechnologyLevel;
	}

	/**
	 * @param int $gravitonTechnologyLevel
	 */
	public function setGravitonTechnologyLevel($gravitonTechnologyLevel)
	{
		$this->gravitonTechnologyLevel = $gravitonTechnologyLevel;
	}

	public function isOnCoordinates(ValueObject\Coordinates $coordinates) : bool
	{
		return $this->coordinates->isSame($coordinates);
	}

	public function getCoordinates() : Coordinates
	{
		return $this->coordinates;
	}

}
