<?php

namespace App\Model\Logging;

use Codeception\Module;
use Kdyby\Monolog\Logger;

class CodeceptionMonologAdapter extends Module
{

	/** @var Logger */
	private $logger;

	public function setLogger(Logger $logger)
	{
		$this->logger = $logger;
	}

	public function getLogger() : Logger
	{
		return $this->logger;
	}

}