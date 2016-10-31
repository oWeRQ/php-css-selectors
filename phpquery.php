<?php

require_once 'vendor/autoload.php';

//phpQuery::$debug = true;

class phpQueryObjectPublic extends phpQueryObject
{
	public $XQuery = [];

	public function runQuery($XQuery, $selector = null, $compare = null) {
		$this->XQuery[] = $XQuery;
		return parent::runQuery($XQuery, $selector, $compare);
	}
}

$documentID = phpQuery::newDocument('<div class="item"><h4><a href="#"></a></h4></div>')->getDocumentID();
$pqo = new phpQueryObjectPublic($documentID);
$pqo->find('div.item > h4 > a');
var_dump($pqo->XQuery);
