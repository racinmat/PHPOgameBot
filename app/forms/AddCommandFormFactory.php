<?php

namespace App\Forms;

use App\Enum\Building;
use App\Enum\Defense;
use App\Enum\Research;
use App\Enum\Ships;
use Nette\Object;


class AddCommandFormFactory extends Object
{
	/** @var FormFactory */
	private $factory;

	public function __construct(FormFactory $factory)
	{
		$this->factory = $factory;
	}

	public function create()
	{
		$form = $this->factory->create();

		$commands = [
			Building::class => Building::getSelectBoxValues(),
			Research::class => Research::getSelectBoxValues(),
			Ships::class => Ships::getSelectBoxValues(),
			Defense::class => Defense::getSelectBoxValues()
		];

		$form->addSelect('action', 'Action: ', $commands);
		$form->addSubmit('send', 'Add to queue');
		return $form;
	}

}
