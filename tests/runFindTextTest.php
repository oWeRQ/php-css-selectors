<?php

require_once '../vendor/autoload.php';
require_once 'FindText.php';

$test = new FindText(file_get_contents('fixtures/test2.html'));

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
	'ZendDom' => function($selector){
		return \Zend\Dom\Document\Query::cssToXpath($selector);
	}
];

foreach ($translators as $desc => $translator) {
	foreach ($selectors as $selector) {
		try {
			$test->run($selector, $translator($selector), $interations, $desc);
		} catch (Exception $e) {
			
		}
	}

	echo $test->getTop().",\n";
	$test->resetTop();
}
