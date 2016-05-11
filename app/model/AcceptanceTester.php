<?php

namespace App\Model;

/**
 * Only as mock during container upgrade. It does nothing.
 */
class AcceptanceTester extends \AcceptanceTester
{

	/**
	 * AcceptanceTester constructor.
	 */
	public function __construct()
	{
		//Do not call parent::__construct()!
	}
}