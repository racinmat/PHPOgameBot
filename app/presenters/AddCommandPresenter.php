<?php

namespace App\Presenters;

use App\Forms\AddCommandFormFactory;
use App\Model\GenresManager;
use App\Model\Queue\QueueProducer;
use App\Model\SongsManager;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Tracy\Debugger;

class AddCommandPresenter extends BasePresenter
{

	/**
	 * @var AddCommandFormFactory
	 * @inject
	 */
	public $addCommandFormFactory;

	public function actionDefault()
	{
	}

	public function renderDefault()
	{
	}

	public function createComponentAddCommandForm()
	{
		$form = $this->addCommandFormFactory->create();
		$form->onSuccess[] = $this->addCommand;
		return $form;
	}

	public function addCommand(Form $form, array $values)
	{
		$command = $values['command'];
		Debugger::barDump($command, 'command');
		$this->redirect('this');
	}

}
