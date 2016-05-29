<?php

namespace App\Commands;

use App\Model\Game\SignManager;
use App\Model\Queue\QueueConsumer;
use Nette\DI\Container;
use Nette\Utils\Validators;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessQueueCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class ProcessQueueCommand extends CodeceptionUsingCommand {

	/** @var QueueConsumer */
	private $queueConsumer;

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	protected function configure()
	{
		$this->setName('bot:queue')
			->setDescription('Processes queue.')
			->addOption(
				'repeat',
				'r',
				InputArgument::OPTIONAL,
				'Processes the queue every X minutes',
				0
			);

	}

	protected function validate(InputInterface $input, OutputInterface $output) : bool
	{
		return Validators::isNumericInt($input->getOption('repeat'));
	}

	protected function executeDelegated(InputInterface $input, OutputInterface $output)
	{
		if ($this->validate($input, $output) !== true) {
			return 1;
		}

		$minutesInterval = $input->getOption('repeat');


		$signManager = $this->container->getByType(SignManager::class);
		$this->queueConsumer = $this->container->getByType(QueueConsumer::class);
		$signManager->signIn();

		if ($minutesInterval > 0) {
			while (true) {
				$this->process($output);
				sleep(60 * $minutesInterval);
			}
		} else {
			$this->process($output);
		}

		$signManager->signOut();
		return 0; // zero return code means everything is ok
	}

	private function process(OutputInterface $output)
	{
		$this->queueConsumer->processQueue();
		$output->writeln('Queue processed');
		
		$output->writeln('Attacks checked');
	}

} 