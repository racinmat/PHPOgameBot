<?php
 
namespace App\Model\Entity;

use App\Enum\Building;
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
	private $probesToLastEspionage;

	/**
	 * @ORM\Column(type="boolean")
	 * @var boolean
	 */
	private $gotAllInformationFromLastEspionage;

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
		$this->probesToLastEspionage = 0;
		$this->gotAllInformationFromLastEspionage = false;
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

	public function gotAllInformationFromLastEspionage() : bool
	{
		return $this->gotAllInformationFromLastEspionage;
	}

	public function setGotAllInformationFromLastEspionage(bool $gotAllInformationFromLastEspionage)
	{
		$this->gotAllInformationFromLastEspionage = $gotAllInformationFromLastEspionage;
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

}
