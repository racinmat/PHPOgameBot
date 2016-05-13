<?php

namespace App\Commands;

use App\Model\Game\SignManager;
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
		$signManager = $this->container->getByType(SignManager::class);
		$signManager->signIn();
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::SOLAR_POWER_PLANT)));
//		$this->queueProducer->addToQueue(new UpgradeBuildingCommand(Building::_(Building::METAL_MINE)));
		return 0; // zero return code means everything is ok
	}


} 