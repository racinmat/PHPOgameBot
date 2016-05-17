<?php

namespace App\Model\Game;
 
use App\Enum\Buildable;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette;

class BuildManager extends EnhanceManager implements ICommandProcessor
{

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, Menu $menu, Logger $logger)
	{
		parent::__construct($I, $planetManager, $resourcesCalculator, $menu, $logger);
	}

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
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		/** @var IBuildCommand $command */
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesToEnhance($planet, $command);
		return $datetime1;
	}

	public function isProcessingAvailable(Planet $planet, IEnhanceCommand $command) : bool
	{
		//building ships and defense is stackable. No need to check if something is being built right now.
		/** @var IBuildCommand $command */
		return $this->resourcesCalculator->isEnoughResourcesToEnhance($planet, $command);
	}

}
