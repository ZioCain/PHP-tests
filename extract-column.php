<?php
$argc = count($argv);
if($argc<3 || $argc>4){
	echo "\n\tcsv column extractor\n\n\tusage: php ".$argv[0]." FILE.csv <columns> [separator=;]\n\nColumns patterns:".
	"\n1\tSingle number, this will extract just one single column".
	"\n1,2,..\tList of numbers, this will extract specified columns".
	"\n1-3\tRange of numbers, this will extract all the columns in that range\n\n";
	exit();
}
$sep = ";";
if($argc==4) $sep = $argv[3];
//*
$file = $argv[1];
if (($handle = fopen($file, "r")) === FALSE) {
	echo "Could not open source file.\nStop.\n";
	exit();
}//*/
$columns=[];
if(preg_match_all("/[^,]*/", $argv[2], $matches)>0){
	foreach($matches as $match){
		foreach($match as $m){
			if($m=="")continue;
			if(($pos = strpos($m, "-"))!==FALSE){
				$start = intval(substr($m, $start, $pos));
				$end = intval(substr($m, $pos+1));
				for($k=$start; $k<=$end; ++$k) $columns[]=$k;
			}else{
				$columns[]=intval($m);
			}
		}
	}
}else{
	echo "No column to extract.\nStop.\n";
}
while (($data = fgetcsv($handle, 1000, $sep)) !== FALSE) {
	$s = "";
	foreach($columns as $col){
		echo $s.$data[$col];
		$s=$sep;
	}
	echo "\n";
}
fclose($handle);
