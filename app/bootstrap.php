<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../vendor/codeception/codeception/autoload.php';

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

$options = [
	'config' => NULL,
	'report' => false,
	'html' => 'report.html',
	'xml' => 'report.xml',
	'tap' => 'report.tap.log',
	'json' => 'report.json',
	'colors' => false,
	'no-colors' => false,
	'silent' => false,
	'steps' => false,
	'debug' => false,
	'coverage' => 'coverage.serialized',
	'coverage-html' => 'coverage',
	'coverage-xml' => 'coverage.xml',
	'coverage-text' => 'coverage.txt',
	'no-exit' => true,
	'group' => [],
	'skip' => [],
	'skip-group' => [],
	'env' => [],
	'fail-fast' => false,
	'no-rebuild' => false,
	'help' => false,
	'quiet' => false,
	'verbose' => false,
	'version' => false,
	'ansi' => false,
	'no-ansi' => false,
	'no-interaction' => false,
];
$config = \Codeception\Configuration::config($options['config']);

if (!$options['colors']) {
	$options['colors'] = $config['settings']['colors'];
}

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
	echo get_class($e->actor);
}

//end of codeception test actor initializer

return $container;

