<?php 
	
	if(!$pageTitle){
		require_once ("../common.php");
		$formObj = CommonFunctions::setFormObj(4);
	}
	
	//echo "<div class='block'>";
	//echo "<p>&nbsp</p>";
    
            
    echo $formObj->startForm("courses","schedule.php");
    
    	// Add Class Fieldset
        //echo $formObj->startFieldset("Add Class","classForm");
        
        //Category drop-down lists
        $formObj->setFormValue("category","1");
        $contents = array("category" => array ("1"=>"Class", "2"=>"Work", "3"=>"Personal", "6"=>"Meeting"));
        
        if($pageTitle != "Add Courses") {
        
	        $onSelect = "if(this.selectedIndex==1||this.selectedIndex==2||this.selectedIndex==3) {showEventForm();}";
	        echo $formObj->insertDropdown(1, "dropdown", "", "Category", "", $contents, "", "leftLabel","",false,0,0,$onSelect);
			//$contents = array("category" => array ("1"=>"Class", "2|default"=>"Work", "3"=>"Personal"));
			//$categoryMessage = "Category:";	        
	        //echo $formObj->insertDropdown(1,"dropdown","",$categoryLabel,"",$contents,$categoryMessage,"",0,$onSelect); 
        }
        //Textboxes for entering courses
		
		//echo $formObj->insertDescription("Please enter the course number and section of the class.");
		echo $formObj->insertSpacer();
		echo "Please enter the course number and section of the class (15-100A).";
		echo $formObj->insertSpacer();
		
		$db = new DB();
		$semesters = array();
		$db->query("SELECT DISTINCT semester FROM tblCourse");
		
		while($row = $db->getRow())
		{
	        if(mktime() > FALL_MINI_START && $row["semester"]{0} == "S")
	        	$start = "|default";
	        else
	        	$start = "";

			$semesters[$row["semester"].$start] = $row["semester"];
		}
		
        //$contents = array("Semester" => array ("F06"=>"F06", "S07"=>"S07"));
        $contents = array("Semester" => $semesters);
        	
        echo $formObj->insertDropdown(1, "dropdown", "", "Semester", "", $contents, "", "Select Semester:");

		echo $formObj->insertSpacer();
		
		$interaction = "onkeyup=\"validateCourse(this);\"";
		
		echo $formObj->insertTextboxes("text","opt_course","course","","",9,3,"",25,"",0,0, $interaction);
		
    	echo $formObj->insertSubmitButton("Submit","","Add Class");
                
        //echo $formObj->endFieldset();
        
    // End form        
    echo $formObj->endForm("",0,0);

    // Set the Session FormObj
    $_SESSION["formObj"] = $formObj;            
    //echo "</div>";

?>	