<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Research;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Resources;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class UpgradeResearchCommand extends BaseCommand implements IUpgradeCommand
{

	/** @var Research */
	private $research;

	/** @var bool */
	private $buildStoragesIfNeeded;

	public function __construct(Coordinates $coordinates, array $data, Uuid $uuid = null)
	{
		parent::__construct($coordinates, $data, $uuid);
	}

	public static function getAction() : string
	{
		return static::ACTION_UPGRADE_RESEARCH;
	}

	public function getUpgradable() : Upgradable
	{
		return $this->research;
	}

	public static function fromArray(array $data) : UpgradeResearchCommand
	{
		return new UpgradeResearchCommand(Coordinates::fromArray($data['coordinates']), $data['data'], isset($data['uuid']) ? Uuid::fromString($data['uuid']) : null);
	}

	public function toArray() : array
	{
		$data = [
			'action' => $this->getAction(),
			'data' => [
				'research' => $this->research->getValue(),
				'buildStoragesIfNeeded' => $this->buildStoragesIfNeeded
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->research = Research::_($data['research']);
		$this->buildStoragesIfNeeded = isset($data['buildStoragesIfNeeded']) ? $data['buildStoragesIfNeeded'] : IEnhanceCommand::DEFAULT_BUILD_STORAGE_IF_NEEDED;
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

}