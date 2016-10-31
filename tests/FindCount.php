<?php

require_once '../vendor/autoload.php';
require_once 'FindAbstract.php';

class FindCount extends FindAbstract
{
	/**
	 * XPath
	 */
	public function before_runXPath($html)
	{
		return \Parse\DOMHelper::htmlXPath($html, false);
	}

	public function runXPath(\DOMXPath $xpath, $selector, $expression)
	{
		return $xpath->query($expression)->length;
	}

	/**
	 * XPathUtf8
	 */
	public function before_runXPathUtf8($html)
	{
		return \Parse\DOMHelper::htmlXPath($html);
	}

	public function runXPathUtf8(\DOMXPath $xpath, $selector, $expression)
	{
		return $xpath->query($expression)->length;
	}

	/**
	 * XPathDocument
	 */
	public function before_runXPathDocument($html)
	{
		return \Parse\DOMHelper::htmlXPath($html);
	}

	public function runXPathDocument(\DOMXPath $xpath, $selector, $expression)
	{
		return $xpath->query($expression, $xpath->document)->length;
	}

	/**
	 * XPathQuery
	 */
	public function before_runXPathQuery($html)
	{
		$xpath = \Parse\DOMHelper::htmlXPath($html);
		return new \Parse\XPathQuery($xpath->document, $xpath);
	}

	public function runXPathQuery(\Parse\XPathQuery $xpathQuery, $selector, $expression)
	{
		return $xpathQuery->xpath($expression)->count();
	}

	/**
	 * ParseQuery
	 */
	public function before_runParseQuery($html)
	{
		return \Parse\ParseQuery::loadHtml($html);
	}

	public function runParseQuery(\Parse\ParseQuery $parseQuery, $selector, $expression)
	{
		return $parseQuery->find($selector)->length();
	}

	/**
	 * SimpleXML
	 */
	public function before_runSimpleXML($html)
	{
		return new SimpleXMLElement($html);
	}

	public function runSimpleXML(\SimpleXMLElement $simpleXML, $selector, $expression)
	{
		return count($simpleXML->xpath($expression));
	}

	/**
	 * ZendQuery
	 */
	public function before_runZendQuery($html)
	{
		return new \Zend\Dom\Query($html);
	}

	public function runZendQuery(\Zend\Dom\Query $zendDom, $selector, $expression)
	{
		return count($zendDom->execute($selector));
	}

	/**
	 * ZendQueryExpr
	 */
	public function before_runZendQueryExpr($html)
	{
		return new \Zend\Dom\Query($html);
	}

	public function runZendQueryExpr(\Zend\Dom\Query $zendDom, $selector, $expression)
	{
		return count($zendDom->queryXpath($expression, $selector));
	}

	/**
	 * DomCrawler
	 */
	public function before_runDomCrawler($html)
	{
		return new \Symfony\Component\DomCrawler\Crawler($html);
	}

	public function runDomCrawler(\Symfony\Component\DomCrawler\Crawler $domCrawler, $selector, $expression)
	{
		return $domCrawler->filter($selector)->count();
	}

	/**
	 * DomCrawlerExpr
	 */
	public function before_runDomCrawlerExpr($html)
	{
		return new \Symfony\Component\DomCrawler\Crawler($html);
	}

	public function runDomCrawlerExpr(\Symfony\Component\DomCrawler\Crawler $domCrawler, $selector, $expression)
	{
		return $domCrawler->filterXPath($expression)->count();
	}

	/**
	 * FluentDOMExpr
	 */
	public function before_runFluentDOMExpr($html)
	{
		return \FluentDOM::Query($html);
	}

	public function runFluentDOMExpr(\FluentDOM\Query $fluentDOM, $selector, $expression)
	{
		return $fluentDOM->find($expression)->length;
	}

	/**
	 * FluentDOMCssXml
	 */
	public function before_runFluentDOMCssXml($html)
	{
		return \FluentDOM::QueryCss($html, 'text/xml');
	}

	public function runFluentDOMCssXml(\FluentDOM\Query $fluentDOM, $selector, $expression)
	{
		return $fluentDOM->find($selector)->length;
	}

	/**
	 * FluentDOMCssHtml
	 */
	public function before_runFluentDOMCssHtml($html)
	{
		return \FluentDOM::QueryCss($html, 'text/html');
	}

	public function runFluentDOMCssHtml(\FluentDOM\Query $fluentDOM, $selector, $expression)
	{
		return $fluentDOM->find($selector)->length;
	}

	/**
	 * CDom
	 */
	public function before_runCDom($html)
	{
		return \CDom::fromString($html);
	}

	public function runCDom(\CDomDocument $cDom, $selector, $expression)
	{
		return $cDom->find($selector)->length;
	}

	/**
	 * SimpleHtmlDom
	 */
	public function before_runSimpleHtmlDom($html)
	{
		new \simple_html_dom; // force autoload
		return str_get_html($html);
	}

	public function runSimpleHtmlDom(\simple_html_dom $simpleHtmlDom, $selector, $expression)
	{
		return count($simpleHtmlDom->find($selector));
	}

	/**
	 * PhpQuery
	 */
	public function before_runPhpQuery($html)
	{
		return \phpQuery::newDocument($html);
	}

	public function runPhpQuery(\phpQueryObject $phpQuery, $selector, $expression)
	{
		return $phpQuery[$selector]->size();
	}

	/**
	 * QueryPath
	 */
	public function before_runQueryPath($html)
	{
		return \QueryPath::with($html);
	}

	public function runQueryPath(\QueryPath\DOMQuery $queryPath, $selector, $expression)
	{
		return $queryPath->find($selector)->length;
	}

	/**
	 * QueryPathHtml
	 */
	public function before_runQueryPathHtml($html)
	{
		return \QueryPath::withHTML($html);
	}

	public function runQueryPathHtml(\QueryPath\DOMQuery $queryPath, $selector, $expression)
	{
		return $queryPath->find($selector)->length;
	}

	/**
	 * QueryPathHtml5
	 */
	public function before_runQueryPathHtml5($html)
	{
		return \QueryPath::withHTML5($html);
	}

	public function runQueryPathHtml5(\QueryPath\DOMQuery $queryPath, $selector, $expression)
	{
		return $queryPath->find($selector)->length;
	}

	/**
	 * PQuery
	 */
	public function before_runPQuery($html)
	{
		return \pQuery::parseStr($html);
	}

	public function runPQuery(\pQuery\DomNode $pQuery, $selector, $expression)
	{
		return $pQuery->query($selector)->count();
	}

	/**
	 * XParser
	 */
	public function before_runXParser($html)
	{
		return new \gymadarasz\xparser\XNode($html);
	}

	/**
	 * @todo Recursion detected, check later
	 */
	public function _runXParser(\gymadarasz\xparser\XNode $xParser, $selector, $expression)
	{
		return count($xParser->find($selector)->getElements());
	}
}
