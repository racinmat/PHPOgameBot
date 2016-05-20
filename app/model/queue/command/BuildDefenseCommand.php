<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Enum\Enhanceable;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class BuildDefenseCommand extends BaseCommand implements IBuildCommand
{

	/** @var Defense */
	private $defense;

	/** @var int */
	private $amount;

	/** @var bool */
	private $buildStoragesIfNeeded;
	
	public static function getAction() : string
	{
		return static::ACTION_BUILD_DEFENSE;
	}

	public function getBuildable() : Buildable
	{
		return $this->defense;
	}

	public function getAmount() : int
	{
		return $this->amount;
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'defense' => $this->defense->getValue(),
				'amount' => $this->amount,
				'buildStoragesIfNeeded' => $this->buildStoragesIfNeeded
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->defense = Defense::_($data['defense']);
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

}