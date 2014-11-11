<?php


/**  ======================================
  	PERSON CLASS
    This class is responsible for all 
    functionality regarding a Person. 
    Profiles are created and edited using
    this class. Activation and reset pswd
    emails are also sent through this 
    class. This class interacts directly
    with the person table in our database.   
    ======================================    */

class Person { /** PERSON CLASS BEGINS */

	private $person_id;
	private $first_name;
	private $last_name;
	private $screenname;
	private $phone;
	private $pswd;
	private $email;
	private $active;
	private $andrewID;
	private $question_id;	
	private $question_answer;
	private $aim_time;
	private $email_time;
	private $last_group;

	/**  CONSTRUCTOR 
	    
	    Note: Constructor is a special function that kicks in whenever an 
	    instance of the class is created.  Typically it is used to set 
	    initial values to zero or reset to whatever the defaults should be.
	
	    ========================================   */

	function __construct($person_id = NULL, $email = NULL) {

		$this->person_id = $person_id;
		$this->first_name = "";
		$this->last_name = "";
		$this->screenname = "NULL";
		$this->phone = "NULL";
		$this->pswd = "";
		$this->email = $email;
		$this->active = "";
		$this->andrewID = "";
		$this->question_id = 0;
		$this->question_answer = "";
		$this->aim_time = "NULL";
		$this->email_time = "NULL";
		$this->last_group = "NULL";

		if ($person_id || $email)
			$this->initialize();

	} /** End of Constructor */

	/** GET AND SET METHODS */

	/** SET METHODS */

	public function setPersonID($int) {
		$this->person_id = $int;
	}

	public function setFirstName($string) {
		$this->first_name = $string;
	}

	public function setLastName($string) {
		$this->last_name = $string;
	}

	public function setScreenname($string) {
		$this->screenname = $string;
	}

	public function setPhone($string) {
		$this->phone = $string;
	}

	public function setAndrewID($string) {
		$this->andrewID = $string;
	}

	public function setQuestionID($int) {
		$this->question_id = $int;
	}
	
	public function setLastGroup($int) {
		$this->last_group = $int;
	}

	public function setQuestionAnswer($string) {
		$this->question_answer = $string;
	}

	public function setEmail($string) {
		$this->email = $string;
	}

	public function setPswd($string) {
		$this->pswd = $string;
	}

	public function setActive($string) {
		$this->active = $string;
	}

	public function setAIMTime($int) {
		$this->aim_time = $int;
	}

	public function setEmailTime($int) {
		$this->email_time = $int;
	}

	/** GET METHODS */

	public function getPersonID() {
		return $this->person_id;
	}

	public function getFirstName() {
		return $this->first_name;
	}

	public function getLastName() {
		return $this->last_name;
	}

	public function getScreenname() {
		return $this->screenname;
	}

	public function getPhone() {
		return $this->phone;
	}
	
	public function getLastGroup() {
		return $this->last_group;
	}

	public function getAndrewID() {
		return $this->andrewID;
	}

	public function getQuestionID() {
		return $this->question_id;
	}

