<?php
class DB{
	private $db = null;
	public $log = false;
	// log current query to standard file
	private function qlog($query){
		file_put_contents(__DIR__.'/queries.txt', $query."\n", FILE_APPEND);
		$err = $this->db->errorInfo();
		if(intval($err[0])!=0) // if there's an error
			file_put_contents(__DIR__.'/queries.txt', print_r($err,1), FILE_APPEND);
	}
	public function __construct($host, $dbname, $user, $pass){
		try {
			$this->db = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage()."\n";
			throw $e;
		}
	}
	// insert into database
	// `table` must be the exact name of the table in the database
	// `values` must be an association
	// returns last insert ID
	public function Insert($table, $values){
		if(gettype($values)!=="array") throw new Exception("Values must be an array or an association", 4000);
		$columns = "";
		$vals = "";
		$s = "";
		foreach($values as $k=>$v){
			$columns .= $s.'`'.$k.'`';
			$vals .= $s.'?';
			$s=',';
		}
		$stmt = $this->db->prepare("INSERT INTO `$table` ($columns) VALUES ($vals)");
		$c = 1;
		foreach($values as $k=>$v){
			if(is_null($v)) $v = NULL;
			$stmt->bindValue($c++, $v);
		}
		if($this->log){
			$this->qlog(print_r($values,1));
			$this->qlog($stmt->queryString);
		}
		$stmt->execute();
		return $this->db->lastInsertId();
	}
	// replaces data in database
	// `table` must be the exact name of the table in the database
	// `values` must be an association
	// returns last insert ID (might be null)
	public function Replace($table, $values){
		if(gettype($values)!=="array") throw new Exception("Values must be an array or an association", 4000);
		$columns = "";
		$vals = "";
		$s = "";
		foreach($values as $k=>$v){
			$columns .= $s.'`'.$k.'`';
			$vals .= $s.'?';
			$s=',';
		}
		$stmt = $this->db->prepare("REPLACE INTO `$table` ($columns) VALUES ($vals)");
		$c = 1;
		foreach($values as $k=>$v){
			if(is_null($v)) $v = NULL;
			$stmt->bindValue($c++, $v);
		}
		if($this->log){
			$this->qlog(print_r($values,1));
			$this->qlog($stmt->queryString);
		}
		$stmt->execute();
		return $this->db->lastInsertId();
	}
	// updates data in database
	// `table` must be the exact name of the table in the database
	// `values` must be an association
	// `where` must be a string, it is optional and therefore be considered an empty string => no condition
	public function Update($table, $values, $where=''){
		if(gettype($values)!=="array") throw new Exception("Values must be an array or an association", 4000);
		$vals = "";
		$s = "";
		foreach($values as $k=>$v){
			$vals .= $s."`$k`=?";
			$s=',';
		}
		$stmt = $this->db->prepare("UPDATE `$table` SET $vals ".($where!==''?"WHERE $where":''));
		$c = 1;
		foreach($values as $k=>$v){
			if(is_null($v)) $v = "NULL";
			$stmt->bindValue($c++, $v);
		}
		if($this->log){
			$this->qlog(print_r($values,1));
			$this->qlog($stmt->queryString);
		}
		$stmt->execute();
	}
	// deletes data from given table
	// `table` must be the exact name of the table in the database
	// `where` must be a string, it is optional and therefore be considered an empty string => no condition
	public function Delete($table, $where){
		$stmt = $this->db->prepare('DELETE FROM `'.$table.'` WHERE '.$where);
		if($this->log) $this->qlog($stmt->queryString);
		$stmt->execute();
	}
	// executes query made from parameters
	// `where` must be a string, it is optional and therefore be considered an empty string => no condition
	public function Select($fields, $from, $where="", $groupBy="", $having="", $orderBy="", $limit=""){
		$query = "SELECT $fields FROM $from";
		if($where!=="") $query.= " WHERE $where";
		if($groupBy!=="") $query.= " GROUP BY $groupBy";
		if($having!=="") $query .= " HAVING $having";
		if($orderBy!=="") $query .= " ORDER BY $orderBy";
		if($limit!=="") $query .= " LIMIT $limit";
		if($this->log) $this->qlog($query);
		return $this->db->query($query)->fetchAll(PDO::FETCH_OBJ);
	}
	public function SelectFirst($fields, $from, $where="", $groupBy="", $having="", $orderBy=""){
		$query = "SELECT $fields FROM $from";
		if($where!=="") $query.= " WHERE $where";
		if($groupBy!=="") $query.= " GROUP BY $groupBy";
		if($having!=="") $query .= " HAVING $having";
		if($orderBy!=="") $query .= " ORDER BY $orderBy";
		$query .= " LIMIT 1";
		if($this->log) $this->qlog($query);
		$results = $this->db->query($query)->fetchAll(PDO::FETCH_OBJ);
		if($results) return $results[0];
		return FALSE;
	}
	public function Count($from, $where=''){
		$query = "SELECT count(*)as conteggio FROM $from";
		if($where!=="") $query.= " WHERE $where";
		if($this->log) $this->qlog($query);
		$results = $this->db->query($query)->fetchAll(PDO::FETCH_OBJ);
		if($results) return $results[0]->conteggio;
		return FALSE;
	}
	function CheckErrors(){
		$this->db->errorInfo();
	}
	public function Query($query){
		if($this->log) $this->qlog($query);
		$results = $this->db->query($query);
		if(!$results) return null;
		return $results->fetchAll(PDO::FETCH_OBJ);
	}
	public function AlreadyExists($table, $data){
		$where = ''; $c = '';
		foreach($data as $k=>$v){
			$where .= $c." $k = '$v'";
			$c = ' AND ';
		}
		return $this->SelectFirst('*', $table, $where);
	}
}