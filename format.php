<?php
$f = fopen("input.txt","r");
while(($line = fgets($f)) !== false){
	$final = str_replace(["- ","\n"],"",$line);
	if(preg_match("/^\d+\.\D/",$line)){
		$final = "<b>$final</b>";
	}
	$final.="<br>\n";
	echo $final;
}
