<?php


/*
 * Created on Apr 1, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

include_once ("../common.php");
$db = new DB();

if (isset ($_GET["addMember"])) {
	$num = $_GET["count"];
	$formObj = CommonFunctions :: setFormObj(3);

	echo $formObj->insertTextbox(1, "text", "opt_andrew|1", "member[]", "member$num", "Member $num");
}

if (isset ($_GET["addNewMember"])) {
	$num = $_GET["newCount"];
	$formObj = CommonFunctions :: setFormObj(3);

	echo $formObj->insertTextbox(1, "text", "opt_andrew|1", "newMember", "newMember$num", "New Member $num");
}
/*******************************************************************
 * EDIT GORUP FUNCTIONALITY
 * calls edit group form after
 ******************************************************************/
if (isset ($_GET["editID"]) && isset ($_GET["groupName"]) && isset ($_GET["endDate"])) {

	$groupID = $_GET["editID"];
	$groupName = stripslashes(trim($_GET["groupName"]));
	$endDate = stripslashes(trim($_GET["endDate"]));

	if ($endDate === "SEMESTER") {
		$endDate = SEMESTER_END;
	}
	elseif ($endDate === "MINI") {
		$endDate = MINI_END;
	}
	elseif ($endDate === "FOREVER") {
		$endDate = FOREVER;
	} else {
		$pieces = explode("/", $endDate);
		$untilMonth = $pieces[0];
		$untilDay = $pieces[1];
		$untilYear = $pieces[2];
		$endDate = mktime(0, 0, 0, $untilMonth, $untilDay, $untilYear);
	}

	$newMembers = array ();
	$ids = array ();
	$count = 1;
	while (isset ($_GET["newMember$count"])) {
		$newMembers[] = $_GET["newMember$count"];
		$count ++;
	}

	//Arrays::printArray($newMembers, 1);
	$db = new DB();

	$group = new Group();
	$group->setGroupID($groupID);
	$group->setGroupName($groupName);
	$group->setEndDateTime($endDate);

	if (!empty ($newMembers)) {
		$errors = "";
		foreach ($newMembers as $andrewID) {
			if ($andrewID != "") {
				$query = "SELECT person_id FROM tblPerson WHERE andrewID = '$andrewID'";
				if ($p_id = $db->getScalar($query)) {
					$ids[] = $p_id;
					if(!$group->invitePerson($p_id))
					{
						echo "$andrewID was not added because that andrewID has already been invited to the system.  Please wait for them to confirm the invitation before adding them to any groups.<br/>";
					}
				} else { // if the person does not have a profile, give it an ID

					if ($email = LDAPwrapper($andrewID)) {
						$person = new Person();
						$person->setEmail($email);
						$person->setAndrewID($andrewID);
						$person->createProfile();
						$id = $person->getPersonID();

						$ids[] = $id;
						if(!$group->invitePerson($p_id))
						{
							echo "$andrewID was not added because that andrewID has already been invited to the system.  Please wait for them to confirm the invitation before adding them to any groups.<br/>";
						}
					} else {
						$errors .= "<div class=\"member_error\"> The ID $andrewID was not a valid CMU identification, and so was not added to the group </div>";
					}
				}
			} //end if andrew != ""
		} //end foreach
	} //end if		

	if (!empty ($ids)) {
		$group->emailInvitation($ids);
	}

	if ($errors != "")
		echo "<div id=\"group_errors\" class=\"error\"> $errors </div>";

	$group->updateGroup();

}

/*******************************************************************
 * PRINTING OUT THE EDIT GROUP FORM
 ******************************************************************/

