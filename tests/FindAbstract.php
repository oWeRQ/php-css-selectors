<?php

abstract class FindAbstract
{
	public $parsers = [];
	public $parsersData = [];
	public $parsersTime = [];

	public function __construct($data = null)
	{
		$this->parsers = preg_grep('/^run[A-Z]/', get_class_methods($this));

		echo "{\n";

		foreach ($this->parsers as $parser) {
			$beforeMethod = 'before_'.$parser;

			$startTime = microtime(true);
			
			$this->parsersData[$parser] = (method_exists($this, $beforeMethod) ? $this->$beforeMethod($data) : $data);

			$beforeTime = microtime(true) - $startTime;

			echo "    ".str_pad(substr($parser, 3).':', 18)." {";
			echo "before: ".$this->timeFormat($beforeTime)."},\n";
		}

		echo "},\n";

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

			$beforeData = $this->parsersData[$parser];

			$startTime = microtime(true);

			try {
				for ($i = 0; $i < $interations; $i++) {
					$result = $this->$parser($beforeData, $selector, $expression);
				}
			} catch (\Exception $e) {
				$result = null;
				echo "error: '".$e->getMessage()."', ";
			}

			$runTime = microtime(true) - $startTime;

			echo "run: ".$this->timeFormat($runTime).", ";
			echo "result: ".json_encode($result);
			echo "},\n";

			if (empty($this->parsersTime[$parser]))
				$this->parsersTime[$parser] = 0;

			$this->parsersTime[$parser] += $runTime;
		}

		echo "  }\n},\n";
	}

	function timeFormat($seconds)
	{
		return number_format($seconds, 3);
	}

	public function getTop()
	{
		$top = [];
		asort($this->parsersTime);
		foreach ($this->parsersTime as $parser => $time) {
			$top[substr($parser, 3)] = round($time, 3);
		}
		return json_encode($top, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}

	public function resetTop()
	{
		$this->parsersTime = [];
	}
}
