<?php

require_once '../vendor/autoload.php';

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

class ParseTest
{
	public static $total = [];

	public static function top()
	{
		asort(self::$total);
		return self::$total;
	}

	public function __construct()
	{
		$this->parsers = preg_grep('/^run[A-Z]/', get_class_methods($this));
		
		sort($this->parsers);
	}

	public function run($selector, $interations = 1000)
	{
		echo "{\n  selector: '$selector',\n  parsers: {\n";

		foreach ($this->parsers as $parser) {
			$before = 'before_'.$parser;
			
			if (method_exists($this, $before))
				$this->$before();
		}

		foreach ($this->parsers as $parser) {
			echo "    ".str_pad(substr($parser, 3).':', 20)." {";

			$start = microtime(true);

			for ($i = 0; $i < $interations; $i++) {
				$this->$parser($selector);
			}

			$time = round(microtime(true) - $start, 4);

			echo "time: $time},\n";

			if (empty(self::$total[$parser]))
				self::$total[$parser] = 0;

			self::$total[$parser] += $time;
		}

		echo "  }\n},\n";
	}

	public function runCDom($selector)
	{
		new CDomSelectorPublic($selector);
	}

	public function before_runPhpSelector()
	{
		new SelectorDOM(null); // force autoload
	}

	public function runPhpSelector($selector)
	{
		selector_to_xpath($selector);
	}

	public function runPhpQuery($selector)
	{
		$phpQuery = new \phpQueryObjectPublic;
		$phpQuery->parseSelector($selector);
	}

	public function runPQuery($selector)
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

	public function runQueryPath($selector)
	{
		$handler = new \QueryPath\CSS\Selector;
		$parser = new \QueryPath\CSS\Parser($selector, $handler);
		$parser->parse();
	}

	public function runSimpleHtmlDom($selector)
	{
		$node = new \simple_html_dom_node_public(new \simple_html_dom);
		$node->parse_selector($selector);
	}

	public function before_runSymfony()
	{
		$this->converter = new \Symfony\Component\CssSelector\CssSelectorConverter;
	}

	public function runSymfony($selector)
	{
		$this->converter->toXPath($selector);
	}

	public function runZend($selector)
	{
		\Zend\Dom\Document\Query::cssToXpath($selector);
	}

	public function runSelectorHelper($selector)
	{
		\Parse\SelectorHelper::toXPath($selector);
	}

	public function runSelectorHelperPlain($selector)
	{
		\Parse\SelectorHelper::toXPathPlain($selector);
	}

	public function runPhpCss($selector)
	{
		\PhpCss::toXpath($selector);
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

print_r(ParseTest::top());