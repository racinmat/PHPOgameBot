<?php

namespace App\Model\Queue\Command;
 
use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlayerStatus;

use App\Enum\ProbingStatus;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Nette\Utils\Arrays;


class AttackFarmsCommand extends BaseCommand
{

	/** @var int */
	private $limit;

	/** @var Carbon */
	private $visitedAfter;

	public static function getAction() : string
	{
		return static::ACTION_ATTACK_FARMS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'limit' => $this->limit,
				'visitedAfter' => $this->visitedAfter->__toString()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->limit = $data['limit'];
		$this->visitedAfter = $data['visitedAfter'] ? Carbon::instance(new \DateTime($data['visitedAfter'])) : Carbon::minValue();
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	public function getLimit() : int
	{
		return $this->limit;
	}

	public function getVisitedAfter() : Carbon
	{
		return $this->visitedAfter;
	}

	public function isEvaluatedForNextRun() : bool
	{
		return false;
	}

}
