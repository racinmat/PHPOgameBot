<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Building;
use App\Enum\Research;
use App\Enum\Upgradable;
use Nette;
 
class UpgradeResearchCommand extends Nette\Object implements IUpgradeCommand
{

	/** @var Research */
	private $research;

	public function __construct(Research $research)
	{
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
		return new UpgradeResearchCommand(Research::_($data['research']));
	}

	public function toArray() : array
	{
		return [
			'action' => $this->getAction(),
			'data' => [
				'research' => $this->research->getValue()
			]
		];
	}

}