<?php

namespace App\Model\Queue\Command;


use App\Enum\Buildable;

interface IEnhanceCommand extends ICommand
{

	const DEFAULT_BUILD_STORAGE_IF_NEEDED = true;

	public function buildStoragesIfNeeded() : bool;

}