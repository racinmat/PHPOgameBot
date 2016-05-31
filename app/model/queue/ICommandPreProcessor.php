<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 12. 5. 2016
 * Time: 16:48
 */

namespace App\Model\Queue;

use App\Model\Queue\Command\ICommand;
use App\Utils\ArrayCollection;

interface ICommandPreProcessor
{

	public function canPreProcessCommand(ICommand $command) : bool;

	public function preProcessCommand(ICommand $command, ArrayCollection $queue) : bool;

}