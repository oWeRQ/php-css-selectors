<?php

require_once '../vendor/autoload.php';
require_once 'FindCount.php';

$test = new FindCount(file_get_contents('fixtures/test1.html'));

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
