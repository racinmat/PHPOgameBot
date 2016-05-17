<?php

namespace App\Model\Queue\Command;


use App\Enum\Buildable;

interface IBuildCommand extends IEnhanceCommand
{

	public function getAmount() : int;

	public function getBuildable() : Buildable;
	
}