<?php

namespace App\Model;
 
use Codeception\Codecept;
use Codeception\Scenario;
use Codeception\Test\Unit;
use Kdyby\Monolog\Logger;
use Nette;

class AcceptanceTesterFactory extends Nette\Object
{

	/** @var \AcceptanceTester */
	private $acceptanceTester;

	/** @var Logger */
	private $logger;
	
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
		$this->acceptanceTester = null;
	}

	/**
	 * @return \AcceptanceTester
	 */
	public function getAcceptanceTester()
	{
		if (!$this->initialized()) {
			$this->initialize();
		}
		return $this->acceptanceTester;
	}

	private function initialized()
	{
		return $this->acceptanceTester != null;
	}

	private function initialize()
	{
		$userOptions = [
			'xml' => false,
			'html' => false,
			'json' => false,
			'tap' => false,
			'coverage' => false,
			'coverage-xml' => false,
			'coverage-html' => false,
			'verbosity' => 0,
			'interactive' => true,
			'filter' => NULL,
		];
		$suite = 'acceptance';
		$test = 'basicTestCept';
		$codecept = new Codecept($userOptions);

		try {
			$codecept->run($suite, $test);
		} catch(\ActorException $e) {
			$actor = $e->actor;
			$actor->setLogger($this->logger);
			$this->acceptanceTester = $actor;
		}

//		//fake tester
//		$this->acceptanceTester = new \AcceptanceTester(new Scenario(new Unit()));
	}
}
