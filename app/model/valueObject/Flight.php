<?php

namespace App\Model\ValueObject;
 
use App\Enum\FleetMission;
use App\Enum\FlightStatus;
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

	public function __construct(Fleet $fleet, Coordinates $from, Coordinates $to, FleetMission $mission, Carbon $arrivalTime, bool $returning, FlightStatus $status)
	{
		$this->fleet = $fleet;
		$this->from = $from;
		$this->to = $to;
		$this->mission = $mission;
		$this->arrivalTime = $arrivalTime;
		$this->returning = $returning;
		$this->status = $status;
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

	/**
	 * @return Carbon
	 */
	public function getArrivalTime()
	{
		return $this->arrivalTime;
	}

	/**
	 * @return boolean
	 */
	public function isReturning()
	{
		return $this->returning;
	}

	/**
	 * @return FlightStatus
	 */
	public function getStatus()
	{
		return $this->status;
	}

}