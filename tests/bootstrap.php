<?php

/**
 * @author Martin Jirasek <jertin@seznam.cz>
 */

require __DIR__ . '/../src/Common.php';
require __DIR__ . '/../src/Skip.php';

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}
// configure environment
Tester\Environment::setup();
class_alias('Tester\Assert', 'Assert');
date_default_timezone_set('Europe/Prague');
function run(Tester\TestCase $testCase) {
	$testCase->run();
}