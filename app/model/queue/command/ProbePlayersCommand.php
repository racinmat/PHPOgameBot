<?php

namespace App\Model\Queue\Command;
 
use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlayerStatus;

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

	public static function getAction() : string
	{
		return static::ACTION_PROBE_PLAYERS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'statuses' => $this->statuses->map(Functions::enumToValue())->toArray(),
				'orderType' => $this->orderType,
				'limit' => $this->limit,
				'orderBy' => $this->orderBy
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->statuses = (new ArrayCollection($data['statuses']))->map(function ($string) {return PlayerStatus::_($string);});
		$this->orderType = $data['orderType'];
		$this->limit = $data['limit'];
		$this->orderBy = $data['orderBy'];
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

}
