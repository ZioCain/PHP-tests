#!/usr/bin/php
<?php
$argc = count($argv);
if($argc!=2){
        echo "Usage: php ".$argv[0]." FILE\nRemoves all \\n before open curly braces\n";
        exit();
}

$file = fopen($argv[1], "r");
if(!$file){
	die("Can't open file.\n");
}
fclose($file);
echo preg_replace("/}\n\s*else/","}else", preg_replace("/\n\s*{/",'{', file_get_contents($argv[1])) );
