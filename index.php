<?php

require_once('Parser.php');

//$a = new Parser('data/xml.xml', 'conf/config.php');
//$a = new Parser('data/yml.yml');
$a = new Parser('data/csv.csv');
print_r($a->parse());
