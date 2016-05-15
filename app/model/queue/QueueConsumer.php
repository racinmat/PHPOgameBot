<?php

namespace App\Model\Queue;
 
use App\Model\CronManager;
use App\Model\Game\BuildManager;
use App\Model\Game\UpgradeManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Model\ResourcesCalculator;
use App\Utils\Functions;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
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

	/** @var QueueManager */
	private $queueManager;

	public function __construct(QueueManager $queueManager, UpgradeManager $upgradeManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, BuildManager $buildManager)
	{
		$this->queueManager = $queueManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->processors = [
			$upgradeManager,
			$buildManager
		];
	}

	public function processQueue()
	{
		$this->planetManager->refreshAllData();
		$queue = $this->queueManager->getQueue();
		$planetsQueue = [];
		foreach ($queue as $command) {
			$coordinatesString = $command->getCoordinates()->__toString();
			if (!array_key_exists($coordinatesString, $planetsQueue)) {
				$planetsQueue[$coordinatesString] = [];
			}
			$planetsQueue[$coordinatesString][] = $command;
		}
		$failedCommands = [];
		foreach ($planetsQueue as $planetCoordinates => $queue) {
			$success = true;    //aby se zastavilo procházení fronty, když se nepodaří postavit budovu a zpracování tak skončilo
			/** @var ICommand $command */
			foreach ($queue as $key => $command) {
				foreach ($this->processors as $processor) {
					if ($processor->canProcessCommand($command)) {
						echo 'going to process the command' .  $command->__toString() . PHP_EOL;
						$success = $processor->processCommand($command);
						$this->planetManager->refreshAllResourcesData();
						break;
					}
				}
				if ($success) {
					echo 'command processed successfully' . PHP_EOL;
					$this->queueManager->removeFromQueue($command->getUuid());
				} else {
					echo 'command failed to process' . PHP_EOL;
					$failedCommands[] = $command;
					break;
				}
			}
			if (!$success) {
				$nextStarts = [];
				foreach ($failedCommands as $failedCommand) {
					foreach ($this->processors as $processor) {
						if ($processor->canProcessCommand($failedCommand)) {
							echo 'found processor to determine when to process last command' . PHP_EOL;
							$datetime = $processor->getTimeToProcessingAvailable($failedCommand);
							echo 'new run set to ' . $datetime->__toString() . PHP_EOL;
							$nextStarts[] = $datetime;
							break;
						}
					}
				}

				usort($nextStarts, Functions::compareCarbonDateTimes());
				var_dump($nextStarts);
				$this->cronManager->setNextStart($nextStarts[0]);
			}
		}
	}

}