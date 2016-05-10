<?php

namespace App\Model;
 
use Nette;
 
class AcceptanceTesterFactory extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $acceptanceTester;

	public function __construct()
	{
		$this->acceptanceTester = new AcceptanceTester();
	}

	/**
	 * @return \AcceptanceTester
	 */
	public function getAcceptanceTester()
	{
		return $this->acceptanceTester;
	}

	/**
	 * @param \AcceptanceTester $acceptanceTester
	 */
	public function setAcceptanceTester(\AcceptanceTester $acceptanceTester)
	{
		$this->acceptanceTester = $acceptanceTester;
	}
	
}