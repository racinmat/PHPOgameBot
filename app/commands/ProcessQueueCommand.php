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
		$queueConsumer = $this->container->getByType(QueueConsumer::class);
		$signManager->signIn();

		if ($minutesInterval > 0) {
			while (true) {
				$queueConsumer->processQueue();
				$output->writeln('Queue processed');
				sleep(60 * $minutesInterval);
			}
		} else {
			$queueConsumer->processQueue();
			$output->writeln('Queue processed');
		}

		$signManager->signOut();
		return 0; // zero return code means everything is ok
	}


} 