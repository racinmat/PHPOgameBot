<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Upgradable;
use Nette;
 
class UpgradeBuildingCommand extends Nette\Object implements IUpgradeCommand
{

	/** @var Building */
	private $building;

	public function __construct(Building $building)
	{
		$this->building = $building;
	}

	public static function getAction() : string
	{
		return static::ACTION_UPGRADE_BUILDING;
	}

	public function getUpgradable() : Upgradable
	{
		return $this->building;
	}

	public static function fromArray(array $data) : UpgradeBuildingCommand
	{
		return new UpgradeBuildingCommand(Building::_($data['building']));
	}

	public function toArray() : array
	{
		return [
			'action' => $this->getAction(),
			'data' => [
				'building' => $this->building->getValue()
			]
		];
	}

}