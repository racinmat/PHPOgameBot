<?php

namespace App\Model\Command;
 
use App\Enum\Building;
use App\Enum\Defense;
use Nette;
 
class BuildDefenseCommand extends Nette\Object implements ICommand
{

	/** @var Defense */
	private $defense;

	/** @var int */
	private $amount;

	protected function __construct(array $data)
	{
		$this->defense = Defense::_($data['defense']);
		$this->amount = $data['amount'];
	}

	public static function getAction() : string
	{
		return static::ACTION_BUILD_DEFENSE;
	}

	public function getDefense() : Building
	{
		return $this->defense;
	}

	public function getAmount() : int
	{
		return $this->amount;
	}

	public static function fromArray(array $data) : UpgradeBuildingCommand
	{
		return new BuildDefenseCommand($data['data']);
	}

	public function toArray() : array
	{
		return [
			'action' => $this->getAction(),
			'data' => [
				'defense' => $this->defense->getValue(),
				'amount' => $this->amount
			]
		];
	}

}