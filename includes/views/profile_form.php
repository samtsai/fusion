<?php 

//panel graphic
/*
echo "<div class=\"floatLeft\">";
echo "<div id=\"profileTop\"></div>";
echo "<div id=\"panel\" style=\"padding-bottom: 45px; _padding-bottom: 0px\">";
*/

$output = HTMLHelper::startPanel("profileTop");
// Profile Information
// Form Name, Form Title, Action
$output .= $formObj->startForm("person","","1","post","multipart/form-data");

	// Personal Information
    $output .= $formObj->insertTitle("Personal Information",1,"bigTitle"); 
    $output .= "<div class=\"instruction\">* Fields are Required.</div>";
    $output .= $formObj->insertTextbox(1,"text","name","firstName","firstName","First Name","",$firstName);    
	$output .= $formObj->insertTextbox(1,"text","name","lastName","lastName","Last Name","",$lastName);
	
	if($formType == "create") {
		//if($_SESSION["modify"]==true)
			//$andrewRead = "1";
		//else
			$andrewRead = "0";
		$buttonName = "Submit";
	}
	elseif($formType =="edit") {
		$andrewRead ="1";
		$buttonName = "Edit Profile";
	}

	//this spits out the email field "verified_email_andrewID"
	//which we then use to send the actual activation email 

	
	if($formType=="create"){
		$output .= $formObj->insertSpacer();
		$andrewDescription = "If your primary e-mail is @cmu.edu, enter that ID instead.";
		$output .= $formObj->insertDescription($andrewDescription);
		$output .= $formObj->insertTextbox(1,"text","andrew","andrewID","andrewID","Andrew ID","",$andrewID,"","","",$andrewRead);
		$output .= $formObj->insertTextbox(1,"password","passwordVerification","password1","password1","Password");
	    $output .= $formObj->insertTextbox(1,"password","passwordVerification","password2","password2","Verify Password");
		
		// Secret Question & Answer      
		$output .= $formObj->insertSpacer();  	   
	    $secretDescription = "Please answer a secret question for password retrieval in the future.";
	    $output .= $formObj->insertDescription($secretDescription);
	    $contents = array("secretQuestion" => array ("1"=>"What is your dog's name?", "2"=>"What is your mother's maiden name?", "3"=>"Where were you born?"));
	    $output .= $formObj->insertDropdown(1,"dropdown","","Secret Question","",$contents,"Please select one");
	    $output .= $formObj->insertTextbox(1,"text","text","secretAnswer","secretAnswer","Secret Answer");       
	}
	
	if($formType=="edit"){
		$output .= $formObj->insertTextbox(1,"text","andrew","andrewID","andrewID","Andrew ID","",$andrewID,"","","",$andrewRead);		
		$output .= $formObj->insertSpacer();
		$output .= $formObj->insertTextBox(1,"password","text","oldPassword","oldPassword","Current Password");	
		$output .= $formObj->insertTextbox(1,"password","opt_passwordVerification","password1","password1","New Password");
	    $output .= $formObj->insertTextbox(1,"password","opt_passwordVerification","password2","password2","Verify New Password");
	
		$output .= $formObj->insertSpacer();
		$secretDescription = "Leave Secret Question field blank if you prefer your current question and answer.";
	    $output .= $formObj->insertDescription($secretDescription);
	    $contents = array("secretQuestion" => array ("1"=>"What is your dog's name?", "2"=>"What is your mother's maiden name?", "3"=>"What was your city of birth?"));
	    $output .= $formObj->insertDropdown(1,"opt_dropdown","secretQuestion","Secret Question","",$contents,"Please select one");
	    $output .= $formObj->insertTextbox(1,"text","opt_text","secretAnswer","secretAnswer","Secret Answer");
	}
	  
   	$output .= $formObj->insertSpacer();
    // Contact Information 
    $output .= $formObj->insertTitle("Contact Information",1,"bigTitle");
    
        // Email, Phone, AIM
     
     	if($formType == "edit" && !$email)
     		$email = LDAPWrapper($andrewID);
     
	    $emailDescription = "Leave E-mail field blank if you prefer to use your CMU email.";
	    $output .= $formObj->insertDescription($emailDescription);
        
        $output .= $formObj->insertTextbox(1,"text","opt_email","email","email","E-mail","",$email,"",40);
        $output .= $formObj->insertTextbox(1,"text","opt_phone","phone","phone","Phone","(i.e. 412-862-2323)",$phone);
       	if(!$aim||$aim == NULL||$aim=="NULL")
       		$aim="";
        $output .= $formObj->insertTextbox(1,"text","opt_aim","aim","aim","AIM","",$aim,"",25);
        // Reminder Information        		        
        $hh = array (1,2,3,4,5,6,12,24);      
        
        foreach ($hh as $value) {
        	if ($value == $emailHours)
        		$hours["$value|default"] = $value;
        	else 
        		$hours[$value] = $value;	
        }
        
        if ($emailHours > 0) {
        	$formObj->setFormValue("emailCheckbox","y");
        }
        
        $values = array ("emailHours" => $hours);        
        $output .= $formObj->insertCheckboxDropdown("emailCheckbox","Yes, I would like to be e-mailed ","",1,"y","","hour(s) before a meeting.","",$values);
		
     	
     	$output .= $formObj->insertTitle("Import iCal",1,"bigTitle");
/*		$output .= $formObj->insertSpacer();
		$output .= $formObj->insertSpacer();*/
		$ical_description = "Import iCal formatted calendar and event data (Google Calendar, Apple iCal, etc)";
		$output .= $formObj->insertDescription($ical_description);
		$output .= $formObj->insertTextBox(1,"file","opt_dropdown","import","import","Import iCal");
		$ical_message = "<a href='javascript:void(0);'><div onClick='window.open(\"icalHelp.php\",\"\",\"height=520,width=400\");'>iCal Import Help</div></a>";
		$output .= $formObj->insertDescription($ical_message);
		
    // End form             
    $output .= $formObj->endForm($buttonName,1);
        
    if (!CommonFunctions::inAgent("MSIE")) {
    	$output .= $formObj->insertSpacer();
    }
            
    //End panel graphic
	$output .= HTMLHelper::endPanel();
    
    /** implement this later 
	$secretStuff = array ("secretQuestion", "secretAnswer");
	$formObj->setFormValidationGroup("secretQuestion", array ("dropdownTextbox" => $secretStuff));    
    */
    
    /******* Printing out of the actual page goes here *******/
    CommonFunctions::printPage($output,$formObj);    
?>	

