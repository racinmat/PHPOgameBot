<?php

namespace App\Model\Queue\Command;


interface IBuildCommand extends ICommand
{

	public function getAmount() : int;

}