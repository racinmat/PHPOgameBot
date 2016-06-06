<?php

namespace App\Forms;

use App\Model\DatabaseManager;


use App\Model\Queue\Command\ScanGalaxyCommand;
use Nette\Application\UI\Form;
use Nette\Object;

class ScanGalaxyCommandFormFactory extends Object
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

	public function create(ScanGalaxyCommand $command = null) : Form
	{
		$form = $this->formFactory->create();

		$form->addGroup('General');
		$form->addSelect('planet', 'Planet: ', $this->databaseManager->getAllMyPlanetsIdsNamesAndCoordinates());

		$form->addGroup('Scanning from system');
		$from = $form->addContainer('from');

		$from->addText('galaxy', 'Galaxy:')
			->setType('number');
		$from->addText('system', 'System:')
			->setType('number');

		$form->addGroup('Scanning to system');
		$to = $form->addContainer('to');
		$to->addText('galaxy', 'Galaxy:')
			->setType('number');
		$to->addText('system', 'System:')
			->setType('number');

		$form->addSubmit('send', $command ? 'Edit command' : 'Add command');

		if ($command) {
			$form->setDefaults([
				'planet' => $this->databaseManager->getPlanet($command->getCoordinates())->getId(),
				'from' => $command->getFrom()->toArray(),
				'to' => $command->getTo()->toArray()
			]);
		}

		return $form;
	}

}