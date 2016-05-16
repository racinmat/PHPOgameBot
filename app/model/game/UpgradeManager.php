<?php

namespace App\Model\Game;
 
use App\Enum\Building;
use App\Enum\MenuItem;
use App\Enum\Upgradable;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IUpgradeCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Strings;

class UpgradeManager extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	protected $I;

	/** @var ResourcesCalculator */
	protected $resourcesCalculator;

	/** @var PlanetManager */
	protected $planetManager;

	/** @var Menu */
	protected $menu;

	/** @var Logger */
	private $logger;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, Menu $menu, Logger $logger)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->menu = $menu;
		$this->logger = $logger;
	}

	/**
	 * @param IUpgradeCommand $command
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function upgrade(IUpgradeCommand $command) : bool
	{
		$upgradable = $command->getUpgradable();
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		if (!$this->isProcessingAvailable($planet, $command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->openMenu($upgradable);
		$I = $this->I;
		$I->click($upgradable->getBuildButtonSelector());
		usleep(Random::microseconds(2, 2.5));
		return true;
	}

	protected function openMenu(Upgradable $upgradable)
	{
		$I = $this->I;
		$this->menu->goToPage($upgradable->getMenuLocation());
		$I->click($upgradable->getSelector());
		usleep(Random::microseconds(1.5, 2));
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IUpgradeCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IUpgradeCommand $command */
		return $this->upgrade($command);
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		/** @var IUpgradeCommand $command */
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesForUpgrade($planet, $command->getUpgradable());
		$datetime2 = $this->planetManager->getTimeToFinish($command->getUpgradable());
		return $datetime1->max($datetime2);
	}

	public function isProcessingAvailable(Planet $planet, IUpgradeCommand $command) : bool
	{
		$this->menu->goToPlanet($planet);
		$currentlyProcessing = $this->planetManager->currentlyProcessing($command->getUpgradable());
		$enoughResources = $this->resourcesCalculator->isEnoughResourcesForUpgrade($planet, $command->getUpgradable());
		return $enoughResources && ! $currentlyProcessing;
	}

}
