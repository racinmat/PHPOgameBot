<?php

namespace App\Components;



interface IDisplayCommandFactory
{

	/**
	 * @return DisplayCommand
	 */
	public function create();
	
}