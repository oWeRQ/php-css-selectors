<?php

require_once '../vendor/autoload.php';
require_once 'FindAbstract.php';

class FindText extends FindAbstract
{
	public function processResult($result)
	{
		return [
			'count' => count($result),
			'len' => strlen(preg_replace('/\s+/m', '', implode($result))),
		];
	}

	/**
	 * XPath
	 */
	public function before_runXPath($html)
	{
		return \Parse\DOMHelper::htmlXPath($html, false);
	}

	public function runXPath(\DOMXPath $xpath, $selector, $expression)
	{
		$result = [];
		foreach ($xpath->query($expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($xpath->query($expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($xpath->query($expression, $xpath->document) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($xpathQuery->xpath($expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($parseQuery->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($simpleXML->xpath($expression) as $node) {
			$result[] = trim((string)$node);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($zendDom->execute($selector) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($zendDom->queryXpath($expression, $selector) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		$domCrawler->filter($selector)->each(function($node) use(&$result){
			$result[] = trim($node->text());
		});
		return $this->processResult($result);
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
		$result = [];
		$domCrawler->filterXPath($expression)->each(function($node) use(&$result){
			$result[] = trim($node->text());
		});
		return $this->processResult($result);
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
		$result = [];
		foreach ($fluentDOM->find($expression) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($fluentDOM->find($selector) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($fluentDOM->find($selector) as $node) {
			$result[] = trim($node->textContent);
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($cDom->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
	}

	/**
	 * SimpleHtmlDom
	 */
	public function before_runSimpleHtmlDom($html)
	{
		return str_get_html($html);
	}

	public function runSimpleHtmlDom(\simple_html_dom $simpleHtmlDom, $selector, $expression)
	{
		$result = [];
		foreach ($simpleHtmlDom->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($phpQuery[$selector] as $node) {
			$result[] = trim(pq($node)->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($queryPath->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($queryPath->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($queryPath->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($pQuery->query($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
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
		$result = [];
		foreach ($xParser->find($selector) as $node) {
			$result[] = trim($node->text());
		}
		return $this->processResult($result);
	}
}
