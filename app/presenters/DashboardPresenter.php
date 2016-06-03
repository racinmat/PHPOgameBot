<?php

namespace App\Presenters;

use App\Components\IDisplayCommandFactory;
use App\Model\DatabaseManager;
use App\Model\Entity\Planet;
use App\Model\Queue\CommandDispatcher;
use App\Model\Queue\QueueFileRepository;
use App\Model\ValueObject\Coordinates;
use Carbon\Carbon;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Strings;
use Tracy\Debugger;


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

	public function renderDefault()
	{
		$cronTime = file_get_contents($this->cronFile);
		$isEmpty = ctype_space($cronTime) || Strings::length($cronTime) === 0;
		$nextRunTime = !$isEmpty ? Carbon::instance(new \DateTime($cronTime)) : 'Time for next run is not set. Please, run the bot manually.';
		$this->template->queue = $this->queueRepository->loadQueue();
		$this->template->repetitiveCommands = $this->queueRepository->loadRepetitiveCommands();
		$this->template->nextRunTime = $nextRunTime;
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
