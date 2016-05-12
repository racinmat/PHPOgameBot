<?php

namespace App\Commands;
use App\Fixtures\RootFixture;
use App\Model\BuildingManager;
use App\Model\QueueConsumer;
use App\Model\SignManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessQueueCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class ProcessQueueCommand extends Command {

	/** @var SignManager */
	private $signManager;

	/** @var QueueConsumer */
	private $queueConsumer;

	/**
	 * TestCommand constructor.
	 * @param SignManager $signManager
	 * @param QueueConsumer $queueConsumer
	 */
	public function __construct(SignManager $signManager, QueueConsumer $queueConsumer)
	{
		parent::__construct();
		$this->signManager = $signManager;
		$this->queueConsumer = $queueConsumer;
	}

	protected function configure()
	{
		$this->setName('bot:queue')
			->setDescription('Processes queue.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->signManager->signIn();
		$this->queueConsumer->processQueue();
		$this->signManager->signOut();
		$output->writeln('Queue processed');
		return 0; // zero return code means everything is ok
	}


} 