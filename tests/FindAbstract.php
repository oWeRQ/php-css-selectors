<?php

abstract class FindAbstract
{
	public $parsers;
	public $data;

	public static $jsonOptions = 448; // JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
	public static $total = [];

	public static function top()
	{
		$top = [];
		asort(self::$total);
		foreach (self::$total as $parser => $time) {
			$top[substr($parser, 3)] = round($time, 3);
		}
		return json_encode($top, self::$jsonOptions);
	}

	public static function resetTop()
	{
		self::$total = [];
	}

	public function __construct($data = null)
	{
		$this->parsers = preg_grep('/^run[A-Z]/', get_class_methods($this));
		$this->data = $data;

		sort($this->parsers);
	}

	public function run($selector, $expression = null, $interations = 1, $desc = null)
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
			
			$beforeData = (method_exists($this, $beforeMethod) ? $this->$beforeMethod($this->data) : $this->data);

			$start = microtime(true);

			try {
				for ($i = 0; $i < $interations; $i++) {
					$result = $this->$parser($beforeData, $selector, $expression);
				}
			} catch (\Exception $e) {
				$result = 0;
				echo "error: '".$e->getMessage()."', ";
			}

			$end = microtime(true);

			echo "time: ".$this->timeFormat($end - $start).", ";
			echo "before: ".$this->timeFormat($start - $before).", ";
			echo "result: ".json_encode($result);
			echo "},\n";

			if (empty(self::$total[$parser]))
				self::$total[$parser] = 0;

			self::$total[$parser] += $end - $before;
		}

		echo "  }\n},\n";
	}

	function timeFormat($seconds)
	{
		return number_format($seconds, 3);
	}
}
