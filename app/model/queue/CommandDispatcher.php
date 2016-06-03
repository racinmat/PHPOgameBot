<?php

namespace App\Model\Queue;
 
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\ICommand;
use App\Utils\ArrayCollection;
use App\Utils\Functions;
use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\DI\Container;
use Nette\Object;
use Tracy\Debugger;

class CommandDispatcher extends Object
{

	/** @var PlanetManager */
	private $planetManager;

	/** @var ICommandProcessor[] */
	private $processors;

	/** @var ICommandPreProcessor[] */
	private $preprocessors;

	/** @var Logger */
	private $logger;

	/** @var Container */
	private $container;

	public function __construct(PlanetManager $planetManager, Logger $logger, Container $container)
	{
		$this->planetManager = $planetManager;
		$this->logger = $logger;
		$this->container = $container;
		$this->processors = $this->getServicesImplementing(ICommandProcessor::class);
		$this->preprocessors = $this->getServicesImplementing(ICommandPreProcessor::class);
	}

	private function getServicesImplementing(string $interfaceName) : ArrayCollection
	{
		return (new ArrayCollection($this->container->findByType($interfaceName)))->map(Functions::getService($this->container));
	}

	public function getTimeToProcessingAvailable(ICommand $command) : Carbon
	{
		$processor = $this->getProcessor($command);
		$this->logger->addInfo("Going to find the next run of command $command.");
		$datetime = $processor->getTimeToProcessingAvailable($command);
		$this->logger->addInfo("Next run of command $command is $datetime.");
		return $datetime;
	}

	public function isProcessingAvailable(ICommand $command)
	{
		$processor = $this->getProcessor($command);
		return $processor->isProcessingAvailable($command);
	}
	
	public function preProcessCommand(ICommand $command, ArrayCollection $queue)
	{
		$preprocessor = $this->getPreProcessor($command);
		$this->logger->addInfo("Going to preProcess the command $command.");
		$preprocessor->preProcessCommand($command, $queue);
	}

	public function processCommand(ICommand $command) : bool
	{
		$processor = $this->getProcessor($command);
		$this->logger->addInfo("Going to process the command $command.");
		$success = $processor->processCommand($command);
		$this->planetManager->refreshResourcesDataOnCoordinates($command->getCoordinates());
		return $success;
	}

	private function getPreProcessor(ICommand $command) : ICommandPreProcessor
	{
		return $this->preprocessors->filter(function (ICommandPreProcessor $preProcessor) use ($command) {return $preProcessor->canPreProcessCommand($command);})->first();
	}

	private function getProcessor(ICommand $command) : ICommandProcessor
	{
		return $this->processors->filter(function (ICommandProcessor $processor) use ($command) {return $processor->canProcessCommand($command);})->first();
	}
}
