<?php

namespace App\Model\Game;
 
use App\Enum\Buildable;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use app\model\queue\ICommandProcessor;
use App\Model\Queue\QueueManager;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

abstract class EnhanceManager extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	protected $I;

	/** @var PlanetManager */
	protected $planetManager;
	
	/** @var ResourcesCalculator */
	protected $resourcesCalculator;

	/** @var Menu */
	protected $menu;

	/** @var Logger */
	protected $logger;

	/** @var QueueManager */
	protected $queueManager;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, Menu $menu, Logger $logger)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->menu = $menu;
		$this->logger = $logger;
	}

	/**
	 * @param IEnhanceCommand $command
	 * @return bool returns true when building was built, otherwise returns false
	 */
	protected function enhance(IEnhanceCommand $command) : bool
	{
		$enhanceable = $command->getEnhanceable();
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);

//		if ($command->buildStoragesIfNeeded() && $this->resourcesCalculator->isNeedToUpgradeStoragesToHaveResources($planet, $command->getPrice($planet))) {
//			$neededStoragesLevels = $this->resourcesCalculator->getMinimalStorageLevelsForResources($command->getPrice($planet));
//			$metalStorageLevel = $planet->getMetalStorageLevel();
//			$crystalStorageLevel = $planet->getCrystalStorageLevel();
//			$deueriumTankLevel = $planet->getDeuteriumTankLevel();
//
//		}

		if (!$this->isProcessingAvailable($planet, $command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->openMenu($enhanceable);
		$this->fillAdditionalInfo($command);
		$this->I->click($enhanceable->getBuildButtonSelector());
		usleep(Random::microseconds(2, 2.5));
		return true;
	}

	abstract protected function fillAdditionalInfo(IEnhanceCommand $command);

	abstract public function isProcessingAvailable(Planet $planet, IEnhanceCommand $command) : bool;

	protected function openMenu(Buildable $buildable)
	{
		$I = $this->I;
		$this->menu->goToPage($buildable->getMenuLocation());
		$I->click($buildable->getSelector());
		usleep(Random::microseconds(1.5, 2));
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IEnhanceCommand $command */
		return $this->enhance($command);
	}

}
