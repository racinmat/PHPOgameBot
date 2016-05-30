<?php

namespace App\Model\Queue;
 
use App\Enum\FleetMission;
use App\Enum\Ships;
use App\Model\Entity\Planet;
use App\Model\Game\FleetManager;
use App\Model\Game\PlayersProber;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\UpgradeStoragesPreProcessor;
use App\Model\CronManager;
use App\Model\Game\BuildManager;
use App\Model\Game\GalaxyBrowser;
use App\Model\Game\UpgradeManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Model\ResourcesCalculator;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Resources;
use App\Utils\ArrayCollection;
use App\Utils\ChangesAwareCollection;
use App\Utils\Functions;
use Kdyby\Monolog\Logger;
use Nette\Object;

class QueueConsumer extends Object
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var CronManager */
	private $cronManager;

	/** @var ICommandProcessor[] */
	private $processors;

	/** @var ICommandPreProcessor[] */
	private $preprocessors;

	/** @var QueueManager */
	private $queueManager;

	/** @var Logger */
	private $logger;

	public function __construct(QueueManager $queueManager, UpgradeManager $upgradeManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, BuildManager $buildManager, Logger $logger, GalaxyBrowser $galaxyBrowser, UpgradeStoragesPreProcessor $buildStoragesPreProcessor, FleetManager $fleetManager, PlayersProber $playersProber)
	{
		$this->queueManager = $queueManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->processors = [
			$upgradeManager,
			$buildManager,
			$galaxyBrowser,
			$fleetManager,
			$playersProber
		];
		$this->logger = $logger;
		$this->preprocessors = [
			$buildStoragesPreProcessor
		];
	}

	public function processQueue()
	{
		$this->planetManager->refreshAllData();
		$queue = $this->queueManager->getQueue();
		/** @var ArrayCollection[] $dependencyTypes */
		$dependencyTypes = [];
		foreach ($queue as $command) {
			$dependencyType = $command->getDependencyType();
			if (!isset($dependencyTypes[$dependencyType])) {
				$dependencyTypes[$dependencyType] = new ArrayCollection();
			}
			$dependencyTypes[$dependencyType]->add($command);
		}
		$failedCommands = new ArrayCollection();
		foreach ($dependencyTypes as $planetCoordinates => $queue) {
			/** @var ICommand $command */
			while(!$queue->isEmpty()) {
				$command = $queue->first();

				$this->preProcessCommand($command, $queue);
				$command = $queue->first();     //because preprocessor modifies the queue

				$success = $this->processCommand($command);

				if ($success) {
					$this->logger->addInfo("Command processed successfully. Removing command from queue.");
					$this->queueManager->removeFromQueue($command->getUuid());
					$queue->remove(0);
				} else {
					$this->logger->addInfo("Command failed to process.");
					$failedCommands->add($command);
					break;
				}
			}
		}

		//repetitive commands
		$repetitiveCommands = $this->queueManager->getRepetitiveCommands();
		foreach ($repetitiveCommands as $repetitiveCommand) {
			$this->processCommand($repetitiveCommand);
		}

		$this->resolveTimeOfNextRun($failedCommands->merge($repetitiveCommands));
	}

	private function resolveTimeOfNextRun(ArrayCollection $commands)
	{
		if ($commands->isEmpty()) {
			return;
		}

		$nextStarts = [];
		/** @var ICommand $command */
		foreach ($commands as $command) {
			foreach ($this->processors as $processor) {
				if ($processor->canProcessCommand($command)) {
					$this->logger->addInfo("Going to find the next run of command $command.");
					$datetime = $processor->getTimeToProcessingAvailable($command);
					$this->logger->addInfo("Next run of command $command is $datetime.");
					$nextStarts[] = $datetime;
					break;
				}
			}
		}

		usort($nextStarts, Functions::compareCarbonDateTimes());
		$this->logger->addDebug("Nearest next run is {$nextStarts[0]->__toString()}.");
		$this->cronManager->setNextStart($nextStarts[0]);
	}

	private function preProcessCommand(ICommand $command, ArrayCollection $queue)
	{
		foreach ($this->preprocessors as $preprocessor) {
			if ($preprocessor->canPreProcessCommand($command)) {
				$this->logger->addInfo("Going to preProcess the command $command.");
				$preprocessor->preProcessCommand($command, $queue);
				break;
			}
		}
	}

	private function processCommand(ICommand $command) : bool
	{
		$success = false;

		foreach ($this->processors as $processor) {
			if ($processor->canProcessCommand($command)) {
				$this->logger->addInfo("Going to process the command $command.");
				$success = $processor->processCommand($command);
				$this->planetManager->refreshResourcesDataOnCoordinates($command->getCoordinates());
				break;
			}
		}

		return $success;
	}

}
