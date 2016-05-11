<?php

namespace App\Model\Command;
 
use App\Enum\Building;
use Nette;
 
class UpgradeBuildingCommand extends Nette\Object implements ICommand
{

	/** @var Building */
	private $building;

	protected function __construct(array $data)
	{
		$this->building = Building::_($data['building']);
	}

	public static function getAction() : string
	{
		return static::ACTION_UPGRADE;
	}

	public function getBuilding() : Building
	{
		return $this->building;
	}

	public static function fromArray(array $data) : UpgradeBuildingCommand
	{
		return new UpgradeBuildingCommand($data);
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