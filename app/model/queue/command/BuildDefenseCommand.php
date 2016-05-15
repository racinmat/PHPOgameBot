<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Model\ValueObject\Coordinates;
use Nette\Utils\Arrays;

class BuildDefenseCommand extends BaseCommand implements IBuildCommand
{

	/** @var Defense */
	private $defense;

	/** @var int */
	private $amount;

	public function __construct(Coordinates $coordinates, Defense $defense, $amount)
	{
		parent::__construct($coordinates);
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
		return new BuildDefenseCommand(Coordinates::fromArray($data['coordinates']), Defense::_($data['defense']), $data['amount']);
	}

	public function toArray() : array
	{
		$data = [
			'data' => [
				'defense' => $this->defense->getValue(),
				'amount' => $this->amount
			]
		];
		return Arrays::mergeTree($data, parent::toArray());
	}

}