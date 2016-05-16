<?php
 
namespace App\Model\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

class Coordinates extends Object
{

	private $minGalaxy = 1;
	private $minSystem = 1;
	private $minPlanet = 1;

	private $maxGalaxy = 6;
	private $maxSystem = 499;
	private $maxPlanet = 15;

	/**
	 * @var integer
	 */
	private $galaxy;

	/**
	 * @var integer
	 */
	private $system;
	/**
	 * @var integer
	 */
	private $planet;

	public function __construct(int $galaxy, int $system, int $planet)
	{
		if ($galaxy < $this->minGalaxy) {
			$galaxy = $this->minGalaxy;
		}
		if ($galaxy > $this->maxGalaxy) {
			$galaxy = $this->maxGalaxy;
		}

		if ($system < $this->minSystem) {
			$system = $this->minSystem;
		}
		if ($system > $this->maxSystem) {
			$system = $this->maxSystem;
		}

		if ($planet < $this->minPlanet) {
			$planet = $this->minPlanet;
		}
		if ($planet > $this->maxPlanet) {
			$planet = $this->maxPlanet;
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
		if ($this->galaxy <= $coordinates->galaxy) {
			return true;
		}
		if ($this->system <= $coordinates->system) {
			return true;
		}
		if ($this->planet <= $coordinates->planet) {
			return true;
		}
		return false;
	}

	public function nextSystem() : Coordinates
	{
		return new Coordinates($this->galaxy, $this->system + 1, $this->planet);
	}

	public function isEndOfGalaxy() : bool
	{
		return $this->system === $this->maxSystem;
	}

	public function isEndOfUniverse() : bool
	{
		return $this->galaxy === $this->maxGalaxy;
	}

}
