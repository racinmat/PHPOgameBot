<?php

namespace App\Model\Queue\Command;
 


use App\Model\ValueObject\Coordinates;

use Nette\Utils\Arrays;


class ScanGalaxyCommand extends BaseCommand
{

	/** @var Coordinates */
	private $from;

	/** @var Coordinates */
	private $to;

	public static function getAction() : string
	{
		return static::ACTION_SCAN_GALAXY;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'from' => $this->from->toArray(),
				'to' => $this->to->toArray()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->from = Coordinates::fromArray($data['from']);
		$this->to = Coordinates::fromArray($data['to']);
	}

	public function setFrom(Coordinates $from)
	{
		$this->from = $from;
	}

	public function setTo(Coordinates $to)
	{
		$this->to = $to;
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

	public function getDependencyType() : string
	{
		return self::DEPENDENCY_NOTHING;
	}

}
