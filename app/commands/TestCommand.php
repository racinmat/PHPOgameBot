<?php

namespace App\Commands;
use App\Enum\Building;
use App\Fixtures\RootFixture;
use App\Model\BuildingManager;
use App\Model\Command\UpgradeBuildingCommand;
use App\Model\CronManager;
use App\Model\PlanetManager;
use App\Model\QueueProducer;
use App\Model\ResourcesCalculator;
use App\Model\SignManager;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class TestCommand extends Command {

	/** @var Container */
	private $container;

	public function __construct(Container $container)
	{
		parent::__construct();
		$this->container = $container;
	}

	protected function configure()
	{
		$this->setName('bot:test')
			->setDescription('It does stuff.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$signManager = $this->container->getByType(SignManager::class);
		$signManager->signIn();
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::SOLAR_POWER_PLANT)));
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::METAL_MINE)));
		return 0; // zero return code means everything is ok
	}


} 