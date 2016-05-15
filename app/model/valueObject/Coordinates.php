<?php
 
namespace App\Model\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities;
use Nette\Object;

/**
 * @ORM\Entity
 */
class Coordinates extends Object
{

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
		return "[$this->galaxy:$this->system:$this->planet]";
	}
}
