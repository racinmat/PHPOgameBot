<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Model\ValueObject\Coordinates;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class ScanGalaxyCommand extends BaseCommand
{

	/** @var bool */
	private $onlyInactive;

	/** @var Coordinates */
	private $middle;

	/** @var Coordinates */
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
				'onlyInactive' => $this->onlyInactive,
				'middle' => $this->middle->toArray(),
				'range' => $this->range->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->onlyInactive = $data['onlyInteractive'];
		$this->middle = Coordinates::fromArray($data['middle']);
		$this->range = Coordinates::fromArray($data['range']);
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

}
