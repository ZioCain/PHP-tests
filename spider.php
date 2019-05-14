<?php
set_time_limit(0); // don't ever end this bc of time

if(!file_exists("results"))
	mkdir("results");
chdir("results");
echo "PWD: ".getcwd()."\n";
$start="http://google.com/search?q=";
echo "Type the word to search: ";
$line=readline();
$query=urlencode($line);
if(!file_exists($line))
	mkdir($line);
chdir($line);
echo "Getting ".$start.$query." ...\n";

function getCurl($url){
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	$o=curl_exec($ch);
	curl_close($ch);
	return $o;
}

$res=getCurl($start.$query);
// file_put_contents("../startingpoint.html",$res);

$linksN=0;
$history=[$start.$query];
$nextRes=[];
$from="google";
$iterations=0;
do{
	$iterations++;
	echo "Analysing document...\n";
	preg_match_all("/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[A-Z0-9+&@#\/%=~_|$])/i", $res, $links);
	$links=$links[0];
	$currN=count($links);
	echo "Found $currN links, saving to file\n";
	foreach($links as $l){
		file_put_contents($from,$l."\n", FILE_APPEND);
		$nextRes[]=$l;
	}

	// prepare for next round
	$nextRes = array_unique($nextRes);
	$linksN=count($nextRes);
	do{
		$nextUrl = array_shift($nextRes);
	}while($nextUrl=="" || in_array($nextUrl, $history));
	echo $iterations.".\nNext URL: ".$nextUrl."\nRemaining links: ".$linksN."\n";
	$parse=parse_url($nextUrl);
	$from=$parse['host'];
	if($from==""){
		$from="unknown";
	}
	$res=getCurl($nextUrl);
	echo "From: $from\n";
}while($linksN!=0);

echo "Analyzed $iterations URLS\n";
?>
