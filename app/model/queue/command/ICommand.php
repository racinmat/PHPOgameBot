<?php

namespace App\Model\Queue\Command;


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

}