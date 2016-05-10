<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/codeception/codeception/autoload.php';
require_once __DIR__ . '/../tests/_support/_generated/AcceptanceTesterActions.php';
require_once __DIR__ . '/../tests/_support/AcceptanceTester.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

//codeception test actor initializer
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
$codecept = new \Codeception\Codecept($userOptions);

try {
	$codecept->run($suite, $test);
} catch(\ActorException $e) {
	$actor = $e->actor;
	$container->removeService();
	$container->addService('codeception', $actor);
}

//end of codeception test actor initializer

return $container;

