<?php

namespace App\Model\Game;

use App\Enum\Enhanceable;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\ICommandProcessor;
use App\Model\Queue\QueueManager;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
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

		if (!$this->isProcessingAvailable($planet, $command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->openMenu($enhanceable);
		if ( ! $this->I->seeElementExists($enhanceable->getBuildButtonSelector())) {
			$this->logger->addWarning('Processing is available, but enhance button can not be clicked. Failing to process the command.');
			return false;
		}
		$this->fillAdditionalInfo($command);
		$this->I->click($enhanceable->getBuildButtonSelector());
		usleep(Random::microseconds(2, 2.5));
		return true;
	}

	abstract protected function fillAdditionalInfo(IEnhanceCommand $command);

	abstract public function isProcessingAvailable(Planet $planet, IEnhanceCommand $command) : bool;

	protected function openMenu(Enhanceable $enhanceable)
	{
		$I = $this->I;
		$this->menu->goToPage($enhanceable->getMenuLocation());
		$I->click($enhanceable->getSelector());
		usleep(Random::microseconds(1.5, 2));
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IEnhanceCommand $command */
		return $this->enhance($command);
	}

}
