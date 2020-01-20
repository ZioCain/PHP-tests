<?php
$f = fopen("input.csv","r");
$fout = fopen("output.csv","w");
$row = 0;
while( ($data=fgetcsv($f,1000,";")) !== FALSE ){
	++$row;
	if($row===1) continue;
	$names = explode(" ",$data[0]);
	$data[1] = $names[count($names)-1];
	$data[0] = $names[0];
	if(count($names)>2){
		if(strlen($names[1])>3){
			$data[0].=" ".$names[1];
		}else{
			$data[1]=$names[1]." ".$data[1];
		}
	}
	fputcsv($fout,$data,';');
}
