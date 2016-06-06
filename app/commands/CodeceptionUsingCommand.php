<?php

namespace App\Commands;

use Carbon\Carbon;
use Kdyby\Monolog\Logger;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

abstract class CodeceptionUsingCommand extends Command {

	/** @var Container */
	protected $container;

	public function __construct(Container $container)
	{
		parent::__construct();
		$this->container = $container;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$wwwDir = $this->container->getParameters()['wwwDir'];
		file_put_contents("$wwwDir/running.txt", 'running');
		try {
			$this->executeDelegated($input, $output);
		} catch(\Throwable $e) {
			/** @var \AcceptanceTester $acceptanceTester */
			$acceptanceTester = $this->container->getByType(\AcceptanceTester::class);
			/** @var Logger $logger */
			$logger = $this->container->getByType(Logger::class);
			$logger->addCritical('Exception thrown: ' . $e->getMessage());
			$logger->addCritical('Exception stacktrace: ' . $e->getTraceAsString());
			$logger->addAlert("Exception thrown on computer {$_SERVER['USERNAME']}. Please, check it.");
			$acceptanceTester->logFailedAction(Debugger::$logDirectory, 'exception-codeception-fail-'.Carbon::now()->format('Y-m-d--H-i'));
			file_put_contents("$wwwDir/running.txt", '');
			throw $e;
		}
		file_put_contents("$wwwDir/running.txt", '');
		return 0; // zero return code means everything is ok
	}

	abstract protected function executeDelegated(InputInterface $input, OutputInterface $output);

}