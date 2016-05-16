<?php
 
namespace App\Model\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity
 */
class Player extends Object
{

	const
		STATUS_INACTIVE = 'inactive',
		STATUS_LONG_INACTIVE = 'long inactive',
		STATUS_NOOB = 'noob',
		STATUS_HONORABLE_TARGET = 'honorable target',
		STATUS_ACTIVE = 'active',
		STATUS_VACATION = 'vacation',
		STATUS_STRONG = 'strong'
	;

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
	 * @ORM\OneToMany(targetEntity="Planet", mappedBy="player")
	 * @var Planet[]
	 */
	private $planets;

	/**
	 * @ORM\Column(type="boolean")
	 * @var bool
	 */
	private $me;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $status;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 * @var string
	 */
	private $alliance;

	/**
	 * @ORM\Column(type="carbon")
	 * @var Carbon
	 */
	private $lastVisited;

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

	public function __construct(string $name, bool $me = false)
	{
		$this->name = $name;
		$this->me = $me;
		$this->status = Player::STATUS_ACTIVE;
		$this->alliance = null;
		$this->lastVisited = Carbon::now();
		$this->espionageTechnologyLevel = 0;
		$this->computerTechnologyLevel = 0;
		$this->weaponTechnologyLevel = 0;
		$this->shieldingTechnologyLevel = 0;
		$this->armourTechnologyLevel = 0;
		$this->energyTechnologyLevel = 0;
		$this->hyperspaceTechnologyLevel = 0;
		$this->combustionDriveLevel = 0;
		$this->impulseDriveLevel = 0;
		$this->hyperspaceDriveLevel = 0;
		$this->laserTechnologyLevel = 0;
		$this->ionTechnologyLevel = 0;
		$this->plasmaTechnologyLevel = 0;
		$this->intergalacticResearchNetworkLevel = 0;
		$this->astrophysicsLevel = 0;
		$this->gravitonTechnologyLevel = 0;
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
	 * @return Planet[]
	 */
	public function getPlanets()
	{
		return $this->planets;
	}

	/**
	 * @param Planet[] $planets
	 */
	public function setPlanets($planets)
	{
		$this->planets = $planets;
	}

	/**
	 * @return boolean
	 */
	public function isMe()
	{
		return $this->me;
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
	public function setLastVisited($lastVisited)
	{
		$this->lastVisited = $lastVisited;
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

	/**
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @param string $alliance
	 */
	public function setAlliance($alliance)
	{
		$this->alliance = $alliance;
	}

}
