<?php
 
namespace App\Model\Entity;

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
	private $robotFactoryLevel;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $hangarLevel;

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
	 * Planet constructor.
	 * @param string $name
	 * @param Coordinates $coordinates
	 * @param bool $my
	 */
	public function __construct($name, Coordinates $coordinates, $my)
	{
		$this->name = $name;
		$this->coordinates = $coordinates;
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
	public function getRobotFactoryLevel()
	{
		return $this->robotFactoryLevel;
	}

	/**
	 * @param int $robotFactoryLevel
	 */
	public function setRobotFactoryLevel($robotFactoryLevel)
	{
		$this->robotFactoryLevel = $robotFactoryLevel;
	}

	/**
	 * @return int
	 */
	public function getHangarLevel()
	{
		return $this->hangarLevel;
	}

	/**
	 * @param int $hangarLevel
	 */
	public function setHangarLevel($hangarLevel)
	{
		$this->hangarLevel = $hangarLevel;
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

	public function getResources()
	{
		return new Resources($this->metal, $this->crystal, $this->deuterium);
	}
}
