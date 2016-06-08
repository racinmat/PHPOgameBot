<?php

namespace App\Presenters;

use App\Enum\Building;
use App\Enum\Defense;

use App\Enum\OrderPlanetsBy;
use App\Enum\OrderType;
use App\Enum\PlayerStatus;
use App\Enum\ProbingStatus;
use App\Enum\Research;
use App\Enum\Ships;
use App\Forms\FormFactory;
use App\Forms\ScanGalaxyCommandFormFactory;
use App\Forms\SendFleetCommandFormFactory;
use App\Model\DatabaseManager;

use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbeFarmsCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\SendFleetCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Model\Queue\QueueManager;
use App\Model\ValueObject\Coordinates;
use Nette\Application\UI\Form;


class AddCommandPresenter extends BasePresenter
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
	
	public function createComponentAddEnhanceCommandsForm()
	{
		$form = $this->formFactory->create();
		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetsIdsNamesAndCoordinates())
			->setDefaultValue($this->planet);

		$form->addCheckbox('buildStoragesIfNeeded', 'Build storages if needed')
			->setDefaultValue(IEnhanceCommand::DEFAULT_BUILD_STORAGE_IF_NEEDED);

		$form->addSelect('building', 'Building: ', Building::getSelectBoxValues())
			->setPrompt('-');

		$form->addSelect('research', 'Research: ', Research::getSelectBoxValues())
			->setPrompt('-');

		$form->addSelect('ships', 'Ships: ', Ships::getSelectBoxValues())
			->setPrompt('-');
		$form->addText('shipsAmount', 'Ships amount: ')->setType('number');

		$form->addSelect('defense', 'Defense: ', Defense::getSelectBoxValues())
			->setPrompt('-');
		$form->addText('defenseAmount', 'Defense amount: ')->setType('number');

		$form->addSubmit('send', 'Add commands');

		$form->addCheckbox('repetitive', 'Repetitive command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];
			
			$commands = [];
			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toArray();
			if ($values['building'] !== null) {
				$commands[] = UpgradeBuildingCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'building' => $values['building'],
						'buildStoragesIfNeeded' => $values['buildStoragesIfNeeded']
					]
				]);
			}
			if ($values['research'] !== null) {
				$commands[] = UpgradeResearchCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'research' => $values['research'],
						'buildStoragesIfNeeded' => $values['buildStoragesIfNeeded']
					]
				]);
			}
			if ($values['ships'] !== null) {
				$commands[] = BuildShipsCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'ships' => $values['ships'], 
						'amount' => $values['shipsAmount'],
						'buildStoragesIfNeeded' => $values['buildStoragesIfNeeded']
					]
				]);
			}
			if ($values['defense'] !== null) {
				$commands[] = BuildDefenseCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'defense' => $values['defense'], 
						'amount' => $values['defenseAmount'],
						'buildStoragesIfNeeded' => $values['buildStoragesIfNeeded']
					]
				]);
			}
			foreach ($commands as $command) {
				if ($values['repetitive']) {
					$this->queueManager->addToRepetitiveCommands($command);
				} else {
					$this->queueManager->addToQueue($command);
				}
			}
			if (count($commands) == 0) {
				$message = 'No command added';
			} elseif (count($commands) == 1) {
				$message = 'Command added';
			} else {
				$message = 'Commands added';
			}
			$this->flashMessage($message, 'success');
			$this->redirect('this');
		};

		return $form;
	}

	public function createComponentAddScanGalaxyCommandForm()
	{
		$form = $this->scanGalaxyCommandFormFactory->create();

		$form->addCheckbox('repetitive', 'Repetitive command');

		$form->setDefaults(['planet' => $this->planet]);

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toArray();
			$command = ScanGalaxyCommand::fromArray([
				'coordinates' => $coordinates, 
				'data' => [
					'from' => [
						'galaxy' => $values['from']['galaxy'],
						'system' => $values['from']['system'],
						'planet' => Coordinates::$minPlanet
					],
					'to' => [
						'galaxy' => $values['to']['galaxy'],
						'system' => $values['to']['system'],
						'planet' => Coordinates::$maxPlanet
					]
				]
			]);
			if ($values['repetitive']) {
				$this->queueManager->addToRepetitiveCommands($command);
			} else {
				$this->queueManager->addToQueue($command);
			}
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

	public function createComponentAddProbePlayersCommandForm()
	{
		$form = $this->formFactory->create();

		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetsIdsNamesAndCoordinates())
			->setDefaultValue($this->planet);

		$form->addMultiSelect('statuses', 'Only players with statuses', PlayerStatus::getSelectBoxValues())
			->getControlPrototype()->addAttributes(['size' => count(PlayerStatus::getSelectBoxValues())]);

		$form->addMultiSelect('probingStatuses', 'Only players probing statuses', ProbingStatus::getSelectBoxValues())
			->getControlPrototype()->addAttributes(['size' => count(ProbingStatus::getSelectBoxValues())]);

		$form->addText('limit', 'Limit of scanned planets:')
			->setRequired('Limit must be filled (to scan all planets, insert dome really high number).')
			->setType('number');

		$form->addSelect('orderBy', 'Order planets to probe by: ', OrderPlanetsBy::getSelectBoxValues())
			->setPrompt('-');

		$form->addSelect('orderType', 'Order type: ', OrderType::getSelectBoxValues())
			->setPrompt('-');
		
		$form->addSubmit('send', 'Add command');

		$form->addCheckbox('repetitive', 'Repetitive command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toArray();
			$command = ProbePlayersCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => [
					'statuses' => $values['statuses'],
					'probingStatuses' => $values['probingStatuses'],
					'limit' => $values['limit'],
					'orderBy' => $values['orderBy'],
					'orderType' => $values['orderType']
				]
			]);
			if ($values['repetitive']) {
				$this->queueManager->addToRepetitiveCommands($command);
			} else {
				$this->queueManager->addToQueue($command);
			}
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

	public function createComponentAddSendFleetCommandForm()
	{
		$form = $this->sendFleetCommandFormFactory->create();

		$form->setDefaults(['planet' => $this->planet]);

		$form->addCheckbox('repetitive', 'Repetitive command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$fleet = [];
			foreach (Ships::getEnumValues() as $index => $ship) {
				$fleet[$ship] = $values['fleet'][$index];
			}

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toArray();
			$command = SendFleetCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => [
					'to' => $values['to'],
					'fleet' => $fleet,
					'mission' => $values['mission'],
					'waitForResources' => $values['waitForResources'],
					'resources' => $values['resources']
				]
			]);
			if ($values['repetitive']) {
				$this->queueManager->addToRepetitiveCommands($command);
			} else {
				$this->queueManager->addToQueue($command);
			}
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

	public function createComponentAddProbeFarmsCommandForm()
	{
		$form = $this->formFactory->create();

		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetsIdsNamesAndCoordinates())
			->setDefaultValue($this->planet);

		$form->addText('limit', 'Limit of scanned farms:')
			->setRequired('Set some low number.')
			->setType('number');

		$form->addSubmit('send', 'Add command');

		$form->addCheckbox('repetitive', 'Repetitive command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toArray();
			$command = ProbeFarmsCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => [
					'limit' => $values['limit']
				]
			]);
			if ($values['repetitive']) {
				$this->queueManager->addToRepetitiveCommands($command);
			} else {
				$this->queueManager->addToQueue($command);
			}
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

}
