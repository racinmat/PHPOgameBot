<?php

namespace App\Commands;

use App\Model\Game\SignManager;
use Carbon\Carbon;
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
		try {
			$this->executeDelegated($input, $output);
		} catch(\Throwable $e) {
			/** @var \AcceptanceTester $acceptanceTester */
			$acceptanceTester = $this->container->getByType(\AcceptanceTester::class);
			$acceptanceTester->logFailedAction(Debugger::$logDirectory, 'exception-codeception-fail-'.Carbon::now()->format('Y-m-d--H-i'));
			throw $e;
		}
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::SOLAR_POWER_PLANT)));
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::METAL_MINE)));
		return 0; // zero return code means everything is ok
	}

	abstract protected function executeDelegated(InputInterface $input, OutputInterface $output);

} 