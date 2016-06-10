<?php

namespace App\Model\Queue\Command;
 
use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlayerStatus;

use App\Enum\ProbingStatus;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Nette\Utils\Arrays;


class AttackFarmsCommand extends BaseCommand
{

	/** @var int */
	private $limit;

	/** @var CarbonInterval interval from now (from time of execution, very handy for repetitive command) */
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
		$this->visitedAfter = CarbonInterval::instance(\DateInterval::createFromDateString($data['visitedAfter'] ?? 'now'));
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	public function getLimit() : int
	{
		return $this->limit;
	}

	public function getVisitedAfter() : CarbonInterval
	{
		return $this->visitedAfter;
	}

	public function isEvaluatedForNextRun() : bool
	{
		return false;
	}

}
