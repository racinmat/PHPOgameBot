<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Enhanceable;
use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
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
		$this->buildStoragesIfNeeded = $data['buildStoragesIfNeeded'] ?? IEnhanceCommand::DEFAULT_BUILD_STORAGE_IF_NEEDED;
	}

	public function getDependencyType() : string
	{
		return $this->coordinates->toString() . self::DEPENDENCY_RESOURCES;
	}

	public function buildStoragesIfNeeded() : bool
	{
		return $this->buildStoragesIfNeeded;
	}

	public function getPrice(Planet $planet) : Resources
	{
		return $this->getBuildable()->getPrice()->multiplyByScalar($this->amount);
	}

	public function getEnhanceable() : Enhanceable
	{
		return $this->getBuildable();
	}

	public function getInfo(Planet $planet) : string
	{
		return "Build ships, $this->amount of $this->ships.";
	}

}