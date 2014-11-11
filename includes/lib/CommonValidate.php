<?php
/**************************************************
Name:	Common Validate
Note: 	Validation Type can hold two pieces of information
		The "|" separates the two pieces
		Piece 1: normal Validation
		Piece 2: show label (DEFAULT: 1 = yes)
		
@param  string[name]   	Name
@param  string[value] 	Value
@param  string[valType] Validation Type
@param	string[label]	Label
@return   void

****************************************************/ 

include_once ("../common.php");

$customErrorMessages = array ("firstName" => "Please enter a valid first name", 
                            "lastName" => "Please enter a valid last name", 
                            "address1" => "Please enter a valid address line 1", 
                            "address2" => "Please enter a valid address line 2", 
                            "zip" => "Please enter a valid zip code", 
                            "city" => "Please enter a valid city name", 
                            "phone" => "Please enter a valid 10-digit phone number", 
                            "password1" => "Please do not leave the password field blank",
                            "password2" => "Please do not leave the password field blank",
                            "email" => "Please enter a valid email address", 
                            "loginEmail" => "Please enter a valid email address", 
                            "state" => "Please select a valid state name",
                            "expdate" => "Please enter a valid non-expired expiration date",
                            "student" => "Please enter a valid name",
                            "tutor" => "Please enter a valid name",
                            "course" => "Please enter a valid course number",
                            "location" => "Please enter a valid location",
                            "name" => "Please enter a valid name",
                            "comments" => "Please enter a description",      
                            "count" => "Please enter a valid count",
                            "major" => "Please enter a valid major",
                            "qpa" => "Please enter a valid QPA",
                            "secretAnswer" => "Please answer one of the secret questions",
                            "aim" => "Please enter a valid AIM screenname",
                            "andrewID" => "Invalid AndrewID entered",
                            "none" => ""
                            );  
          
$name = stripslashes(trim($_GET["name"]));                                                                
$value = stripslashes(trim($_GET["value"]));
$valType = stripslashes(trim($_GET["valType"]));
	
$label = "";
//msnider, andrew, andrewID
$pieces = explode("|",$valType);
$valType = $pieces[0];

if (isset($pieces[1])) {
	if ($pieces[1] == 1) {
		$label = ucwords($name).": ";
	}	
}

$formObj = $_SESSION["formObj"];
$fes = $formObj->getFormErrorSummary();
if (in_array($name,$fes)) {
	$label = ucwords($name).": ";
}

/*
$fvg = $formObj->getFormValidationGroups();

if (Arrays::in_multi_array($name,$fvg)) {
	$arrayPath = Arrays::multi_array_search($name,$fvg);	
	$group = $fvg[$arrayPath[0]];
	$valType = $arrayPath[1];
	$names = $group[$valType];

	$values = array();
	foreach ($names as $stuff) {
		$storedValue = $formObj->getFormValue($stuff);
		if (isset($storedValue))  
			$values[] = $storedValue;	
	}
	
	if (!Validate::isValid($valType,$values)) {  
		echo $label."Make sure both fields are filled out for this group";	    
	}
	else {
		return;
	}	
}
*/


// check for the optional tag in the front of valType
$opt = substr($valType, 0, 3);

if (strcasecmp("opt",$opt)==0) {
	if (empty($value)){
		return;
	}
	else {
		$valType = substr($valType, 4);
	}
}
elseif (empty($value) && !is_numeric($value)) {
	echo $label."Required field";
	return;
}

if ($valType == "andrew") {
	if (strlen($value) < 3) {
		echo $label."The string is too short to be a valid ID";
		return;
	}
	elseif (!eregi("[a-zA-Z0-9]",$value)) {
		echo $label."The AndrewID must be a string of characters";
		return;
	}
}
elseif ($valType == "passwordVerification") {
	if ($name == "password1" && empty($value)) {
		echo $label."Please enter a password";
		return;
	}
	elseif ($name == "password2") {
		$pieces = explode("|",$value);
		if (empty($pieces[0])) {
			echo $label."Please verify your password";
			return;
		}
		elseif ($pieces[0] != $pieces[1]) {
			echo $label."Passwords must match";
			return;	
		}		
	}
	return;
}

if (!Validate::isValid($valType,$value)) {  
    if (isset($customErrorMessages[$name])) {
        echo $label.$customErrorMessages[$name];
    }
    else {
        echo $label."Please enter a valid entry for this field";
    }
}
else {
	return;
}

//}



?>
