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
	
}
