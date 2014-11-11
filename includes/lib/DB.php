<?php
require_once ("ConnectionPooler.php");

class DB {
	private $connPooler;
	private $lastQuery;
	private $result;

	public function __construct() {
		$this->connPooler = ConnectionPooler :: pooler();
	}

	public function isConnected() {
		return (!(is_null($this->connPooler->getConnection())) && $this->connPooler->getConnection() !== false);
	}

	public function query($sql) {
		if ($this->isConnected()) {
			$this->lastQuery = $sql;
			$this->result = mysql_query($sql, $this->connPooler->getConnection());
			return $this->result;
		} else {
			return FALSE;
		}
	}

	public function getLastQuery() {
		return $this->lastQuery;
	}

	public function numReturned() {
		if ($this->result != null) {
			return mysql_num_rows($this->result);
		}
		return 0;
	}

	public function numAffected() {
		return mysql_affected_rows($this->connPooler->getConnection());
	}

	public function endQuery() {
		mysql_free_result($this->result);
	}

	public function getRow() {
		//echo "<br /><br />" . $this->lastQuery;
		return mysql_fetch_array($this->result);
	}

	public function lastInsertID() {
		return mysql_insert_id($this->connPooler->getConnection());
	}

	public function escapeString($str) {
		return mysql_escape_string($str);
	}

	public function error() {
		return mysql_error($this->connPooler->getConnection());
	}

	public function getRecords($user_query) {
		
		$user_query = $this->cleanStr($user_query);
		
		if ($this->verifyQuery($user_query, "select")) {
			if ($this->query($user_query)) {
				return $this->result;
			}
			else {
				return false;
			}
		} else
			return false;
	}

	public function getScalar($user_query) {

		//  This function returns a single value from the database. 

		if ($this->getRecords($user_query) && $this->numReturned()) {
			$scalar = stripslashes(trim(mysql_result($this->result, 0)));
			return $scalar;
		} else
			return FALSE;

	} // end of getScalar()

	public function getArray($user_query) {

		//  This function returns a simple array from the database.  

		if ($this->getRecords($user_query) && $this->numReturned()) {
			while ($row = $this->getRow()) {
				$array[] = stripslashes(trim($row[0]));
			}
			return $array;
		} else
			return FALSE; //  Else inform the user that no data was returned by the query

	} // end of getArray()

	public function get2DArray($user_query) {
		//  This function returns an associative array from the database.  

		if ($this->getRecords($user_query) && $this->numReturned()) {
			while ($row = $this->getRow()) {
				$array[stripslashes(trim($row[0]))] = stripslashes(trim($row[1]));
			}
			return $array;
		} else
			return FALSE;
	}

	public function getOneRecord($table, $conditions = '') {
		if ($fields_array = $this->getFields($table)) {
			//print_r($fields_array);
			$numColumns = count($fields_array);
			$one_record = array ();
			//echo "GETTING ONE RECORD: SELECT * FROM $table $conditions <br/>";
			
			if ($this->getRecords("SELECT * FROM $table $conditions")) {
				while ($row = $this->getRow()) {
					for ($i = 0; $i < $numColumns; $i ++) {
						//echo $fields_array[$i] . "-->" . $row[$i] . "<br/>";
						$one_record[$fields_array[$i]] = stripslashes(trim($row[$i]));
					} // end of for loop
				} // end of while loop
				return $one_record;
			} else
				return false;
		} else
			return false;

	} // end of getOneRecord()

	public function insertRecord($user_query) {
		
		$user_query = $this->cleanStr($user_query);

		if ($this->query($user_query)) {
			$new_id = $this->lastInsertID();
			return $new_id;
		} else {
			return FALSE;
		}
	
	}

	public function updateRecord($user_query) {

		$user_query = $this->cleanStr($user_query);
		if ($this->verifyQuery($user_query, 'update')) {
			//	Test to make sure the record is there to update
			$table = substr($user_query, 6, strpos($user_query, 'SET ') - 7);
			$condition = substr($user_query, strpos($user_query, 'WHERE'), strlen($user_query) - 1);

			if (!$this->getOneRecord($table, $condition))
				return FALSE;

			// Execute the query
			if ($this->query($user_query)) {
				return TRUE;
			} else
				return FALSE;
		} else
			return FALSE;

	}

	public function deleteRecord($table, $field, $value) {
		$fields_array = $this->getFields($table);
		if (Arrays :: checkInArray($field, $fields_array)) {
			$query = "SELECT * FROM $table WHERE $field = $value";
			if ($this->getRecords($query) && $this->numReturned()) {
				$del_query = "DELETE FROM $table WHERE $field = $value";
				if ($this->query($del_query))
					return TRUE;
				else
					return FALSE;
			} else
				return FALSE;
		} else
			return FALSE;
	}

	private function verifyQuery($query, $type) {
		/* return FALSE if either query or type are blank or if type is not a valid type */
		if (empty ($query) || empty ($type)) {
			return FALSE;
		}
			
		if ($type == 'select') {
			if (eregi("^SELECT[[:alnum:][:space:][:punct:]]+FROM[[:alnum:][:space:][:punct:]]+$", $query)) {
				return TRUE;
				//echo "<BR><BR>SELECT query is: $tested_query<BR><BR>";
			}
		}
		elseif ($type == 'update') {
			if (eregi("^UPDATE[[:alnum:][:space:]]+SET[[:alnum:][:space:][:punct:]]+$", $query)) {
				return TRUE;
				//echo "<BR><BR>Update query is: $tested_query<BR><BR>";
			}
		}
		elseif ($type == 'insert') {
			if (eregi("^INSERT INTO[[:alnum:][:space:]]+VALUES \([[:alnum:][:space:][:punct:]]+\)$", $query)) {
				return TRUE;
				//echo "<BR><BR>Update query is: $tested_query<BR><BR>";
			}
		}
		return FALSE;
	}

	private function cleanStr($string) {
		/**return trim(stripslashes($string));*/
		return trim($string);
	}

	public function getFields($table) {
		if ($this->query("SHOW COLUMNS FROM $table") && $this->numReturned()) {
			while ($row = $this->getRow()) {
				$field_name = trim($row[0]);
				$fields_array[] = $field_name;
			} // end while loop
			return $fields_array;
		} // end of if fields found
		return FALSE;
	}

}
?>