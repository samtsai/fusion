<?php
/***********************************************************************************
 *                            CREATE GROUP PANEL                                   *
 ***********************************************************************************/

        echo HTMLHelper::startPanel("createGroupTop");
        //This is the div where the forms will change create <-> edit
	        	
		        echo $formObj->insertTextbox(1,"text","opt_address","groupName","groupName","Group Name");
			    echo $formObj->insertSpacer();
			    echo $formObj->insertDescription("Enter in the Andrew IDs of your group members.");
			    echo $formObj->insertTextboxes("text","opt_andrew","member","Member ","",5,1);
			    echo $formObj->startDiv("members");    	        
		        echo $formObj->endDiv();
		        echo "<input type=hidden name='memberCount' id = 'memberCount' value = 5>";
		        $link = "<a href = 'javascript:addGroupMember()'>:: Add more members</a>";
	        	echo $formObj->insertDescription($link);
	        	echo $formObj->insertSpacer();
	        	
	        	$values = array ("SEMESTER|default"=>"End of Semester", "MINI"=>"End of Mini", "DATE"=>"Specify Date", "FOREVER"=>"Forever");
	        	$untilValues = array ("until"=>$values);         
				$onSelect = "if(this.selectedIndex==2) {insertDateWidget('untilDate');} else {document.getElementById('untilDateDiv').innerHTML='';}";
			
				echo $formObj->insertDropdown(1, "opt_dropdown", "until", "Until", "", $untilValues,"","","",false,0,1,$onSelect);
				echo $formObj->startDiv("untilDateDiv","","untilDateClass");
				echo $formObj->endDiv();  
		        
		        echo $formObj->insertSubmitButton("Submit","","Create Group");
		        echo $formObj->insertSpacer();
	        
		//end panel
		echo HTMLHelper::endPanel();
		//echo "<div class=\"clear\"></div>";		
 
?>
