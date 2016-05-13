<?php

namespace App\Commands;

use App\Model\Game\SignManager;
use App\Model\Queue\QueueConsumer;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
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
			->setDescription('Processes queue.');
	}

	protected function executeDelegated(InputInterface $input, OutputInterface $output)
	{
		$signManager = $this->container->getByType(SignManager::class);
		$queueConsumer = $this->container->getByType(QueueConsumer::class);
		$signManager->signIn();
		$queueConsumer->processQueue();
		$signManager->signOut();
		$output->writeln('Queue processed');
		return 0; // zero return code means everything is ok
	}


} 