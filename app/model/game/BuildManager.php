<?php

namespace App\Model\Game;
 


use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;

use App\Model\Queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;

use Kdyby\Monolog\Logger;

class BuildManager extends EnhanceManager implements ICommandProcessor
{
	
	protected function fillAdditionalInfo(IEnhanceCommand $command) {
		/** @var IBuildCommand $command */
		$amount = $command->getAmount();
		$this->I->fillField('#number', $amount);
		usleep(Random::microseconds(1.5, 2));
	}
	
	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IBuildCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		/** @var IBuildCommand $command */
		$datetime1 = $this->getTimeToEnoughResourcesToEnhance($command);
		return $datetime1;
	}

	public function isProcessingAvailable(ICommand $command) : bool
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		//building ships and defense is stackable. No need to check if something is being built right now.
		/** @var IBuildCommand $command */
		return $this->resourcesCalculator->isEnoughResourcesToEnhance($planet, $command);
	}

}
