<?php

namespace App\Model\Game;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\MenuItem;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette;

class BuildManager extends Nette\Object implements ICommandProcessor
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
	 * @param IBuildCommand $command
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function build(IBuildCommand $command) : bool
	{
		$buildable = $command->getBuildable();
		$amount = $command->getAmount();
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		if (!$this->resourcesCalculator->isEnoughResourcesForBuild($planet, $buildable, $amount)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->openMenu($buildable);
		$I = $this->I;
		$I->fillField('#number', $amount);
		$I->wait(1);
		$I->click($buildable->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	protected function openMenu(Buildable $buildable)
	{
		$I = $this->I;
		$this->menu->goToPage($buildable->getMenuLocation());
		$I->click($buildable->getSelector());
		usleep(Random::microseconds(1.5, 2));
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IBuildCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IBuildCommand $command */
		return $this->build($command);
	}
	
	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		/** @var IBuildCommand $command */
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesForBuild($planet, $command->getBuildable(), $command->getAmount());
		$datetime2 = $this->planetManager->getTimeToFinish($command->getBuildable());
		return $datetime1->max($datetime2);
	}

	public function isProcessingAvailable(Planet $planet, IBuildCommand $command) : bool
	{
		//building ships and defense is stackable. No need to check if something is being built right now.
		/** @var IBuildCommand $command */
		return $this->resourcesCalculator->isEnoughResourcesForBuild($planet, $command->getBuildable(), $command->getAmount());
	}

}
