<?php

namespace App\Presenters;

use App\Components\IDisplayCommandFactory;
use App\Utils\Functions;
use Carbon\Carbon;
use Nette;
use App\Model;
use Tracy\Debugger;


class DashboardPresenter extends BasePresenter
{

	/** @var string */
	private $cronFile;

	/**
	 * @var Model\Queue\QueueFileRepository
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
		$nextRunTime = Carbon::instance(new \DateTime(file_get_contents($this->cronFile)));
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
		exec('cd ' . __DIR__ . '/../.. && php ' . __DIR__ . '/../../www/index.php bot:queue --debug-mode');
		$this->flashMessage('Bot started.', 'info');
		$this->redirect('default');
	}

}
