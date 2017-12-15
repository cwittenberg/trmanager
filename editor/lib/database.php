<?php

	function checkExists($table) {
		global $db_name;
		$rows = query2Array("select table_name from information_schema.tables where table_schema='{$db_name}' and table_name='{$table}'");
		
		foreach($rows as $r) {
			if($r["table_name"] == $table)
				return true;
		}
		
		return false;
	}
	
	// connect with pdo 
	try {
		$dbh = new PDO("mysql:host=$db_host;dbname=$db_name;", $db_user, $db_pass);
	}
	catch(PDOException $e) {
		die('pdo connection error: ' . $e->getMessage());
	}		
	
	function isAdmin() {
		return $_SESSION['user']['isAdmin']==1;
	}
	
	function registerEvent($category, $description) {
		$IP = $_SERVER['REMOTE_ADDR'];
		$UID= 0;		
		if(isset($_SESSION) && isset($_SESSION['user']['ID'])) {			
			$UID= $_SESSION['user']['ID'];
		}
		
		sql("insert into audit (userID,IP,category,description,time) values (:uid, :ip, :cat, :desc,now())", array("uid"=>$UID, "ip"=>$IP, "cat"=>$category, "desc"=>$description));
	}
	
	function sql($sql, $binds=array()) {
		try {
			global $dbh;		
			$sql = $dbh->prepare($sql);
			
			if(sizeof($binds)>0) {
				foreach($binds as $k=>$v) {
					$sql->bindValue(":" . $k, $v);
				}
			}
			
			return $sql->execute();			
			
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}

	function isRunning($pid) {
		return file_exists("/proc/{$pid}");
	}

	function query2Array($sql, $binds=array()) {
		try {
			global $dbh;		
			$sql = $dbh->prepare($sql);
			
			if(sizeof($binds)>0) {
				foreach($binds as $k=>$v) {
					$sql->bindValue(":" . $k, $v);
				}
			}
			
			$sql->execute();
			
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			die($e->getMessage());
		}
	}
	
	function getConfigValue($key) {
			$row = query2Array("select configurationValue from settings where configurationName=:key", array('key' => $key));
			return $row[0]['configurationValue'];
	}	
	

	function columns($tblName) {
		global $db_name;
		return query2Array("SELECT column_name,data_type FROM information_schema.COLUMNS WHERE table_schema='{$db_name}' and table_name='" . $tblName . "'");
	}	
	
	
	function arrayToCSV($array, $filename = "export.csv", $delimiter=";") {
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.$filename.'";');

		$f = fopen('php://output', 'w');

		foreach ($array as $line) {
			fputcsv($f, $line, $delimiter);
		}
	}   
?>
