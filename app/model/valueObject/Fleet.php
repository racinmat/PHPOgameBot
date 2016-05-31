<?php

namespace App\Model\ValueObject;

use App\Enum\Ships;
use App\Utils\ArrayCollection;
use Nette\Object;

class Fleet extends Object
{

	/** @var ArrayCollection string => int */
	private $fleet;

	public function __construct()
	{
		$this->fleet = new ArrayCollection();
	}

	public function addShips(Ships $ships, int $count)
	{
		if ($this->fleet->containsKey($ships->getValue())) {
			$this->fleet[$ships->getValue()] += $count;
		} else {
			$this->fleet[$ships->getValue()] = $count;
		}
	}

	public function getCapacity() : int
	{
		$capacity = 0;
		foreach ($this->fleet as $shipName => $count) {
			$ship = Ships::_($shipName);
			$capacity += $ship->getCapacity() * $count;
		}
		return $capacity;
	}

	public function toArray() : array
	{
		return $this->fleet->toArray();
	}

	public static function fromArray(array $data) : Fleet
	{
		$fleet = new Fleet();
		foreach ($data as $ship => $count) {
			$fleet->addShips(Ships::_($ship), $count);
		}
		return $fleet;
	}

	public function isEmpty() : bool
	{
		return count($this->fleet) === 0;
	}

	public function getNonZeroShips() : array
	{
		return $this->fleet->filter(function ($count) {return $count > 0;})->toArray();
	}

}