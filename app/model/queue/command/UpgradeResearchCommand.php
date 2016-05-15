<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Research;
use App\Enum\Upgradable;
use App\Model\ValueObject\Coordinates;
use Nette\Utils\Arrays;

class UpgradeResearchCommand extends BaseCommand implements IUpgradeCommand
{

	/** @var Research */
	private $research;

	public function __construct(Coordinates $coordinates, Research $research)
	{
		parent::__construct($coordinates);
		$this->research = $research;
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
		return new UpgradeResearchCommand(Coordinates::fromArray($data['coordinates']), Research::_($data['research']));
	}

	public function toArray() : array
	{
		$data = [
			'action' => $this->getAction(),
			'data' => [
				'research' => $this->research->getValue()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

}