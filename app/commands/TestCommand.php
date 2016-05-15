<?php

namespace App\Commands;

use App\Enum\Building;
use App\Model\DatabasePlanetManager;
use App\Model\Game\PlanetManager;
use App\Model\Game\SignManager;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\QueueConsumer;
use App\Model\Queue\QueueManager;
use App\Model\ResourcesCalculator;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 * @package App\Commands
 * @author: Matěj Račinský 
 */
class TestCommand extends CodeceptionUsingCommand {

	public function __construct(Container $container)
	{
		parent::__construct($container);
	}

	protected function configure()
	{
		$this->setName('bot:test')
			->setDescription('It does stuff.');
	}

	protected function executeDelegated(InputInterface $input, OutputInterface $output)
	{
//		$signManager = $this->container->getByType(SignManager::class);
//		$signManager->signIn();
//		/** @var QueueConsumer $queueConsumer */
//		$queueConsumer = $this->container->getByType(QueueConsumer::class);
//		$queueConsumer->processQueue();
		/** @var ResourcesCalculator $resourcesCalculator */
		$resourcesCalculator = $this->container->getByType(ResourcesCalculator::class);
		/** @var DatabasePlanetManager $databasePlanetManager */
		$databasePlanetManager = $this->container->getByType(DatabasePlanetManager::class);
		$planet = array_values($databasePlanetManager->getAllMyPlanets())[0];
		$production = $resourcesCalculator->getProductionPerHour($planet);
		var_dump($production);
		return 0; // zero return code means everything is ok
	}


} 