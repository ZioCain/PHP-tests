<?php
$argc = count($argv);
if($argc!=2){
	echo "You need to specify the filename containing the URLs to analyze";
	exit();
}
$file = fopen($argv[1], "r");
if(!$file) die("Can't open file.");
while( ($data=fgets($file))!==FALSE ){
	$hasDomain = strpos($data, "http");
	if($hasDomain===false){
		$data = "http://".$data;
	}
	$domainSlash = strpos($data, "/", 8);
	if($domainSlash==0) echo $data;
	else echo substr($data, 0, $domainSlash)."\n";
}
fclose($file);
