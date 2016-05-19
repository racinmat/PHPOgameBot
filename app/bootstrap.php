<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/codeception/codeception/autoload.php';
require_once __DIR__ . '/../tests/_support/_generated/AcceptanceTesterActions.php';
require_once __DIR__ . '/../tests/_support/AcceptanceTester.php';

$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
if (Kdyby\Console\DI\BootstrapHelper::setupMode($configurator)) {
     // pass
} elseif (getenv('NETTE_DEBUG') === '0' || getenv('NETTE_DEBUG') === '1') {
     $configurator->setDebugMode((bool) getenv('NETTE_DEBUG'));
}

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;

