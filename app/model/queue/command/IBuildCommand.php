<?php

namespace App\Model\Queue\Command;


use App\Enum\Buildable;

interface IBuildCommand extends ICommand
{

	public function getAmount() : int;

	public function getBuildable() : Buildable;
	
}