<?php

require_once 'vendor/autoload.php';

use QueryPath\CSS\Selector;
use QueryPath\CSS\Parser;

$handler = new Selector();
$parser = new Parser('div.item > h4 > a', $handler);
$parser->parse();

//var_dump($handler);

foreach ($handler as $selectorGroup) {
	var_dump($selectorGroup);
}