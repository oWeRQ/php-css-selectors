<?php

require_once 'vendor/autoload.php';

use Zend\Dom\Document\Query;

var_dump(Query::cssToXpath('div.item > h4 > a'));