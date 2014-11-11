<?php
    
     echo $formObj->startForm("groups","",0);
        //edit group confirmation
        echo $formObj->startDiv("editMSG","","invisible");  
        echo $formObj->endDiv();
    //Find all groups to accept or reject
		$db = new DB();
		$queryGroups = "SELECT group_id FROM tblPersonGroup WHERE person_id = $p_id AND confirmed = 0";
		$groups = $db->getArray($queryGroups);
		$query = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$p_id' && confirmed = 0";
		if ($groups = $db->getArray($query)) {
			foreach ($groups as $groupID) {
				$query2 = "SELECT group_name FROM tblGroup WHERE group_id = '$groupID'";
				$groupName = $db->getScalar($query2);
				$groupNames["$groupID"] = $groupName;
			}
		}
		if ($groups = $db->getArray($query)) {
			foreach ($groups as $groupID) {
				$query3 = "SELECT creator_id FROM tblGroup WHERE group_id = '$groupID'";
				$creatorID = $db->getScalar($query3);
				//get the andrew id of the creator
				$query4 = "SELECT andrewID FROM tblPerson WHERE person_id = '$creatorID'";
				$creatorName = $db->getScalar($query4);
				$groupCreators["$groupID"] = $creatorName;
			}
		} 
		
		//PRINT OUT JOINS
        if (!empty($groupNames)){
        		foreach ($groupNames as $id => $name) {	 
        			$creator = $groupCreators["$id"];        			
        			
        			echo $formObj->startDiv("div_name_based_on_$id");
	        			echo "<div class=\"roundcont\">
							   <div class=\"roundtop\">
								 <img src=\"images/tl.gif\" 
								 width=\"15\" height=\"15\" class=\"corner\" 
								 style=\"display: none\" />
							   </div>
								<span class=\"bigTitle\">You have been invited to join $name by $creator</span>
							   <p>"; 
							   
						//$buttonNames = array ("Accept", "Reject");
						$pid = $_SESSION["person_id"];
						echo $formObj->insertButton("Accept$id","Accept$id","Accept","javascript:joinGroup($id, $pid)");
						echo $formObj->insertButton("Reject$id","Reject$id","Reject","javascript:rejectGroup($id, $pid)");
						//echo "<a href='javascript:joinGroup($id,$pid);' id = \"accept$id\">Accept</a>";
						//echo "<a href='javascript:joinGroup($id,$pid);' id = \"reject$id\">Reject</a>";
						
						echo "</p>			  
							   <div class=\"roundbottom\">
								 <img src=\"images/bl.gif\" 
								 width=\"15\" height=\"15\" class=\"corner\" 
								 style=\"display: none\" />
							   </div>
							</div>"; 
					echo $formObj->endDiv();      			
        		}//end for each    
        		
        }// end if there are invitations
        
        //Create Group panel
        
        echo $formObj->startDiv("data");
        	echo $error;
       	 	include("create_group2.php");
        echo $formObj->endDiv();
        
       //My Groups
       $db = new DB();
       $id = $_SESSION["person_id"];
       $queryInGroup = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$id' && confirmed = 1";
       $inGroups = $db->getArray($queryInGroup);
        
        if (!empty($inGroups)){
        	
        	//start side panel
			echo HTMLHelper::startPanel("myGroupsTop","");
			echo HTMLHelper::startWidget();
				//echo "<div style=\"margin-bottom: 10px;\">Click on a group to edit it:</div>";	  	
				 
				echo $formObj->startDiv("teamList"); 
        		foreach ($inGroups as $id) {	
        		
        			$group = new Group();
        			$group->setGroupID($id);
        			$group->initialize();
        			$groupID = $group->getGroupID();
        			$groupName = $group->getGroupName();
        			
        			echo "<a href = \"javascript:editGroup($id);\"><div id=\"$groupID\" class=\"group\" onmouseover=\"document.getElementById('$groupID').className='groupOn';\" onmouseout=\"document.getElementById('$groupID').className='groupOff'\";>$groupName</div></a>";

        			//$chooseGroup =  "<a href = \"javascript:editGroup($id);\">Team $groupName</a>";
        			//echo $formObj->insertDescription($chooseGroup);
        		}//end foreach
				echo $formObj->endDiv();//end teamList button
				
				echo $formObj->startDiv("createGroupButton");
				echo $formObj->endDiv();
				
       	 	//end side panel
       	 	echo HTMLHelper::endWidget();
			echo "</div>";
        }// end if there are groups they belong to
        
    

    echo $formObj->endForm("",0,0);
    
    // Set the Session FormObj
    //$_SESSION["formObj"] = $formObj;
?>