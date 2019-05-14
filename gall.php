<?php
$countEmpty = true;
$filename = "";
$sum = 0;
$lines = 0;
$non_empty_lines = 0;
switch($argc){
	case 2:
		$filename = $argv[1];
		break;
	case 3:
		$filename = $argv[1];
		$countEmpty = !($argv[2]=="--ignore-empty" || $argv[2]=="-i");
		break;
	default:
		die("\tGet Average Line Length\n\nUsage: `php $argv[0] <filename> [options]`\nOptions:\n\t-i, --ignore-empty: should empty lines be ignored? If this option is set then they will be ignored from the count\n\n");
}
if(!file_exists($filename)){
	die("$filename: no such file\n");
}
$f = fopen($filename,"r");
if(!$f){
	die("$filename could not be opened for reading.\n");
}
while( ($line = fgets($f))!== false ){
	++$lines;
	$len = strlen($line);
	if($len>0){
		++$non_empty_lines;
	}
	$sum += $len;
}
fclose($f);
$sum /= $countEmpty?$lines:$non_empty_lines;
echo $sum.PHP_EOL;
