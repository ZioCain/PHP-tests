<?php
function getCurl($url){
	$ch=curl_init();
	$fn=basename($url);
	if(strlen($fn)>30) $fn=substr($fn,0,30);
	$f=fopen($fn,"w");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_FILE, $f);
	$o=curl_exec($ch);
	curl_close($ch);
	fclose($f);
	return $o;
}
if(count($argv)!=2){
	echo "usage: php ".$argv[0]." <file with urls to download>\n";
	exit();
}
$f=fopen($argv[1],"r");
if(!$f){
	echo "Could not open file.\n";
	exit();
}
$fld=basename($argv[1]);
if(!file_exists($fld))
	mkdir($fld);
chdir($fld);
while(($line=fgets($f))!==false){
	$line=trim($line);
	echo $line." ... \n";
	getCurl($line);
}
fclose($f);
?>
