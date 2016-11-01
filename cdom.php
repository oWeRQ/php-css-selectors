<?php

$selector = 'div.item > h4 > a';

require_once 'vendor/autoload.php';

CDom::$debug = true;

class CDomSelectorPublic extends CDomSelector
{
	public $struct;
}

$cDomSelector = new CDomSelectorPublic($selector);

var_dump($cDomSelector->struct);