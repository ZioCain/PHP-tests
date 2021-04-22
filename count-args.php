#!/usr/local/bin/php
<?php
$k=0;
foreach($argv as $arg){
	echo ++$k." => ".$arg."\n";
}
