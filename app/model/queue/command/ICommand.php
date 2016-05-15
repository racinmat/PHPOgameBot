<?php

namespace App\Model\Queue\Command;

use App\Model\ValueObject\Coordinates;
use Ramsey\Uuid\Uuid;

interface ICommand extends ArraySerializable
{

	const
		ACTION_UPGRADE_BUILDING = 'upgrade building',
		ACTION_UPGRADE_RESEARCH = 'upgrade research',
		ACTION_BUILD_DEFENSE = 'build defense',
		ACTION_BUILD_SHIPS = 'build ships'
	;

	public static function getAction() : string;

	public function __toString() : string;

	public function getCoordinates() : Coordinates;

	public function getUuid() : Uuid;

	public function equals(ICommand $another) : bool ;

}