<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Research;
use App\Enum\Upgradable;
use App\Model\ValueObject\Coordinates;
use Nette\Utils\Arrays;
use Ramsey\Uuid\Uuid;

class UpgradeResearchCommand extends BaseCommand implements IUpgradeCommand
{

	/** @var Research */
	private $research;

	public function __construct(Coordinates $coordinates, array $data)
	{
		parent::__construct($coordinates, $data);
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
				'research' => $this->research->getValue()
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

	protected function loadFromArray(array $data)
	{
		$this->research = Research::_($data['research']);
	}

}