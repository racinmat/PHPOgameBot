<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Ships;
use App\Model\ValueObject\Coordinates;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class BuildShipsCommand extends BaseCommand implements IBuildCommand
{

	/** @var Ships */
	private $ships;

	/** @var int */
	private $amount;

	/** @var bool */
	private $buildStoragesIfNeeded;

	public function __construct(Coordinates $coordinates, array $data, Uuid $uuid = null)
	{
		parent::__construct($coordinates, $data, $uuid);
	}

	public static function getAction() : string
	{
		return static::ACTION_BUILD_SHIPS;
	}

	public function getBuildable() : Buildable
	{
		return $this->ships;
	}

	public function getAmount() : int
	{
		return $this->amount;
	}

	public static function fromArray(array $data) : BuildShipsCommand
	{
		return new BuildShipsCommand(Coordinates::fromArray($data['coordinates']), $data['data'], isset($data['uuid']) ? Uuid::fromString($data['uuid']) : null);
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'ships' => $this->ships->getValue(),
				'amount' => $this->amount,
				'buildStoragesIfNeeded' => $this->buildStoragesIfNeeded
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->ships = Ships::_($data['ships']);
		$this->amount = $data['amount'];
		$this->buildStoragesIfNeeded = $data['buildStoragesIfNeeded'];
	}

	public function getDependencyType() : string
	{
		return $this->coordinates->toString() . self::DEPENDENCY_RESOURCES;
	}

	public function buildStoragesIfNeeded() : bool
	{
		return $this->buildStoragesIfNeeded;
	}

}