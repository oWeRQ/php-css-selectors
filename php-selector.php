<?php

require_once 'vendor/autoload.php';

new SelectorDOM(null); // force autoload

var_dump(selector_to_xpath('div.item > h4 > a'));