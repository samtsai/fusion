<?php


/**
 * GROUP CLASS
 * The Group class represents any group in the system. It provides all 
 * information pertaining to the group as well as the members of that group. 
 */

class Group { 

	private $group_id;
	private $person_array;
	private $group_name;
	private $end_datetime;
	private $creator_id;


/**
* CONSTRUCTOR 
* Initializes variables in the Group class.
*/

	function __construct($group_id = NULL) {

		$this->group_id = $group_id;
		$this->person_array = array ();
		$this->group_name = "";
		$this->end_datetime = "NULL";
		$this->creator_id = "";

		if ($group_id != NULL)
			$this->initialize();

	} // End of Constructor

	/*  =============================================================
	
	    METHOD SET 1:  SET & GET FUNCTIONS
	
	    =============================================================   */

	// SET METHODS

	public function setGroupID($int) {
		$this->group_id = $int;
	}

	public function setPersonArray($array) {
		$this->person_array = $array;
	}

	public function setGroupName($string) {
		$this->group_name = $string;
	}

	public function setEndDateTime($int) {
		$this->end_datetime = $int;
	}

	public function setCreatorID($int) {
		$this->creator_id = $int;
	}


	public function getGroupID() {
		return $this->group_id;
	}

	public function getPersonArray() {
		return $this->person_array;
	}

	public function getGroupName() {
		return $this->group_name;
	}

	public function getEndDateTime() {
		return $this->end_datetime;
	}

	public function getCreatorID() {
		return $this->creator_id;
	}

/** 
 * INITIALIZE
 * Takes the group id and retrieves all rows that corresponds to the id.
 * Sets all the group object variables with those in the database.
 */

	public function initialize() {

		$group_id = $this->getGroupID();

		if ($group_id == "") {     
			return FALSE;
		}

		$condition = "WHERE group_id = $group_id";

		$db = new DB();
		$group_data = $db->getOneRecord("tblGroup", $condition);

		if (!$group_data) { 
			return FALSE;
		} 
		else { 
			$this->group_name = stripslashes($group_data["group_name"]);
			$this->end_datetime = $group_data["end_datetime"];
			$this->creator_id = $group_data["creator_id"];
		} 
		
		/**Set the person array from PersonGroup*/
		$query = "SELECT person_id, confirmed  FROM tblPersonGroup WHERE group_id = '$group_id'";
		$group_person = $db->get2DArray($query);

		if (!$group_person) {
			$this->person_array = array ();
		} 
		else {
			$this->person_array = $group_person;
		}
		return TRUE;
	} 

	/****************************************************************************
	Name: 	createGroup
	Desc:	Use this to create a new group and add it to tblGroup. 
			Invite group members and add them to tblPersonGroup with confirmed = 0.
			If invited member doesn't have a profile, they are inserted into the DB.
			Emails is sent to all invited members. 
	Note:	
	
	@param  
	@return   
	******************************************************************************/


	public function createGroup() {

		$group_id = $this->getGroupID();
		$persons = $this->getPersonArray();
		$group_name = addslashes($this->getGroupName());
		$end_datetime = $this->getEndDateTime();
		$creator_id = $this->getCreatorID();
		
		if (empty ($group_name) || empty ($end_datetime) || empty ($creator_id)) {
			return FALSE;
		}
		
		$query2 = "INSERT INTO tblGroup VALUES (NULL,'$group_name',$end_datetime, '$creator_id')";

		$db = new DB();
		$result = $db->insertRecord($query2);

		if (is_numeric($result)) {
			$this->group_id = $result; 
		} else { 
			return FALSE;
		}

		$group_id = $this->group_id;

		foreach ($persons as $person_id) {
			/** if the person id is the creator then we automatically set them as confirmed
			 * otherwise they are unconfirmed or confirmed= 0 */
			if ($person_id == $creator_id) { 
				$qryPerson = "INSERT INTO tblPersonGroup VALUES ($person_id, $group_id, 1)";
				$ResultPerson = $db->insertRecord($qryPerson);
			} 
			else { 
				$result = $db->getOneRecord("tblPersonGroup", "WHERE group_id = $group_id AND person_id = $person_id");
				if (is_array($result) && empty ($result)) {
					$qryPerson = "INSERT INTO tblPersonGroup VALUES ($person_id, $group_id, 0)";
					$ResultPerson = $db->insertRecord($qryPerson);

					if ($ResultPerson === FALSE) {
					//	echo "result was REALLY false";
					}
					if (!is_numeric($ResultPerson)) {
					//	echo "result was not a number";
					}
				} /** end if person not already in the group */
			} /** end if person is not the creator */
		} /** end foreach */
		return true;
	} 

	/************************************************************************
	*Name: 	updateGroup
	*Desc:	This function gets the group id of the group we wish to update.
	*		It only updates the group name, and end date. 
	Note:	
	
	@param  
	@return   
	***********************************************************************/

	public function updateGroup() {

		$groupID = $this->getGroupID();
		$groupName = addslashes($this->getGroupName());
		$endDateTime = $this->getEndDateTime();

		if ($groupID == "") { 
			return FALSE;
			echo "no group id";
		}

		$query = "UPDATE tblGroup
										    		SET group_name = '$groupName',
															end_datetime = $endDateTime
															WHERE group_id = $groupID";

		$db = new DB();
		$db->updateRecord($query);

	}

