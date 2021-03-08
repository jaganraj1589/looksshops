<?php
include DIR_SYSTEM.'library/db/Query.php';
class DB {
	private $db;
	
	private static $log = array();
	private static $logEnabled = false;

	public function __construct($driver, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'DB\\' . $driver;

		if (class_exists($class)) {
			$this->db = new $class($hostname, $username, $password, $database, $port);
		} else {
			exit('Error: Could not load database driver ' . $driver . '!');
		}
	}

	public function query($sql) {
		if(self::$logEnabled) {
			self::$log[] = $sql;
		}
		
		return $this->db->query($sql);
	}
	
	public function multiQuery($sql) {
		return $this->db->multiQuery($sql);
	}

	public function escape($value) {
		return $this->db->escape($value);
	}

	public function countAffected() {
		return $this->db->countAffected();
	}

	public function getLastId() {
		return $this->db->getLastId();
	}
	
	public function table($table) {
	    $class='db\\' .'QueryBuilder\Query';
		return new $class($table,$this->db);
	}
	
	public static function enableLog() {
		self::$logEnabled = true;
	}
	
	public static function getExecutedQueries() {
		return self::$log;
	}
	
	public static function printExecutedQueries() {
	    foreach(self::$log as $sql) {
            echo '<pre>'.$sql.'</pre>';
        }
	}


	
}
