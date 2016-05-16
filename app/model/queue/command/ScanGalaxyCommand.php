<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\CoordinatesDifference;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

class ScanGalaxyCommand extends BaseCommand
{

	/** @var Coordinates */
	private $middle;

	/** @var CoordinatesDifference */
	private $range;

	public function __construct(Coordinates $coordinates, array $data, Uuid $uuid = null)
	{
		parent::__construct($coordinates, $data, $uuid);
	}

	public static function getAction() : string
	{
		return static::ACTION_SCAN_GALAXY;
	}

	public static function fromArray(array $data) : ScanGalaxyCommand
	{
		return new ScanGalaxyCommand(Coordinates::fromArray($data['coordinates']), $data['data'], isset($data['uuid']) ? Uuid::fromString($data['uuid']) : null);
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'middle' => $this->middle->toArray(),
				'range' => $this->range->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		Debugger::barDump($data, 'array data');
		$this->middle = Coordinates::fromArray($data['middle']);
		$this->range = CoordinatesDifference::fromArray($data['range']);
	}

	/**
	 * @return boolean
	 */
	public function isOnlyInactive()
	{
		return $this->onlyInactive;
	}

	/**
	 * @return Coordinates
	 */
	public function getMiddle()
	{
		return $this->middle;
	}

	/**
	 * @return Coordinates
	 */
	public function getRange()
	{
		return $this->range;
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_NOTHING;
	}

}
