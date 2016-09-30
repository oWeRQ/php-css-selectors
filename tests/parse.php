<?php

require_once '../vendor/autoload.php';
require_once '../vendor/php-selector/selector.inc';
require_once '../vendor/phpquery/phpQuery/phpQuery.php';
require_once '../vendor/phpquery/phpQuery/phpQuery/phpQueryObject.php';
require_once '../vendor/simplehtmldom/simple_html_dom.php';
require_once '../vendor/parse/ParseHelper.php';

class phpQueryObjectPublic extends phpQueryObject
{
	public function __construct() {}

	public function parseSelector($query) {
		return parent::parseSelector($query);
	}
}

class simple_html_dom_node_public extends simple_html_dom_node
{
	public function parse_selector($selector_string) {
		return parent::parse_selector($selector_string);
	}
}

class ParseTest
{
	protected $parsers = [
		'parsePhpSelector',
		'parsePhpQuery',
		'parsePQuery',
		'parseQueryPath',
		'parseSimpleHtmlDom',
		'converterSymfony',
		'converterZend',
		'converterParseHelper',
		'converterParseHelperPlain',
	];

	public function run($selector, $interations = 1000)
	{
		shuffle($this->parsers);

		foreach ($this->parsers as $parser) {
			$before = 'before_'.$parser;
			
			if (method_exists($this, $before))
				$this->$before();

			$this->$parser('div');
		}

		foreach ($this->parsers as $parser) {
			echo "{selector: '$selector', parser: '$parser', ";

			$start = microtime(true);

			for ($i = 0; $i < $interations; $i++) {
				$this->$parser($selector);
			}

			$time = round(microtime(true) - $start, 4);

			echo "time: $time},\n";
		}
	}

	public function parsePhpSelector($selector)
	{
		selector_to_xpath($selector);
	}

	public function parsePhpQuery($selector)
	{
		$phpQuery = new \phpQueryObjectPublic;
		$phpQuery->parseSelector($selector);
	}

	public function parsePQuery($selector)
	{
		$parser = new \pQuery\CSSQueryTokenizer;
		$parser->setDoc($selector);
		$parser->setPos(0);

		$start = 0;
		$token = $parser->token;

		do {
			$end = $parser->getPos();

			$str = substr($selector, $start, $end - $start + 1);

			//echo "pos: $start-$end, str: \"$str\", token: $token\n";

			$start = $end + 1;
		//} while ($token = $parser->next_no_whitespace());
		} while ($token = $parser->next());
	}

	public function parseQueryPath($selector)
	{
		$handler = new \QueryPath\CSS\Selector;
		$parser = new \QueryPath\CSS\Parser($selector, $handler);
		$parser->parse();
	}

	public function parseSimpleHtmlDom($selector)
	{
		$node = new \simple_html_dom_node_public(new \simple_html_dom);
		$node->parse_selector($selector);
	}

	public function before_converterSymfony()
	{
		$this->converter = new \Symfony\Component\CssSelector\CssSelectorConverter;
	}

	public function converterSymfony($selector)
	{
		$this->converter->toXPath($selector);
	}

	public function converterZend($selector)
	{
		\Zend\Dom\Document\Query::cssToXpath($selector);
	}

	public function converterParseHelper($selector)
	{
		\ParseHelper::css2XPath($selector);
	}

	public function converterParseHelperPlain($selector)
	{
		\ParseHelper::css2XPathPlain($selector);
	}
}

$selectors = [
	'div.item > h4 > a',
	'input[type=text]',
	'div#id1.class1.class2.class3',
];

foreach ($selectors as $selector) {
	$test = new ParseTest;
	$test->run($selector, 10000);
}