<?php

namespace App\Model\ValueObject;
 
use App\Utils\Functions;
use Doctrine\Common\Collections\ArrayCollection;
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
	public function __construct(int $metal, int $crystal, int $deuterium)
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
	
	public function multiplyByScalar(float $number) : Resources
	{
		return new Resources($this->metal * $number, $this->crystal * $number, $this->deuterium * $number);
	}

	public function divideByScalar(float $number) : Resources
	{
		return new Resources(round($this->metal / $number), round($this->crystal / $number), round($this->deuterium / $number));
	}

	public function divide(Resources $resources) : array
	{
		return $this->map(function($key, $value) use ($resources) {
			$divisor = $resources->toArray()[$key];
			if ($value === 0) {
				return 0;
			}
			if ($divisor === 0) {
				return PHP_INT_MAX;
			}
			return $value / $divisor;
		});
	}

	public function map(callable $function) : array 
	{
		$result = [];
		foreach ($this->toArray() as $key => $element) {
			$result[$key] = $function($key, $element);
		}
		return $result;
	}
	
	public function forAll(callable $predicate)
	{
		foreach ($this->toArray() as $element) {
			if ( ! $predicate($element)) {
				return false;
			}
		}
		return true;
	}

	public function forAny(callable $predicate)
	{
		foreach ($this->toArray() as $element) {
			if ($predicate($element)) {
				return true;
			}
		}
		return false;
	}

	public function toArray() : array
	{
		return [
			'metal' => $this->metal,
			'crystal' => $this->crystal,
			'deuterium' => $this->deuterium
		];
	}

	public static function fromArray(array $data) : Resources
	{
		return new Resources($data['metal'], $data['crystal'], $data['deuterium']);
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function toString()
	{
		return "metal: $this->metal, crystal: $this->crystal, deuterium: $this->deuterium";
	}

	public function isZero() : bool
	{
		return $this->forAll(Functions::isZero());
	}

	public function getTotal() : int
	{
		return $this->metal + $this->crystal + $this->deuterium;
	}

}