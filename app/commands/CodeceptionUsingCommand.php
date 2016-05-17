<?php

namespace App\Commands;

use App\Model\Game\SignManager;
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
		file_put_contents('C:\xampp\htdocs\ogameBot\www\running.txt', 'running');
		try {
			$this->executeDelegated($input, $output);
		} catch(\Throwable $e) {
			/** @var \AcceptanceTester $acceptanceTester */
			$acceptanceTester = $this->container->getByType(\AcceptanceTester::class);
			/** @var Logger $logger */
			$logger = $this->container->getByType(Logger::class);
			$acceptanceTester->logFailedAction(Debugger::$logDirectory, 'exception-codeception-fail-'.Carbon::now()->format('Y-m-d--H-i'));
			$logger->addCritical('Exception thrown: ' . $e->getMessage());
			throw $e;
		}
		file_put_contents('C:\xampp\htdocs\ogameBot\www\running.txt', '');
		return 0; // zero return code means everything is ok
	}

	abstract protected function executeDelegated(InputInterface $input, OutputInterface $output);

} 