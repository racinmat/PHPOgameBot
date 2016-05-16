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
use App\Model\Queue\Command\UpgradeBuildingCommand;
use App\Model\Queue\Command\UpgradeResearchCommand;
use App\Model\Queue\QueueManager;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class AddCommandPresenter extends BasePresenter
{

	/**
	 * @var FormFactory
	 * @inject
	 */
	public $formFactory;

	/**
	 * @var string
	 * @persistent
	 */
	public $commandAction;

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

	public function createComponentAddCommandForm()
	{
		$form = $this->formFactory->create();
		$form->addSelect('planet', 'Planet: ', $this->planetManager->getAllMyPlanetIdsAndCoordinates());
		if ($this->commandAction === UpgradeBuildingCommand::getAction()) {
			$form->addSelect('enum', 'Type: ', Building::getSelectBoxValues());
		} elseif ($this->commandAction === UpgradeResearchCommand::getAction()) {
			$form->addSelect('enum', 'Type: ', Research::getSelectBoxValues());
		} elseif ($this->commandAction === BuildShipsCommand::getAction()) {
			$form->addSelect('enum', 'Type: ', Ships::getSelectBoxValues());
			$form->addText('amount', 'Amount: ')->setType('number');
		} elseif ($this->commandAction === BuildDefenseCommand::getAction()) {
			$form->addSelect('enum', 'Type: ', Defense::getSelectBoxValues());
			$form->addText('amount', 'Amount: ')->setType('number');
		}

		$form->addSubmit('send', 'Add command');
		$form->onSuccess[] = function (Form $form, array $values) {
			$coordinates = $this->planetManager->getPlanetById($values['planet'])->getCoordinates()->toValueObject()->toArray();
			$command = '';
			if ($this->commandAction === UpgradeBuildingCommand::getAction()) {
				$command = UpgradeBuildingCommand::fromArray(['coordinates' => $coordinates, 'data' => ['building' => $values['enum']]]);
			} elseif ($this->commandAction === UpgradeResearchCommand::getAction()) {
				$command = UpgradeResearchCommand::fromArray(['coordinates' => $coordinates, 'data' => ['research' => $values['enum']]]);
			} elseif ($this->commandAction === BuildShipsCommand::getAction()) {
				$command = BuildShipsCommand::fromArray(['coordinates' => $coordinates, 'data' => ['ships' => $values['enum'], 'amount' => $values['amount']]]);
			} elseif ($this->commandAction === BuildDefenseCommand::getAction()) {
				$command = BuildDefenseCommand::fromArray(['coordinates' => $coordinates, 'data' => ['defense' => $values['enum'], 'amount' => $values['amount']]]);
			}
			$this->queueManager->addToQueue($command);
			$this->flashMessage('Command added', 'success');
			$this->redirect('this');
		};

		return $form;
	}

}
