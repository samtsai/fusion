<?php
/*
 * Created on Mar 16, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include_once ("../common.php");

if (isset ($_GET["person_id"])){
	$id = $_GET["person_id"];
	$person = new Person($id);
	$person->setActive("1");
	$person->updateDB();
	
}

if (isset ($_GET["getSecret"]))
	$email = stripslashes(trim($_GET["getSecret"]));
	//determining if email exists in system
	$db = new DB();
	$condition = "WHERE email = '$email'";
	$result = $db->getOneRecord("tblPerson",$condition);
	if(!$result){
		echo "<div><small class=\"error\">Email does not exist in system.</small></div>";
	}
	else {
			
		$query = "SELECT active from tblPerson WHERE email = '$email'";
		$activeResult = $db->getScalar($query);
		if($activeResult == "1"){
			$person = new Person(NULL, $email);
			$person_id = $person->getPersonID();
			$question_id = $person->getQuestionID();
		
			$db = new DB();
			$formObj = CommonFunctions :: setFormObj(8);
				
			$question = $db->getScalar("SELECT question FROM tblQuestion WHERE question_id = $question_id");
		
			$output = "<div id=\"newUsers\">";
			$output .= "Once you answer your secret question and hit submit, the system will send you a new password which you can change after you login.";
			
			$output .= $formObj->insertSpacer();
			$output .= $formObj->startForm("Question", "index.php");

			$output .= $formObj->insertTextbox(1, "text", "opt_text", "answer", "answer", $question,"","","left");
		
			$output .= $formObj->insertTextbox(1, "hidden", "text", "person_id", "person_id", "","",$person_id,"left","","",0,0);
			
			$output .= "<div class=\"marginLeft\">";
			$output .= $formObj->insertButton("Cancel","","Cancel","document.getElementById('secret').innerHTML = '';" .
					"document.getElementById('loginEmail').value = '$email'");
			$output .= $formObj->insertButton("Email","","Submit","",1);
			$output .= "</div>";
			
			$output .= $formObj->endForm("Question",1,0,0,0);
			$output .= "</div>";
			
			echo $output;
	
		} //end of if activeResult
		else {
			echo "<div><small class=\"error\">Please activate your profile by following the link in your email.</small></div>";
		}
	}

?>



