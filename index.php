<?php

require_once('Parser.php');

$a = new Parser('data/xml.xml', 'conf/config.php');
print_r($a->parse());
