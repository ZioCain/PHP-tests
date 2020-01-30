#!/usr/bin/php
<?php
$argc = count($argv);
if($argc!=2){
        echo "Usage: php ".$argv[0]." FILE\nPretty print a JSON file\n\n";
        exit();
}

$json = file_get_contents($argv[1]);
if(!$json) die("Coudln't open file.\n");
// print_r(json_decode($json));
$len = strlen($json);
$tabs = 0;
$appendENDL = false;
for($k=0; $k<$len; ++$k){
	switch($json[$k]){
		case "{": if($k>0) $tabs++; $appendENDL=true; break;
		case "}": $tabs--; $appendENDL=true; break;
		case "[": $tabs++; $appendENDL=true; break;
		case "]": $tabs--; $appendENDL=true; break;
		case ",": $appendENDL=true; break;
	}
	echo $json[$k];
	if($appendENDL){
		echo "\n";
		for($t=0; $t<$tabs; ++$t) echo "\t";
	}
	$appendENDL = false;
}
