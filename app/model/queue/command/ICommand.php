<?php

namespace App\Model\Queue\Command;


interface ICommand extends ArraySerializable
{

	const
		ACTION_UPGRADE = 'upgrade',
		ACTION_BUILD_DEFENSE = 'build defense',
		ACTION_BUILD_FLEET = 'action build fleet'
	;

	public static function getAction() : string;

}