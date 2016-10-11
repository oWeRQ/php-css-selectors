<?php

require_once '../vendor/autoload.php';
require_once '../vendor/powertools/dom-query/vendor/Loader.php';
require_once '../vendor/cdom/CDom.php';
require_once '../vendor/phpquery/phpQuery/phpQuery.php';
require_once '../vendor/simplehtmldom/simple_html_dom.php';
require_once '../vendor/parse/DOMHelper.php';
require_once '../vendor/parse/XPathHelper.php';
require_once '../vendor/parse/XPathQuery.php';
require_once '../vendor/parse/ParseQuery.php';

function time_format($seconds)
{
	return str_replace('0', 'o', number_format($seconds, 3));
}

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
	}

	public function toXPath($selector)
	{
		return \XPathHelper::toXPath($selector);

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
			echo "    ".str_pad(substr($parser, 3).':', 16)." {";

			$load = microtime(true);

			$before = 'before_'.$parser;
			
			if (method_exists($this, $before))
				$this->$before($selector);

			$start = microtime(true);

			try {
				for ($i = 0; $i < $interations; $i++) {
					$count = $this->$parser($selector);
				}
			} catch (\Exception $e) {
				$count = 0;
				echo "error: '".$e->getMessage()."', ";
			}

			$end = microtime(true);

			echo "count: $count, ";
			echo "time: ".time_format($end - $start).", ";
			echo "load: ".time_format($start - $load);
			echo "},\n";

			if (empty(self::$total[$parser]))
				self::$total[$parser] = 0;

			self::$total[$parser] += $end - $load;
		}

		echo "  }\n},\n";
	}

	public function before_runSimpleXML($selector)
	{
		$this->SimpleXML = new SimpleXMLElement($this->html);
	}

	public function runSimpleXML($selector)
	{
		return count($this->SimpleXML->xpath($this->expression));
	}

	public function before_runXPath($selector)
	{
		$this->xpath = \DOMHelper::htmlXPath($this->html, false);
	}

	public function runXPath($selector)
	{
		return $this->xpath->query($this->expression)->length;
	}

	public function before_runXPathExt($selector)
	{
		$this->xpathExt = \DOMHelper::htmlXPath($this->html);
	}

	public function runXPathExt($selector)
	{
		return $this->xpathExt->query($this->expression, $this->xpathExt->document)->length;
	}

	public function before_runXPathQuery()
	{
		$xpath = \DOMHelper::htmlXPath($this->html);
		$this->xpathQuery = new \XPathQuery($xpath->document, $xpath);
	}

	public function runXPathQuery($selector)
	{
		return $this->xpathQuery->xpath($this->expression)->length();
	}

	public function before_runParseQuery()
	{
		$this->parseQuery = \ParseQuery::loadHtml($this->html);
	}

	public function runParseQuery($selector)
	{
		return $this->parseQuery->find($selector)->length();
	}

	public function before_runDomCrawlerXP($selector)
	{
		$this->domCrawlerXP = new \Symfony\Component\DomCrawler\Crawler($this->html);
	}

	public function runDomCrawlerXP($selector)
	{
		return $this->domCrawlerXP->filterXPath($this->expression)->count();
	}

	public function before_runDomCrawler($selector)
	{
		$this->domCrawler = new \Symfony\Component\DomCrawler\Crawler($this->html);
	}

	public function runDomCrawler($selector)
	{
		return $this->domCrawler->filter($selector)->count();
	}

	public function before_runZendQueryXP($selector)
	{
		$this->zendDomXP = new \Zend\Dom\Query($this->html);
	}

	public function runZendQueryXP($selector)
	{
		return count($this->zendDomXP->queryXpath($this->expression, $selector));
	}

	public function before_runZendQuery($selector)
	{
		$this->zendDom = new \Zend\Dom\Query($this->html);
	}

	public function runZendQuery($selector)
	{
		return count($this->zendDom->execute($selector));
	}

	public function before_runFluentDOM($selector)
	{
		$this->FluentDOM = FluentDOM::Query($this->html);
	}
	public function runFluentDOM($selector)
	{
		return $this->FluentDOM->find($this->expression)->length;
	}

	public function before_runFluentDOMCSS1($selector)
	{
		$this->FluentDOMCSS1 = FluentDOM::QueryCss($this->html, 'text/xml');
	}

	public function runFluentDOMCSS1($selector)
	{
		return $this->FluentDOMCSS1->find($selector)->length;
	}

	public function before_runFluentDOMCSS2($selector)
	{
		$this->FluentDOMCSS2 = FluentDOM::QueryCss($this->html, 'text/html');
	}

	public function runFluentDOMCSS2($selector)
	{
		return $this->FluentDOMCSS2->find($selector)->length;
	}

	public function before_runPhpQuery($selector)
	{
		$this->phpQuery = \phpQuery::newDocument($this->html);
	}

	public function _runPhpQuery($selector)
	{
		return $this->phpQuery[$selector]->size();
	}

	public function before_runQueryPath($selector)
	{
		$this->queryPath = qp($this->html);
	}

	public function _runQueryPath($selector)
	{
		return $this->queryPath->find($selector)->length;
	}

	public function before_runSimpleHtmlDom($selector)
	{
		$this->simpleHtmlDom = str_get_html($this->html);
	}

	public function _runSimpleHtmlDom($selector)
	{
		return count($this->simpleHtmlDom->find($selector));
	}

	public function before_runCDom($selector)
	{
		$this->cDom = \CDom::fromString($this->html);
	}

	public function _runCDom($selector)
	{
		return $this->cDom->find($selector)->length;
	}

	public function before_runPQuery($selector)
	{
		$this->pQuery = \pQuery::parseStr($this->html);
	}

	public function _runPQuery($selector)
	{
		return $this->pQuery->query($selector)->count();
	}

	/*
	public function before_runDomQuery($selector)
	{
		$this->domQuery = \PowerTools\DOM_Query::loadHTML($this->html);
	}

	public function runDomQuery($selector)
	{
		return count($this->domQuery->select($selector)->nodes);
	}
	*/

	public function before_runXParser($selector)
	{
		$this->XParser = new \gymadarasz\xparser\XNode($this->html);
	}

	public function _runXParser($selector)
	{
		return count($this->XParser->__invoke($selector)->getElements());
	}
}

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

foreach ($selectors as $selector) {
	$test = new FindTest('fixtures/test1.html');
	$test->run($selector, 1);
}

print_r(FindTest::top());