	/**************************************************
	*Name:  joinGroup
	*Desc:	Use this to join a group and set confimed = 1.
			
	*Note:	Updates object's person array.
	
	@param  string[p_id] Person ID of the person logged in.
	@return   
	****************************************************/

	public function joinGroup($p_id) {

		$persons = $this->person_array;
		$g_id = $this->getGroupID();

		foreach ($persons as $person_id => $confirmed) {
			if ($person_id == $p_id) {
				$persons[$person_id] = 1;
			}
			$db = new DB();
			$qryPerson = "UPDATE tblPersonGroup SET confirmed = '1' WHERE person_id = '$p_id' AND group_id = '$g_id'";
			$ResultPerson = $db->updateRecord($qryPerson);

			if (!$ResultPerson) { 
				$errno = 7;
				$this->print_errors($errno);
				return FALSE;
			} 
		} 
		$this->person_array = $persons;
	}

	/**************************************************
	*Name:  leaveGroup
	*Desc:	Use this to leave a group and delete the entry in tblPersonGroup.
			
	*Note:	Updates object's person array.
	
	@param  string[p_id] Person ID of the person logged in.
	@return   
	****************************************************/

	public function leaveGroup($p_id) {

		if ($p_id == "") { 
			return FALSE;
		}

		$g_id = $this->getGroupID();

		if ($g_id == "") { 
			$errno = 1;
			return FALSE;
		}
		$db = new DB();
		$query3 = "DELETE FROM tblPersonGroup WHERE group_id = $g_id && person_id = $p_id";

		$Result3 = $db->query($query3);

		if ($Result3) {
			
			$resetGroupPrefQuery = "UPDATE tblPerson
										  SET group_preference = NULL
										  WHERE person_id = $p_id";
			
			$resetResult = $db->query($resetGroupPrefQuery);
			if ($resetResult) {
				return TRUE;
			}										  
			else {
				return FALSE;
			}							
		} else { 
			return FALSE;
		}

	} 

	/**************************************************
	*Name:   invitePerson
	*Desc:	Use this to invite additional members to a group that was already created.
			
	*Note:	Only Updates object's person array.
	
	@param  string[p_id] Person ID of the person logged in.
	@return   
	****************************************************/

	public function invitePerson($p_id) {

		$db = new DB();
		$groupID = $this->group_id;
		$personID = $p_id;

		$Result = $db->getOneRecord("tblPersonGroup", "WHERE group_id = $groupID AND person_id = $personID");
		if (is_array($Result) && empty ($Result)) {
			$qryPerson = "INSERT INTO tblPersonGroup VALUES ($personID, $groupID, 0)";
			$ResultPerson = $db->insertRecord($qryPerson);
		}
		if ($ResultPerson === FALSE) {
			//echo "result was REALLY false";
			return false;
		}
		if (!is_numeric($ResultPerson)) {
			
			//echo "result was not a number<br/>";
			return false;
		}
	} 

	/**************************************************
	*Name:   emailInvitation
	*Desc:	This emails people if a user invites them.
			
	*Note:	There are two different emails. One email is for users with
	*		a profile on the system. The other email is for user without
	*		a profile, and prompts them to create a profile. 
	
	@param  string[p_id] Person ID of the person logged in.
	@return   
	****************************************************/
	public function emailInvitation($p_array) {

		$this->initialize();

		foreach ($p_array as $person_id) {

			$person = new Person($person_id);

			$name = $person->getFirstName();
			$email = $person->getEmail();
			$groupName = $this->group_name;

			if ($name != "" || $name != NULL) {
				$subject = "You have been invited to join Team $groupName";
				$message = "Dear $name,\n\n"."Please follow the link below to accept or reject this invitation:\n\n"."http://rook.hss.cmu.edu/~team04-S06/groups.php\n\n"."--FUSION";

			} else {
				$subject = "You have been invited to join Team $groupName";
				$message = "You have been invited to join a team on the Fusion System. \n\n"."Please follow the link below to create a profile and accept this invitation: \n\n"."http://rook.hss.cmu.edu/~team04-S06/profile.php?pid=$person_id\n\n"."--FUSION";
			}
			$from = "From: Fusion Administrator <support.fusion@gmail.com>";

			mail($email, $subject, $message, $from);
			// add email hack here?
		} 
	}

	/**************************************************
	*Name:   emailRejection
	*Desc:	This emails the creator if a person rejects their group invitation.
			
	*Note:	
	@param  string[p_id] Person ID of the person logged in.
	@return   
	****************************************************/
	public function emailRejection($p_id) {

		$this->initialize();

		$person = new Person($p_id);
		$andrewID = $person->getAndrewID();
		$email = $person->getEmail();

		$creator = new Person($this->creator_id);
		$creatorEmail = $creator->getEmail();
		$creatorName = $creator->getFirstName();

		$groupName = $this->group_name;

		$subject = "$andrewID has rejected you";
		$message = "Dear $creatorName,\n\n"."$andrewID has rejected your invitation to join Team $groupName\n\n";

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";

		mail($email, $subject, $message, $from);
		// add email hack here?		
	} 
/**
	public function updateMembers() {

		$group_id = $this->group_id;
		if ($group_id) {
			$db = new DB();
			$group_person = $db->get2DArray("SELECT person_id, confirmed  FROM tblPersonGroup WHERE group_id = $group_id");

			if (!$group_person) {
				$this->person_array = array ();
			} else {
				$this->person_array = $group_person;
			}
		} else
			$this->person_array = array ();
	}
*/
} 
?>