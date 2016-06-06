<?php
 
namespace App\Model\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;


/**
 * @ORM\Embeddable()
 */
class Coordinates extends Object
{

	public static $minGalaxy = 1;
	public static $minSystem = 1;
	public static $minPlanet = 1;

	public static $maxGalaxy = 6;
	public static $maxSystem = 499;
	public static $maxPlanet = 15;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $galaxy;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $system;

	/**
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	private $planet;

	public function __construct(int $galaxy, int $system, int $planet)
	{
		if ($galaxy < static::$minGalaxy) {
			$galaxy = static::$minGalaxy;
		}
		if ($galaxy > static::$maxGalaxy) {
			$galaxy = static::$maxGalaxy;
		}

		if ($system < static::$minSystem) {
			$system = static::$minSystem;
		}
		if ($system > static::$maxSystem) {
			$system = static::$maxSystem;
		}

		if ($planet < static::$minPlanet) {
			$planet = static::$minPlanet;
		}
		if ($planet > static::$maxPlanet) {
			$planet = static::$maxPlanet;
		}

		$this->galaxy = $galaxy;
		$this->system = $system;
		$this->planet = $planet;
	}

	public function getGalaxy() : int
	{
		return $this->galaxy;
	}

	public function getSystem() : int
	{
		return $this->system;
	}

	public function getPlanet() : int
	{
		return $this->planet;
	}

	public static function fromArray(array $data) : Coordinates
	{
		return new Coordinates($data['galaxy'], $data['system'], $data['planet']);
	}

	public function toArray() : array
	{
		return [
			'galaxy' => $this->galaxy,
			'system' => $this->system,
			'planet' => $this->planet
		];
	}

	public function equals(Coordinates $another) : bool
	{
		return $this->galaxy === $another->getGalaxy() && $this->system === $another->getSystem() && $this->planet === $another->getPlanet();
	}

	public function __toString() : string
	{
		return $this->toString();
	}

	public function toString() : string
	{
		return "[$this->galaxy:$this->system:$this->planet]";
	}

	public function subtract(Coordinates $coordinates) : Coordinates
	{
		return new Coordinates($this->galaxy - $coordinates->galaxy, $this->system - $coordinates->system, $this->planet - $coordinates->planet);
	}

	public function add(Coordinates $coordinates) : Coordinates
	{
		return new Coordinates($this->galaxy + $coordinates->galaxy, $this->system + $coordinates->system, $this->planet + $coordinates->planet);
	}

	public function isGreaterThan(Coordinates $coordinates) : bool
	{
		if ($this->galaxy > $coordinates->galaxy) {
			return true;
		}
		if ($this->system > $coordinates->system) {
			return true;
		}
		if ($this->planet > $coordinates->planet) {
			return true;
		}
		return false;
	}

	public function isLesserThan(Coordinates $coordinates) : bool
	{
		if ($this->galaxy < $coordinates->galaxy) {
			return true;
		}
		if ($this->system < $coordinates->system) {
			return true;
		}
		if ($this->planet < $coordinates->planet) {
			return true;
		}
		return false;
	}

	public function isLesserThanOrEquals(Coordinates $coordinates) : bool
	{
		if ($this->galaxy < $coordinates->galaxy) {
			return true;
		}
		if ($this->galaxy > $coordinates->galaxy) {
			return false;
		}
		//same galaxy
		if ($this->system < $coordinates->system) {
			return true;
		}
		if ($this->system > $coordinates->system) {
			return false;
		}
		//same galaxy and system
		if ($this->planet <= $coordinates->planet) {
			return true;
		}
		return false;
	}

	public function nextSystem() : Coordinates
	{
		if ($this->system == static::$maxSystem) {
			return new Coordinates($this->galaxy + 1, static::$minSystem, $this->planet);
		}
		return new Coordinates($this->galaxy, $this->system + 1, $this->planet);
	}

	public function isEndOfGalaxy() : bool
	{
		return $this->system === static::$maxSystem;
	}

	public function isEndOfUniverse() : bool
	{
		return $this->galaxy === static::$maxGalaxy;
	}

	public function planet(int $planet) : Coordinates
	{
		return new Coordinates($this->galaxy, $this->system, $planet);
	}

}