if (isset ($_GET["editGroupID"]) || isset ($_GET["editID"])) {
	if (isset ($_GET["editGroupID"])) {
		$id = stripslashes(trim($_GET["editGroupID"]));
	}
	if (isset ($_GET["editID"])) {
		$id = stripslashes(trim($_GET["editID"]));
	}

	if ($id) {

		$group = new Group();
		$group->setGroupID($id);
		$_SESSION["groupID"] = $id;
		$group->initialize();

		$groupName = $group->getGroupName();
		$person_array = $group->getPersonArray();
		$endDate = $group->getEndDateTime();
		$creatorID = $group->getCreatorID();

		$queryCreaterID = "WHERE person_id = '$creatorID'";
		$createrInfo = $db->getOneRecord("tblPerson", $queryCreaterID);
		$creater_fn = $createrInfo["first_name"];
		$creater_ln = $createrInfo["last_name"];

		$formObj = new FormManager();
		//panel start	
		echo "<div class=\"floatLeft\">";
		echo "<div id=\"editGroupTop\"></div>";
		echo "<div id=\"panel\">";

		//echo $formObj->startFieldset("Edit Group:", "editGroupForm", 1);

		echo "<div id=\"groupTitle\">$groupName</div>";

		if ($_SESSION["person_id"] == $creatorID) {
			echo "<div id=\"membersLabel\">Edit Details</div>";

			echo "<div id=\"editDetails\">";
			$_POST["groupName"] = $groupName;
			echo $formObj->insertTextbox(1, "text", "opt_name", "groupName", "groupName", "Group Name", "", $groupName);

			if ($endDate == SEMESTER_END) {
				$values = array ("SEMESTER|default" => "End of Semester", "MINI" => "End of Mini", "DATE" => "Specify Date", "FOREVER" => "Forever");
			}
			elseif ($endDate == MINI_END) {
				$values = array ("SEMESTER" => "End of Semester", "MINI|default" => "End of Mini", "DATE" => "Specify Date", "FOREVER" => "Forever");
			}
			elseif ($endDate == FOREVER) {
				$values = array ("SEMESTER" => "End of Semester", "MINI" => "End of Mini", "DATE" => "Specify Date", "FOREVER|default" => "Forever");
			} else {
				$values = array ("SEMESTER" => "End of Semester", "MINI" => "End of Mini", "DATE|default" => "Specify Date", "FOREVER" => "Forever");
				date("m/d/Y", $endDate);
			}
			$untilValues = array ("until" => $values);
			$onSelect = "if(this.selectedIndex==2) {insertDateWidget('untilDate');} else {document.getElementById('untilDateDiv').innerHTML='';}";

			echo $formObj->insertDropdown(1, "opt_dropdown", "until", "Until", "", $untilValues, "", "", "", false, 0, 1, $onSelect);

			echo $formObj->startDiv("untilDateDiv", "", "untilDateClass");
			if ($endDate != SEMESTER_END && $endDate != MINI_END && $endDate != FOREVER) {
				$dateValue = date("m/d/Y", $endDate);
				$datePost = "";
				//$interaction = "onchange=\"selectedDay();\"";
				echo $formObj->insertTextbox(1, "dateWidget", "opt_date", "untilDate", "untilDate", "", $datePost, $dateValue, "", "", "", 0, 2);
			}

			echo $formObj->endDiv();

			echo $formObj->insertSpacer();
			echo $formObj->startDiv("newMembers");
			//add new members	        
			echo $formObj->endDiv();
			echo "<input type=hidden name='newMemberCount' id = 'newMemberCount' value = 0>";
			$link = "<a href = 'javascript:addNewGroupMember();'>:: Add more members</a>";
			echo $formObj->insertDescription($link);
			echo $formObj->insertSpacer();

			echo "</div>";
			echo "<div class=\"groupSpace\" style=\"margin-left: 100px; margin-top: -20px\">".$formObj->insertButton("", "", "Edit Group", "javascript:edit($id)")."</div>";
		} else {
			echo "<div id=\"membersLabel\">Details</div>";

			echo "<div id=\"groupDetails\">";
			echo "<div class=\"spanRow\">Group Name:</div> $groupName<br>";
			echo "<div class=\"spanRow\">Created by:</div> $creater_fn $creater_ln<br>";
			echo "<div class=\"spanRow\">In session until:</div> ".date("l, F j, Y", $endDate)."<br>";

			echo "</div>";
		}

		echo "<div id=\"membersLabel\">Members</div>";
		echo "<div class=\"membersHeader\">"."<div style=\"width: 200px\">Name</div>"."<div style=\"width: 200px\">E-mail</div>"."<div style=\"width: 100px\">Phone</div>"."<div style=\"width: 100px\">AIM</div>"."<div>Confirmed?</div>"."</div>";

		foreach ($person_array as $p_id => $confirmed) {
			$queryMemberInfo = "WHERE person_id = '$p_id'";
			$memberInfo = $db->getOneRecord("tblPerson", $queryMemberInfo);
			//$confirmedInfo = $db->getOneRecord("tblPersonGroup", $queryMemberInfo);

			$fn = $memberInfo["first_name"];
			$ln = $memberInfo["last_name"];
			$sn = $memberInfo["screenname"];
			$phone = $memberInfo["phone"];
			$andrewID = $memberInfo["andrewID"];
			$email = $memberInfo["email"];
			

	
			if($phone == "NULL")
				$phone = "N/A";
			if($sn == "NULL")
				$sn = "N/A";

			//$confirmedStatus = $confirmedInfo["confirmed"];
			//$notification = $memberInfo["email_time"];

			if ($confirmed == "0") {
				$unconfirmed = "unconfirmed";
			} else {
				$unconfirmed = "";
			}

			if($fn != "") { //don't show blank members that are in the database...
				echo "<div class=\"memberInfo $unconfirmed\">";
	
				echo "<div style=\"width: 200px\">$fn $ln</div>"."<div style=\"width: 200px\"><a href=\"mailto:$email\">$email</a></div>";
	
				if (($phone != "") || ($phone != NULL))
					echo "<div style=\"width: 100px\">$phone</div>";
				else
					echo "<div style=\"width: 100px\">N/A</div>";
	
				if ($sn != NULL || $sn == "NULL")
					echo "<div style=\"width: 100px\">$sn</div>";
				else
					echo "<div style=\"width: 100px\">N/A</div>";
	
				if ($confirmed == 1)
					echo "<div>Yes</div>";
				else
					echo "<div>No</div>";
	
				echo "</div>";
			}
		} //end foreach

		//echo "<div class=\"membersHeader\"><a href='' style=\"color: #6c7794\">:: Add Members</a></div>";

		$pid = $_SESSION['person_id'];
		//echo "<div class=\"groupSpace\">".$formObj->insertButton("Leave$id", "Leave$id", "Leave", "javascript:reject($id, $pid)")."</div>";

		echo "<div class=\"groupSpace\" align = \"right\">".$formObj->insertButton("Leave$id", "Leave$id", "Leave Group", "javascript:leaveGroup($id, $pid)")."</div>";
		/*echo "<br>";
		echo $formObj->insertTextboxes("text", "opt_andrew|1", "newMember", "New Member ", "", 5, 1);
		echo "<a href = '' style='padding-left: 130px;'> add more members </a>";*/

		//panel end
		echo "</div>";
		echo "<div id=\"panelBottom\"></div>";
		echo "</div>";
	} //end if id
} //end edit group form

