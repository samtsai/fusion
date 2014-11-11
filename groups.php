<?php
require_once ("includes/common.php");

$formObj = CommonFunctions :: setFormObj(3);

if ($_SESSION["status"] == 2) {
	$pageTitle = "My Groups";

	include ("includes/header.php");
} elseif($_SESSION["status"]==1) {
		//as of this page they are done with the whole profile creation thing.
		//$_SESSION["status"] = 2; 
		$pageTitle = "Set Groups";
		include ("includes/header.php");
		//$_SESSION["status"] = 2;
		$person_id = $_SESSION["person_id"];
		echo "<div class=\"roundcont\">
	   <div class=\"roundtop\">
		 <img src=\"images/tl.gif\" 
		 width=\"15\" height=\"15\" class=\"corner\" 
		 style=\"display: none\" />
	   </div>
		<span class=\"bigTitle\">Invite your team mates to join a group.</span>
	   <p style=\"height: 100px;\">1. If you don't see an invitation to join a group, that means you should probably create one!<br />"."2. Choose an appropriate name for your group.<br>"."3. Type in the Andrew IDs of your group members.<br>"."4. Specify how long the group will last.<br>"."<span class=\"right\">Once you're done, click here: <a href=\"request.php?login=true&person_id=$person_id\"><img src=\"images/finish.gif\" class=\"noBorder\"></a></span></p>
	  
	   <div class=\"roundbottom\">
		 <img src=\"images/bl.gif\" 
		 width=\"15\" height=\"15\" class=\"corner\" 
		 style=\display: none\" />
	   </div>
	</div>";
} 
else {
	CommonFunctions::showLoginMessage("groups");
}

$db = new DB();
$p_id = $_SESSION["person_id"];

if (isset ($_POST["Submit"]) && ($_POST["Submit"] == "Create Group" || $_POST["Submit"] == "Edit Group")) {

	$groupData = Arrays :: getPostVars("");
	
	unset($groupData["memberCount"]);
	$groupNoBlanks = Arrays::clearBlanks($groupData["member"]);

	if (count($groupNoBlanks) > 0) {
		
		if ($formObj->validateForm($groupData)) {
			
			$error = "";
			//person has submitted new group data, retrieve from POST
			$groupName = trim($_POST["groupName"]);
			//echo $groupName;
			
			$member_list = array ();
			$member_list = $groupNoBlanks;
			
			//convert the andrew ids into their person id's
			$db = new DB();
			$id_list = array();
			
			//figure out which semester we're talking about'
			$current = mktime();
			if ($current > FALL_SEMESTER_END)
			{
				$semesterEnd = SPRING_SEMESTER_END;
				if($current > SPRING_MINI_END)
				{
					$mini_end = SPRING_SEMESTER_END;
				}
				else
				{
					$mini_end = SPRING_MINI_END;					
				}
				
			}
			else
			{
				$semesterEnd = FALL_SEMESTER_END;
				if($current > FALL_MINI_END)
				{
					$mini_end = FALL_SEMESTER_END;
				}
				else
				{
					$mini_end = FALL_MINI_END;					
				}
			}
			
			
			
			foreach ($member_list as $andrew) {
				$andrew = trim($andrew);
				
				$query = "SELECT person_id FROM tblPerson WHERE andrewID = '$andrew'";
				if ($id = $db->getScalar($query)) {
					$id_list[] = $id;
				} else { // if the person does not have a profile, give it an ID
					$person = new Person();
					$email_address = LDAPwrapper($andrew);
					$person->setEmail("$email_address");
					$person->setAndrewID($andrew);
					$person->createProfile();
					$id = $person->getPersonID();
					$id_list[] = $id;
				}
		
			} //end foreach
		
			$id_list[] = $_SESSION["person_id"];
			//print_r($id_list);
			//convert end date an int	
			$endDate = trim($_POST["until"]);
			//echo "endDate: $endDate";
			if ($endDate == "SEMESTER") {
				$endDate = $semesterEnd;
			} else
				if ($endDate == "MINI") {
					$endDate = $mini_end;
				} else
					if ($endDate == "DATE") {
						$pieces = explode("/", $_POST["untilDate"]);
						$untilMonth = $pieces[0];
						$untilDay = $pieces[1];
						$untilYear = $pieces[2];
						$endDate = mktime(0, 0, 0, $untilMonth, $untilDay, $untilYear);
					} else
						if ($endDate == "FOREVER") {
							$endDate = FOREVER;
						}
			//set creator as the person id in session
			$creatorID = $_SESSION["person_id"];
	
			//finds all ids that aren't creator - for email
			foreach($id_list as $id){
				if($id!=$creatorID)
					$new_id_list[]=$id;
			}
			if ($groupName != "") { //if the group name isn't empty process the form
				if ($_POST["Submit"] == "Create Group") { //create group...
					$group = new Group();
					$group->setGroupName($groupName);
					$group->setPersonArray($id_list);
					$group->setEndDateTime($endDate);
					$group->setCreatorID($creatorID);

					if($group->createGroup())
					{
						$group->emailInvitation($new_id_list);
					//show confirmation message
						echo "<div id = \"editMSG\"><div class=\"roundcont\">
								   <div class=\"roundtop\">
									 <img src=\"images/tl.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\"display: none\" />
								   </div>
								   
									<span class=\"bigTitle\">$groupName has been created</span>
							  		
								   <div class=\"roundbottom\">
									 <img src=\"images/bl.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\display: none\" />
								   </div>
								</div></div>";
					}
					else
					{
						//need to get a red one of these
						echo "<div id = \"editMSG\"><div class=\"roundcont-f\">
								   <div class=\"roundtop-f\">
									 <img src=\"images/tl-f.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\"display: none\" />
								   </div>
								   
									<span class=\"bigTitle\">$groupName was not created!</span>
							  		
								   <div class=\"roundbottom-f\">
									 <img src=\"images/bl-f.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\display: none\" />
								   </div>
								</div></div>";
					}
					include ("includes/views/createGroup_form.php");
				}
			}
			else
			{
				echo "<div id = \"editMSG\"><div class=\"roundcont-f\">
						<div class=\"roundtop-f\">
		<img src=\"images/tl-f.gif\" width=\"15\" height=\"15\" class=\"corner\" style=\"display: none\" />
						</div>
								   
						<span class=\"bigTitle\">$groupName was not created!</span>
							  		
						<div class=\"roundbottom-f\">
		<img src=\"images/bl-f.gif\" width=\"15\" height=\"15\" class=\"corner\" style=\display: none\" />
					   </div></div></div>";
				include ("includes/views/createGroup_form.php");
			}
				
		}
		else {
			include ("includes/views/createGroup_form.php");
		}
	}
	//there are problems, rerun form with error messages
	else {
		/** no members set */
		$error = "<span class=\"error\">You must enter member names to create a group.</span>";
		include ("includes/views/createGroup_form.php");
	}
} // end if submitted
else { //user has not tried to create a group
	include ("includes/views/createGroup_form.php");
}



include ("includes/footer.php");
?>








