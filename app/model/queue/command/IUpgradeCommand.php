<?php

namespace App\Model\Queue\Command;


use App\Enum\Upgradable;

interface IUpgradeCommand extends ICommand
{

	public function getUpgradable() : Upgradable;

}