	public function getQuestionAnswer() {
		return $this->question_answer;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPswd() {
		return $this->pswd;
	}

	public function getActive() {
		return $this->active;
	}

	public function getAIMTime() {
		return $this->aim_time;
	}

	public function getEmailTime() {
		return $this->email_time;
	}

	/** FUNCTION: INITIALIZE
	 * This function initialized all values 
	 * for a person when an instance of the 
	 * class is created.
	 */
	public function initialize() {

		/** Step 1: Get the ID of the person we want to get data on */
		$person_id = $this->getPersonID();
		$email = $this->getEmail();

		if ($person_id == NULL && $email == NULL) { /** person ID hasn't been set yet, that's not a good thing*/
			echo "no data to init with!";        
			return FALSE;
		}

		/** Step 2: Get all the data from tblPerson for that person
		* Specify a particular account from tblPerson */
		if ($person_id != NULL)
			$condition = "WHERE person_id = $person_id";
		else
			$condition = "WHERE email = '$email'";

		/** Set up a link to the database and get the record */

		$db = new DB();
		$person_data = $db->getOneRecord("tblPerson", $condition);

		if (!$person_data) { /** No results were found */
			return FALSE;
		} else { /** set properties to values in db */

			$this->person_id = $person_data["person_id"];
			$this->first_name = stripslashes($person_data["first_name"]);
			$this->last_name = stripslashes($person_data["last_name"]);
			$this->question_id = $person_data["question_id"];
			$this->screenname = stripslashes($person_data["screenname"]);
			$this->andrewID = $person_data["andrewID"];
			$this->question_answer = stripslashes($person_data["question_answer"]);
			$this->aim_time = $person_data["aim_time"];
			$this->email_time = $person_data["email_time"];
			$this->phone = $person_data["phone"];
			$this->email = stripslashes($person_data["email"]);
			$this->pswd = stripslashes($person_data["pswd"]);
			$this->active = $person_data["active"];
			$this->last_group = $person_data["group_preference"];

		} /** end of else */

		return TRUE;

	} /** End of initialize() */


	/** FUNCTION: UPDATE RECORDS 
	 * This function updates all 
	 * fields in the Person table.
	 * It is used when a user 
	 * 'edits' their profile.
	 */
	public function updateDB() {

		/** Get info needed to update database */
		$person_id = $this->getPersonID();
		$firstName = addslashes($this->getFirstName());
		$lastName = addslashes($this->getLastName());
		$screenname = addslashes($this->getScreenname());
		$andrewID = $this->getAndrewID();
		$questionID = $this->getQuestionID();
		$questionAnswer = addslashes($this->getQuestionAnswer());
		$aimTime = $this->getAIMTime();
		$emailTime = $this->getEmailTime();

		$active = $this->getActive();

		$phone = $this->getPhone();
		$email = addslashes($this->getEmail());
		
		if(!$email)
			$email = LDAPWrapper($this->andrewID);
		
		$pswd = addslashes($this->getPswd());
		$group_preference = $this->last_group;
		
		if ($person_id == "") { /** person ID hasn't been set yet...*/
			return FALSE;
		}

		/** Update database
		* Set up a query to update the person information */

		$query = "UPDATE tblPerson
						    		SET question_id = '$questionID',
											first_name = '$firstName',
						                    last_name = '$lastName',
											screenname = '$screenname',
						                    phone = '$phone',
						                    pswd = '$pswd',
						                    email = '$email',
											andrewID = '$andrewID',
											question_answer = '$questionAnswer',
						                    aim_time = '$aimTime',
						                    email_time = '$emailTime',
						                    active = '$active',		
											group_preference = '$group_preference'
											WHERE person_id = $person_id";
		
		/** Execute the query and return the result */
		$db = new DB();
		$result = $db->updateRecord($query);

		if (!$result) {
			return FALSE;
		}

		return TRUE;

	} /** End of updateDB()*/


	/** FUNCTION: CREATE PROFILE 
	 * This function is called when
	 * a user is creating a new 
	 * profile within the system. 
	 * It takes all entered information 
	 * and inserts it in the Person
	 * table in our database. 
	 */
	public function createProfile() {

		/** Get info needed to insert into database */

		$person_id = addslashes($this->getPersonID());
		$firstName = addslashes($this->getFirstName());
		$lastName = addslashes($this->getLastName());
		$screenname = addslashes($this->getScreenname());
		$andrewID = addslashes($this->getAndrewID());
		$questionID = addslashes($this->getQuestionID());
		$questionAnswer = addslashes($this->getQuestionAnswer());
		$aimTime = addslashes($this->getAIMTime());
		$emailTime = addslashes($this->getEmailTime());
		$phone = addslashes($this->getPhone());
		$email = addslashes($this->getEmail());
		$pswd = addslashes($this->getPswd());
		$group = NULL;
		$active = 0; /** assume upon insertion that profile is not activated */

		/** Hash Password
		* We has the password so that we don't keep a record of the user's actual password */
		$pswdHash = Login :: generateHash($pswd);
		$pswdAndHash = $pswdHash."|".$pswd;

		/** we temporarily keep the user's password so we can send it to them in the activation email*/
		$this->pswd = $pswdAndHash;
		
		/**we also has the user's secret question answer */
		$questionAnswerHashed = Login :: generateHash($questionAnswer);

		/** Insert records into database
		* Set up a query to insert the account information */
		$db = new DB();
		if ($id = $db->getScalar("SELECT person_id FROM tblPerson WHERE person_id = $person_id")) {
			$this->person_id = $id;
			$this->updateDB();
		} else {
			$personQuery = "INSERT INTO tblPerson
							                VALUES (NULL,$questionID,'$firstName','$lastName','$screenname','$phone','$pswdAndHash','$email',$active,'$andrewID',
											'$questionAnswerHashed','$aimTime','$emailTime', '$group')";
			/** Execute the query and return the result */
		
			$result = $db->insertRecord($personQuery);

			if (is_numeric($result)) {
				$this->person_id = $result; /** set the id property of the person */
				return TRUE;
			} else { /** if not numeric, then insert didn't work in this case */
				return FALSE;
			}
		}

	} /** End of createProfile()*/

	/** FUNCTION: DEACTIVATE PERSON
	 * 	This function deactivates 
	 *  a person in the database.
	 *  We do not currently use this
	 *  function yet. 
	 */
	public function deactivatePerson() {

		$this->setActive(0);

	} /** End of deactivatePerson() */

	/** FUNCTION: ACTIVATE PERSON
	 * 	This function activates 
	 *  a person in the database.
	 *  This occurs when the user 
	 *  follows the link in their
	 *  activation email.
	 */
	public function activatePerson() {

		$this->setActive(1);

	} /** End of activatePerson()*/

	/** FUNCTION: Generate authorization code
     * This code is used in the activation process.
	 * It is included in the link in the activation email.
	 */
	private function generateAuthorization() {

		/**	This will return a totally bogus 
		 * 20 character authorization string 
		 * (no l or O chars -- too much 
		 * like 1 and 0)
		 */

		$chars = array ('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$length = 20;
		$auth = "";

		for ($i = 0; $i < $length; $i ++) {
			$char_index = rand(0, count($chars) - 1);
			$auth .= $chars[$char_index];
		}
		$this->authorization = $auth; 
		return $auth;

	} /** End of generateAuthorization()*/


	/** FUNCTION: EMAIL ACTIVATION
	* NOTE: this is specifically run 
	* after someone has just created 
	* a profile
	*/
	public function emailActivation($email_to_use = "") {

		/** generate the authorization string and then set authCode */
		$authCode = $this->generateAuthorization();

		/** get the most up to date informaiton */
		$this->initialize();

		$pswdAndHash = $this->getPswd();

		/** pull out just the password */
		$pswd = substr($pswdAndHash, 44);
		/** store the authorization string in the active_field in the db */
		$this->setActive($authCode);

		/** update the persons record */
		$this->updateDB();

		$name = $this->getFirstName();
		
		$email = $this->getEmail();
		
		if($email_to_use)
		{
			$email2 = $email_to_use;
		}
		else
		{
			$email2 = $email;
		}

		$subject = "Welcome to Fusion!";
		$message = "Dear $name,\n\n"."Thank you for registering!\n\n"."Now you can build your personal schedule and begin to work with your groups\n\n"."Below is your login information. Please keep it for your records.\n"."After you activate your profile, you will be able to login using the following information:\n\n"."Username: $email\n"."Password: $pswd\n\n"."Please click on the link below to activate your profile and begin\n"."building your schedule.\n\n"."http://rook.hss.cmu.edu/~team04s06/index.php?activate=$authCode\n\n"."Thanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";
		/**echo ("$email2 <br/> $subject <br/> $message <br/> $from ");&*/ 
		mail($email2, $subject, $message, $from);
	} /** End of emailActivation */
	

	/** FUNCTION: EMAIL RESET PASSWORD
	 * This is used when a user clicks the 'forgot password' link.
	 * They are sent a temporary password which they can use to
	 * log in to the system. They can then go to edit profile and 
	 * change their password. 
	 */
	public function emailResetPassword() { 

		$new_password = $this->generateRandomPassword();
		$hashed_pw = Login :: generateHash($new_password);
		$this->pswd = $hashed_pw;
		$this->updateDB();

		$name = $this->getFirstName();
		$email = $this->getEmail();

		$subject = "Forgotten Password";
		$message = "Dear $name,\n\n"."We have reset your password.\n\n"."Username: $email\n"."Your new password is: $new_password\n\n"."Please return to the login page and enter your new password, which will\n"."give you access to your account.  From there you can reset your password\n"."by visiting the profile page.\n\n"."http://rook.hss.cmu.edu/~team04s06/index.php\n\n"."Thanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";

		mail("support.fusion@gmail.com",$subject, $message, $from);
		mail($email, $subject, $message, $from);

	}

	/** FUNCTION: GENERATE RANDOM PASSWORD
	 * This is used in the emailResetPassword function above.
	 * This generates a random 8 character password that can
	 * be used to log in until a new password is set by the user.
	 */
	private function generateRandomPassword($length = 8) {

		/** start with a blank password */
		$password = "";

		/** define possible characters */
		$possible = "123456789bcdfghjkmnpqrstvwxyz";

		/** set up a counter */
		$i = 0;

		/** add random characters to $password until $length is reached */
		while ($i < $length) {

			/** pick a random character from the possible ones */
			$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);

			/** we don't want this character if it's already in the password*/
			if (!strstr($password, $char)) {
				$password .= $char;
				$i ++;
			}

		}

		return $password;

	}
	
	/** FUNCTION: GET GROUPS
	 * This function returns the id's of the groups the 
	 * user is currently part of. This is used on the group 
	 * page where all groups for that user are listed.
	 */
	public function getGroups() {
		
		$db = new DB();
		
		return $db->get2DArray("SELECT tblGroup.group_id, CASE WHEN CHAR_LENGTH(tblGroup.group_name)>20 THEN CONCAT(SUBSTRING(tblGroup.group_name,1,20),'...') ELSE tblGroup.group_name END FROM tblPersonGroup, tblGroup WHERE tblPersonGroup.person_id = ". $this->person_id . " AND tblPersonGroup.confirmed = 1 AND tblPersonGroup.group_id = tblGroup.group_id"); 	
		
		
	}

} /** END OF Person CLASS */
?>