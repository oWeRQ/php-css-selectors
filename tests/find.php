<?php

require_once '../vendor/autoload.php';
//require_once '../vendor/cdom/CDom.php';
//require_once '../vendor/php-selector/selector.inc';
//require_once '../vendor/phpquery/phpQuery/phpQuery.php';
//require_once '../vendor/phpquery/phpQuery/phpQuery/phpQueryObject.php';
//require_once '../vendor/simplehtmldom/simple_html_dom.php';
require_once '../vendor/parse/ParseQuery.php';

class FindTest
{
	protected $parsers = [
		'runXPath',
		'runParseQuery',
	];

	public $html;

	public function __construct()
	{
		$this->html = file_get_contents('fixtures/test1.html');
	}

	public function run($selector, $interations = 1000)
	{
		shuffle($this->parsers);

		foreach ($this->parsers as $parser) {
			$before = 'before_'.$parser;
			
			if (method_exists($this, $before))
				$this->$before($selector);

			$this->$parser('div');
		}

		foreach ($this->parsers as $parser) {
			echo "{selector: '$selector', parser: '$parser', ";

			$start = microtime(true);

			for ($i = 0; $i < $interations; $i++) {
				$count = $this->$parser($selector);
			}

			$time = round(microtime(true) - $start, 4);

			echo "count: $count, time: $time},\n";
		}
	}

	public function before_runXPath($selector)
	{
		$this->xpath = \ParseHelper::htmlXPath($this->html);
		$this->expression = \ParseHelper::css2XPath($selector);
	}

	public function runXPath($selector)
	{
		return $this->xpath->query($this->expression)->length;
	}

	public function before_runParseQuery()
	{
		$this->parseQuery = \ParseQuery::loadHtml($this->html);
	}

	public function runParseQuery($selector)
	{
		return $this->parseQuery->find($selector)->length();
	}
}

$selectors = [
	'ul',
	'#list100 li',
	//'li',
	'.item100',
	'ul .item100',
	'ul li.item100',
	'ul > .item100',
];

foreach ($selectors as $selector) {
	$test = new FindTest;
	$test->run($selector, 1);
}