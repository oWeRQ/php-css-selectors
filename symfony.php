<?php

require_once 'vendor/autoload.php';

use Symfony\Component\CssSelector\CssSelectorConverter;

$converter = new CssSelectorConverter();
var_dump($converter->toXPath('div.item > h4 > a'));