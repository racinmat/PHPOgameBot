<?php

namespace App\Model\Game;
 
use App\Model\Entity\Planet;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\IUpgradeCommand;
use app\model\queue\ICommandPreProcessor;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Kdyby\Monolog\Logger;

class UpgradeManager extends EnhanceManager implements ICommandProcessor
{

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, Menu $menu, Logger $logger)
	{
		parent::__construct($I, $planetManager, $resourcesCalculator, $menu, $logger);
	}

	protected function fillAdditionalInfo(IEnhanceCommand $command) {
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IUpgradeCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		/** @var IUpgradeCommand $command */
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesToEnhance($planet, $command);
		$datetime2 = $this->planetManager->getTimeToFinish($command->getUpgradable());
		return $datetime1->max($datetime2);
	}

	public function isProcessingAvailable(Planet $planet, IEnhanceCommand $command) : bool
	{
		$this->menu->goToPlanet($planet);
		$currentlyProcessing = $this->planetManager->currentlyProcessing($command->getEnhanceable());
		$enoughResources = $this->resourcesCalculator->isEnoughResourcesToEnhance($planet, $command);
		return $enoughResources && ! $currentlyProcessing;
	}

}
