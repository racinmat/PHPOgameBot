<?php

namespace App\Model;
 
use Nette;
 
class SignManager extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	public function __construct(\AcceptanceTester $I, string $user, string $password)
	{
		$this->I = $I;
		$this->user = $user;
		$this->password = $password;
	}

	public function signIn()
	{
		$I = $this->I;
		$I->amOnPage('/');
		$I->click('#loginBtn');
		$I->selectOption('#serverLogin', 's124-cz.ogame.gameforge.com');
		$I->fillField('#usernameLogin', $this->user);
		$I->fillField('#passwordLogin', $this->password);
	}
}