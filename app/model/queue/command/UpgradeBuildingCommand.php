<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Enhanceable;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class UpgradeBuildingCommand extends BaseCommand implements IUpgradeCommand
{

	/** @var Building */
	private $building;

	/** @var bool */
	private $buildStoragesIfNeeded;

	public static function getAction() : string
	{
		return static::ACTION_UPGRADE_BUILDING;
	}

	public function getUpgradable() : Upgradable
	{
		return $this->building;
	}

	public function toArray() : array
	{
		$data = [
			'action' => $this->getAction(),
			'data' => [
				'building' => $this->building->getValue(),
				'buildStoragesIfNeeded' => $this->buildStoragesIfNeeded
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->building = Building::_($data['building']);
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
		$currentLevel = $this->getUpgradable()->getCurrentLevel($planet);
		return $this->getUpgradable()->getPriceToNextLevel($currentLevel);
	}

	public function getEnhanceable() : Enhanceable
	{
		return $this->getUpgradable();
	}

	public function getInfo(Planet $planet) : string
	{
		return "Upgrade building $this->building from current level {$this->building->getCurrentLevel($planet)}.";
	}
	
}