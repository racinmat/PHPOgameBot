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
use Tracy\Debugger;


class ProbeFarmsCommand extends BaseCommand
{

	/** @var int */
	private $limit;

	/** @var CarbonInterval interval from now (from time of execution, very handy for repetitive command) */
	private $visitedBefore;

	public static function getAction() : string
	{
		return static::ACTION_PROBE_FARMS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'limit' => $this->limit,
				'visitedBefore' => $this->visitedBefore->__toString()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->limit = $data['limit'];
		$this->visitedBefore = CarbonInterval::instance(\DateInterval::createFromDateString($data['visitedBefore'] ?? 'now'));
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	public function getLimit() : int
	{
		return $this->limit;
	}

	public function getVisitedBefore() : CarbonInterval
	{
		return $this->visitedBefore;
	}

	public function isEvaluatedForNextRun() : bool
	{
		return false;
	}

}
