<?php

require_once '../vendor/autoload.php';
require_once 'ParseSelector.php';

$test = new ParseSelector($selector);

$interations = 10000;

$selectors = [
	'div.item > h4 > a',
	'input[type=text]',
	'div#id1.class1.class2.class3',
];

foreach ($selectors as $selector) {
	try {
		$test->run($selector, null, $interations);
	} catch (Exception $e) {
		
	}
}

echo $test->getTop().",\n";
