<?php

abstract class FindAbstract
{
	public $parsers;
	public $html;

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

	public function run($selector, $expression, $interations = 1, $desc = null)
	{
		echo "{\n";

		if ($desc) {
			echo "  desc: '{$desc}',\n";
		}

		echo "  selector: '{$selector}',\n  expression: '{$expression}',\n  parsers: {\n";

		foreach ($this->parsers as $parser) {
			echo "    ".str_pad(substr($parser, 3).':', 18)." {";

			$before = microtime(true);

			$beforeMethod = 'before_'.$parser;
			
			$beforeData = (method_exists($this, $beforeMethod) ? $this->$beforeMethod($this->html) : $this->html);

			$start = microtime(true);

			try {
				for ($i = 0; $i < $interations; $i++) {
					$count = $this->$parser($beforeData, $selector, $expression);
				}
			} catch (\Exception $e) {
				$count = 0;
				echo "error: '".$e->getMessage()."', ";
			}

			$end = microtime(true);

			echo "count: $count, ";
			echo "time: ".$this->timeFormat($end - $start).", ";
			echo "before: ".$this->timeFormat($start - $before);
			echo "},\n";

			if (empty(self::$total[$parser]))
				self::$total[$parser] = 0;

			self::$total[$parser] += $end - $before;
		}

		echo "  }\n},\n";
	}

	function timeFormat($seconds)
	{
		return str_replace('0', 'o', number_format($seconds, 3));
	}
}
