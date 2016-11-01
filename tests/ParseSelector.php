<?php

require_once '../vendor/autoload.php';
require_once 'FindAbstract.php';

class CDomSelectorPublic extends CDomSelector
{
	public $struct;
}

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

class ParseSelector extends FindAbstract
{
	public function runCDom($data, $selector)
	{
		new CDomSelectorPublic($selector);
	}

	public function runPhpSelector($data, $selector)
	{
		return selector_to_xpath($selector);
	}

	public function runPhpQuery($data, $selector)
	{
		$phpQuery = new \phpQueryObjectPublic;
		$phpQuery->parseSelector($selector);
	}

	public function runPQuery($data, $selector)
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

	public function runQueryPath($data, $selector)
	{
		$handler = new \QueryPath\CSS\Selector;
		$parser = new \QueryPath\CSS\Parser($selector, $handler);
		$parser->parse();
	}

	public function runSimpleHtmlDom($data, $selector)
	{
		$node = new \simple_html_dom_node_public(new \simple_html_dom);
		$node->parse_selector($selector);
	}

	public function before_runSymfony()
	{
		return new \Symfony\Component\CssSelector\CssSelectorConverter;
	}

	public function runSymfony($converter, $selector)
	{
		return $converter->toXPath($selector);
	}

	public function runZend($data, $selector)
	{
		return \Zend\Dom\Document\Query::cssToXpath($selector);
	}

	public function runSelectorHelper($data, $selector)
	{
		return \Parse\SelectorHelper::toXPath($selector);
	}

	public function runSelectorHelperPlain($data, $selector)
	{
		return \Parse\SelectorHelper::toXPathPlain($selector);
	}

	public function runPhpCss($data, $selector)
	{
		return \PhpCss::toXpath($selector);
	}
}
