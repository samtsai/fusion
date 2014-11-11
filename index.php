<?php
require_once ("includes/common.php");

$pageTitle = "Home";

///attempting cookie creationnnnnnn
$time = time();



/////end of attempting cookie creation 

// already logged in, redirect to request meeting page
if ($_SESSION["status"] == 2) {
	Redirect :: gotoPage("request.php");	
}

// set the Form Object
$formObj = CommonFunctions :: setFormObj(1);

if (isset ($_GET["meeting_id"]) && isset ($_GET["person_id"])) {

	$_SESSION["meetingInfo"][1] = $_GET["person_id"];
	$_SESSION["meetingInfo"][2] = $_GET["meeting_id"];
	$_SESSION["meetingInfo"][3] = $_GET["action"];
	$person_id = $_GET["person_id"];
	$meeting_id = $_GET["meeting_id"];
	$action = $_GET["action"];

	$db = new DB();
	$condition = "WHERE person_id='$person_id'";
	$result = $db->getOneRecord("tblPerson", "$condition");
	if ($result) {
		$_POST["loginEmail"] = $result["email"];
		//	$_POST["Submit"] == "Login";
	}

	//exit();

}
//determines if normal login or if coming from activation link
elseif (isset ($_GET["activate"])) {
	//sets authorization code from url

	$authCode = $_GET["activate"];

	//new instance of database class, find person who has matching authorization code
	$db = new DB();
	$condition = "WHERE active='$authCode'";
	$result = $db->getOneRecord("tblPerson", "$condition");
	if ($result)
		echo "result true";
	echo "activate reached";

	//set login fields on the form so the user is automatically logged in
	$_POST["loginEmail"] = $result["email"];

	$pswdAndHash = $result["pswd"];
	echo "pw code = $pswdAndHash <br/>";
	$pswd = substr($pswdAndHash, 44);

	$_POST["loginPassword"] = $pswd;
	$pswdHash = substr($pswdAndHash, 0, 43);

	$id = $result["person_id"];
	$person = new Person($id);
	$person->setPswd($pswdHash);
	$updateResult = $person->updateDB();
	if ($updateResult)
		echo "update worked";
	//automatically submit form so user is redirected to event page
	$_POST["Submit"] = "Login";

}
// regular login page

if (!empty ($_POST)) {
	if (isset ($_POST["Email"])) {

		$answer = stripslashes(trim($_POST["answer"]));
		$person = new Person(trim($_POST["person_id"]));

		$login = new Login();
		$login->attach(new SessionSetter());
		$error = $login->checkSecret($person->getEmail(), $answer);
		// include header now so that it can call SecretQuestion
		echo "ERROR: ".$error;
		if ($error) {
			$secretError = "Password not sent: <br/>Answer did not match stored response";
		} else {
			$secretError = "";
			$person->emailResetPassword();
		}

		include ("includes/header.php");
		include ("includes/views/login_form.php");
		include ("includes/views/info.php");			
	}
	elseif (isset ($_POST["Submit"]) && $_POST["Submit"] == "Login") { // The user tried to submit the form

		$loginData = Arrays :: getPostVars("");

		// if there are no errors, try to login
		if ($formObj->validateForm($loginData)) {
			// Trim each of the variables passed 
			$loginEmail = trim($_POST["loginEmail"]);
			$loginPassword = trim($_POST["loginPassword"]);

			// initiate login 
			$login = new Login();
			// set observers
			$login->attach(new SessionSetter());
			$error = $login->checkLogin($loginEmail, $loginPassword);
			
			if ($error) { // If there is an error, redisplay the form w/ error messages
				$formObj->setFormError($error[0], $error[1]);
				include ("includes/header.php");
				include ("includes/views/login_form.php");
				include ("includes/views/info.php");
			} else { // No error, display create profile, or add event if this is activate
				// found an activation code
				if ($authCode) {

					// update the status session
					$_SESSION["status"] = 1;
					$_SESSION["personID"] = $id;
					$_SESSION["firstName"] = $result["first_name"];

					CommonFunctions :: resetFormObj(1);
					// step two in the profile setup
					Redirect :: gotoPage("schedule.php");
				}
				// regular login for a normal user 
				else {

					$db = new DB();
					$condition = "WHERE email='$loginEmail'";
					$result = $db->getOneRecord("tblPerson", "$condition");			
					
					if ($result["active"] != 1)
						$_SESSION["status"] = 1;
					else
						$_SESSION["status"] = 2;
					$_SESSION["personID"] = $result["person_id"];
					$_SESSION["firstName"] = $result["first_name"];
					
					// go to request meeting
					if ($_POST["meetingCon"] == "yes") {
						$person_id = $_SESSION["meetingInfo"][1];
						$meeting_id = $_SESSION["meetingInfo"][2];
						$action = $_SESSION["meetingInfo"][3];
						Redirect :: gotoPage("meeting_confirmation.php?action=$action&meeting_id=".$meeting_id."&person_id=".$person_id);
					} else {
						if (isset($_SESSION["goto"])) {
							$pageName = $_SESSION["goto"];
							Redirect::gotoPage($pageName);
						}
						else {
							if ($result["active"] != 1)
								Redirect :: gotoPage("schedule.php");
							else
								Redirect :: gotoPage("request.php");
						}	
					}
				}
			}
		} else { // login form did not validate so reprint form with errors and stuff		
			include ("includes/header.php");
			include ("includes/views/login_form.php");
			include ("includes/views/info.php");
		}
	} else {
		include ("includes/header.php");
		include ("includes/views/login_form.php");
		include ("includes/views/info.php");
	}
} else { // The user hasn't tried to submit, so display the form w/o error msgs
	include ("includes/header.php");
	
	
	//echo "cookie global: ".$_COOKIE["cookie_data"];
	/*
	if (isset($_COOKIE["cookie_data"]))  {
	   $cookie_info = explode("&", $cookie_data); 
	   $cook_email = $cookie_info[0];
	   $visits = $cookie_info[1];
	   $visits ++;
	   $cookie_string = $f_name.'&'.$l_name.'&'.$visits; 
	   setcookie ("cookie_data",$cookie_string, $time+3600*24*100);
	   echo "Welcome back $f_name $l_name, this is visit number: $visits"; 
	} 
	*/
	
	/* I THINK THIS CODE SHOULD GO SOMEWHERE ELSE
	else {
	   $cook_email = $_POST["loginEmail"];
	   $visits = 1;
	   $cookie_string = $cook_email.'&'.$visits; 
	   setcookie ("cookie_data",$cookie_string, $time+3600*24*100);
	   echo "Your cookie has been set.";
	} 
	$_POST["loginEmail"]=$cook_email;
	
	echo "cookie email ".$cook_email;
	*/
	include ("includes/views/login_form.php");
	include ("includes/views/info.php");
	//description of fusion
}

include ("includes/footer.php");

//end else statement
?>