<?php

require_once '../vendor/autoload.php';

class FindTest
{
	public $parsers;
	public $html;
	public $expression;

	public static $total = [];

	public static function top()
	{
		asort(self::$total);
		return self::$total;
	}

	public function __construct($filename)
	{
		$this->parsers = preg_grep('/^run[A-Z]/', get_class_methods($this));
		$this->html = file_get_contents($filename);

		sort($this->parsers);
	}

	public function toXPath($selector)
	{
		return \Parse\SelectorHelper::toXPath($selector);

		return \PhpCss::toXpath($selector);

		return \PhpCss::toXpath($selector, \PhpCss\Ast\Visitor\Xpath::OPTION_EXPLICIT_NAMESPACES);

		$converter = new \Symfony\Component\CssSelector\CssSelectorConverter;
		return $converter->toXPath($selector);
	}

	public function run($selector, $interations = 1)
	{
		//shuffle($this->parsers);

		$this->expression = $this->toXPath($selector);

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

			if (empty(self::$total[$parser]))
				self::$total[$parser] = 0;

			self::$total[$parser] += $time;
		}

		echo "  }\n},\n";
	}

	public function before_runXPath($selector)
	{
		$this->xpath = \Parse\DOMHelper::htmlXPath($this->html, false);
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
		$this->xpathExt = \Parse\DOMHelper::htmlXPath($this->html);
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
		$xpath = \Parse\DOMHelper::htmlXPath($this->html, false);
		$this->xpathQuery = new \Parse\XPathQuery($xpath->document, $xpath);
	}

	public function runXPathQuery($selector)
	{
		$result = [];
		foreach ($this->xpathQuery->xpath($this->expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return count($result);
	}

	public function before_runParseQuery()
	{
		$this->parseQuery = \Parse\ParseQuery::loadHtml($this->html);
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

	public function before_runFluentDOM($selector)
	{
		$this->FluentDOM = \FluentDOM::Query($this->html);
	}
	public function runFluentDOM($selector)
	{
		$result = [];
		foreach ($this->FluentDOM->find($this->expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return count($result);
	}

	public function before_runFluentDOMCSS($selector)
	{
		$this->FluentDOMCSS = \FluentDOM::QueryCss($this->html, 'text/html');
	}

	public function runFluentDOMCSS($selector)
	{
		$result = [];
		foreach ($this->FluentDOMCSS->find($selector) as $node) {
			$result[] = trim($node->textContent);
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

	public function before_runSimpleHtmlDom($selector)
	{
		new \simple_html_dom; // force autoload
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

print_r(FindTest::top());