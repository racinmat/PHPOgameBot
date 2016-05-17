<?php

namespace App\Model\Queue\Command;


use App\Enum\Upgradable;

interface IUpgradeCommand extends IEnhanceCommand
{

	public function getUpgradable() : Upgradable;

}