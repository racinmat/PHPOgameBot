<?php

namespace App\Model\Game;

use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\MenuItem;
use App\Enum\PlanetProbingStatus;
use App\Enum\ProbingStatus;
use App\Enum\Research;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\Object;

class ReportReader extends Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var Logger */
	private $logger;

	/** @var Menu */
	private $menu;

	/** @var DatabaseManager */
	private $databaseManager;

	private $reportPopupSelector = '.ui-dialog.ui-widget';

	public function __construct(\AcceptanceTester $I, Logger $logger, Menu $menu, DatabaseManager $databaseManager)
	{
		$this->I = $I;
		$this->logger = $logger;
		$this->menu = $menu;
		$this->databaseManager = $databaseManager;
	}

	public function readEspionageReportsFrom(Carbon $from)
	{
		$I = $this->I;
		$this->menu->goToPage(MenuItem::_(MenuItem::OVERVIEW));
		usleep(Random::microseconds(1, 2));
		$I->click('a.comm_menu.messages');
		usleep(Random::microseconds(1, 2));

		$firstReportDetailsSelector = 'ul.tab_inner li.msg a.fright.txt_link.msg_action_link.overlay';  //there does not have to be nth-of-type(1), because the webDriver clicks only on the first occurrence, and that is what we want.
		$I->waitForElementVisible($firstReportDetailsSelector);
		$I->click($firstReportDetailsSelector);
		usleep(Random::microseconds(1.5, 2.5));
		$I->waitForText('Podrobnosti', null, '.ui-dialog-title');
		$I->waitForElementVisible("$this->reportPopupSelector li.p_li.active > a.fright.txt_link.msg_action_link.active");
		$reports = $I->grabTextFrom("$this->reportPopupSelector li.p_li.active > a.fright.txt_link.msg_action_link.active");
		list($currentReport, $reportsCount) = OgameParser::parseSlash($reports);
		if ($currentReport != 1) {
			$I->click("$this->reportPopupSelector .pagination > li:nth-of-type(1)");    //go to first report
			usleep(Random::microseconds(1.5, 2.5));
			$reports = $I->grabTextFrom($this->reportPopupSelector . ' li.p_li.active > a.fright.txt_link.msg_action_link.active');
			list($currentReport, $reportsCount) = OgameParser::parseSlash($reports);
			if ($currentReport != 1) {
				$this->logger->addWarning('Not at the first espionage report.');
			}
		}
		$this->logger->addInfo("Going to read max $reportsCount logs to date $from.");
		for ($i = 1; $i < $reportsCount; $i++) {
			//check report time
			$reportTimeString = $I->grabTextFrom("$this->reportPopupSelector .msg_date.fright");
			$reportTime = Carbon::instance(new \DateTime($reportTimeString));
			if ($reportTime->lt($from)) {
				break;
			}

			$this->readCurrentEspionageReport();
			$I->click("$this->reportPopupSelector .pagination > li:nth-of-type(4) a");
			usleep(Random::microseconds(1, 1.5));
		}

		$this->logger->addInfo("Done reading reports.");

		//close the last opened report
		$I->click("$this->reportPopupSelector button.ui-dialog-titlebar-close");
	}

	private function readCurrentEspionageReport()
	{
		$I = $this->I;
		if ( ! $I->seeExists('Špionážní zpráva z planety', '.detail_msg .msg_title.new.blue_txt')) {
			return;     //when espionage report is not opened, do not try to parse it
		}

		$reportTimeString = $I->grabTextFrom("$this->reportPopupSelector .msg_date.fright");
		$reportTime = Carbon::instance(new \DateTime($reportTimeString));

		$probingStatus = ProbingStatus::_(ProbingStatus::GOT_ALL_INFORMATION);

		$coordinatesText = $I->grabTextFrom($this->reportPopupSelector . ' .msg_title a.txt_link');
		$coordinates = OgameParser::parseOgameCoordinates($coordinatesText);

		$planet = $this->databaseManager->getPlanet($coordinates);
		$this->logger->addDebug("Going to parse report for planet {$planet->getCoordinates()->toString()}.");

		$resourcesSelector = $this->reportPopupSelector . ' div.mCSB_container > ul:nth-of-type(1)';
		$fleetSelector = $this->reportPopupSelector . ' div.mCSB_container > ul:nth-of-type(2)';
		$defenseSelector = $this->reportPopupSelector . ' div.mCSB_container > ul:nth-of-type(3)';
		$buildingsSelector = $this->reportPopupSelector . ' div.mCSB_container > ul:nth-of-type(4)';
		$researchSelector = $this->reportPopupSelector . ' div.mCSB_container > ul:nth-of-type(5)';

		$metal = $I->grabTextFrom($resourcesSelector . ' > li:nth-of-type(1) > .res_value');
		$crystal = $I->grabTextFrom($resourcesSelector . ' > li:nth-of-type(2) > .res_value');
		$deuterium = $I->grabTextFrom($resourcesSelector . ' > li:nth-of-type(3) > .res_value');
		$energy = $I->grabTextFrom($resourcesSelector . ' > li:nth-of-type(4) > .res_value');

		$metal = OgameParser::parseResources($metal);
		$crystal = OgameParser::parseResources($crystal);
		$deuterium = OgameParser::parseResources($deuterium);
		$energy = OgameParser::parseResources($energy);

		$planet->setMetal($metal);
		$planet->setCrystal($crystal);
		$planet->setDeuterium($deuterium);
		$planet->setLastVisited($reportTime);

		$this->readSection($buildingsSelector,
			function () use (&$probingStatus, &$planetProbingStatus) {
				$probingStatus = $probingStatus->min(ProbingStatus::_(ProbingStatus::MISSING_BUILDINGS));
			},
			function (string $name, string $value) use ($planet) {
				$ships = Building::_(Building::getFromTranslatedName($name));
				$ships->setCurrentLevel($planet, $value);
			}
		);

		$this->readSection($researchSelector,
			function () use (&$probingStatus, &$planetProbingStatus) {
				$probingStatus = $probingStatus->min(ProbingStatus::_(ProbingStatus::MISSING_RESEARCH));
			},
			function (string $name, string $value) use ($planet) {
				$ships = Research::_(Research::getFromTranslatedName($name));
				$ships->setCurrentLevel($planet, $value);
			}
		);

		$this->readSection($defenseSelector,
			function () use (&$probingStatus, &$planetProbingStatus) {
				$probingStatus = $probingStatus->min(ProbingStatus::_(ProbingStatus::MISSING_DEFENSE));
			},
			function (string $name, string $value) use ($planet) {
				$ships = Defense::_(Defense::getFromTranslatedName($name));
				$ships->setAmount($planet, $value);
			}
		);

		$this->readSection($fleetSelector,
			function () use (&$probingStatus, &$planetProbingStatus) {
				$probingStatus = $probingStatus->min(ProbingStatus::_(ProbingStatus::MISSING_FLEET));
			},
			function (string $name, string $value) use ($planet) {
				$ships = Ships::_(Ships::getFromTranslatedName($name));
				$ships->setAmount($planet, $value);
			}
		);

		if ($probingStatus->missingAnyInformation()) {
			$planetProbingStatus = PlanetProbingStatus::_(PlanetProbingStatus::DID_NOT_GET_ALL_INFORMATION);
		} else {
			$planetProbingStatus = PlanetProbingStatus::_(PlanetProbingStatus::GOT_ALL_INFORMATION);
		}

		$this->logger->addDebug("Done parsing report for planet {$planet->getCoordinates()->toString()}. Probing status is $probingStatus.");
		$planet->getPlayer()->setProbingStatus($probingStatus);
		$planet->setProbingStatus($planetProbingStatus);

		$this->databaseManager->flush();
	}

	private function readSection(string $selector, callable $unsuccessful, callable $setValue)
	{
		$I = $this->I;
		$I->waitForElementVisible($selector);
		if ($I->seeElementExists($selector . ' li.detail_list_fail')) {
			$unsuccessful();
			throw new MissingSectionException();
		} else {
			$count = $I->getNumberOfElements($selector . ' li');
			for ($i = 1; $i <= $count; $i++) {
				$nameSelector = $selector . " li:nth-of-type($i) > span.detail_list_txt";
				$levelSelector = $selector . " li:nth-of-type($i) > span.fright";
				$name = $I->grabTextFrom($nameSelector);    //pokud je element mimo obrazovku, vrátí se prázdný string
				if ($name == '') {
					$I->click($nameSelector);      //click vyvolá scrollnutí na element, je to nejjednodušší způsob, jak scrollnout
					$name = $I->grabTextFrom($nameSelector);
				}
				$level = $I->grabTextFrom($levelSelector);
				$setValue($name, $level);
			}
		}

	}
}

class MissingSectionException extends \Exception {};