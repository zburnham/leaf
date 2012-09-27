<?php
$bootstrap = include('bootstrap/bootstrap.php');

$autoloaderConfig = $bootstrap['config']['Autoloader'];
include($autoloaderConfig['autoloaderFactoryPath']);
$af = new $autoloaderConfig['autoloaderFactoryClassName']($bootstrap['config']);
$af->registerAutoloaders();

$applicationConfig = $bootstrap['config']['Application'];
$application = new $applicationConfig['className']($applicationConfig);
$application->run();
