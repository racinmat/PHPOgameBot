<?php

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nextras\Forms\Rendering\Bs3FormRenderer;

class FormFactory extends Nette\Object
{

	/**
	 * @return Form
	 */
	public function create()
	{
		$form = new Form();
		$form->setRenderer(new Bs3FormRenderer());
		return $form;
	}

}
