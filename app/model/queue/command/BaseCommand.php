<?php

namespace App\Model\Queue\Command;
 
use App\Enum\Buildable;
use App\Enum\Defense;
use App\Model\ValueObject\Coordinates;
use Nette;
use Ramsey\Uuid\Uuid;

abstract class BaseCommand extends Nette\Object implements ICommand
{

	/** @var Coordinates */
	protected $coordinates;

	/** @var Uuid */
	protected $uuid;

	public function __construct(Coordinates $coordinates, array $data, Uuid $uuid = null)
	{
		$this->coordinates = $coordinates;
		if ($uuid) {
			$this->uuid = $uuid;
		} else {
			$this->uuid = Uuid::uuid4();
		}
		$this->loadFromArray($data);
	}

	public function toArray() : array
	{
		return [
			'coordinates' => $this->coordinates->toArray(),
			'uuid' => $this->getUuid()->toString(),
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

	public function getUuid() : Uuid
	{
		return $this->uuid;
	}

	public function equals(ICommand $another) : bool 
	{
		return $this->getUuid()->equals($another->getUuid());
	}
}