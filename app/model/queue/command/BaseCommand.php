<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Model\ValueObject\Coordinates;
use Nette;
 
abstract class BaseCommand extends Nette\Object implements ICommand
{

	/** @var Coordinates */
	protected $coordinates;

	public function __construct(Coordinates $coordinates, array $data)
	{
		$this->coordinates = $coordinates;
		$this->loadFromArray($data);
	}

	public function toArray() : array
	{
		return [
			'coordinates' => $this->coordinates->toArray(),
			'action' => $this->getAction()
		];
	}

	public function __toString() : string
	{
		return Nette\Utils\Json::encode($this->toArray());
	}

	public function getCoordinates() : Coordinates
	{
		return $this->coordinates;
	}

	abstract protected function loadFromArray(array $data);
}