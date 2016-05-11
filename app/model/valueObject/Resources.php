<?php

namespace App\Model\ValueObject;
 
use Nette;
 
class Resources extends Nette\Object
{

	/** @var int */
	private $metal;

	/** @var int */
	private $crystal;

	/** @var int */
	private $deuterium;

	/**
	 * Resources constructor.
	 * @param int $metal
	 * @param int $crystal
	 * @param int $deuterium
	 */
	public function __construct($metal, $crystal, $deuterium)
	{
		$this->metal = $metal;
		$this->crystal = $crystal;
		$this->deuterium = $deuterium;
	}

	/**
	 * @return int
	 */
	public function getMetal()
	{
		return $this->metal;
	}

	/**
	 * @return int
	 */
	public function getCrystal()
	{
		return $this->crystal;
	}

	/**
	 * @return int
	 */
	public function getDeuterium()
	{
		return $this->deuterium;
	}

	public function add(Resources $resources) : Resources
	{
		return new Resources($this->metal + $resources->getMetal(), $this->crystal + $resources->getCrystal(), $this->deuterium + $resources->getDeuterium());
	}

	public function subtract(Resources $resources) : Resources
	{
		return new Resources($this->metal - $resources->getMetal(), $this->crystal - $resources->getCrystal(), $this->deuterium - $resources->getDeuterium());
	} 
	
	public function multiplyScalar(float $number) : Resources
	{
		return new Resources($this->metal * $number, $this->crystal * $number, $this->deuterium * $number);
	}

	public function divide(Resources $resources) : Resources
	{
		return new Resources(round($this->metal / $resources->getMetal()), round($this->crystal / $resources->getCrystal()), round($this->deuterium / $resources->getDeuterium()));
	}
}