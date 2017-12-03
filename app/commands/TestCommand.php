<?php

namespace App\Commands;


use App\Enum\Building;
use App\Enum\FleetMission;
use App\Enum\Ships;

use App\Model\DatabaseManager;
use App\Model\Game\FleetManager;

use App\Model\Game\ReportReader;
use App\Model\Game\SignManager;
use App\Model\PageObject\FleetInfo;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\SendFleetCommand;


use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\QueueConsumer;
use App\Model\Queue\QueueManager;
use App\Model\ResourcesCalculator;
use App\Model\ValueObject\Coordinates;
use Carbon\Carbon;
use Nette\DI\Container;

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
//		/** @var ReportReader $reportReader */
//		$reportReader = $this->container->getByType(ReportReader::class);
//		$signManager = $this->container->getByType(SignManager::class);
//		/** @var DatabaseManager $databaseManager */
//		$databaseManager = $this->container->getByType(DatabaseManager::class);
//		$player = $databaseManager->getPlayer('tonda');
//		$player->getPlanets();
//		/** @var \AcceptanceTester $acceptanceTester */
//		$acceptanceTester = $this->container->getByType(\AcceptanceTester::class);
//		$acceptanceTester->logFailedAction(__DIR__, 'test.png');
//		/** @var FleetInfo $fleetInfo */
//		$fleetInfo = $this->container->getByType(FleetInfo::class);
//		$signManager->signIn();
//		$fleetInfo->isAnyAttackOnMe();
//		$reportReader->readEspionageReportsFrom(Carbon::now()->subHours(2));
//		$command = SendFleetCommand::fromArray([
//			'coordinates' => [
//				'galaxy' => 1,
//				'system' => 357,
//				'planet' => 6
//			],
//			'data' => [
//				'to' => [
//					'galaxy' => 1,
//					'system' => 27,
//					'planet' => 10
////					'system' => 28,
////					'planet' => 11
//				],
//				'fleet' => [Ships::ESPIONAGE_PROBE => 1],
//				'mission' => FleetMission::ESPIONAGE
//			]
//		]);
		/** @var FleetManager $fleetManager */
//		$fleetManager = $this->container->getByType(FleetManager::class);
//		$fleetManager->processCommand($command);
//		/** @var QueueConsumer $queueConsumer */
//		$queueConsumer = $this->container->getByType(QueueConsumer::class);
//		$queueConsumer->processQueue();
//		/** @var ResourcesCalculator $resourcesCalculator */
//		$resourcesCalculator = $this->container->getByType(ResourcesCalculator::class);
		/** @var DatabaseManager $databaseManager */
		$databaseManager = $this->container->getByType(DatabaseManager::class);
//		$planet = array_values($databaseManager->getAllMyPlanets())[0];
//		$production = $resourcesCalculator->getProductionPerHour($planet);
//		var_dump($production);


		$coordinates = [
			'galaxy' => 1,
			'system' => 351,
			'planet' => 8
		];
		$planet = $databaseManager->getPlanet(Coordinates::fromArray($coordinates));
//		$planet->setNaniteFactoryLevel(0);
		$databaseManager->flush();

		//metal/crystal/deu/power
		$c = [];
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::ROBOTIC_FACTORY]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::ROBOTIC_FACTORY]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::ROBOTIC_FACTORY]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::ROBOTIC_FACTORY]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::ROBOTIC_FACTORY]
//		]);
//
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		//1/1/0/1
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		//2/2/1/3
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		//3/2/2/4
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		//3/3/3/5
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		//5/4/4/7
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		//6/6/5/9
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		//8/8/7/11
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		//10/9/7/12
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		//12/11/8/14
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		//13/12/9/15
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		//14/12/10/16
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::SOLAR_POWER_PLANT]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::METAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::CRYSTAL_MINE]
//		]);
//		$c[] = UpgradeBuildingCommand::fromArray([
//			'coordinates' => $coordinates,
//			'data' => ['building' => Building::DEUTERIUM_MINE]
//		]);
//		//15/13/11/17

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		//17/15/12/19

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		//18/16/13/20

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		//19/17/14/21

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		//20/17/15/22

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::METAL_MINE]
		]);
		//21/18/16/23

		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::SOLAR_POWER_PLANT]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::CRYSTAL_MINE]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		$c[] = BuildShipsCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => [
				'ships' => Ships::SOLAR_SATELLITE,
				'amount' => 7
			]
		]);
		$c[] = UpgradeBuildingCommand::fromArray([
			'coordinates' => $coordinates,
			'data' => ['building' => Building::DEUTERIUM_MINE]
		]);
		//21/20/18/24



		/** @var QueueManager $queueManager */
		$queueManager = $this->container->getByType(QueueManager::class);
		foreach ($c as $command) {
			$queueManager->addToQueue($command);
		}
		return 0; // zero return code means everything is ok
	}


} 