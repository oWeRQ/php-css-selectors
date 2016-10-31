<?php

require_once 'vendor/autoload.php';

class simple_html_dom_node_public extends simple_html_dom_node
{
	public function parse_selector($selector_string) {
		return parent::parse_selector($selector_string);
	}
}

$node = new simple_html_dom_node_public(new simple_html_dom);

var_dump($node->parse_selector('div.item > h4 > a'));