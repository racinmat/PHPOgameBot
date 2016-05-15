<?php
 
namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity
 */
class Coordinates extends Object
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	private $id;

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

	/**
	 * Coordinates constructor.
	 * @param int $galaxy
	 * @param int $system
	 * @param int $planet
	 */
	public function __construct($galaxy, $system, $planet)
	{
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

	public function toValueObject() : \App\Model\ValueObject\Coordinates
	{
		return new \App\Model\ValueObject\Coordinates($this->galaxy, $this->system, $this->planet);
	} 
	
	public function isSame(\App\Model\ValueObject\Coordinates $coordinates) : bool 
	{
		return $this->toValueObject()->equals($coordinates);
	}

}
