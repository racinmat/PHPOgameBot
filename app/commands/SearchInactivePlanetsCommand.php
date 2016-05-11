<?php

namespace App\Commands;
use App\Fixtures\RootFixture;
use App\Model\BuildingsManager;
use App\Model\SignManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SearchInactivePlanetsCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class SearchInactivePlanetsCommand extends Command {

	/** @var SignManager */
	private $signManager;

	/** @var BuildingsManager */
	private $buildingsManager;

	/**
	 * SearchInactivePlanetsCommand constructor.
	 * @param SignManager $signManager
	 */
	public function __construct(SignManager $signManager, BuildingsManager $buildingsManager)
	{
		parent::__construct();
		$this->signManager = $signManager;
		$this->buildingsManager = $buildingsManager;
	}

	protected function configure()
	{
		$this->setName('bot:search-inactive')
			->setDescription('Searches for inactive planets and adds them to database.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->signManager->signIn();
		$this->buildingsManager->buildMetalMine();
		$output->writeln('Hello world');
		return 0; // zero return code means everything is ok
	}


} 