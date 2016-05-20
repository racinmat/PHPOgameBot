<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Enum\Enhanceable;
use App\Enum\FleetMission;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class SendFleetCommand extends BaseCommand
{

	/** @var Coordinates */
	private $to;

	/** @var array string => int */
	private $fleet;

	/** @var FleetMission */
	private $mission;
	
	public static function getAction() : string
	{
		return static::ACTION_SEND_FLEET;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'to' => $this->to->toArray(),
				'fleet' => $this->fleet,
				'mission' => $this->mission->getValue()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->to = Coordinates::fromArray($data['to']);
		$this->fleet = $data['fleet'];
		$this->mission = FleetMission::_($data['mission']);
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	public function getTo() : Coordinates
	{
		return $this->to;
	}

	/**
	 * @return array
	 */
	public function getFleet()
	{
		return $this->fleet;
	}

	public function getMission() : FleetMission
	{
		return $this->mission;
	}

}