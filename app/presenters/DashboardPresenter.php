<?php

namespace App\Presenters;

use App\Utils\Functions;
use Carbon\Carbon;
use Nette;
use App\Model;


class DashboardPresenter extends BasePresenter
{

	/** @var string */
	private $cronFile;

	/**
	 * @var Model\Queue\QueueRepository
	 * @inject
	 */
	public $queueRepository;

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

}
