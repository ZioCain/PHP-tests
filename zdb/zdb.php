<?php
// this is used to manage DB in a weird way.
include_once 'lib.php';
$db = new ZDB();
$db->Insert(['ID'=>1415, 'name'=>'mike']);
print_r($db->Find(['ID'=>142]));

echo "Updated: ".$db->Update(['name'=>'miko'], ['ID'=>142], 1).PHP_EOL;
echo "Deleted: ".$db->Delete(['ID'=>145]).PHP_EOL;
