<?php

namespace App\Presenters;

use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\FleetMission;
use App\Enum\PlayerStatus;
use App\Enum\Research;
use App\Enum\Ships;
use App\Forms\FormFactory;
use App\Forms\ScanGalaxyCommandFormFactory;
use App\Forms\SendFleetCommandFormFactory;
use App\Model\DatabaseManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\ICommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Model\Queue\QueueManager;
use App\Model\ValueObject\Coordinates;
use App\Model\ValueObject\Fleet;
use App\Model\ValueObject\Resources;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\Strings;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

class ManageCommandsPresenter extends BasePresenter
{

	/**
	 * @var FormFactory
	 * @inject
	 */
	public $formFactory;

	/**
	 * @var ScanGalaxyCommandFormFactory
	 * @inject
	 */
	public $scanGalaxyCommandFormFactory;

	/**
	 * @var SendFleetCommandFormFactory
	 * @inject
	 */
	public $sendFleetCommandFormFactory;
	
	/**
	 * @var QueueManager
	 * @inject
	 */
	public $queueManager;

	/**
	 * @var DatabaseManager
	 * @inject
	 */
	public $planetManager;

	/**
	 * @var string
	 * @persistent
	 */
	public $planet;

	/** @var ICommand */
	private $command;

	public function actionScanGalaxy(string $uuid)
	{
		$this->command = $this->queueManager->getCommand(Uuid::fromString($uuid));
	}

	public function createComponentEditScanGalaxyCommandForm()
	{
		/** @var ScanGalaxyCommand $command */
		$command = $this->command;
		
		$form = $this->scanGalaxyCommandFormFactory->create($command);

		$form->onSuccess[] = function (Form $form, array $values) use ($command) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates();
			$command->setCoordinates($coordinates);
			$command->setFrom(Coordinates::fromArray([
				'from' => [
					'galaxy' => $values['from']['galaxy'],
					'system' => $values['from']['system'],
					'planet' => Coordinates::$minPlanet
				]
			]));
			$command->setTo(Coordinates::fromArray([
				'to' => [
					'galaxy' => $values['to']['galaxy'],
					'system' => $values['to']['system'],
					'planet' => Coordinates::$maxPlanet
				]
			]));
			$this->queueManager->saveCommand($command);
			$this->flashMessage('Command updated', 'success');
			$this->redirect('this', ['uuid' => $command->getUuid()]);
		};

		return $form;
	}

	public function actionSendFleet(string $uuid)
	{
		$this->command = $this->queueManager->getCommand(Uuid::fromString($uuid));
	}


	public function createComponentEditSendFleetCommandForm()
	{
		/** @var SendFleetCommand $command */
		$command = $this->command;

		$form = $this->sendFleetCommandFormFactory->create($command);

		$form->onSuccess[] = function (Form $form, array $values) use ($command) {
			$this->planet = $values['planet'];

			$fleet = [];
			foreach (Ships::getEnumValues() as $index => $ship) {
				$fleet[$ship] = $values['fleet'][$index];
			}

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates();
			$command->setCoordinates($coordinates);
			$command->setMission(FleetMission::_($values['mission']));
			$command->setTo(Coordinates::fromArray($values['to']));
			$fleet = new Fleet();
			foreach (Ships::getEnumValues() as $index => $ship) {
				$fleet->addShips(Ships::_($ship), $values['fleet'][$index]);
			}
			$command->setFleet($fleet);
			$command->setResources(Resources::fromArray($values['resources']));
			$command->setWaitForResources($values['waitForResources']);
			$this->queueManager->saveCommand($command);
			$this->flashMessage('Command updates', 'success');
			$this->redirect('this');
		};

		return $form;
	}

}
