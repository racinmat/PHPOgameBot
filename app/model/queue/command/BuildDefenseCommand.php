<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use Nette;
 
class BuildDefenseCommand extends Nette\Object implements IBuildCommand
{

	/** @var Defense */
	private $defense;

	/** @var int */
	private $amount;

	public function __construct(Defense $defense, $amount)
	{
		$this->defense = $defense;
		$this->amount = $amount;
	}

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

	public static function fromArray(array $data) : BuildDefenseCommand
	{
		return new BuildDefenseCommand(Defense::_($data['defense']), $data['amount']);
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