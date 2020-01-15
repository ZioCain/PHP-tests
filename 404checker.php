<?php
$argc = count($argv);
if($argc<2 || $argc>3){
	echo "Usage: php ".$argv[0]." URL FILE\nURL must have protocol (http or https)\nFILE must be a list of urls to be added to the url (like /index.html)\n";
	exit();
}

function getStatus($url){
	$ch=curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//	curl_setopt($ch, CURLOPT_CAINFO, "/Users/michelecastelletti/Documents/cacert.pem");
	curl_exec($ch);
	$error = curl_errno($ch);
	if($error) echo "CURL ERROR: ".$error."\n\n";
	$http_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
	curl_close($ch);
	return $http_code;
}
echo "\n\t404 URL checker\n\n";
$url = $argv[1];
$file = $argv[2];

if($argc===2){
	$url = "";
	$file = $argv[1];
}

if (($handle = fopen($file, "r")) === FALSE) {
	echo "Could not open urls file.\nStop.\n";
	exit();
}
echo "File was opened.\nStarting process...\n";
$row = 0;
$good = 0;
$bad = 0;
while (($data = fgets($handle)) !== FALSE) {
	++$row;
	$full_url = $url.trim($data);
	$status = getStatus($full_url);
    switch($status){
		case 200: // ok
			$good++;
			break;
		default:
			$bad++;
			echo $row.". ".$status." ".$full_url."\n";
	}
}
fclose($handle);
echo "\n\nCOMPLETED PROCESS.\nGOOD urls: $good\nBAD urls: $bad\nTotal urls: $row\n";
