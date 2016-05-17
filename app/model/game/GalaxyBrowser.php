<?php

namespace App\Model\Game;
 
use App\Enum\MenuItem;
use App\Model\DatabaseManager;
use App\Model\Entity\Player;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use app\model\queue\ICommandProcessor;
use App\Model\ValueObject\Coordinates;
use App\Utils\Functions;
use App\Utils\Random;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverKeys;
use Kdyby\Monolog\Logger;
use Nette\Object;
use Nette\Utils\Strings;

class GalaxyBrowser extends Object implements ICommandProcessor
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var Menu */
	private $menu;

	/** @var PlanetManager */
	private $planetManager;

	/** @var DatabaseManager */
	private $databaseManager;

	/** @var Logger */
	private $logger;

	public function __construct(\AcceptanceTester $I, Menu $menu, PlanetManager $planetManager, DatabaseManager $databaseManager, Logger $logger)
	{
		$this->I = $I;
		$this->menu = $menu;
		$this->planetManager = $planetManager;
		$this->databaseManager = $databaseManager;
		$this->logger = $logger;
	}

	protected function scanGalaxy(ScanGalaxyCommand $command)
	{
		$planet = $this->planetManager->getPlanet($command->getCoordinates());
		$this->menu->goToPlanet($planet);
		$this->menu->goToPage(MenuItem::_(MenuItem::GALAXY));
		$from = $command->getMiddle()->subtract($command->getRange());
		$to = $command->getMiddle()->add($command->getRange());

		$this->logger->addInfo("Going to scan galaxy from system {$from->toString()} to system {$to->toString()}.");
		for ($i = $from; $i->isLesserThanOrEquals($to); $i = $i->nextSystem()) {
			$this->scanSystem($i);
		}
	}

	protected function scanSystem(Coordinates $coordinates)
	{
		$myPlanetsCoordinates = $this->databaseManager->getAllMyPlanetsCoordinates();
		
		$I = $this->I;

		$currentGalaxy = $I->grabValueFrom('#galaxy_input', $coordinates->getGalaxy());
		$currentSystem = $I->grabValueFrom('#system_input', $coordinates->getSystem());
		$isNextSystem = $currentGalaxy === $coordinates->getGalaxy() && ($currentSystem + 1) === $coordinates->getSystem();

		$classToStatus = [
			'status_abbr_noob' => Player::STATUS_NOOB,
			'status_abbr_active' => Player::STATUS_NOOB,
			'status_abbr_honorableTarget' => Player::STATUS_NOOB,
			'status_abbr_vacation' => Player::STATUS_VACATION,
			'status_abbr_inactive' => Player::STATUS_INACTIVE,
			'status_abbr_strong' => Player::STATUS_STRONG,
			'status_abbr_longinactive' => Player::STATUS_LONG_INACTIVE,
			'status_abbr_admin' => Player::STATUS_ADMIN,
			'status_abbr_outlaw' => Player::STATUS_OUTLAW
		];

		if ($isNextSystem) {
			$this->goToNextSystem();
		} else {
			$this->goToSystem($coordinates);
		}

		$planetCount = Coordinates::$maxPlanet;
		for ($i = 1; $i <= $planetCount; $i++) {
			$coordinates = $coordinates->planet($i);
			//check to avoid parsing empty spots
			if ( ! $I->seeElementExists("tbody tr:nth-of-type($i) > td.colonized")) {
				continue;
			}

			//checking my planet, do not wate time with it. Status of my planets is missing
			if ($myPlanetsCoordinates->exists(Functions::equalCoordinates($coordinates))) {
				continue;
			}

			$planetName = $I->grabTextFrom("tbody tr:nth-of-type($i) .planetname");
			$playerName = $I->grabTextFrom("tbody tr:nth-of-type($i) .playername > a > span");
			$playerStatusClass = $I->grabAttributeFrom("tbody tr:nth-of-type($i) .playername > a > span", 'class');
			if ($I->seeElementExists("tbody tr:nth-of-type($i) .allytagwrapper")) {
				$alliance = $I->grabTextFrom("tbody tr:nth-of-type($i) .allytagwrapper");
			} else {
				$alliance = null;
			}
			$hasDebris = ! $I->seeElementExists("tbody tr:nth-of-type($i) .debris.js_no_action");
			$hasMoon = ! $I->seeElementExists("tbody tr:nth-of-type($i) .moon.js_no_action");

			$playerStatus = $classToStatus[$playerStatusClass];
			if ($hasDebris) {
				$I->moveMouseOver("tbody tr:nth-of-type($i) .debris");
				usleep(Random::microseconds(0.5, 1));
				$debrisMetalString = $I->grabTextFrom("#debris$i .ListLinks li:nth-of-type(1)");
				$debrisCrystalString = $I->grabTextFrom("#debris$i .ListLinks li:nth-of-type(2)");
				$debrisMetal = Strings::split($debrisMetalString, '~:~')[1];
				$debrisCrystal = Strings::split($debrisCrystalString, '~:~')[1];
			} else {
				$debrisMetal = 0;
				$debrisCrystal = 0;
			}
			
			$player = $this->databaseManager->getPlayer($playerName);
			if ($player === null) {
				$player = $this->databaseManager->addPlayer($playerName);
			}
			$planet = $this->databaseManager->getPlanet($coordinates);
			if ($planet === null) {
				$planet = $this->databaseManager->addPlanet($coordinates, $player);
			}
			
			$player->setStatus($playerStatus);
			$player->setAlliance($alliance);
			$player->setLastVisited(Carbon::now());

			$planet->setName($planetName);
			$planet->setMoon($hasMoon);
			$planet->setDebrisMetal($debrisMetal);
			$planet->setDebrisCrystal($debrisCrystal);
			$planet->setLastVisited(Carbon::now());

		}
		$this->databaseManager->flush();
	}

	protected function goToNextSystem()
	{
		$I = $this->I;
		$I->pressKey('body', WebDriverKeys::ARROW_RIGHT);
		usleep(Random::microseconds(2.5, 3.5));
	}

	protected function goToSystem(Coordinates $coordinates)
	{
		$I = $this->I;

		$I->fillField('#galaxy_input', $coordinates->getGalaxy());
		$I->fillField('#system_input', $coordinates->getSystem());
		$I->click('#galaxyHeader > form > div:nth-child(9)');
		usleep(Random::microseconds(2.5, 3.5));
	}
	
	public function canProcessCommand(ICommand $command) : bool
	{
		return $command instanceof ScanGalaxyCommand;
	}

	public function processCommand(ICommand $command) : bool
	{
		/** @var ScanGalaxyCommand $command */
		$this->scanGalaxy($command);
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		// TODO: Implement getTimeToProcessingAvailable() method.
	}
}