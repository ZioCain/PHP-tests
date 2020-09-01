<?php
class ZDB{
	var $file, $max_row_length, $operations_file, $log;
	function __construct($f="main.zdb", $mrl=1000){
		$this->file = $f;
		$this->max_row_length = $mrl;
		$this->operations_file = "ops.zdb";
		$this->log = false;
	}
	public static function Matching($pattern, $json){
		$matching = true;
		foreach($pattern as $key=>$val){
			if(!isset($json->$key) || $json->$key !== $val){
				$matching = false; break;
			}
		}
		return $matching;
	}
	function Insert($data){
		$type = gettype($data);
		if($type!=='array' && $type!=='object') throw new Exception('Data must be array or object.');
		$enc = json_encode($data);
		if(strlen($enc)>$this->max_row_length) throw new Exception('Data is too long, either shrink the data or change the MAX_ROW_LENGTH');
		file_put_contents($this->file, $enc."\n", FILE_APPEND);
	}
	function FindFirst($pattern){
		$type = gettype($pattern);
		$output = [];
		if($type!=='array' && $type!=='object') throw new Exception('Pattern must be array or object.');
		if (($handle = fopen($this->file, "r")) !== FALSE) {
		    while (($data = fgets($handle, $this->max_row_length)) !== FALSE) {
				$json = json_decode($data);
				if(ZDB::Matching($pattern, $json)){
					fclose($handle);
					return $data;
				}
		    }
		    fclose($handle);
			return null;
		}else{
			throw new Exception("Cannot find DB file. (".$this->file.")");
		}
	}
	function Find($pattern, $limit=0){
		$type = gettype($pattern);
		if($type!=='array' && $type!=='object') throw new Exception('Pattern must be array or object.');
		$output = [];
		if (($handle = fopen($this->file, "r")) !== FALSE) {
		    while (($data = fgets($handle, $this->max_row_length)) !== FALSE) {
				$json = json_decode($data);
				if(ZDB::Matching($pattern, $json)) $output[]=$data;
		    }
		    fclose($handle);
			return $output;
		}else{
			throw new Exception("Cannot find DB file. (".$this->file.")");
		}
	}
	function Truncate(){
		file_put_contents($this->file, "");
	}
	function Update($data, $pattern, $limit=0){
		$type = gettype($pattern);
		if($type!=='array' && $type!=='object') throw new Exception('Pattern must be array or object.');
		$type = gettype($data);
		if($type!=='array' && $type!=='object') throw new Exception('Pattern must be array or object.');

		$ops = fopen($this->operations_file, "w");
		$main = fopen($this->file, "r");

		$updated = 0;

		while( ($row=fgets($main, $this->max_row_length)) !== FALSE ){
			if(ZDB::Matching($pattern, $json = json_decode($row))){
				// update and then save
				foreach($data as $key=>$val){
					$json->$key = $val;
				}
				$row = json_encode($json)."\n";
				$updated ++;
				if($updated == $limit) break;
			}
			fwrite($ops, $row, $this->max_row_length);
		}

		fclose($main);
		fclose($ops);
		unlink($this->file);
		rename($this->operations_file, $this->file);
		return $updated;
	}
	function Delete($pattern, $limit=0){
		$type = gettype($pattern);
		if($type!=='array' && $type!=='object') throw new Exception('Pattern must be array or object.');

		$ops = fopen($this->operations_file, "w");
		$main = fopen($this->file, "r");

		$deleted = 0;

		while( ($row=fgets($main, $this->max_row_length)) !== FALSE ){
			// if not matching, then copy
			if(!ZDB::Matching($pattern, json_decode($row))){
				fwrite($ops, $row, $this->max_row_length);
			}else{
				$deleted++;
				if($deleted == $limit) break;
			}
		}

		fclose($main);
		fclose($ops);
		unlink($this->file);
		rename($this->operations_file, $this->file);
		return $deleted;
	}
}