/*******************************************************************
 * PRINTING OUT THE CREATE GROUP FORM
 ******************************************************************/

if (isset ($_GET["createGroup"])) {

	$formObj = new FormManager();
	include ("../views/create_group2.php");

	//figure out the groups left the user has still been invited to
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
}

/*******************************************************************
 * JOIN GROUP
 ******************************************************************/
if (isset ($_GET["joinGroupID"]) && isset ($_GET["personID"])) {

	$id = stripslashes(trim($_GET["joinGroupID"]));
	$p_id = stripslashes(trim($_GET["personID"]));
	if ($id && $p_id) {
		$group = new Group();
		$group->setGroupID($id);
		$group->initialize();
		$group->joinGroup($p_id);

		//figure out the groups left the user has still been invited to
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

		//replacing the right panel with the updated group list
		$queryInGroup = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$p_id' && confirmed = 1";
		$inGroups = $db->getArray($queryInGroup);
		foreach ($inGroups as $id) {

			$group = new Group();
			$group->setGroupID($id);
			$group->initialize();
			$groupID = $group->getGroupID();
			$groupName = $group->getGroupName();

			echo "<a href = \"javascript:editGroup($id);\"><div id=\"$groupID\" class=\"group\" onmouseover=\"document.getElementById('$groupID').className='groupOn';\" onmouseout=\"document.getElementById('$groupID').className='groupOff'\";>$groupName</div></a>";

		} //end foreach

	} //end if
} //end if isset

/*******************************************************************
 * PRINTING OUT LEFT PANEL OF GROUP NAMES
 ******************************************************************/
