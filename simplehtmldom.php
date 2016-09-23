<?php

// https://sourceforge.net/projects/simplehtmldom/files/

require_once 'vendor/simplehtmldom/simple_html_dom.php';

class simple_html_dom_node_public extends simple_html_dom_node
{
	public function parse_selector($selector_string) {
		return parent::parse_selector($selector_string);
	}
}

$node = new simple_html_dom_node_public(new simple_html_dom);

var_dump($node->parse_selector('div.item > h4 > a'));