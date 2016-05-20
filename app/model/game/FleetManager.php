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
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Carbon\CarbonInterval;
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
		//todo: later add checking for amount of ships in planet from command
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		usleep(Random::microseconds(1.5, 2.5));
		$I->click('#js_eventDetailsClosed');
		$I->waitForText('Události', null, '#eventHeader h2');
		$fleetRows = $I->getNumberOfElements('#eventContent > tbody > tr');
		$minimalTime = Carbon::now()->addYears(666);    //just some big date in the future
		for ($i = 0; $i < $fleetRows; $i++) {
			//I want only returning flights
			if ($I->seeElementExists("#eventContent > tbody > tr:nth-of-type($i)", ['data-return-flight' => 'false'])) {
				continue;
			}

			$timeString = $I->grabTextFrom("#eventContent > tbody > tr:nth-of-type($i) > .countDown.friendly");
			$minimalTime = $minimalTime->min(Carbon::now()->add(OgameParser::parseOgameTimeInterval($timeString)));
		}
		return $minimalTime;
	}

	public function isProcessingAvailable(SendFleetCommand $command) : bool
	{
		//todo: later add checking for amount of ships in planet from command
		$this->menu->goToPage(MenuItem::_(MenuItem::FLEET));
		$fleets = $this->I->grabTextFrom('.fleft .tooltop');
		list($occupied, $total) = OgameParser::parseOgameFleets($fleets);
		return $occupied < $total;
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

		if (!$this->isProcessingAvailable($command)) {
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