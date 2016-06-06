<?php

namespace App\Model\Queue\Command;
 
use App\Enum\PlayerStatus;

use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Nette\Utils\Arrays;


class ProbePlayersCommand extends BaseCommand
{

	/** @var PlayerStatus[]|ArrayCollection */
	private $statuses;
	
	public static function getAction() : string
	{
		return static::ACTION_PROBE_PLAYERS;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'statuses' => $this->statuses->map(Functions::enumToValue())->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->statuses = (new ArrayCollection($data['statuses']))->map(function ($string) {return PlayerStatus::_($string);});
	}

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_FLEET;
	}

	/**
	 * @return \App\Enum\PlayerStatus[]|ArrayCollection
	 */
	public function getStatuses()
	{
		return $this->statuses;
	}

	public function getStatusTexts()
	{
		return $this->statuses->map(Functions::enumToValue())->toArray();
	}
}
