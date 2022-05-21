<?php

class Database {
	public function dbConnect() {
		try {
			$db = new PDO("mysql:host=".HOST.";dbname=".DATABASE.";charset=utf8", USER, PASSWORD, [
				PDO::ATTR_EMULATE_PREPARES => false, 
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			]);
			
		} catch (PDOException $error) {
			echo $error->getMessage();
			exit('Database error');
		}
		return $db;
	}
}