if (isset ($_GET["relistTeams"])) {

	//figure out the groups left the user has still been invited to
	$p_id = $_SESSION["person_id"];
	$database = new DB();
	$queryGroups = "SELECT group_id FROM tblPersonGroup WHERE person_id = $p_id AND confirmed = 0";
	$groups = $database->getArray($queryGroups);
	$query = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$p_id' && confirmed = 0";
	if ($groups = $database->getArray($query)) {
		foreach ($groups as $groupID) {
			$query2 = "SELECT group_name FROM tblGroup WHERE group_id = '$groupID'";
			$groupName = $database->getScalar($query2);
			$groupNames["$groupID"] = $groupName;
		}
	}
	if ($groups = $database->getArray($query)) {
		foreach ($groups as $groupID) {
			$query3 = "SELECT creator_id FROM tblGroup WHERE group_id = '$groupID'";
			$creatorID = $database->getScalar($query3);
			//get the andrew id of the creator
			$query4 = "SELECT andrewID FROM tblPerson WHERE person_id = '$creatorID'";
			$creatorName = $database->getScalar($query4);
			$groupCreators["$groupID"] = $creatorName;
		}
	}

	//replacing the right panel with the updated group list
	$queryInGroup = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$p_id' && confirmed = 1";
	$inGroups = $database->getArray($queryInGroup);
	foreach ($inGroups as $id) {

		$group = new Group();
		$group->setGroupID($id);
		$group->initialize();
		$groupID = $group->getGroupID();
		$groupName = $group->getGroupName();

		echo "<a href = \"javascript:editGroup($id);\"><div id=\"$groupID\" class=\"group\" onmouseover=\"document.getElementById('$groupID').className='groupOn';\" onmouseout=\"document.getElementById('$groupID').className='groupOff'\";>$groupName</div></a>";

	} //end foreach
}

/*******************************************************************
 * REJECT GROUP
 ******************************************************************/

if (isset ($_GET["rejectGroupID"]) && isset ($_GET["personID"])) {
	$id = stripslashes(trim($_GET["rejectGroupID"]));
	$p_id = stripslashes(trim($_GET["personID"]));
	if ($id && $p_id) {
		$group = new Group();
		$group->setGroupID($id);
		$group->initialize();
		$groupName = $group->getGroupName();
		$group->leaveGroup($p_id);

		//figure out the groups left the user has still been invited to
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
		/*echo "<div class=\"roundcont\"><div class=\"roundtop\">
				 <img src=\"images/tl.gif\" 
				 width=\"15\" height=\"15\" class=\"corner\" 
				 style=\"display: none\" />
			   </div>
				<span class=\"bigTitle\">You have rejected an invitation to $groupName </span>
		  
			   <div class=\"roundbottom\">
				 <img src=\"images/bl.gif\" 
				 width=\"15\" height=\"15\" class=\"corner\" 
				 style=\display: none\" />
			   </div></div>";
		//replacing the right panel with the updated group list
		/*$queryInGroup = "SELECT group_id FROM tblPersonGroup WHERE person_id = '$p_id' && confirmed = 1";
			$inGroups = $db->getArray($queryInGroup);
		foreach ($inGroups as $id) {	
				
			$group = new Group();
			$group->setGroupID($id);
			$group->initialize();
			$groupName = $group->getGroupName();
					
			echo "<a href = \"javascript:editGroup($id);\"><div id=\"$groupName\" class=\"group\" onmouseover=\"document.getElementById('$groupName').className='groupOn';\" onmouseout=\"document.getElementById('$groupName').className='groupOff'\";>$groupName</div></a>";
		
		}//end foreach*/
	} //end if
} //end if isset

/*******************************************************************
 * LEAVE GROUP
 ******************************************************************/
if (isset ($_GET["leaveGroupID"]) && isset ($_GET["personID"])) {
	$id = stripslashes(trim($_GET["leaveGroupID"]));
	$p_id = stripslashes(trim($_GET["personID"])); //echo "leave wrapper reached!";
	if ($id && $p_id) {
		$group = new Group();
		$group->setGroupID($id);
		$group->initialize();
		$groupName = $group->getGroupName();
		$group->leaveGroup($p_id);

	} //end if
} //end if isset
