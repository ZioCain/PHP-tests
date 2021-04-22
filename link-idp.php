<?php
include_once "libraries/db.php";
try{
	$db = new DB("127.0.0.1", "safetyjob_idp", "root", "root");
}catch(Exception $e){
	die ("Errore di connessione al server: ".$e->getMessage()."\n");
}

define( 'INPUT_CSV_FILE', '/Users/michelecastelletti/Downloads/export_Cantieri-Imprese.csv' );

$input = fopen(INPUT_CSV_FILE, 'r');

if(!$input){
	die('No file.');
}
$row=0;
while (($data = fgetcsv($input, 1000, ",")) !== FALSE) {
	// get impresa id
	$impresa = $db->SelectFirst('id', 'workspaces', 'piva like "%'.$data[0].'%"');
	// if not found by piva, use name
	if($impresa===FALSE){
		$impresa = $db->SelectFirst('id', 'workspaces', 'name like "%'.$data[2].'%"');
	}
	$constr = $db->SelectFirst('id', 'constructions', 'name like "%'.$data[1].'%"');
	if($impresa===FALSE || $constr===FALSE){
		echo "ROW: $row CANNOT CONNECT: ".$data[0]." - ".$data[1]."\n";
	}else{
		//*
		try{
			$db->Insert(
				'construction_has_supplier',
				[
					'construction_id' => $constr->id,
					'workspace_id' => $impresa->id
				]
			);//*/
		}catch(Exception $e){
			echo "ROW: $row CANNOT CONNECT (duplicate): ".$data[0]." - ".$data[1]." - ".$data[2]."\n";
		}
	}
	$row++;
}
fclose($input);