<?php

require_once '../vendor/autoload.php';
require_once 'FindText.php';

$jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

$interations = 10;

$selectors = [
	'table',
	'th',
	'td',
	'table > tbody > tr > td',
	'table tbody tr td',
	'*',
];

$translators = [
	'SelectorHelper' => function($selector){
		return \Parse\SelectorHelper::toXPath($selector);
	},
	'PhpCss' => function($selector){
		return \PhpCss::toXpath($selector);
	},
	'PhpCssNS' => function($selector){
		return \PhpCss::toXpath($selector, \PhpCss\Ast\Visitor\Xpath::OPTION_EXPLICIT_NAMESPACES);
	},
	'CssSelector' => function($selector){
		$converter = new \Symfony\Component\CssSelector\CssSelectorConverter;
		return $converter->toXPath($selector);
	},
];

foreach ($translators as $desc => $translator) {
	foreach ($selectors as $selector) {
		$test = new FindText('fixtures/test2.html');
		$test->run($selector, $translator($selector), $interations, $desc);
	}

	echo json_encode(FindText::top(), $jsonOptions)."\n";
	FindText::$total = [];
}
