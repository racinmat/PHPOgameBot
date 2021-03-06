<?php

namespace App\Model\ValueObject;
 
use App\Enum\FleetMission;
use App\Enum\FlightStatus;
use App\Model\Entity\Planet;
use Carbon\Carbon;
use Nette;
 
class Flight extends Nette\Object
{

	/** @var Fleet */
	private $fleet;

	/** @var Coordinates */
	private $from;

	/** @var Coordinates */
	private $to;

	/** @var FleetMission */
	private $mission;

	/** @var Carbon */
	private $arrivalTime;

	/** @var bool */
	private $returning;

	/** @var FlightStatus */
	private $status;

	/** @var Resources */
	private $resources;

	public function __construct(Fleet $fleet, Coordinates $from, Coordinates $to, FleetMission $mission, Carbon $arrivalTime, bool $returning, FlightStatus $status, Resources $resources)
	{
		$this->fleet = $fleet;
		$this->from = $from;
		$this->to = $to;
		$this->mission = $mission;
		$this->arrivalTime = $arrivalTime;
		$this->returning = $returning;
		$this->status = $status;
		$this->resources = $resources;
	}

	/**
	 * @return Fleet
	 */
	public function getFleet()
	{
		return $this->fleet;
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

	/**
	 * @return FleetMission
	 */
	public function getMission()
	{
		return $this->mission;
	}

	public function getArrivalTime() : Carbon
	{
		return $this->arrivalTime;
	}

	/**
	 * @return boolean
	 */
	public function isReturning()
	{
		if ($this->getMission() === FleetMission::_(FleetMission::DEPLOYMENT)) {
			return true;    //deployment behaves as returning fleet
		}
		return $this->returning;
	}

	/**
	 * @return FlightStatus
	 */
	public function getStatus()
	{
		return $this->status;
	}

	public function toArray() : array
	{
		return [
			'from' => $this->from->toArray(),
			'to' => $this->to->toArray(),
			'arrivalTime' => $this->arrivalTime->__toString(),
			'returning' => $this->isReturning(),
			'mission' => $this->mission->__toString(),
			'status' => $this->status->__toString(),
			'fleet' => $this->fleet->toArray(),
			'resources' => $this->resources->toArray()
		];
	}

	public static function incomingAttacks() : callable
	{
		return function (Flight $flight) {
			return $flight->getStatus()->getValue() === FlightStatus::ENEMY && ! $flight->isReturning();
		};
	}

	public static function myReturning() : callable
	{
		return function (Flight $flight) : bool {
			return $flight->getStatus()->getValue() === FlightStatus::MINE && $flight->isReturning();
		};
	}

	public static function withMission(FleetMission $mission)
	{
		return function (Flight $flight) use ($mission) : bool {
			return $flight->getMission() === $mission;
		};
	}

	public static function toArrivalTime() : callable
	{
		return function (Flight $flight) : Carbon {
			return $flight->getArrivalTime();
		};
	}

	public static function withFleet(Fleet $fleet)
	{
		return function (Flight $flight) use ($fleet) {
			return $flight->getFleet()->contains($fleet);
		};
	}

	public static function fromPlanet(Planet $planet) : callable
	{
		return function (Flight $flight) use ($planet) {
			return $flight->from->equals($planet->getCoordinates());
		};
	}

	public static function toPlanet(Planet $planet) : callable
	{
		return function (Flight $flight) use ($planet) {
			return $flight->to->equals($planet->getCoordinates());
		};
	}

	public function getResources() : Resources
	{
		return $this->resources;
	}

	public function carriesResources() : bool
	{
		$hasResources = ! $this->getResources()->isZero();
		$isNotReturningTransport = $this->getStatus() === FleetMission::_(FleetMission::TRANSPORT) && ! $this->isReturning();
		$isDeployment = $this->getStatus() === FleetMission::_(FleetMission::DEPLOYMENT);
		return $hasResources && ($isNotReturningTransport || $isDeployment);
	}
}