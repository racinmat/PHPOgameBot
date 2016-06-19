<?php

namespace App\Model\Game;
 
use Kdyby\Monolog\Logger;
use Nette;
 
class SignManager extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $I;

	/** @var string */
	private $user;

	/** @var string */
	private $password;

	/** @var Logger */
	private $logger;

	public function __construct(\AcceptanceTester $I, string $user, string $password, Logger $logger)
	{
		$this->I = $I;
		$this->user = $user;
		$this->password = $password;
		$this->logger = $logger;
	}

	public function signIn()
	{
		$this->logger->addDebug('Going to sign in.');
		$I = $this->I;
		$I->amOnPage('/');
		if (! $I->seeElementExists('#loginBtn.open')) { //class="open" is when login window is already opened
			$I->click('#loginBtn');
		}
		$I->selectOption('#serverLogin', 's124-cz.ogame.gameforge.com');
		$I->fillField('#usernameLogin', $this->user);
		$I->fillField('#passwordLogin', $this->password);
		$I->click('#loginSubmit');
	}

	public function signOut()
	{
		$this->logger->addDebug('Going to sign out.');
		$I = $this->I;
		$I->click('Odhlásit se');
		$I->closeBrowser();
	}

	public function checkSignedIn()
	{
		if ($this->I->seeExists('OGame - Ovládni vesmír', '.inner-box.clearfix > h2')) {
			$this->signIn();
		}
	}

}
