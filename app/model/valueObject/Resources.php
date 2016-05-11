<?php

namespace App\Model\ValueObject;
 
use Nette;

/**
 * Class Resources
 * This class behaves like immutable and returns the new class with modified state.
 * @package App\Model\ValueObject
 */
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
		$this->metal = $metal > 0 ? $metal : 0;
		$this->crystal = $crystal > 0 ? $crystal : 0;
		$this->deuterium = $deuterium > 0 ? $deuterium : 0;
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

	public function forAll(callable $predicate)
	{
		$elements = [$this->metal, $this->crystal, $this->deuterium];
		foreach ($elements as $element) {
			if ( ! $predicate($element)) {
				return false;
			}
		}
		return true;
	}

	public function forAny(callable $predicate)
	{
		$elements = [$this->metal, $this->crystal, $this->deuterium];
		foreach ($elements as $element) {
			if ($predicate($element)) {
				return true;
			}
		}
		return false;
	}

}