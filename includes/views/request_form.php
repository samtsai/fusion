<?php

$formObj = CommonFunctions::setFormObj(6);

$titleValue = "Title";
$descValue = "Description";
$timeTitle = "Start Time:";
$dateValue = date("m/d/Y");
$fromTitle = "<span></span>";

$durTitle = "<span>Duration:</span>";
$dateLabel = "";
$datePost = "";

$submitValue = "Confirm";
$submitClass = "smallForm";
$submitStatus = "1";

$fromCurHour = date("H");
$fromCurMinute = date("i");

$reccurenceClass = "enabled";

/**** Defaulting the Hour ****/
if($fromCurHour==0)
	$fromCurHour=12;
elseif($fromCurHour==12) {			
	$fromCurAMPM = "PM";
}
elseif($fromCurHour>12){
	$fromCurHour = $fromCurHour-12;
	$fromCurAMPM = "PM";
}
else{
	$fromCurAMPM = "AM";
}
				
$hourKeys = range(1, 12);

foreach ($hourKeys as $value) {
	$hours[$value] = $value;
}

foreach ($hours as $value) {
	if ($value == $fromCurHour)
		$fromHours["$value|default"] = $value;
	else
		$fromHours[$value] = $value;
}

for ($min = 0; $min <= 55; $min += 5) {
	$minFive = $min +5;
	if ($fromCurMinute >= $min && $fromCurMinute < $minFive) {
		$minutes["$min|default"] = $min;
	} else {
		$minutes[$min] = $min;
	}
}

$AMPM = array ("AM" => "AM", "PM" => "PM");

$fromAMPM = array ();

foreach ($AMPM as $value) {
	if ($value == $fromCurAMPM)
		$fromAMPM["$value|default"] = $value;
	else
		$fromAMPM[$value] = $value;
}

$fromContents = array ("fromHH" => $fromHours, "fromMM" => $minutes, "fromAMPM" => $fromAMPM);

// Form Name, Form Title, Action
$formStart = $formObj->startForm("request","request.php");
//Title, description textboxes     

$output .= $formObj->startDiv("confirmed_info");

	$output .= $formObj->insertTextbox(1, "text", "address", "title","title", $titleLabel, "", $titleValue, "", "", "", 0, 2, "onfocus=\"if(this.value=='Title') {this.value='';}\"");
	$output .= $formObj->insertTextarea(1, "opt_text", "desc", $descLabel, "", $descValue, "", 15, 4, 0, 2, "onfocus=\"if(this.value=='Description') {this.value='';}\"");
		
	$output .= $formObj->insertSmallTitle("Require response within:");
	$responseHours = array(3=>3,6=>6,12=>12,24=>24,36=>36,48=>48);
	$contents2 = array ("responseHH" => $responseHours);
	$output .= $formObj->startDiv("","","smallIndent");
	$output .= $formObj->insertDropdown(1, "dropdown", "", "", "hours", $contents2, "", "", "", false, 0, 2);
	$output .= $formObj->endDiv();
	$output .= $formObj->insertLine("padding-top: 5px; margin-bottom: 5px;");
$output .= $formObj->endDiv();

$output .= $formObj->startDiv("setTimes");
//echo $formObj->startDiv("time.$count"); 
//echo $formObj->endDiv(); 
$output .= $formObj->endDiv();
$output .= $formObj->startDiv("hidden_info");
$output .= $formObj->endDiv();
$output .= $formObj->insertQuickTextbox("hidden", "count", "count",0);

if ($timeTitle)
	$output .= $formObj->insertSmallTitle($timeTitle);

$output .= $formObj->startDiv("","","smallIndent");
$interaction = "onchange=\"selectedDay();\"";
$output .= $formObj->insertTextbox(1, "dateWidget", "date", "userDate", "userDate",$dateLabel, $datePost, $dateValue, "", "", "", 0, 2, $interaction);
$output .= $formObj->insertDropdown(1, "dropdown", "fromTime", $fromLabel, "", $fromContents, "", "formTime", "", false, 0, 2);
$output .= $formObj->endDiv();

//make duration drop down	
$hourKeys2 = range(0, 12);

foreach ($hourKeys2 as $value) {
	if ($value == 1)
		$hours2["$value|default"] = $value;
	else
		$hours2[$value] = $value;
}

for ($min = 0; $min <= 55; $min += 5) {
	if ($min == 0)
		$minutes2["$min|default"] = $min;
	else
		$minutes2[$min] = $min;
}
//Duration drop-down lists
//$contents2 = array ("durationHH" => $hours);

if ($durTitle)
	$output .= $formObj->insertSmallTitle($durTitle);

$contents = array ("durationHH" => $hours2, "durationMM" => $minutes2);
$postLabels = array("hr","min");
$output .= $formObj->startDiv("","","smallIndent");
$output .= $formObj->insertDropdown(1, "opt_dropdown", "duration", "", $postLabels, $contents, "", "", "", false, 0, 2);
$output .= $formObj->endDiv();
$output .= $formObj->insertSpacer();
if (!CommonFunctions::inAgent("MSIE")) {
	$output .= $formObj->insertSpacer();
}
$interaction = "onclick=\"hideShow('recurrenceBox');\"";

$output .= $formObj->startDiv("recurrenceCheckbox", "", "$reccurenceClass");
	$output .= $formObj->insertCheckbox(4, "opt_checkbox", "recurrence", "recurrence", "", "Recurrence", "yes", "$reccurenceClass", "", 0, 2, $interaction);
$output .= $formObj->endDiv();

