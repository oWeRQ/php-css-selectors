<?php

require_once '../vendor/autoload.php';
require_once 'FindCount.php';

$jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

$interations = 1;

$selectors = [
	'ul',
	'.item100',
	'li.item100',
	'#list100 > li',
	'ul#list100 > li',
	'#list100 li',
	'ul#list100 li',
	'ul > .item100',
	'ul > li.item100',
	'ul .item100',
	'ul li.item100',
	'ul > *',
	'ul *',
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
		$test = new FindCount('fixtures/test1.html');
		$test->run($selector, $translator($selector), $interations, $desc);
	}

	echo json_encode(FindCount::top(), $jsonOptions).",\n";
	FindCount::$total = [];
}

