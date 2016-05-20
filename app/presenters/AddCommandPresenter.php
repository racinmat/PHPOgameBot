<?php

namespace App\Presenters;

use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\PlayerStatus;
use App\Enum\Research;
use App\Enum\Ships;
use App\Forms\FormFactory;
use App\Model\DatabaseManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
use App\Model\Queue\Command\IEnhanceCommand;
use App\Model\Queue\Command\ProbePlayersCommand;
use App\Model\Queue\Command\ScanGalaxyCommand;
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Model\Queue\QueueManager;
use App\Model\ValueObject\Coordinates;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Tracy\Debugger;

class AddCommandPresenter extends BasePresenter
{

	/**
	 * @var FormFactory
	 * @inject
	 */
	public $formFactory;

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

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];
			
			$commands = [];
			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toValueObject()->toArray();
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
				$this->queueManager->addToQueue($command);
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
		$form = $this->formFactory->create();

		$general = $form->addGroup('General', false);

		$general->add($form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetsIdsNamesAndCoordinates())
			->setDefaultValue($this->planet));

		$middle = $form->addContainer('from');
		$middleGroup = $form->addGroup('Scanning from system', false);

		$middleGroup->add($middle->addText('galaxy', 'Galaxy:')
			->setType('number'));
		$middleGroup->add($middle->addText('system', 'System:')
			->setType('number'));

		$rangeGroup = $form->addGroup('Scanning to system', false);
		$range = $form->addContainer('to');
		$rangeGroup->add($range->addText('galaxy', 'Galaxy:')
			->setType('number'));
		$rangeGroup->add($range->addText('system', 'System:')
			->setType('number'));

		$form->addSubmit('send', 'Add command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toValueObject()->toArray();
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
						'planet' => Coordinates::$maxPlanet,
					]
				]
			]);
			$this->queueManager->addToQueue($command);
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

		$form->addSubmit('send', 'Add command');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toValueObject()->toArray();
			$command = ProbePlayersCommand::fromArray([
				'coordinates' => $coordinates,
				'data' => [
					'statuses' => $values['statuses']
				]
			]);
			$this->queueManager->addToQueue($command);
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

}
