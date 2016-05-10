<?php

namespace App\Model;
 
use Nette;
 
class GalaxyBrowser extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;
	
	public function __construct(\AcceptanceTester $I)
	{
		$this->I = $I;
	}
	
}