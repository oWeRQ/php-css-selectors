<?php

require_once 'vendor/autoload.php';

//$options = 0;
$options = PhpCss\Ast\Visitor\Xpath::OPTION_EXPLICIT_NAMESPACES;

var_dump(PhpCss::toXpath('div.item > h4 > a', $options));