<?php

namespace App\Model\Queue\Command;


interface ICommand extends ArraySerializable
{

	const
		ACTION_UPGRADE = 'upgrade',
		ACTION_BUILD_DEFENSE = 'build defense',
		ACTION_BUILD_SHIPS = 'build ships'
	;

	public static function getAction() : string;

}