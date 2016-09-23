<?php

// https://github.com/TobiaszCudnik/phpquery

require_once('vendor/phpquery/phpQuery/phpQuery.php');
require_once('vendor/phpquery/phpQuery/phpQuery/phpQueryObject.php');

class phpQueryObjectPublic extends phpQueryObject
{
	public function __construct() {}

	public function parseSelector($query) {
		return parent::parseSelector($query);
	}
}

$pqo = new phpQueryObjectPublic;
var_dump($pqo->parseSelector('div.item > h4 > a'));
