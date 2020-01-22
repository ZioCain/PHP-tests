<?php
$argc = count($argv);
if($argc!=2){
	echo "Must specify file with URLs";
	exit();
}
$file = fopen($argv[1], "r");
if(!$file) die("Can't open file");
include "libraries/curl-helper.php";
while( ($data=fgets($file, 4096))!==FALSE ){
	$data = trim($data);
	$js = json_decode( getCurl($data."/wp-json/wp/v2/posts") );
	if($js===NULL || $js===FALSE) continue;
	// if respose is JSON and an array (meaning there's no error) then it's very likely it works
	if( gettype($js) === "array") echo $data."\n";
}
fclose( $file );
