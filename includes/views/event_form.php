<?php 
	if(!$pageTitle){
		require_once ("../common.php");
		$formObj = CommonFunctions::resetFormObj(4);
	}

	/**these values originate in the event wrapper */
	if($formType == "Edit Event"){
		$submitValue = "Edit Event";

		if ($typeID == 4) 
			$contents = array("category"=>array("4"=>"Meeting"));	
		else 
			$contents = array("category"=>array("1"=>"Class","2"=>"Work","3"=>"Personal","6"=>"Meeting"));
		
		$titleValue = $name;
		$descValue = $description;
		$dateValue = $startDate;
		
		$fromCurHour = $startHour;
		$fromCurMinute = $startMinutes;
		$fromCurAMPM = $startAMPM;
		
		$toCurHour = $endHour;
		$toCurMinute = $endMinutes;
		$toCurAMPM = $endAMPM;
		$whatevArray = array("category"=>$typeID,"title"=>$titleValue,"desc"=>$descValue,"userDate"=>$dateValue,"fromTime"=>array("fromHH"=>$fromCurHour,"fromMM"=>$fromCurMinute,"fromAMPM"=>$fromCurAMPM),"toTime"=>array("toHH"=>$toCurHour,"toMM"=>$toCurMinute,"toAMPM"=>$toCurAMPM));
		$formObj->setFormData($whatevArray);
				
		if($recurrenceStart || $_POST["recurrence"] == "yes"){
			$formObj->setFormValue("recurrence","yes");
			
			if (isset($frequency)) {
				if($frequency=="we")
					$formObj->setFormValue("frequency","we");
				else
					$formObj->setFormValue("frequency","ed");
			}

			$formObj->setFormValue("weekdays",$recDays);
			$element_count = 0;
			if(!empty($exceptions)){				
				$formObj->setFormValue("exceptions","yes");
				foreach($exceptions as $exceptionTime){
					
					$exceptionTimeArray = getdate($exceptionTime);
					$exceptionMonth = $exceptionTimeArray["mon"];
					$exceptionDay = $exceptionTimeArray["mday"];
					$exceptionYear = $exceptionTimeArray["year"];
					$formattedTime = $exceptionMonth."/".$exceptionDay."/".$exceptionYear;
					$newExceptionArray[]=$formattedTime;
					$exceptionHF .= "<input type ='hidden' name='exception$element_count' id = 'exception$element_count' value = '$formattedTime'>";
					$element_count++;
				}

			}/**end of if exceptions*/
			
			$formObj->setFormValue("weekly",$recurrenceWeeks);
			$formObj->setFormValue("until",$until);
			if($until=="DATE" || $_POST["until"] == "DATE")
				$untilWidget = "yes";
			else 
				$untilWidget = "no";

		}/**end of if recurrence */
				
		$untilValue = $until;
	
	}
	else {
		/***** Time stuff *****/	
		$fromCurHour = date("H");
		$fromCurMinute = date("i");
		$toCurMinute=$fromCurMinute;

		if($fromCurHour==0)
			$fromCurHour=12;
		elseif($fromCurHour==12) {			
			$fromCurAMPM = "PM";
			$toCurAMPM = "PM";
		}
		elseif($fromCurHour>12){
			$fromCurHour = $fromCurHour-12;
			if($fromCurHour==11){
				$fromCurAMPM = "PM";
				$toCurAMPM = "AM";
			}
			else{
				$toCurAMPM = "PM";
				$fromCurAMPM = "PM";
			}
		}
		else{
			if($fromCurHour==11){
				$fromCurAMPM = "AM";
				$toCurAMPM = "PM";
			}
			else{
				$toCurAMPM = "AM";
				$fromCurAMPM = "AM";
			}
		}
		
		if($fromCurHour==12)
			$toCurHour == 1;
		else
			$toCurHour=$fromCurHour+1;
		/***** End Time Stuff *****/
		
		$formType = "Add Event";
							
		if (isset($_GET["index"])) {
			unset($_POST);
		}

		if($_GET["index"]==2){
			$formObj->setFormValue("category","3");
		}
		elseif ($_GET["index"]==1) {
			$formObj->setFormValue("category","2");
		}
		elseif ($_GET["index"]==3) {
			$formObj->setFormValue("category","6");
		}
		else {			
			$cat = $formObj->getFormValue("category");
			
			if (!isset($cat))
				$formObj->setFormValue("category","2");	
		}
		
		$contents = array("category" => array ("1"=>"Class", "2"=>"Work", "3"=>"Personal","6"=>"Meeting"));						
		$titleValue = "Title";
		$descValue = "Description";
		$dateValue = date("m/d/Y");
		
		$submitValue = "Add Event";
	}
	
	if(!$element_count)
		$element_count = 0;
		
	$hourKeys = range(1,12);		
	foreach ($hourKeys as $value) {
		$hours[$value] = $value;	
	}
	
	
	foreach($hours as $value){
		if ($value == $fromCurHour)
    		$fromHours["$value|default"] = $value;
    	else 
    		$fromHours[$value] = $value;
	}
	foreach($hours as $value){
		if ($value == $toCurHour)
    		$toHours["$value|default"] = $value;
    	else 
    		$toHours[$value] = $value;
	}
	
	for($min = 0; $min<=55;$min+=5){
		$minFive = $min+5;
		if($fromCurMinute>=$min && $fromCurMinute<$minFive){
			$fromMinutes["$min|default"]=$min;
		}
		else{
			$fromMinutes[$min]=$min;
		}
	}
	for($min = 0; $min<=55;$min+=5){
		$minFive = $min+5;
		if($toCurMinute>=$min && $toCurMinute<$minFive){
			$toMinutes["$min|default"]=$min;
		}
		else{
			$toMinutes[$min]=$min;
		}
	}
	
	$AMPM = array ("AM"=>"AM","PM"=>"PM");
	
	$fromAMPM = array();
	$toAMPM = array();
	
	foreach($AMPM as $value){
		if($value == $fromCurAMPM)
			$fromAMPM["$value|default"] = $value;			
		else
			$fromAMPM[$value] = $value;			
	}
	
	foreach($AMPM as $value){
		if($value ==$toCurAMPM)
			$toAMPM["$value|default"]=$value;
		else
			$toAMPM[$value] = $value;
	}

	$fromContents = array ("fromHH"=>$fromHours,"fromMM"=>$fromMinutes,"fromAMPM"=>$fromAMPM);		
	$toContents = array ("toHH"=>$toHours,"toMM"=>$toMinutes,"toAMPM"=>$toAMPM);			
	
	$fromTitle = "<span>From:</span>";
	$toTitle = "<span>To:</span>";
	
	//$submitValue = "Add Event";
	$submitClass = "smallForm";	
 	     
    // Profile Information
    // Form Name, Form Title, Action
    $formStart = $formObj->startForm("events","schedule.php");
        //Category drop-down lists
        if($formType=="Edit Event"){
        	$formStart .= "<a href = 'javascript:showEventForm();'>:: Return to Add Event</a><br/><br/>";
        	$formStart .= "<input type=hidden name='eventID' id = 'eventID' value =".$event_id.">";

        	$onSelect = "";
        }
        else {
        	$onSelect = "if(this.selectedIndex==0) {showCourseForm();}";
        }
        $formStart .= "<input type=\"hidden\" id=\"current_calendar\" name=\"current_calendar\" value='' />";
        $categoryLabel = "Category";

        //print_r($contents);
        $output .= $formObj->insertDropdown(1,"dropdown","",$categoryLabel,"",$contents,"","leftLabel","",false,0,2,$onSelect); 
        $output .= $formObj->insertSpacer();   
        //Title, description textboxes       
   		$output .= $formObj->insertTextbox(1,"text","address","title","title",$titleLabel,"",$titleValue,"",50,75,0,2,"onfocus=\"if(this.value=='Title') {this.value='';}\"");
   		$output .= $formObj->insertTextarea(1,"opt_text","desc",$descLabel,"",$descValue,"",9,4,0,2,"onfocus=\"if(this.value=='Description') {this.value='';}\"");
				
		//$interaction = "onchange=\"selectedDay();\"";		
		$output .= $formObj->insertTextbox(1,"dateWidget","date","userDate","userDate",$dateLabel,$datePost,$dateValue,"","","",0,2,$interaction);			
					
		if ($fromTitle)
			$output .= $fromTitle;
		
		$output .= $formObj->insertDropdown(1,"dropdown","fromTime",$fromLabel,"",$fromContents,"","formTime","",false,0,2);
		
		if ($toTitle)
			$output .= $toTitle;
			
		$output .= $formObj->insertDropdown(1,"dropdown","toTime",$toLabel,"",$toContents,"","formTime","",false,0,2);                         
			
		$interaction = "onclick=\"hideShow('recurrenceBox');\"";
		$output .= $formObj->insertSpacer();
		$output .= $formObj->insertCheckbox(4,"opt_checkbox","recurrence","recurrence","","Recurrence","yes","","",0,2,$interaction);
		
		$visibility = $formObj->checkCheckbox("recurrence");
		
		$output .= $formObj->startDiv("recurrenceBox|$visibility");			

			// Recurrence stuff here
			
			$values = array("ed"=>"|Every Day","we"=>"|Weekly");
			$interaction = array("1"=>"onclick=\"if(this.checked==true){hide('weeklyBox');}\"","2"=>"onclick=\"if(this.checked==true){show('weeklyBox');selectedDay();}\"");		
			$output .= $formObj->insertRadios(4, "radio", "frequency[]","",$values,"",0,2,$interaction);
			
			$visibility = $formObj->checkCheckbox("frequency","we");
			
			$output .= $formObj->startDiv("weeklyBox|$visibility");
				// Weekly Recurrence
				$daysArray = array ("U" => "U", "M" => "M", "T" => "T", "W" => "W", "R" => "R", "F" => "F", "S" => "S");
				$days = array();			
				foreach ($daysArray as $key => $value) {
						$days[$key] = $value;	
				}
				$output .= $formObj->insertCheckboxes(6,"opt_checkbox","weekdays[]","",$days,"",1,2);
			$output .= $formObj->endDiv();
							                     
            $weekly = array ("1"=>1,"2"=>2,"3"=>3,"4"=>4);
                    	
            $values = array ("weekly" => $weekly);
            $output .= $formObj->insertSpacer();  
            $output .= $formObj->insertDropdown(1,"dropdown","","Every","Week(s)",$values,"","","",false,0,2);    				
    	
	        $values = array ("SEMESTER|default"=>"End of Semester", "MINI"=>"End of Mini", "DATE"=>"Specify Date", "FOREVER"=>"Forever");
	        $untilValues = array ("until"=>$values);
           			
			$onSelect = "if(this.selectedIndex==2) {insertDateWidget('untilDate');} else {document.getElementById('untilDateDiv').innerHTML='';}";			
			$output .= $formObj->insertSpacer();
			$output .= $formObj->insertDropdown(1, "dropdown", "", "Until", "", $untilValues,"","","",false,0,2,$onSelect);
			$output .= $formObj->startDiv("untilDateDiv");
				if($untilWidget=="yes"){				
					$output .= $formObj->insertTextbox(1, "dateWidget", "opt_date", "untilDate", "untilDate", "", "", $specDate, "untilDateStuff", "", "", 0, 2, $interaction);					
				}
			$output .= $formObj->endDiv();
			$interaction = "onclick=\"hideShow('exceptionsBox');\"";
			$output .= $formObj->insertSpacer();
			$output .= $formObj->insertCheckbox(4,"opt_checkbox","exceptions","exceptions","","Exceptions","yes","","",0,2,$interaction);

			$visibility = $formObj->checkCheckbox("exceptions");

			$output .= $formObj->startDiv("exceptionsBox|$visibility");	
				$output .= $formObj->startDiv("exceptionsLeft");	
				$output .= $formObj->insertTextbox(1,"dateWidget","opt_date","exceptionDate","exceptionDate","","",$datePost,"","","",0,2);
				//$output .= $formObj->insertButton("addException","addException","Add","javascript:validate(document.getElementById('exceptionDate'),'opt_date',2);");				
				//$output .= $formObj->insertButton("removeException","removeException","Remove","javascript:removeExceptionPlease()");
				if($formType=="Edit Event"){
					if(!empty($newExceptionArray))
						$blankArray = array("exceptionsListBox[]"=>$newExceptionArray);
					else
						$blankArray = array("exceptionsListBox[]"=>array());
				}
				else
					$blankArray = array("exceptionsListBox[]"=>array());
				$output .= $formObj->insertDropdown(1,"opt_dropdown","","","",$blankArray,"","listBox",6);
				$output .= $formObj->endDiv();
				$output .= $formObj->startDiv("exceptionsRight");
					$output .= $formObj->insertButton("addException","addException","Add","javascript:validate(document.getElementById('exceptionDate'),'opt_date',2);");				
					$output .= $formObj->insertButton("removeException","removeException","Remove","javascript:removeExceptionPlease()");
				$output .= $formObj->endDiv();
			$output .= $formObj->endDiv();
			$output .= $formObj->startDiv("exceptionsList");
			$output .= "<input type='hidden' name = 'exCount' id = 'exCount' value = $element_count>";
			if ($exceptionHF) 
				$output .= $exceptionHF;
			
			$output .= $formObj->endDiv();
			

		$output .= $formObj->endDiv(); 
		          		          				                      
    /******* End the Form *******/
    $formEnd .= $formObj->insertSubmitButton("Submit", "", $submitValue, 1, "left", $submitClass, "javascript:enableDisabled('weekdays'); document.getElementById('current_calendar').value = document.getElementById('start_time').value;");
    $formEnd .= $formObj->endForm("Next",1,0);
				
    $errorSummary = $formObj->printErrorSummary();
    
    /******* Printing out of the actual page goes here *******/
    $output = $formStart.$errorSummary.$output.$formEnd;    
    CommonFunctions::printPage($output,$formObj);
     		
?>

	