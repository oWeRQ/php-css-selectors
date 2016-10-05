<?php

require_once '../vendor/autoload.php';
require_once '../vendor/cdom/CDom.php';
require_once '../vendor/phpquery/phpQuery/phpQuery.php';
require_once '../vendor/simplehtmldom/simple_html_dom.php';
require_once '../vendor/parse/XPathQuery.php';
require_once '../vendor/parse/ParseQuery.php';

class FindTest
{
	public $parsers;
	public $html;
	public $expression;

	public function __construct($filename)
	{
		$this->parsers = preg_grep('/^run[A-Z]/', get_class_methods($this));
		$this->html = file_get_contents($filename);
	}

	public function css2XPath($selector)
	{
		return \ParseHelper::css2XPath($selector);

		$converter = new \Symfony\Component\CssSelector\CssSelectorConverter;
		return $converter->toXPath($selector);
	}

	public function run($selector, $interations = 1)
	{
		//shuffle($this->parsers);

		$this->expression = $this->css2XPath($selector);

		echo "{\n  selector: '{$selector}',\n  expression: '{$this->expression}',\n  parsers: {\n";

		foreach ($this->parsers as $parser) {
			$before = 'before_'.$parser;
			
			if (method_exists($this, $before))
				$this->$before($selector);

			//$this->$parser('div');
		}

		foreach ($this->parsers as $parser) {
			echo "    ".str_pad(substr($parser, 3).':', 16)." {";

			$start = microtime(true);

			try {
				for ($i = 0; $i < $interations; $i++) {
					$count = $this->$parser($selector);
				}
			} catch (\Exception $e) {
				$count = 0;
				echo "error: '".$e->getMessage()."', ";
			}

			$time = round(microtime(true) - $start, 3);

			echo "count: $count, time: $time},\n";
		}

		echo "  }\n},\n";
	}

	public function before_runXPath($selector)
	{
		$this->xpath = \ParseHelper::htmlXPath($this->html, false);
	}

	public function runXPath($selector)
	{
		$result = [];
		foreach ($this->xpath->query($this->expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return count($result);
	}

	public function before_runXPathExt($selector)
	{
		$this->xpathExt = \ParseHelper::htmlXPath($this->html);
	}

	public function runXPathExt($selector)
	{
		$result = [];
		foreach ($this->xpathExt->query($this->expression, $this->xpathExt->document) as $node) {
			$result[] = trim($node->textContent);
		}
		return count($result);
	}

	public function before_runXPathQuery()
	{
		$xpath = \ParseHelper::htmlXPath($this->html, false);
		$this->xpathQuery = new \XPathQuery($xpath->document, $xpath);
	}

	public function runXPathQuery($selector)
	{
		$result = [];
		foreach ($this->xpathQuery->xpathQuery($this->expression) as $node) {
			$result[] = trim($node->get(0)->textContent);
		}
		return count($result);
	}

	public function before_runParseQuery()
	{
		$this->parseQuery = \ParseQuery::loadHtml($this->html);
	}

	public function runParseQuery($selector)
	{
		$result = [];
		foreach ($this->parseQuery->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return count($result);
	}

	public function before_runDomCrawlerXP($selector)
	{
		$this->domCrawler = new \Symfony\Component\DomCrawler\Crawler($this->html);
	}

	public function runDomCrawlerXP($selector)
	{
		$result = [];
		$this->domCrawler->filterXPath($this->expression)->each(function($node) use(&$result){
			$result[] = trim($node->text());
		});
		return count($result);
	}

	public function runDomCrawler($selector)
	{
		$result = [];
		$this->domCrawler->filter($selector)->each(function($node) use(&$result){
			$result[] = trim($node->text());
		});
		return count($result);
	}

	public function before_runZendQuery($selector)
	{
		$this->zendDom = new \Zend\Dom\Query($this->html);
	}

	public function runZendQuery($selector)
	{
		$result = [];
		foreach ($this->zendDom->execute($selector) as $node) {
			$result[] = trim($node->textContent);
		}
		return count($result);
	}

	public function before_runSimpleHtmlDom($selector)
	{
		$this->simpleHtmlDom = str_get_html($this->html);
	}

	public function runSimpleHtmlDom($selector)
	{
		$result = [];
		foreach ($this->simpleHtmlDom->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return count($result);
	}

	public function before_runQueryPath($selector)
	{
		$this->queryPath = qp($this->html);
	}

	public function runQueryPath($selector)
	{
		$result = [];
		foreach ($this->queryPath->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return count($result);
	}

	public function before_runPQuery($selector)
	{
		unset($this->pQuery);
		$this->pQuery = \pQuery::parseStr($this->html);
	}

	public function runPQuery($selector)
	{
		$result = [];
		foreach ($this->pQuery->query($selector) as $node) {
			$result[] = trim($node->text());
		}
		return count($result);
	}

	public function before_runCDom($selector)
	{
		$this->cDom = \CDom::fromString($this->html);
	}

	public function runCDom($selector)
	{
		$result = [];
		foreach ($this->cDom->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return count($result);
	}

	public function before_runPhpQuery($selector)
	{
		$this->phpQuery = \phpQuery::newDocument($this->html);
	}

	public function runPhpQuery($selector)
	{
		$result = [];
		foreach ($this->phpQuery[$selector] as $node) {
			$result[] = trim(pq($node)->text());
		}
		return count($result);
	}
}

$selectors = [
	'table',
	'th',
	'td',
	'table > tbody > tr > td',
	'table tbody tr td',
	'*',
];

foreach ($selectors as $selector) {
	$test = new FindTest('fixtures/test2.html');
	$test->run($selector, 10);
}