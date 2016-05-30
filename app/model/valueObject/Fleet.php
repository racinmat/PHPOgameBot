<?php

namespace App\Model\ValueObject;

use App\Enum\Ships;
use Nette\Object;

class Fleet extends Object
{

	/** @var array Ships => int */
	private $fleet;

	public function __construct()
	{
		$this->fleet = [];
	}

	public function addShips(Ships $ships, int $count)
	{
		if (isset($this->fleet[$ships->getValue()])) {
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
		return $this->fleet;
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
		$nonZeroFleet = [];
		foreach ($this->fleet as $ship => $count) {
			if ($count > 0) {
				$nonZeroFleet[$ship] = $count;
			}
		}
		return $nonZeroFleet;
	}
}