<?php

namespace App\Presenters;

use App\Components\IDisplayCommandFactory;


use App\Model\DatabaseManager;
use App\Model\PlanetCalculator;
use App\Model\Queue\QueueFileRepository;

use Carbon\Carbon;

use Nette\Utils\Strings;


class DashboardPresenter extends BasePresenter
{

	/** @var string */
	private $cronFile;

	/**
	 * @var QueueFileRepository
	 * @inject
	 */
	public $queueRepository;

	/**
	 * @var IDisplayCommandFactory
	 * @inject
	 */
	public $displayCommandFactory;

	/**
	 * @var DatabaseManager
	 * @inject
	 */
	public $databaseManager;

	/**
	 * @var PlanetCalculator
	 * @inject
	 */
	public $planetCalculator;

	public function renderDefault()
	{
		$cronTime = file_get_contents($this->cronFile);
		$isEmpty = ctype_space($cronTime) || Strings::length($cronTime) === 0;
		$nextRunTime = !$isEmpty ? Carbon::instance(new \DateTime($cronTime)) : 'Time for next run is not set. Please, run the bot manually.';
		$this->template->queue = $this->queueRepository->loadQueue();
		$this->template->repetitiveCommands = $this->queueRepository->loadRepetitiveCommands();
		$this->template->nextRunTime = $nextRunTime;
		$this->template->players = $this->databaseManager->getAllPlayersCount();
		$this->template->planets = $this->databaseManager->getAllPlanetsCount();
		$this->template->planetsWithAllInformation = $this->databaseManager->getPlanetsWithAllInformationCount();
		$this->template->planetsToFarm = $this->databaseManager->getPlanetsWithoutFleetAndDefenseCount();
	}

	public function renderResources()
	{
		$this->template->resources = $this->planetCalculator->getResourcesEstimateForInactivePlanets();
	}
	
	/**
	 * @param string $cronFile
	 */
	public function setCronFile($cronFile)
	{
		$this->cronFile = $cronFile;
	}

	public function createComponentDisplayCommand()
	{
		return $this->displayCommandFactory->create();
	}

	public function actionRunBot()
	{
		pclose(popen('cd ' . __DIR__ . '/../.. && start php ' . __DIR__ . '/../../www/index.php bot:queue --debug-mode', "r"));
		$this->flashMessage('Bot started.', 'info');
		$this->redirect('default');
	}

}
