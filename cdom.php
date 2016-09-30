<?php

$selector = 'div.item > h4 > a';

// https://github.com/amal/CDom

require_once 'vendor/cdom/CDom.php';

class CDomSelectorPublic extends CDomSelector
{
	public $struct;
}

$cDomSelector = new CDomSelectorPublic($selector);

var_dump($cDomSelector->struct);