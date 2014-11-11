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
		$url=parse_url(getenv("CLEARDB_DATABASE_URL"));

		$dbHost = $url["host"];
		$dbUser = $url["user"];
		$dbPswd = $url["pass"];
		$Database = substr($url["path"],1);

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

			$this->dbConn = mysqli_connect($this->host, $this->userid, $this->pswd);
			
			mysqli_select_db($this->db_name, $this->dbConn);
		}

		return $this->dbConn;
	}
}
?>
