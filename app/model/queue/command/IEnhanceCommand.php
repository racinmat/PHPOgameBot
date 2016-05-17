<?php

namespace App\Model\Queue\Command;


use App\Enum\Buildable;

interface IEnhanceCommand extends ICommand
{

	public function buildStoragesIfNeeded() : bool;

}