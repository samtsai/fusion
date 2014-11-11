<?php
require_once ("includes/common.php");

if ($_SESSION["status"] == 2) {
	$pageTitle = "My Profile";
	include ("includes/header.php");
	$formType = "edit";
	
	$person_id = $_SESSION["person_id"];
	
	$person = new Person($person_id);
	
	$firstName = $person->getFirstName();
	$lastName = $person->getLastName();
	$andrewID = $person->getAndrewID();
	$aim = $person->getScreenname();
	$phone = $person->getPhone();
	$email = $person->getEmail();
	$secretQuestion = $person->getQuestionID();
	$secretAnswer = $person->getQuestionAnswer();
	$emailHours = $person->getEmailTime();
		
	$formObj = CommonFunctions :: setFormObj(2);
} else {
	
	$pageTitle = "Set Profile";
	include ("includes/header.php");
	if($_GET["pid"]){
		$_SESSION["modify"] = true;

		$_SESSION["getID"]=$_GET["pid"];
		
		$person = new Person($_GET["pid"]);
		$andrewID = $person->getAndrewID();
		$email = $person->getEmail();
		$_SESSION["email"]=$email;
	}
	$formType = "create";
	$formObj = CommonFunctions :: setFormObj(2);

} // end else

	// User has added/edited a profile -- need to process new info
	if (isset ($_POST["Submit"]) && ($_POST["Submit"] == "Submit" || $_POST["Submit"] == "Edit Profile")) {
		
		//	get all post vars for all fields
		$personData = Arrays :: getPostVars("");		
		
		if (!isset($personData["emailCheckbox"])) {
			$personData["emailCheckbox"] = "";			
		}
		//if edit form, need to verify current password
		if($formType=="edit"){
			$currentPass = $person->getPswd();
		    $pswdHash = Login :: generateHash($personData["oldPassword"],$currentPass);
			if($pswdHash!=$currentPass){
				//echo $pswdHash."<br/>";
				//echo $currentPass."<br/>";
				//echo "<br/>password error!<br/>";
				$errorPass = "1";
			}else {
				//echo "<br/>no password error<br/>";
				$errorPass = "0";
				$formObj->setFormError("oldPassword", "");
				$formErrors = null;
			}
		} // end of if formtype...
		else {
			$errorPass = "0";	
		}
		$errorAndrew="0";
		if($formType=="create" && $_SESSION["modify"]!=true){
			$db = new DB();
			//echo "helloooo";
			echo $_SESSION["modify"];
			$enteredAndrew = $personData["andrewID"];
			$condition = "WHERE andrewID = '$enteredAndrew'";
			$result = $db->getOneRecord("tblPerson",$condition);
			if($result){
				$errorAndrew = "1";
			}else{
				$errorAndrew = "0";
				$formObj->setFormError("andrewID","");
			}	
		}//end of if formtype
		//validating form
		if ($formObj->validateForm($personData) && $errorPass=="0" && $errorAndrew=="0") {
			if ($_POST["Submit"] == "Submit" || $_POST["Submit"] == "Edit Profile") { //profile is being created...
				if ($formType=="create"){										
					if($_SESSION["modify"]==true)
						$person = new Person($_SESSION["getID"]);
					else
						$person = new Person();
					
					$person->setAndrewID($personData["andrewID"]);						
				}
				$person->setFirstName($personData["firstName"]);
				$person->setLastName($personData["lastName"]);
				
				if($personData["secretQuestion"]!=""){
					$person->setQuestionID($personData["secretQuestion"]);
				}
				
				if($personData["secretAnswer"]!="")	{
					$questionAnswerHashed = Login :: generateHash($personData["secretAnswer"]);
					$person->setQuestionAnswer($questionAnswerHashed);
				}
				//need to hash password if creating a new one in edit form
				if ($formType=="edit")
					$pswd = Login :: generateHash($personData["password1"]);
				else {
					if($_SESSION["modify"]==true){
						$pswdHash = Login :: generateHash($personData["password1"]);
						$pswdTemp = $personData["password1"];
						$pswd = $pswdHash."|".$pswdTemp;
					}
					else
						$pswd = $personData["password1"];
				}
				//set password only if not blank - you can update profile without inputting pswd
				
				if($personData["password1"]!="")	
					$person->setPswd($pswd);
				
			
				//why didn't I think of this sooner, again?
				if(!empty($personData["andrewID"]))
				$email_address = LDAPwrapper($personData["andrewID"]);
				
				if($_SESSION["modify"]==true)
					$email_address = $_SESSION["email"];
				
				if ($personData["email"] == "") {
					$person->setEmail($email_address);
					$email = $email_address;
				} else {
					$person->setEmail($personData["email"]);
				}
				
				$phone = $personData["phone"];
				// Strict Types - accept only dashes	
				$phone = str_replace("-", "", $phone);

				$person->setPhone($phone);		
				
				$aim = $personData["aim"];
				
				$person->setScreenname($aim);		
				
				if (isset($personData["emailHours"]) && $personData["emailCheckbox"] != "") {
					$emailHours = $personData["emailHours"];
					$person->setEmailTime($personData["emailHours"]);
				}
				else
				{
					$emailHours = 0;
					$person->setEmailTime(0);
				}
					
				//create profile and send activation link
				if($formType=="create"){
					if($_SESSION["modify"]==true){
						$person->updateDB();
					}
					else{
						$person->createProfile();	
					}			

					$id = $person->getPersonID();
					$fn = $person->getFirstName();
	
					//E-mail the user their profile information
					$person->emailActivation($email_address);
	
					//Set session variables                
					$_SESSION["personID"] = $id;
					$_SESSION["firstName"] = $fn;
					// use Session status to find out what the person is up to
					// 0 = new user and hasn't activated
					// 1 = new user, has activated and is in profile stage
					// 2 = fully registered user
					$_SESSION["status"] = 0;
	
					// Kill the session
					unset($_SESSION["formObj"]);
					unset($_SESSION["modify"]);
					//Take them to the next step of setting their weekly schedule
					//echo "<br/>notification.php?email=$email_address";
					Redirect :: gotoPage("notification.php?email=$email_address");
				} else{ // updates profile if on edit page
					
						$formObj = CommonFunctions :: setFormObj(2);
						$person->updateDB();

						//include confirmation message that changes have been saved
						echo "<div class=\"roundcont\">
							   <div class=\"roundtop\">
								 <img src=\"images/tl.gif\" 
								 width=\"15\" height=\"15\" class=\"corner\" 
								 style=\"display: none\" />
							   </div>
								<span class=\"bigTitle\">Your changes have been saved!</span>
							  
							   <div class=\"roundbottom\">
								 <img src=\"images/bl.gif\" 
								 width=\"15\" height=\"15\" class=\"corner\" 
								 style=\display: none\" />
							   </div>
							</div>";
						
						include ("includes/views/profile_form.php");
						
						
				}	
		}// end of post submit
		//need to upload calendar data regardless of whether profile is being created or edited
		if(is_uploaded_file($_FILES["import"]["tmp_name"]) && ((strpos($_FILES["import"]["name"], ".ics") !== FALSE) || $_FILES["import"]["tmp_name"] == "text/calendar"))
		{
			//echo "including required ical parsing functionality<br/>";
			$cal_filelist = array(1 => $_FILES["import"]["tmp_name"]);
			include_once('includes/lib/date_functions.php');
			include_once('includes/lib/calendar_functions.php');
			include_once('includes/lib/overlapping_events.php');
			include_once('includes/lib/timezones.php');
			include_once('includes/entities/Event.php');
			//echo "done with includes...";
			//echo "parsing ical<br/>";
			include_once('includes/lib/ical_parser.php');
			//echo "done parsing ical";
		}
		
		if ($iCalErrors)
		{
			echo "<div class='floatLeft'><div id='iCalErrorBox'></div><div class='widgetBox'><div class='widgetContent'>";
			echo $errors;
			echo "</div></div><div class='widgetBottom'></div></div>";
			
		}
		else
		{
			echo "All iCal data uploaded!";
		}
		
	} //end of if no form errors
	else {						
			if($errorPass == "1"){$formObj->setFormError("oldPassword", "Invalid Password");}
			if($errorAndrew == "1"){$formObj->setFormError("andrewID", "This Andrew ID is already registered.");}
			include ("includes/views/profile_form.php");

		}
	} //end of if post submit
	 // no submission , so give the person form
	else {			
		include ("includes/views/profile_form.php");
	}
	
include ("includes/footer.php");
?>
