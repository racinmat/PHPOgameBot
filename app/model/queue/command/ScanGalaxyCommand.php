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
	private $from;

	/** @var CoordinatesDifference */
	private $to;

	public static function getAction() : string
	{
		return static::ACTION_SCAN_GALAXY;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'from' => $this->from->toArray(),
				'to' => $this->to->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->from = Coordinates::fromArray($data['from']);
		$this->to = Coordinates::fromArray($data['to']);
	}

	/**
	 * @return Coordinates
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @return Coordinates
	 */
	public function getTo()
	{
		return $this->to;
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_NOTHING;
	}

}
