<?php

namespace App\Model;

use App\Model\PageObject\FleetInfo;
use App\Utils\OgameParser;
use Kdyby\Monolog\Logger;
use Nette\Object;

class AttackChecker extends Object
{

	/** @var Logger */
	private $logger;

	/** @var FleetInfo */
	private $fleetInfo;

	public function __construct(FleetInfo $fleetInfo, Logger $logger)
	{
		$this->fleetInfo = $fleetInfo;
		$this->logger = $logger;
	}

	public function checkIncomingAttacks()
	{
		$this->logger->addDebug('checking attacks');
		if ($this->fleetInfo->isAnyAttackOnMe()) {
			$nearestAttack = OgameParser::getNearestTime($this->fleetInfo->getAttackArrivalTimes());
			$this->logger->addAlert("Attack on some of my planets! Nearest attack in $nearestAttack.");
			$this->logger->addDebug('attack detected and logged');
		} else {
			$this->logger->addDebug('attack not detected');
		}

	}
	
}