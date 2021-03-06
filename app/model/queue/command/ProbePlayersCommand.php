<?php

namespace App\Model\Queue\Command;
 
use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlanetProbingStatus;
use App\Enum\PlayerStatus;

use App\Enum\ProbingStatus;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Nette\Utils\Arrays;


class ProbePlayersCommand extends BaseCommand
{

	/** @var PlayerStatus[]|ArrayCollection */
	private $statuses;

	/** @var int */
	private $limit;

	/** @var OrderType */
	private $orderType;

	/** @var OrderPlanetsBy */
	private $orderBy;

	/** @var ProbingStatus[]|ArrayCollection */
	private $probingStatuses;

	/** @var PlanetProbingStatus[]|ArrayCollection */
	private $planetProbingStatuses;

	public static function getAction() : string
	{
		return static::ACTION_PROBE_PLAYERS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'statuses' => $this->statuses->map(Functions::enumToValue())->toArray(),
				'probingStatuses' => $this->probingStatuses->map(Functions::enumToValue())->toArray(),
				'planetProbingStatuses' => $this->planetProbingStatuses->map(Functions::enumToValue())->toArray(),
				'orderType' => $this->orderType->getValue(),
				'limit' => $this->limit,
				'orderBy' => $this->orderBy->getValue()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->statuses = (new ArrayCollection($data['statuses']))->map(function ($string) {return PlayerStatus::_($string);});
		$this->probingStatuses = (new ArrayCollection($data['probingStatuses']))->map(function ($string) {return ProbingStatus::_($string);});
		$this->planetProbingStatuses = (new ArrayCollection($data['planetProbingStatuses']))->map(function ($string) {return PlanetProbingStatus::_($string);});
		$this->orderType = OrderType::_($data['orderType']);
		$this->limit = $data['limit'];
		$this->orderBy = OrderPlanetsBy::_($data['orderBy']);
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	/**
	 * @return \App\Enum\PlayerStatus[]|ArrayCollection
	 */
	public function getStatuses() : ArrayCollection
	{
		return $this->statuses;
	}

	public function getStatusTexts() : array
	{
		return $this->statuses->map(Functions::enumToValue())->toArray();
	}

	public function getProbingStatusTexts() : array
	{
		return $this->probingStatuses->map(Functions::enumToValue())->toArray();
	}

	public function getPlanetProbingStatusTexts() : array
	{
		return $this->planetProbingStatuses->map(Functions::enumToValue())->toArray();
	}

	public function isOrderActive() : bool
	{
		return $this->orderBy->isActive() && $this->orderType->isActive();
	}

	public function getLimit() : int
	{
		return $this->limit;
	}

	public function getOrderType() : OrderType
	{
		return $this->orderType;
	}

	public function getOrderBy() : OrderPlanetsBy
	{
		return $this->orderBy;
	}

	/**
	 * @return ProbingStatus[]|ArrayCollection
	 */
	public function getProbingStatuses() : ArrayCollection
	{
		return $this->probingStatuses;
	}

	/**
	 * @return PlanetProbingStatus[]|ArrayCollection
	 */
	public function getPlanetProbingStatuses() : ArrayCollection
	{
		return $this->planetProbingStatuses;
	}

	public function isEvaluatedForNextRun() : bool
	{
		return false;
	}

}
