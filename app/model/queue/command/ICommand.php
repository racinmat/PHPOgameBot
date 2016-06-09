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
		ACTION_BUILD_SHIPS = 'build ships',
		ACTION_SCAN_GALAXY = 'scan galaxy',
		ACTION_PROBE_PLAYERS = 'probe players',
		ACTION_PROBE_FARMS = 'probe farms',
		ACTION_ATTACK_FARMS = 'attack farms',
		ACTION_SEND_FLEET = 'send fleet'
	;

	public static function getAction() : string;

	public function __toString() : string;

	public function toString() : string;

	public function getCoordinates() : Coordinates;

	public function getUuid() : Uuid;

	public function equals(ICommand $another) : bool ;

	/**
	 * Every command has its dependency which it needs to be compoleted.
	 * This method returns string unique to dependency.
	 * When two commands return same string, they has same dependency.
	 * For example, planet coordinates and 'resources' is dependency.
	 * Or 'fleet' is dependency.
	 * @return string
	 */
	public function getDependencyType() : string;

}