$visibility = $formObj->checkCheckbox("recurrence"); 

$output .= $formObj->startDiv("recurrenceBox|$visibility");

// Recurrence stuff here
$interaction = "onclick=\"if(this.checked==true){hide('weeklyBox');}\"";
$output .= $formObj->insertSpacer();

$values = array("ed"=>"|Every Day","we"=>"|Weekly");
$interaction = array("1"=>"onclick=\"if(this.checked==true){hide('weeklyBox');}\"","2"=>"onclick=\"if(this.checked==true){show('weeklyBox');selectedDay();}\"");			
$output .= $formObj->insertRadios(4, "radio", "frequency[]","",$values,"",0,2,$interaction);

$output .= $formObj->startDiv("weeklyBox|$visibility");

// Weekly Recurrence
$daysArray = array ("U" => "U", "M" => "M", "T" => "T", "W" => "W", "R" => "R", "F" => "F", "S" => "S");
$days = array ();
foreach ($daysArray as $key => $value) {
	$days[$key] = $value;
}
$output .= $formObj->insertCheckboxes(6, "checkbox", "weekdays[]", "", $days, "checkboxes", 1, 2);
$output .= $formObj->endDiv();

$checkboxLabel = array ("weeklyRecurrence", "Every", 40, "", 1);
$dropdownLabel = array ("weekly", "weeks", 260);

$weekly = array ("1|default" => 1, "2" => 2, "3" => 3, "4" => 4);
$values = array ("weekly" => $weekly);
$output .= $formObj->insertSpacer();
$output .= $formObj->insertDropdown(1, "dropdown", "", "Every", "Week(s)", $values, "", "", "", false, 0, 2);

$output .= $formObj->insertSpacer();

$values = array ("SEMESTER|default" => "End of Semester", "MINI" => "End of Mini", "DATE" => "Specify Date", "FOREVER" => "Forever");
$untilValues = array ("until" => $values);
$onSelect = "if(this.selectedIndex==2) {insertDateWidget('untilDate');} else {document.getElementById('untilDateDiv').innerHTML='';}";

$output .= $formObj->insertDropdown(1, "dropdown", "", "Until", " ", $untilValues, "", "", "", false, 0, 2, $onSelect, false);
$output .= $formObj->startDiv("untilDateDiv");
	/*if($untilWidget=="yes"){
		$output .= "Until Date: ";
		$output .= $formObj->insertDateWidget("untilDate", $specDate);
	}*/
$output .= $formObj->endDiv();

$output .= $formObj->insertSpacer();
$interaction = "onclick=\"hideShow('exceptionsBox');\"";

$output .= $formObj->insertCheckbox(4, "opt_checkbox", "exceptions", "exceptions", "", "Exceptions", "yes", "", "", 0, 2, $interaction);

$visibility = $formObj->checkCheckbox("exceptions");

$output .= $formObj->startDiv("exceptionsBox|$visibility");

$output .= $formObj->startDiv("exceptionsLeft");
$output .= $formObj->insertTextbox(1, "dateWidget", "opt_date", "exceptionDate", "exceptionDate","", $datePost, "", "", "", 0, 2);
$blankArray = array ("exceptionsListBox[]" => array ());
$output .= $formObj->insertDropdown(1, "opt_dropdown", "", "", "", $blankArray, "", "listBox", 6);
$output .= $formObj->endDiv();

$output .= $formObj->startDiv("exceptionsRight");
	$output .= $formObj->insertButton("addException","addException","Add","javascript:validate(document.getElementById('exceptionDate'),'opt_date',2);");				
	$output .= $formObj->insertButton("removeException","removeException","Remove","javascript:removeExceptionPlease()");
$output .= $formObj->endDiv();

$output .= $formObj->endDiv();
$output .= $formObj->startDiv("exceptionsList");
$output .= "<input type=hidden name = 'exCount' id = 'exCount' value = 0>";
$output .= $formObj->endDiv();
$output .= $formObj->endDiv();

$output .= $formObj->startDiv("buttonDiv");
$output .= $formObj->insertButton("Request", "setTime", "Set Time", "javascript: if(checkDuration() == true) { enableDisabled('weekdays'); enableDisable('recurrence'); listMeeting(); document.getElementById('submitDiv').className = 'visible'; document.getElementById('recurrence').disabled = true; selectedDay(); }");
//$output .= $formObj->insertButton("Request", "setTime", "Request", "javascript:enableDisabled('weekdays'); document.getElementById('recurrenceCheckbox').className = ''; enableDisable('recurrence');listMeeting(); document.getElementById('submitDiv').className = 'visible'; document.getElementById('recurrenceCheckbox').className = 'disabled';");
$output .= $formObj->endDiv();

$output .= "<input type='hidden' name = 'personID' id = 'personID' value=''>";

$output .= $formObj->startDiv("submitDiv","","invisible");
//$output .= "<p class = \"twoButtons\">";
$output .= $formObj->insertSubmitButton("Submit", "", $submitValue, 1, "left", "inline", "document.getElementById('personID').value=document.getElementById('person_ids').value; enableDisabled('weekdays'); enableDisable('recurrence');");
$output .= $formObj->insertSubmitButton("Submit", "", "Cancel", 1, "left", "inline");
//$output .= "</p>";
$output .= $formObj->endDiv();


// End form
$formEnd = $formObj->endForm("", 1, 0);

$errorSummary = $formObj->printErrorSummary();
/******* Printing out of the actual page goes here *******/
$output = $formStart.$errorSummary.$output.$formEnd;    
CommonFunctions::printPage($output,$formObj);
    
?>