<?php
/**
 * Created by PhpStorm.
 * User: Azathoth
 * Date: 12. 5. 2016
 * Time: 16:48
 */

namespace app\model\queue;

use App\Model\Entity\Planet;
use App\Model\Queue\Command\ICommand;
use Carbon\Carbon;

interface ICommandProcessor
{

	public function canProcessCommand(ICommand $command) : bool;

	public function processCommand(ICommand $command) : bool;

	public function getTimeToProcessingAvailable(Planet $planet, ICommand $command) : Carbon;

}