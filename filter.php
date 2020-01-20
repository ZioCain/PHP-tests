<?php
/*
SHOULD CHECK:
- mime, parameter: "--mime,-m"
- filesize, parameter: "--size,-s"
- width, parameter: "--width,-w"
- height, paramter: "--height,-h"
- words, parameter: "--words,-c"
- lines, parameter: "--lines,-l"

EXTRA PARS:
- output: -o <filename>
- append: -a <filename> // if -a, -o ignored/overwritten
- create a file for each mime type: -d // no other parameter is taken if this is defined
*/
function getCurl($url){
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	$o=curl_exec($ch);
	curl_close($ch);
	return $o;
}
function fetch_headers($header, $headers){
	$hdrL = strlen($header);
	foreach($headers as $hdr){
		if(substr($hdr,0,$hdrL)==$header){
			return substr($hdr,$hdrL+2);
		}
	}
}
function check_mime($given, $test){
	if(strpos($given, "*")===FALSE){
		if(strpos($test,";")===false)
			return $given === substr($test, 0, strpos($test,";"));
		return $given === $test;
	}
	if($given==="*") return true;
	if(substr($given,-1)==="*"){
		return substr($given,0,-2) === substr($test, 0, strpos($test,"/"));
	}
	return false;
}

if(count($argv)!=3){
	echo "usage: php ".$argv[0]." <file with urls to download> <mime-type>\n";
	exit();
}
$f=fopen($argv[1],"r");
if(!$f){
	echo "Could not open file.\n";
	exit();
}
$mimetype = $argv[2];
$lines = 0;
$matching = 0;
while(($line=fgets($f))!==false){
	$line=trim($line);
	fwrite(STDERR, "\rLine: ".++$lines.", matching: $matching");
	$ret = array_reverse(explode("\n",getCurl($line)));
	if(check_mime($mimetype, fetch_headers("Content-Type", $ret))){
		echo $line.PHP_EOL;
		++$matching;
	}
}
fclose($f);
?>
