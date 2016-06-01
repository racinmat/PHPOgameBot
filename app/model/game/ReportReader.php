<?php

namespace App\Model\Game;
 
use App\Enum\Buildable;
use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\MenuItem;
use App\Enum\Research;
use App\Enum\Ships;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Model\Queue\Command\IBuildCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use app\model\queue\ICommandPreProcessor;
use app\model\queue\ICommandProcessor;
use App\Model\ResourcesCalculator;
use App\Utils\OgameParser;
use App\Utils\Random;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
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

		$firstReportDetailsSelector = 'ul.tab_inner li:nth-of-type(1).msg a.fright.txt_link.msg_action_link.overlay';
		$I->waitForElementVisible($firstReportDetailsSelector);
		$I->click($firstReportDetailsSelector);
		usleep(Random::microseconds(1.5, 2.5));
		$I->waitForText('Podrobnosti', null, '.ui-dialog-title');
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
		$enoughInformation = true;

		$I = $this->I;
		$coordinatesText = $I->grabTextFrom($this->reportPopupSelector . ' .msg_title a.txt_link');
		$coordinates = OgameParser::parseOgameCoordinates($coordinatesText);

		$planet = $this->databaseManager->getPlanet($coordinates);

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
		$planet->setLastVisited(Carbon::now());

		//todo: přidat do entity planeta informace o letce a obraně

		if ($I->seeElementExists($buildingsSelector . ' li.detail_list_fail')) {
			$enoughInformation = false;
		} else {
			$buildingsCount = $I->getNumberOfElements($buildingsSelector . ' li');
			for ($i = 1; $i <= $buildingsCount; $i++) {
				$name = $I->grabTextFrom($buildingsSelector . " li:nth-of-type($i) > span.detail_list_txt");
				$level = $I->grabTextFrom($buildingsSelector . " li:nth-of-type($i) > span.fright");

				$building = Building::_(Building::getFromTranslatedName($name));
				$building->setCurrentLevel($planet, $level);
			}
		}

		if ($I->seeElementExists($researchSelector . ' li.detail_list_fail')) {
			$enoughInformation = false;
		} else {
			$researchCount = $I->getNumberOfElements($researchSelector . ' li');
			for ($i = 1; $i <= $researchCount; $i++) {
				$name = $I->grabTextFrom($researchSelector . " li:nth-of-type($i) > span.detail_list_txt");
				$level = $I->grabTextFrom($researchSelector . " li:nth-of-type($i) > span.fright");

				$research = Research::_(Research::getFromTranslatedName($name));
				$research->setCurrentLevel($planet, $level);
			}
		}

		if ($I->seeElementExists($researchSelector . ' li.detail_list_fail')) {
			$enoughInformation = false;
		} else {
			$defenseCount = $I->getNumberOfElements($defenseSelector . ' li');
			for ($i = 1; $i <= $defenseCount; $i++) {
				$name = $I->grabTextFrom($defenseSelector . " li:nth-of-type($i) > span.detail_list_txt");
				$level = $I->grabTextFrom($defenseSelector . " li:nth-of-type($i) > span.fright");

				$defense = Defense::_(Defense::getFromTranslatedName($name));
				$defense->setAmount($planet, $level);
			}
		}

		if ($I->seeElementExists($researchSelector . ' li.detail_list_fail')) {
			$enoughInformation = false;
		} else {
			$fleetCount = $I->getNumberOfElements($fleetSelector . ' li');
			for ($i = 1; $i <= $fleetCount; $i++) {
				$name = $I->grabTextFrom($fleetSelector . " li:nth-of-type($i) > span.detail_list_txt");
				$level = $I->grabTextFrom($fleetSelector . " li:nth-of-type($i) > span.fright");

				$ships = Ships::_(Ships::getFromTranslatedName($name));
				$ships->setAmount($planet, $level);
			}
		}

		$planet->setGotAllInformationFromLastEspionage($enoughInformation);

		$this->databaseManager->flush();
	}
}
