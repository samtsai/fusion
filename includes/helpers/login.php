<?php

/*  This class is designed to make login tasks easier 
    (since they are often repeated tasks) and allow 
    logging of information for security purposes.  This 
    uses the Observer pattern and additional observers
    can be added to increase functionality of login
    (e.g., set session vars, add cookies, etc.)
    
    Concept for class and initial code base from 
    Matt Zandstra, 2005.  Any errors here, however,
    fall only on my shoulders.
    
    To use this class, the following assumptions are made:
    
    1.  You are validating against fields from a database.
        Furthermore, the database connection information has
        been specified in db_connect.php and you are using the 
        DB class (in the same directory as this class).
        
    2.	There are two observers already created -- one to log 
    	successful logins (general_logger) and one to log bad
    	logins (security_monitor).  They need to be attached if
    	you want them deployed (not done so by default).
    	
    	To attach/detach, can use code as follows:
    		$login = new Login();
			$login->attach( new SecurityMonitor() );
		
	3.	SessionSetter and CookieCutter are two other observers
		that are generally set up, but have to be customized 
		for each application right now.  (Perhaps in the future
		I will get around to rewriting this class...)
    	
    On to setting up the class...
    ======================================    */

// ------------------------------------
// INTERFACE: OBSERVABLE

interface Observable {
    function attach( Observer $observer );
    function detach( Observer $observer );
    function notify();
}

// ------------------------------------
// MAIN CLASS: LOGIN


class Login implements Observable {

    // -----------------------
    // CLASS PROPERTIES
    // -----------------------
    
    private $observers = array();
    private $login_status;
    private $user_table;
    private $user_id_field;
    private $pswd_field;
    private $user_id;
    private $attempted_login_id;
    private $attempted_pswd;
    
    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS   = 2;
    const LOGIN_ACCESS       = 3;

    // -----------------------
    // CONSTRUCTOR
    // -----------------------
    
    function __construct() {

		$this->user_table = "tblPerson";
    	$this->user_id_field = "email";       
    	$this->pswd_field = "pswd";
    	$this->answer_field = "question_answer";
    	$this->user_id = "";
    	$this->attempted_login_id = "";
    	$this->attempted_pswd = "";
    
    }   // End of Constructor
        
    // -----------------------
    // SET & GET METHODS
    // -----------------------

    public function setUserTable($string) { $this->user_table = $string; }
    
    public function setUserIDField($string) { $this->user_id_field = $string; }
    
    public function setPswdField($string) { $this->pswd_field = $string; }
    
    private function setLoginStatus($int) { $this->login_status = $int; }
    
    public function getLoginStatus() { return $this->login_status; }
    
    private function setUserID($string) { $this->user_id = $string; }
    
    public function getUserID() { return $this->user_id; }
    
    private function setAttemptedID($string) { $this->attempted_login_id = $string; }
    
    public function getAttemptedID() { return $this->attempted_login_id; }
    
    private function setAttemptedPswd($string) { $this->attempted_pswd = $string; }
    
    public function getAttemptedPswd() { return $this->attempted_pswd; }
    
    
    // -----------------------
    // OBSERVER METHODS
    // -----------------------
    
    public function attach( Observer $observer ) {
        $this->observers[] = $observer;
    }

    public function detach( Observer $observer ) {
        $this->observers = array_diff( $this->observers, array($observer) );
    }

    public function notify() {
        foreach ( $this->observers as $obs ) {
            $obs->update( $this );
        }
    }

    // ---------------------------
    // MAIN METHOD - CHECK LOGIN
    // ---------------------------
    
