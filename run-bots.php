#!/usr/local/bin/php
<?php
if(count($argv)!==4) echo "Usage:\nrun-bots.php <bots amount> <start> <end>";

$bots = $argv[1];
$start = $argv[2];
$end = $argv[3];

$botFile = "run-urls.php";
$botsFolder = "bots/";

$amount = $end - $start;
$amountPerBot = $amount/$bots;

$cwd = getcwd();

for($bot = 0; $bot<$bots; ++$bot){
  echo "STARTING BOT ".($bot+1)."\n";
  if(!file_exists($botsFolder.$bot)) mkdir($botsFolder.$bot);
  copy($botFile, $botsFolder.$bot.'/'.$botFile);
  echo "Executing bot script: ".$botsFolder.$bot."/".$botFile." $start ".intval($start+$amountPerBot);
  chdir($botsFolder.$bot); // move to bot subfolder
  exec('./'.$botFile." $start ".($start+$amountPerBot).' >out &');
  chdir($cwd); // get back to root folder
  $start += intval($amountPerBot);
}
