<?php

namespace App\Forms;
 
use App\Enum\FleetMission;
use App\Enum\Ships;
use App\Model\DatabaseManager;

use App\Model\Queue\Command\SendFleetCommand;
use Nette\Application\UI\Form;
use Nette\Object;

class SendFleetCommandFormFactory extends Object
{

	/** @var FormFactory */
	private $formFactory;

	/** @var DatabaseManager */
	private $databaseManager;

	public function __construct(FormFactory $formFactory, DatabaseManager $databaseManager)
	{
		$this->formFactory = $formFactory;
		$this->databaseManager = $databaseManager;
	}

	public function create(SendFleetCommand $command = null) : Form
	{
		$form = $this->formFactory->create();

		$form->addGroup('');

		$form->addSelect('planet', 'Planet: ', $this->databaseManager->getAllMyPlanetsIdsNamesAndCoordinates());

		$form->addSelect('mission', 'Mission: ', FleetMission::getSelectBoxValues());

		$speeds = array_reverse([10, 20, 30, 40, 50, 60, 70, 80, 90, 100]);
		$form->addSelect('speed', 'Speed: ', array_combine($speeds, $speeds))
			->setDefaultValue(100);

		$form->addCheckbox('waitForResources', 'Wait for resources');

		$form->addGroup('Ships');
		$fleet = $form->addContainer('fleet');
		foreach (Ships::getEnumValues() as $index => $ship) {
			$fleet->addText($index, $ship)
				->setType('number')
				->setDefaultValue(0);
		}

		$form->addGroup('Send to planet');
		$to = $form->addContainer('to');

		$to->addText('galaxy', 'Galaxy:')
			->setType('number');
		$to->addText('system', 'System:')
			->setType('number');
		$to->addText('planet', 'Planet:')
			->setType('number');

		$form->addGroup('Resources');
		$to = $form->addContainer('resources');

		$to->addText('metal', 'Metal:')
			->setType('number');
		$to->addText('crystal', 'Crystal:')
			->setType('number');
		$to->addText('deuterium', 'Deuterium:')
			->setType('number');

		$form->addSubmit('send', $command ? 'Edit command' : 'Add command');

		if ($command) {
			$ships = [];
			foreach (Ships::getEnumValues() as $index => $ship) {
				if ( ! isset($command->getFleet()->toArray()[$ship])) {
					continue;
				}

				$ships[$index] = $command->getFleet()->toArray()[$ship];
			}

			$form->setDefaults([
				'planet' => $this->databaseManager->getPlanet($command->getCoordinates())->getId(),
				'to' => $command->getTo()->toArray(),
				'fleet' => $ships,
				'mission' => $command->getMission()->getValue(),
				'waitForResources' => $command->waitForResources(),
				'resources' => $command->getResources()->toArray(),
				'speed' => $command->getSpeed()
			]);
		}

		return $form;
	}
	
}