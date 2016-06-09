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

	/** @var string */
	private $runningFile;

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
		$running = file_get_contents($this->runningFile);
		$isTimeEmpty = ctype_space($cronTime) || Strings::length($cronTime) === 0;
		$isRunningEmpty = ctype_space($running) || Strings::length($running) === 0;
		$nextRunTime = !$isTimeEmpty ? Carbon::instance(new \DateTime($cronTime)) : 'Time for next run is not set. Please, run the bot manually.';
		$this->template->queue = $this->queueRepository->loadQueue();
		$this->template->repetitiveCommands = $this->queueRepository->loadRepetitiveCommands();
		$this->template->nextRunTime = $nextRunTime;
		$this->template->running = ! $isRunningEmpty;
		$this->template->players = $this->databaseManager->getAllPlayersCount();
		$this->template->planets = $this->databaseManager->getAllPlanetsCount();
		$this->template->inactivePlayers = $this->databaseManager->getInactivePlayersCount();
		$this->template->inactivePlanets = $this->databaseManager->getInactivePlanetsCount();
		$this->template->planetsWithAllInformation = $this->databaseManager->getPlanetsWithAllInformationCount();
		$this->template->planetsToFarm = $this->databaseManager->getPlanetsWithoutFleetAndDefenseCount();
	}

	public function renderResources()
	{
		list($resources, $lastVisited) = $this->planetCalculator->getResourcesEstimateAndLastVisitedForInactivePlanets();
		$this->template->resources = $resources;
		$this->template->lastVisited = $lastVisited;
	}
	
	/**
	 * @param string $cronFile
	 */
	public function setCronFile($cronFile)
	{
		$this->cronFile = $cronFile;
	}

	/**
	 * @param string $runningFile
	 */
	public function setRunningFile($runningFile)
	{
		$this->runningFile = $runningFile;
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
