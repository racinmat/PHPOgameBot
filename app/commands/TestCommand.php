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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class TestCommand extends Command {

	/** @var SignManager */
	private $signManager;

	/** @var PlanetManager */
	private $planetManager;

	/** @var ResourcesCalculator */
	private $resourcesCalculator;

	/** @var CronManager */
	private $cronManager;

	/** @var QueueProducer */
	private $queueProducer;

	public function __construct(SignManager $signManager, PlanetManager $planetManager, ResourcesCalculator $resourcesCalculator, CronManager $cronManager, QueueProducer $queueProducer)
	{
		parent::__construct();
		$this->signManager = $signManager;
		$this->planetManager = $planetManager;
		$this->resourcesCalculator = $resourcesCalculator;
		$this->cronManager = $cronManager;
		$this->queueProducer = $queueProducer;
	}

	protected function configure()
	{
		$this->setName('bot:test')
			->setDescription('It does stuff.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::SOLAR_POWER_PLANT)));
		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::METAL_MINE)));
		return 0; // zero return code means everything is ok
	}


} 