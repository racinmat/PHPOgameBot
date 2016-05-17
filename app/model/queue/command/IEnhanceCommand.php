<?php

namespace App\Model\Queue\Command;

use App\Enum\Buildable;
use App\Enum\Enhanceable;
use App\Model\Entity\Planet;
use App\Model\ValueObject\Resources;

interface IEnhanceCommand extends ICommand
{

	const DEFAULT_BUILD_STORAGE_IF_NEEDED = true;

	public function buildStoragesIfNeeded() : bool;

	public function getPrice(Planet $planet) : Resources;

	public function getEnhanceable() : Enhanceable;

}