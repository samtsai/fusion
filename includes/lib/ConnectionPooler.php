<?php
class ConnectionPooler {
	private static $poolerInstance;
	private $dbConn;
	private $config;
	private $connAttempts;

	private $host;
	private $userid;
	private $pswd;
	private $db_name;

	protected function __construct() {

		$dbHost = 'localhost';
		$dbUser = 'fusion';
		$dbPswd = 'shirley';
		$Database = 'fusion';

		$this->connAttempts = 0;
		$this->host = $dbHost;
		$this->userid = $dbUser;
		$this->pswd = $dbPswd;
		$this->db_name = $Database;
	}

	public static function pooler() {
		if (self :: $poolerInstance == null)
			self :: $poolerInstance = new ConnectionPooler();

		return self :: $poolerInstance;
	}

	public function getConnection() {
		
		if (!$this->dbConn) {
			$this->connAttempts++;
			//echo "Connecting: attempt " . $this->connAttempts . "\n";

			$this->dbConn = mysql_connect($this->host, $this->userid, $this->pswd, true);

			if (!$this->dbConn)
				die("Could not make connection to the db server.");

			mysql_select_db($this->db_name, $this->dbConn);

			if (mysql_error())
				die("Could not select database once connected to the db server.");
		}

		return $this->dbConn;
	}
}
?>