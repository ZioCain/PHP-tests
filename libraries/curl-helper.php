<?php
function getCurl($url){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $o=curl_exec($ch);
	$error = curl_errno($ch);
        if($error) echo "CURL ERROR: ".$error."\n\n";
        curl_close($ch);
        return $o;
}
