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
use Carbon\Carbon;
use Carbon\CarbonInterval;
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

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
	}

	/**
	 * @param IUpgradeCommand $command
	 * @return bool returns true when building was built, otherwise returns false
	 */
	public function upgrade(IUpgradeCommand $command) : bool
	{
		$upgradable = $command->getUpgradable();
		//možná refreshnout všechna data hned po zalogování. Refreshovat vše včetně levelů budov, výzkumů apod.
		$this->planetManager->refreshResourceData();
		$planet = $this->planetManager->getMyHomePlanet();
		if (!$this->isProcessingAvailable($planet, $command)) {
			return false;
		}
		$this->openMenu($upgradable);
		$I = $this->I;
		$I->click($upgradable->getBuildButtonSelector());
		$I->wait(1);
		return true;
	}

	protected function openMenu(Upgradable $upgradable)
	{
		$I = $this->I;
		$I->click($upgradable->getMenuLocation()->getSelector());
		$I->click($upgradable->getSelector());
		$I->wait(1);
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof IUpgradeCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var IUpgradeCommand $command */
		$this->upgrade($command->getUpgradable());
	}

	public function getTimeToProcessingAvailable(Planet $planet, ICommand $command) : Carbon
	{
		/** @var IUpgradeCommand $command */
		$datetime1 = $this->resourcesCalculator->getTimeToEnoughResourcesForUpgrade($planet, $command->getUpgradable());
		$datetime2 = $this->planetManager->getTimeToFinish($command->getUpgradable());
		return $datetime1->max($datetime2);
	}

	public function isProcessingAvailable(Planet $planet, IUpgradeCommand $command) : bool
	{
		return $this->resourcesCalculator->isEnoughResourcesForUpgrade($planet, $command->getUpgradable()) && ! $this->planetManager->currentlyProcessing($command->getUpgradable());
	}

}
