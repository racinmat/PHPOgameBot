<?php

namespace App\Model;

use App\Model\PageObject\FleetInfo;
use App\Model\Queue\CommandDispatcher;
use App\Utils\Functions;
use App\Utils\OgameParser;
use Kdyby\Monolog\Logger;
use Nette\Object;

class AttackChecker extends Object
{

	/** @var Logger */
	private $logger;

	/** @var FleetInfo */
	private $fleetInfo;

	/** @var CommandDispatcher */
	private $commandDispatcher;

	public function __construct(FleetInfo $fleetInfo, Logger $logger, CommandDispatcher $commandDispatcher)
	{
		$this->fleetInfo = $fleetInfo;
		$this->logger = $logger;
		$this->commandDispatcher = $commandDispatcher;
	}

	public function checkIncomingAttacks()
	{
		$this->logger->addDebug('checking attacks');
		if ($this->fleetInfo->isAnyAttackOnMe()) {
			$nearestAttack = $this->fleetInfo->getNearestAttackTime();
			$this->logger->addAlert("Attack on some of my planets! Nearest attack in $nearestAttack.");
			$this->logger->addDebug('attack detected and logged');
		} else {
			$this->logger->addDebug('attack not detected');
		}

	}
	
}