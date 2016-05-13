<?php

namespace App\Components;

use App\Model\Queue\Command\ICommand;

interface IDisplayCommandFactory
{

	/**
	 * @return DisplayCommand
	 */
	public function create();
	
}