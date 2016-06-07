<?php
 
namespace App\Model\Entity;

use App\Enum\Building;
use App\Enum\ProbingStatus;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity()
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
	 * @ORM\Embedded(class="\App\Model\ValueObject\Coordinates")
	 * @var Coordinates
	 */
	private $coordinates;

	/**
	 * @ORM\ManyToOne(targetEntity="Player", inversedBy="planets")
	 * @var Player
	 */
	private $player;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	private $moon;

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
	private $allianceDepotLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $naniteFactoryLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $missileSiloLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $researchLabLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $terraformerLevel;
	
	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $debrisMetal;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $debrisCrystal;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $rocketLauncherAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $lightLaserAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $heavyLaserAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $ionCannonAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $gaussCannonAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $plasmaTurretAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $smallShieldDomeAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $largeShieldDomeAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $antiBallisticMissileAmount;

	/** 
	 * @ORM\Column(type="integer") 
	 * @var int 
	 */ 
	private $interplanetaryMissileAmount;
	
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $smallCargoShipAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $largeCargoShipAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $lightFighterAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $heavyFighterAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $cruiserAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $battleshipAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $battlecruiserAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $destroyerAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $deathstarAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $bomberAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $recyclerAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $espionageProbeAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $solarSatelliteAmount;
	
	/** 
 	 * @ORM\Column(type="integer") 
 	 * @var int  
	 */ 
	private $colonyShipAmount;
	
	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $probesToLastEspionage;

	/**
	 * @ORM\Column(type="probingstatus")
	 * @var ProbingStatus
	 */
	private $probingStatus;

	public function __construct(string $name, Coordinates $coordinates, Player $player)
	{
		$this->name = $name;
		$this->coordinates = new Coordinates($coordinates->getGalaxy(), $coordinates->getSystem(), $coordinates->getPlanet());
		$this->player = $player;
		$this->moon = false;
		$this->metal = 0;
		$this->crystal = 0;
		$this->deuterium = 0;
		$this->lastVisited = Carbon::now();
		$this->minimalTemperature = 0;
		$this->maximalTemperature = 0;
		$this->metalStorageLevel = 0;
		$this->crystalStorageLevel = 0;
		$this->deuteriumTankLevel = 0;
		$this->metalMineLevel = 0;
		$this->crystalMineLevel = 0;
		$this->deuteriumMineLevel = 0;
		$this->solarPowerPlantLevel = 0;
		$this->roboticFactoryLevel = 0;
		$this->shipyardLevel = 0;
		$this->fusionReactorLevel = 0;
		$this->debrisMetal = 0;
		$this->debrisCrystal = 0;
		$this->allianceDepotLevel = 0;
		$this->terraformerLevel = 0;
		$this->researchLabLevel = 0;
		$this->missileSiloLevel = 0;
		$this->naniteFactoryLevel = 0;
		$this->rocketLauncherAmount = 0;
		$this->lightLaserAmount = 0;
		$this->heavyLaserAmount = 0;
		$this->ionCannonAmount = 0;
		$this->gaussCannonAmount = 0;
		$this->plasmaTurretAmount = 0;
		$this->smallShieldDomeAmount = 0;
		$this->largeShieldDomeAmount = 0;
		$this->antiBallisticMissileAmount = 0;
		$this->interplanetaryMissileAmount = 0;
		$this->smallCargoShipAmount = 0;
		$this->largeCargoShipAmount = 0;
		$this->lightFighterAmount = 0;
		$this->heavyFighterAmount = 0;
		$this->cruiserAmount = 0;
		$this->battleshipAmount = 0;
		$this->battlecruiserAmount = 0;
		$this->destroyerAmount = 0;
		$this->deathstarAmount = 0;
		$this->bomberAmount = 0;
		$this->recyclerAmount = 0;
		$this->espionageProbeAmount = 0;
		$this->solarSatelliteAmount = 0;
		$this->colonyShipAmount = 0;
		$this->probesToLastEspionage = 0;
		$this->probingStatus = ProbingStatus::_(ProbingStatus::MISSING_FLEET);
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
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

	public function isOnCoordinates(Coordinates $coordinates) : bool
	{
		return $this->coordinates->equals($coordinates);
	}

	public function getCoordinates() : Coordinates
	{
		return $this->coordinates;
	}

	public function getPlayer() : Player
	{
		return $this->player;
	}

	/**
	 * @return boolean
	 */
	public function hasMoon()
	{
		return $this->moon;
	}

	/**
	 * @param boolean $moon
	 */
	public function setMoon($moon)
	{
		$this->moon = $moon;
	}

	/**
	 * @param int $debrisMetal
	 */
	public function setDebrisMetal($debrisMetal)
	{
		$this->debrisMetal = $debrisMetal;
	}

	/**
	 * @param mixed $debrisCrystal
	 */
	public function setDebrisCrystal($debrisCrystal)
	{
		$this->debrisCrystal = $debrisCrystal;
	}

	/**
	 * @return int
	 */
	public function getDebrisMetal()
	{
		return $this->debrisMetal;
	}

	/**
	 * @return mixed
	 */
	public function getDebrisCrystal()
	{
		return $this->debrisCrystal;
	}

	/**
	 * @return int
	 */
	public function getAllianceDepotLevel()
	{
		return $this->allianceDepotLevel;
	}

	/**
	 * @param int $allianceDepotLevel
	 */
	public function setAllianceDepotLevel($allianceDepotLevel)
	{
		$this->allianceDepotLevel = $allianceDepotLevel;
	}

	/**
	 * @return int
	 */
	public function getNaniteFactoryLevel()
	{
		return $this->naniteFactoryLevel;
	}

	/**
	 * @param int $naniteFactoryLevel
	 */
	public function setNaniteFactoryLevel($naniteFactoryLevel)
	{
		$this->naniteFactoryLevel = $naniteFactoryLevel;
	}

	/**
	 * @return int
	 */
	public function getMissileSiloLevel()
	{
		return $this->missileSiloLevel;
	}

	/**
	 * @param int $missileSiloLevel
	 */
	public function setMissileSiloLevel($missileSiloLevel)
	{
		$this->missileSiloLevel = $missileSiloLevel;
	}

	/**
	 * @return int
	 */
	public function getResearchLabLevel()
	{
		return $this->researchLabLevel;
	}

	/**
	 * @param int $researchLabLevel
	 */
	public function setResearchLabLevel($researchLabLevel)
	{
		$this->researchLabLevel = $researchLabLevel;
	}

	/**
	 * @return int
	 */
	public function getTerraformerLevel()
	{
		return $this->terraformerLevel;
	}

	/**
	 * @param int $terraformerLevel
	 */
	public function setTerraformerLevel($terraformerLevel)
	{
		$this->terraformerLevel = $terraformerLevel;
	}

	public function getProbesToLastEspionage() : int
	{
		return $this->probesToLastEspionage;
	}

	public function setProbesToLastEspionage(int $probesToLastEspionage)
	{
		$this->probesToLastEspionage = $probesToLastEspionage;
	}

	public function getProbingStatus() : ProbingStatus
	{
		return $this->probingStatus;
	}

	public function setProbingStatus(ProbingStatus $probingStatus)
	{
		$this->probingStatus = $probingStatus;
	}


	public function hasLoadedTemperature() : bool
	{
		return $this->minimalTemperature != 0 || $this->maximalTemperature != 0;
	}

	public function setMinimalTemperature(int $minimalTemperature)
	{
		$this->minimalTemperature = $minimalTemperature;
	}

	public function setMaximalTemperature(int $maximalTemperature)
	{
		$this->maximalTemperature = $maximalTemperature;
	}

	/**
	 * @return int
	 */
	public function getRocketLauncherAmount()
	{
		return $this->rocketLauncherAmount;
	}

	/**
	 * @param int $rocketLauncherAmount
	 */
	public function setRocketLauncherAmount($rocketLauncherAmount)
	{
		$this->rocketLauncherAmount = $rocketLauncherAmount;
	}

	/**
	 * @return int
	 */
	public function getLightLaserAmount()
	{
		return $this->lightLaserAmount;
	}

	/**
	 * @param int $lightLaserAmount
	 */
	public function setLightLaserAmount($lightLaserAmount)
	{
		$this->lightLaserAmount = $lightLaserAmount;
	}

	/**
	 * @return int
	 */
	public function getHeavyLaserAmount()
	{
		return $this->heavyLaserAmount;
	}

	/**
	 * @param int $heavyLaserAmount
	 */
	public function setHeavyLaserAmount($heavyLaserAmount)
	{
		$this->heavyLaserAmount = $heavyLaserAmount;
	}

	/**
	 * @return int
	 */
	public function getIonCannonAmount()
	{
		return $this->ionCannonAmount;
	}

	/**
	 * @param int $ionCannonAmount
	 */
	public function setIonCannonAmount($ionCannonAmount)
	{
		$this->ionCannonAmount = $ionCannonAmount;
	}

	/**
	 * @return int
	 */
	public function getGaussCannonAmount()
	{
		return $this->gaussCannonAmount;
	}

	/**
	 * @param int $gaussCannonAmount
	 */
	public function setGaussCannonAmount($gaussCannonAmount)
	{
		$this->gaussCannonAmount = $gaussCannonAmount;
	}

	/**
	 * @return int
	 */
	public function getPlasmaTurretAmount()
	{
		return $this->plasmaTurretAmount;
	}

	/**
	 * @param int $plasmaTurretAmount
	 */
	public function setPlasmaTurretAmount($plasmaTurretAmount)
	{
		$this->plasmaTurretAmount = $plasmaTurretAmount;
	}

	/**
	 * @return int
	 */
	public function getSmallShieldDomeAmount()
	{
		return $this->smallShieldDomeAmount;
	}

	/**
	 * @param int $smallShieldDomeAmount
	 */
	public function setSmallShieldDomeAmount($smallShieldDomeAmount)
	{
		$this->smallShieldDomeAmount = $smallShieldDomeAmount;
	}

	/**
	 * @return int
	 */
	public function getLargeShieldDomeAmount()
	{
		return $this->largeShieldDomeAmount;
	}

	/**
	 * @param int $largeShieldDomeAmount
	 */
	public function setLargeShieldDomeAmount($largeShieldDomeAmount)
	{
		$this->largeShieldDomeAmount = $largeShieldDomeAmount;
	}

	/**
	 * @return int
	 */
	public function getAntiBallisticMissileAmount()
	{
		return $this->antiBallisticMissileAmount;
	}

	/**
	 * @param int $antiBallisticMissileAmount
	 */
	public function setAntiBallisticMissileAmount($antiBallisticMissileAmount)
	{
		$this->antiBallisticMissileAmount = $antiBallisticMissileAmount;
	}

	/**
	 * @return int
	 */
	public function getInterplanetaryMissileAmount()
	{
		return $this->interplanetaryMissileAmount;
	}

	/**
	 * @param int $interplanetaryMissileAmount
	 */
	public function setInterplanetaryMissileAmount($interplanetaryMissileAmount)
	{
		$this->interplanetaryMissileAmount = $interplanetaryMissileAmount;
	}

	/**
	 * @return int
	 */
	public function getSmallCargoShipAmount()
	{
		return $this->smallCargoShipAmount;
	}

	/**
	 * @param int $smallCargoShipAmount
	 */
	public function setSmallCargoShipAmount($smallCargoShipAmount)
	{
		$this->smallCargoShipAmount = $smallCargoShipAmount;
	}

	/**
	 * @return int
	 */
	public function getLargeCargoShipAmount()
	{
		return $this->largeCargoShipAmount;
	}

	/**
	 * @param int $largeCargoShipAmount
	 */
	public function setLargeCargoShipAmount($largeCargoShipAmount)
	{
		$this->largeCargoShipAmount = $largeCargoShipAmount;
	}

	/**
	 * @return int
	 */
	public function getLightFighterAmount()
	{
		return $this->lightFighterAmount;
	}

	/**
	 * @param int $lightFighterAmount
	 */
	public function setLightFighterAmount($lightFighterAmount)
	{
		$this->lightFighterAmount = $lightFighterAmount;
	}

	/**
	 * @return int
	 */
	public function getHeavyFighterAmount()
	{
		return $this->heavyFighterAmount;
	}

	/**
	 * @param int $heavyFighterAmount
	 */
	public function setHeavyFighterAmount($heavyFighterAmount)
	{
		$this->heavyFighterAmount = $heavyFighterAmount;
	}

	/**
	 * @return int
	 */
	public function getCruiserAmount()
	{
		return $this->cruiserAmount;
	}

	/**
	 * @param int $cruiserAmount
	 */
	public function setCruiserAmount($cruiserAmount)
	{
		$this->cruiserAmount = $cruiserAmount;
	}

	/**
	 * @return int
	 */
	public function getBattleshipAmount()
	{
		return $this->battleshipAmount;
	}

	/**
	 * @param int $battleshipAmount
	 */
	public function setBattleshipAmount($battleshipAmount)
	{
		$this->battleshipAmount = $battleshipAmount;
	}

	/**
	 * @return int
	 */
	public function getBattlecruiserAmount()
	{
		return $this->battlecruiserAmount;
	}

	/**
	 * @param int $battlecruiserAmount
	 */
	public function setBattlecruiserAmount($battlecruiserAmount)
	{
		$this->battlecruiserAmount = $battlecruiserAmount;
	}

	/**
	 * @return int
	 */
	public function getDestroyerAmount()
	{
		return $this->destroyerAmount;
	}

	/**
	 * @param int $destroyerAmount
	 */
	public function setDestroyerAmount($destroyerAmount)
	{
		$this->destroyerAmount = $destroyerAmount;
	}

	/**
	 * @return int
	 */
	public function getDeathstarAmount()
	{
		return $this->deathstarAmount;
	}

	/**
	 * @param int $deathstarAmount
	 */
	public function setDeathstarAmount($deathstarAmount)
	{
		$this->deathstarAmount = $deathstarAmount;
	}

	/**
	 * @return int
	 */
	public function getBomberAmount()
	{
		return $this->bomberAmount;
	}

	/**
	 * @param int $bomberAmount
	 */
	public function setBomberAmount($bomberAmount)
	{
		$this->bomberAmount = $bomberAmount;
	}

	/**
	 * @return int
	 */
	public function getRecyclerAmount()
	{
		return $this->recyclerAmount;
	}

	/**
	 * @param int $recyclerAmount
	 */
	public function setRecyclerAmount($recyclerAmount)
	{
		$this->recyclerAmount = $recyclerAmount;
	}

	/**
	 * @return int
	 */
	public function getEspionageProbeAmount()
	{
		return $this->espionageProbeAmount;
	}

	/**
	 * @param int $espionageProbeAmount
	 */
	public function setEspionageProbeAmount($espionageProbeAmount)
	{
		$this->espionageProbeAmount = $espionageProbeAmount;
	}

	/**
	 * @return int
	 */
	public function getSolarSatelliteAmount()
	{
		return $this->solarSatelliteAmount;
	}

	/**
	 * @param int $solarSatelliteAmount
	 */
	public function setSolarSatelliteAmount($solarSatelliteAmount)
	{
		$this->solarSatelliteAmount = $solarSatelliteAmount;
	}

	/**
	 * @return int
	 */
	public function getColonyShipAmount()
	{
		return $this->colonyShipAmount;
	}

	/**
	 * @param int $colonyShipAmount
	 */
	public function setColonyShipAmount($colonyShipAmount)
	{
		$this->colonyShipAmount = $colonyShipAmount;
	}

}
