<?php

require_once 'vendor/autoload.php';

use pQuery\CSSQueryTokenizer;

$query = 'div.item > h4 > a';

$parser = new CSSQueryTokenizer;
$parser->setDoc($query);
$parser->setPos(0);

$start = 0;
$token = $parser->token;

do {
	$end = $parser->getPos();

	$str = substr($query, $start, $end - $start + 1);

	echo "pos: $start-$end, str: \"$str\", token: $token\n";

	$start = $end + 1;
//} while ($token = $parser->next_no_whitespace());
} while ($token = $parser->next());