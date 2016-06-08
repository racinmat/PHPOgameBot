<?php

namespace App\Model\Queue\Command;
 



use App\Enum\FleetMission;

use App\Enum\PlayerStatus;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Resources;
use App\Utils\ArrayCollection;
use Nette\Utils\Arrays;


class SendFleetCommand extends BaseCommand
{

	/** @var Coordinates */
	private $to;

	/** @var Fleet */
	private $fleet;

	/** @var FleetMission */
	private $mission;
	
	/** @var Resources */
	private $resources;

	/** @var bool */
	private $waitForResources;

	/**
	 * Used only internally to prevent probing okayers who are not active. Thus it is not persisted
	 * @var PlayerStatus[]|ArrayCollection
	 */
	private $statuses;

	public static function getAction() : string
	{
		return static::ACTION_SEND_FLEET;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'to' => $this->to->toArray(),
				'fleet' => $this->fleet->toArray(),
				'mission' => $this->mission->getValue(),
				'resources' => $this->resources->toArray(),
				'waitForResources' => $this->waitForResources
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->statuses = (new ArrayCollection($data['statuses'] ?? []))->map(function ($string) {return PlayerStatus::_($string);});
		$this->to = Coordinates::fromArray($data['to']);
		$this->fleet = Fleet::fromArray($data['fleet']);
		$this->mission = FleetMission::_($data['mission']);
		$this->resources = isset($data['resources']) ? Resources::fromArray($data['resources']) : new Resources(0, 0, 0);
		$this->waitForResources = $data['waitForResources'] ?? false;

		if ($this->fleet->isEmpty()) {
			throw new \InvalidArgumentException("SendFleetCommand can not have empty fleet.");
		}

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
	 * @return Fleet
	 */
	public function getFleet()
	{
		return $this->fleet;
	}

	public function getMission() : FleetMission
	{
		return $this->mission;
	}

	public function getResources() : Resources
	{
		return $this->resources;
	}

	public function waitForResources() : bool
	{
		return $this->waitForResources;
	}

	public function setTo(Coordinates $to)
	{
		$this->to = $to;
	}

	public function setFleet(Fleet $fleet)
	{
		$this->fleet = $fleet;
	}

	public function setMission(FleetMission $mission)
	{
		$this->mission = $mission;
	}

	public function setResources(Resources $resources)
	{
		$this->resources = $resources;
	}

	public function setWaitForResources(bool $waitForResources)
	{
		$this->waitForResources = $waitForResources;
	}

	/**
	 * @return \App\Enum\PlayerStatus[]|ArrayCollection
	 */
	public function getStatuses() : ArrayCollection
	{
		return $this->statuses;
	}

	public function hasStatuses() : bool
	{
		return ! $this->statuses->isEmpty();
	}

}