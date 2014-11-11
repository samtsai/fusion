<?php


/**********************************************************************************************
*                                      LOGIN PANEL    
* *This has all the form elements that allows a user to login
* *Link to retrieve forgotten password
**********************************************************************************************/

if ($_POST["answer"] && !$error) {
	/** include confirmation message that changes have been saved */
	$panel = "<div class=\"smallroundcont\">
				   <div class=\"roundtop\">
					 <img src=\"images/tl.gif\" 
					 width=\"15\" height=\"15\" class=\"corner\" 
					 style=\"display: none\" />
				   </div>
					<span>Your new password has been</span><br /><span>e-mailed to your account.</span>			  
				   <div class=\"roundbottom\">
					 <img src=\"images/bl.gif\" 
					 width=\"15\" height=\"15\" class=\"corner\" 
					 style=\display: none\" />
				   </div>
				</div>";
} else {
	$forgotten = "<div id='secret_error'>$secretError</div>";
}

$panel .= HTMLHelper :: startPanel("loginTop", "loginBox");

// Login Information
// Form Name, Form Title, Action
$formStart = $formObj->startForm("loginForm");
if (isset ($_GET["meeting_id"])|| isset ($_POST["meetingCon"]))
	$formStart .= "<input type = 'hidden' name = 'meetingCon' id = 'meetingCon' value = 'yes'>";

if (isset ($_GET["msg"])) {
	
	if ($_SESSION["goto"])
		$pageName = substr($_SESSION["goto"],0,-4);
	else
		$pageName = "this";
	
	$loginMsg = "Please log in to access $pageName page.";	
	$output .= $formObj->insertDescription($loginMsg, "left");
}

// Email and Password    	
$output .= $formObj->insertTextbox(1, "text", "opt_email", "loginEmail", "loginEmail", "E-mail", "", "", "left", "", "", 0, 2);
$output .= $formObj->insertTextbox(1, "password", "opt_text", "loginPassword", "loginPassword", "Password", "", "", "left", "", "", 0, 2);
//$output .= $formObj->insertCheckbox("","opt_rememberCheck","rememberCheck","rememberCheck","","Remember me?","remember","left");
$output .= $formObj->insertSubmitButton("Submit", "", "Login", 1, "left", "submitForm marginLeft");

/** Secret Question and Answer*/
$secretQuestion = "Forgotten your password? Click <a href = \"javascript:secretQuestion('');\">here</a>.";
$endForm .= $formObj->insertDescription($secretQuestion, "left");

$endForm .= $formObj->endForm("", 1, 0);

$endForm .= $formObj->startDiv("secret");
$endForm .= $formObj->endDiv();

$endForm .= "<div id=\"newUsers\"><span class=\"title\">:: New Users</span>";
$endForm .= "<p><a href=\"profile.php\"><img src=\"images/createProfile.gif\" alt=\"Create Profile\" class=\"buttonLink\" /></a></p>";
$endForm .= "</div>";

//End panel graphic
$endForm .= $formObj->insertSpacer();
$endForm .= HTMLHelper :: endPanel("loginBottom");

$errorSummary = $formObj->printErrorSummary();
$errorSummary .= $forgotten;
/******* Printing out of the actual page goes here *******/
$output = $panel.$formStart.$output.$errorSummary.$endForm;
CommonFunctions :: printPage($output, $formObj);
?>