    public function checkLogin( $user_id, $pswd ) {
        
        //  First, get the database table and fields to compare against
        $tbl = $this->user_table;
        $id = $this->user_id_field;
        $pw = $this->pswd_field;
        $u_id = trim($user_id);  // in case we forgot to trim it earlier...
        $pswd = trim($pswd);
        $this->attempted_login_id = $u_id;
        $this->attempted_pswd = $pswd;
        
        $query = "SELECT $pw FROM $tbl WHERE $id = '$u_id'";

        $db = new DB();
		$password = $db->getScalar($query);

	    $pswdHash = $this->generateHash($pswd,$password);	    

		if (!$password) {  // user id not found in db
			$this->setLoginStatus(0);
			$this->notify();
			//return "Email not found.";
			$e = array("loginEmail","Email not found");
			return $e;
		}
		else {
			if ($password != $pswdHash)  {  // pswd doesn't match
				$this->setLoginStatus(1);
				$this->notify();
				//return "Incorrect password.";
				$e = array("loginPassword","Incorrect password");
				return $e;
			}
			else {  // all is well...
				$this->setLoginStatus(2);
				$this->user_id = $u_id;
				$this->notify();
				return false;
			}
		}
		
    }  // end of checkLogin()
    
    
    public function checkSecret($loginEmail, $answer) {
        //  First, get the database table and fields to compare against
        $tbl = $this->user_table;   //tblPerson
        $id = $this->user_id_field; //email
        $ans = $this->answer_field; //question_answer
        $email = trim($loginEmail);  // in case we forgot to trim it earlier...
        $this->attempted_login_id = $email;
        
        $query = "SELECT $ans FROM $tbl WHERE $id = '$email'";
		//echo $query;
        $db = new DB();
		$stored = $db->getScalar($query);
	    $answerHash = $this->generateHash($answer,$stored); 

		if (!$stored) {  // user id not found in db
			$this->notify();
			//return "User not found";
			$e = array("loginEmail","Email not found");
			return $e;
		}
		else {
			if ($stored != $answerHash)  {  // pswd doesn't match
				//$this->setLoginStatus(1);
				$this->notify();
				//return "The stored result and the given answer do not match.";
				$e = array("loginPassword","The stored result and the given answer do not match");
				return $e;
			}
			else {  // all is well...
				//$this->setLoginStatus(2);
				$this->user_id = $email;
				$this->notify();
				return false;
			}
		}
		
    }  // end of checkLogin()

    // Generate Hash
    public static function generateHash($plainText, $salt = null)
    {
        if ($salt === null)
        {
            $salt = substr(md5(uniqid(rand(), true)), 0, 3);
        }
        else
        {
            $salt = substr($salt, 0, 3);
        }
    
        return $salt . sha1($salt . $plainText);
    }

}  // End of Login class


// ------------------------------------
// INTERFACE: OBSERVER

interface Observer {
    public function update( Observable $observer );
}


// ------------------------------------
// EXTENSIONS ON OBSERVER

class SecurityMonitor implements Observer {
    
    public function update( Observable $observable ) {
        
        $login_status = $observable->getLoginStatus(); 
        $ip = trim($_SERVER['REMOTE_ADDR']); 
		$cpu = gethostbyaddr($ip);
		$attp_id = $observable->getAttemptedID();
		$attp_pswd = $observable->getAttemptedPswd();
		$date = date("m/d/Y"); 
		$time = date("h:i:s A");
    	
		$attempt = "$ip-$cpu | $attp_id | $attp_pswd | $date | $time \n";
        
        if ($login_status == 0) {
            
            $log_usr = new FileMgr();
            $log_usr->setFileName("bad_userids.txt");
            $log_usr->setFileDirectory("files/logins/");
            $log_usr->appendLineToFile($attempt);    
        }
        
        if ($login_status == 1) {
            
            $log_pw = new FileMgr();
            $log_pw->setFileName("bad_passwords.txt");
            $log_pw->setFileDirectory("files/logins/");
            $log_pw->appendLineToFile($attempt);        
        }
    }
}  // end of SecurityMonitor


class GeneralLogger implements Observer {

    public function update( Observable $observable ) {
    
	$login_status = $observable->getLoginStatus(); 
	$ip = trim($_SERVER['REMOTE_ADDR']); 
    	$cpu = gethostbyaddr($ip);
    	$login_id = $observable->getAttemptedID();
    	$date = date("m/d/Y"); 
    	$time = date("h:i:s A");
    	
    	$attempt = "$ip-$cpu | $login_id | $date | $time \n";
    		
        if ($login_status == 2) {
            
            $log_gen = new FileMgr();
            $log_gen->setFileName("successful_login.txt");
            $log_gen->setFileDirectory("files/logins/");
            $log_gen->appendLineToFile($attempt);  
        }
    	
    }
}  // end of GeneralLogger


class SessionSetter implements Observer {

//  NOTE: This observer is specific for PCC Application
    public function update( Observable $observable ) {
    
        $login_status = $observable->getLoginStatus(); 
        
        if ($login_status == 2) {  // user successfully logs in
            
            $log_id = $observable->getUserID();
            $newdb = new DB();
            
            $condition = "WHERE email = '$log_id'";
            
            $data = $newdb->getOneRecord("tblPerson", $condition);
            
			    $_SESSION["person_id"] = $data["person_id"]; 
				$_SESSION["active"] = $data["active"];
				$_SESSION["first_name"] = $data["first_name"];
				$_SESSION["andrew_id"] = $data["andrewID"];
				$_SESSION["logged_in"] = "yes";    
        }
    } // end of update()
    
}  // end of SessionSetter

?>
