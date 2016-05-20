<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Enum\PlayerStatus;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\CoordinatesDifference;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

class ProbePlayersCommand extends BaseCommand
{

	/** @var PlayerStatus[]|ArrayCollection */
	private $statuses;


	public function __construct(Coordinates $coordinates, array $data, Uuid $uuid = null)
	{
		parent::__construct($coordinates, $data, $uuid);
	}

	public static function getAction() : string
	{
		return static::ACTION_PROBE_PLAYERS;
	}

	public static function fromArray(array $data) : ScanGalaxyCommand
	{
		return new ProbePlayersCommand(Coordinates::fromArray($data['coordinates']), $data['data'], isset($data['uuid']) ? Uuid::fromString($data['uuid']) : null);
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'statuses' => $this->statuses->map(Functions::enumToValue())->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->statuses = (new ArrayCollection($data['statuses']))->map(function ($string) {return PlayerStatus::_($string);});
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
