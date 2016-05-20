<?php

namespace App\Model\Game;

use App\Enum\MenuItem;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\SendFleetCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

class FleetManager extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var PlanetManager */
	private $planetManager;

	/** @var Menu */
	private $menu;

	/** @var Logger */
	private $logger;

	/** @var DatabaseManager */
	private $databaseManager;

	public function __construct(\AcceptanceTester $I, PlanetManager $planetManager, Menu $menu, Logger $logger, DatabaseManager $databaseManager)
	{
		$this->I = $I;
		$this->planetManager = $planetManager;
		$this->menu = $menu;
		$this->logger = $logger;
		$this->databaseManager = $databaseManager;
	}

	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof SendFleetCommand;
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		//todo: implement
	}

	public function isProcessingAvailable(Planet $planet, SendFleetCommand $command) : bool
	{
		//todo: implement
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var SendFleetCommand $command */
		$this->sendFleet($command);
	}

	private function sendFleet(SendFleetCommand $command)
	{
		$I = $this->I;

		$to = $command->getTo();

		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);

		if (!$this->isProcessingAvailable($planet, $command)) {
			$this->logger->addDebug('Processing not available.');
			return false;
		}
		$this->logger->addDebug('Processing available, starting to process the command.');
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		foreach ($command->getFleet() as $ship => $count) {
			$I->fillField(Ships::_($ship)->getFleetInputSelector(), $count);
		}
		$I->click('#continue');
		usleep(Random::microseconds(1.5, 2.5));

		$I->fillField('#galaxy', $to->getGalaxy());
		$I->fillField('#system', $to->getSystem());
		$I->fillField('#position', $to->getPlanet());
		$I->click('#continue');
		usleep(Random::microseconds(1.5, 2.5));

		$I->click($command->getMission()->getMissionSelector());
		usleep(Random::microseconds(1.5, 2.5));

		$I->click('#start');
		usleep(Random::microseconds(1.5, 2.5));

		return true;

	}
}
