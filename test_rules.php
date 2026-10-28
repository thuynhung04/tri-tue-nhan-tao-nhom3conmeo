<?php
require 'c:/xampp/htdocs/expert-system/api/rules.php';
$rules = getRules();
echo 'Tong so luat: ' . count($rules) . PHP_EOL;
foreach ($rules as $r) {
    echo '[' . $r['id'] . '] ' . $r['name'] . PHP_EOL;
}
