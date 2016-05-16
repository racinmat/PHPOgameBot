<?php

namespace App\Presenters;

use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\Research;
use App\Enum\Ships;
use App\Forms\FormFactory;
use App\Model\DatabaseManager;
use App\Model\Game\PlanetManager;
use App\Model\Queue\Command\BuildDefenseCommand;
use App\Model\Queue\Command\BuildShipsCommand;
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
		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetIdsAndCoordinates())
			->setDefaultValue($this->planet);

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
						'building' => $values['building']
					]
				]);
			}
			if ($values['research'] !== null) {
				$commands[] = UpgradeResearchCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'research' => $values['research']
					]
				]);
			}
			if ($values['ships'] !== null) {
				$commands[] = BuildShipsCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'ships' => $values['ships'], 
						'amount' => $values['shipsAmount']
					]
				]);
			}
			if ($values['defense'] !== null) {
				$commands[] = BuildDefenseCommand::fromArray([
					'coordinates' => $coordinates, 
					'data' => [
						'defense' => $values['defense'], 
						'amount' => $values['defenseAmount']
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

	public function createComponentScanGalaxyCommandForm()
	{
		$form = $this->formFactory->create();
		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetIdsAndCoordinates())
			->setDefaultValue($this->planet);

		$middle = $form->addContainer('middle');
		$middleGroup = $form->addGroup('Middle of scanning');

		$middleGroup->add($middle->addText('galaxy')
			->setType('number'));
		$middleGroup->add($middle->addText('system')
			->setType('number'));

		$rangeGroup = $form->addGroup('Range of scanning');
		$range = $form->addContainer('range');
		$rangeGroup->add($range->addText('galaxy')
			->setType('number'));
		$rangeGroup->add($range->addText('system')
			->setType('number'));

		$form->addCheckbox('onlyInactive', 'Only inactive: ');

		$form->addSubmit('send', 'Add commands');

		$form->onSuccess[] = function (Form $form, array $values) {
			$this->planet = $values['planet'];

			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toValueObject()->toArray();
			$command = ScanGalaxyCommand::fromArray([
				'coordinates' => $coordinates, 
				'data' => [
					'onlyInactive' => $values['onlyInactive'],
					'middle' => [
						'galaxy' => $values['middle']['galaxy'],
						'system' => $values['middle']['galaxy'],
						'planet' => Coordinates::$minPlanet
					],
					'range' => [
						'galaxy' => $values['range']['galaxy'],
						'system' => $values['range']['galaxy'],
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

}
