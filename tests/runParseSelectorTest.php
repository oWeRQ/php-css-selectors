<?php

require_once '../vendor/autoload.php';
require_once 'ParseSelector.php';

$jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

$interations = 10000;

$selectors = [
	'div.item > h4 > a',
	'input[type=text]',
	'div#id1.class1.class2.class3',
];

foreach ($selectors as $selector) {
	$test = new ParseSelector($selector);
	$test->run($selector, null, $interations);
}

echo ParseSelector::top().",\n";
ParseSelector::resetTop();