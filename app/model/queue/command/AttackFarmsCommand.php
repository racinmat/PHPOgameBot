<?php

namespace App\Model\Queue\Command;
 
use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlayerStatus;

use App\Enum\ProbingStatus;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Nette\Utils\Arrays;


class AttackFarmsCommand extends BaseCommand
{

	/** @var int */
	private $limit;

	public static function getAction() : string
	{
		return static::ACTION_PROBE_FARMS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'limit' => $this->limit
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->limit = $data['limit'];
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	public function getLimit() : int
	{
		return $this->limit;
	}

}
