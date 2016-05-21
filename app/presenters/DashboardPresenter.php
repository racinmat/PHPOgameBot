<?php

namespace App\Presenters;

use App\Components\IDisplayCommandFactory;
use App\Model\Queue\QueueFileRepository;
use Carbon\Carbon;
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
		$this->template->nextRunTime = $nextRunTime;
	}

	/**
	 * @param string $cronFile
	 */
	public function setCronFile($cronFile)
	{
		$this->cronFile = $cronFile;
	}

	/**
	 * @param string $queueFile
	 */
	public function setQueueFile($queueFile)
	{
		$this->queueFile = $queueFile;
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
