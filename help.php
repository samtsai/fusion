<?php
/*
 * Created on Apr 27, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 require_once ("includes/common.php");
 $pageTitle = "Help";
 include ("includes/header.php");

 
 echo "<div class=\"block\">";
 
 echo "

	<a name=\"top\"></a><b>Table of Contents</b><br><br>
		<ul>
		<li><a href=\"#intro\">Introduction</a><br>
		
		<ul><li><a href=\"#login\">Logging In</a></li>
		<li><a href=\"#logout\">Logging Out</a></li><br>
		</ul>
		</li>
		
		<li><a href=\"#profile\">My Profile</a>
		<ul><li><a href=\"#profEdit\">Editing your Profile</a></li>
		<li><a href=\"#pswdChange\">Changing your Password</a></li>
		<li><a href=\"#pswdReset\">Resetting your Password</a></li><br>
		</ul>
		</li>
	
		<li><a href=\"#groups\">My Groups</a>
		<ul><li><a href=\"#grpInvite\">Responding to a Group Invitation</a></li>
		<li><a href=\"#grpCreate\">Creating a Group</a></li>
		<li><a href=\"#grpEdit\">Editing a Group</a></li>
		<li><a href=\"#grpView\">Viewing Group Details</a></li>
		<li><a href=\"#grpLeave\">Leaving a Group</a></li><br>
		</ul>
		</li>
		
		<li><a href=\"#grpCal\">Group Schedule</a>
		<ul><li><a href=\"#mtgRequest\">Requesting a Meeting</a></li>
		<li><a href=\"#recurr\">Recurrence</a></li>
		<li><a href=\"#mtgRespond\">Responding to Meeting Times</a></li>
		<li><a href=\"#mtgDelete\">Deleting a Meeting</a></li><br>
		</ul>
		</li>
		
		<li><a href=\"#schedule\">My Schedule</a>
		<ul><li><a href=\"#eventAdd\">Adding an Event</a></li>
		<li><a href=\"#recurr\">Recurrence</a></li>
		<li><a href=\"#eventCourse\">Adding a Course</a></li>
		<li><a href=\"#eventView\">Viewing Event Details</a></li>
		<li><a href=\"#eventEdit\">Editing an Event</a></li>
		<li><a href=\"#eventDelete\">Deleting an Event</a></li><br>
		</ul>
		</li>
		</ul>
		<div id=\"help\">
		<a name=\"intro\"></a><b>Introduction</b><br><br>
		
		Fusion is an online system created to facilitate scheduling student group meetings in the easiest and most efficient manner by combining individual schedules into a group calendar.
		<p>
		<center><img width=\"640\" height=\"360\" src=\"images/help/LoginScreen.png\"></center>
		<p>
		The main goals of Fusion are:
		<p>
		<ul>	
			<li>To address the problem of scheduling project meetings for multiple people quickly</li>
			<li>To maximize the ease of finding meeting times while minimizing time conflicts</li>
			<li>To streamline the process of populating the personal schedule</li>
		</ul><br>
		<p>
		This tutorial provides project group members assistance with the main functions of Fusion, including <a href=\"#login\">logging in</a> and <a href=\"#logout\">out</a>, <a href=\"#profile\">profiles</a>, <a href=\"#groups\">groups</a>, <a href=\"#schedule\">personal schedules</a>, <a href=\"#grpCal\">group schedules</a>, and <a href=\"#mtgRequest\">meeting requests</a>.
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"login\"></a><b>Logging In</b><br><br>
		
		The current URL for Fusion is <a href=\"http://rook.hss.cmu.edu/~team04-S06\">http://rook.hss.cmu.edu/~team04-S06</a>. If you already have a valid login email and password, enter them in the log in box now:
		<p>
		<center><img width=\"190.2\" height=\"192.6\" src=\"images/help/LoginBox.png\"></center>
		<p>
		If you are not an active user of Fusion, click on the <img align=\"middle\" width=\"110\" height=\"42\" src=\"images/help/CreateProfile.png\"> button to begin the process of creating and activating your own profile.
		<p>
		To learn more about resetting your login email or password, see <a href=\"#profEdit\">Editing your Profile</a>. If you have forgotten your password, refer to the section on <a href=\"#pswdReset\">Resetting your Password</a>.
		<p>
		Once you have entered your user information and clicked <img width=\"63\" height=\"24.3\" src=\"images/help/Login.png\">, you should be directed to your Fusion home page:
		<p>
		<center><img width=\"515\" height=\"395.5\" src=\"images/help/GroupSch.png\"></center>
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"logout\"></a><b>Logging Out</b><br><br>

		After you have finished using Fusion, click on the <img width=\"58.9\" height=\"17.1\" src=\"images/help/LogOut.png\"> link in the top right-hand corner of your screen, to the left of your user name.
		<p>
		Once you click on this link, you will be logged out of the Fusion system and redirected back to the login page:<p>
		<center><img width=\"517.5\" height=\"267.5\" src=\"images/help/LoginScreen.png\"></center>
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>



		<a name=\"profile\"></a><b>My Profile</b><br><br>

		Your profile page is accessed by clicking on the <img width=\"73\" height=\"18\" src=\"images/help/ProfileBtn.png\"> button in the top menu bar:<p>
		<center><img width=\"523\" height=\"451\" src=\"images/help/Step1ProfBlank.png\"></center>
		<p>
		
		Here, you have the option of <a href=\"#profEdit\">editing</a> your information, which includes:
		<p>
		<ul>
			<li>Personal Information: name, <a href=\"#pswdChange\">password</a>, secret question and answer</li>
			<li>Contact Information: email (if other than your andrew account), phone number, and meeting reminder option</li>
		</ul>

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"profEdit\"></a><b>Editing your Profile</b><br><br>

		After you have navigated to the <a href=\"#profile\">My Profile</a> page, the system will display your current information except for your <a href=\"#pswdChange\">password</a>. <p>
		
		<center><img width=\"508\" height=\"414\" src=\"images/help/EditProfileForm.png\"></center>
		<p>
		To make changes, you must not leave any fields with an * blank. However, you are not allowed to change the Andrew ID field for security purposes.<p>
 
The secret question and answer fields will not reflect your current question and answer, but they are still saved in the system. Only enter new information here if you wish to change it from the question you last entered.<p>

Once you are satisfied with any new changes, click on the <img align=\"middle\" width=\"88\" height=\"24\" src=\"images/help/EditProfile.png\"> button at the bottom of the page. Once the changes have successfully been made, the page will reload with a confirmation and will reflect your new information.<p>
		
		<center><img width=\"504\" height=\"50\" src=\"images/help/ChangesSaved.png\"><br><img width=\"507\" height=\"412\" src=\"images/help/Profile.png\"></center>
		<p>
		In the event that the changes do not get saved or an error is displayed, you may want to try the process again. If it still is not working, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know the problem.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"pswdChange\"></a><b>Changing your Password</b><br><br>

		You have the option of changing your Fusion password under the Personal Information section of the <a href=\"#profile\">My Profile</a> page:
		<p>
		<center><img width=\"278\" height=\"71\" src=\"images/help/ChangePswdBlank.png\"></center>
		<p>
		If you are making any kind of changes to <a href=\"#profile\">your profile page</a>, you are required to at least enter your current password in the first box, since it is a required field with an *. When you want to change your password, you must alslo first enter your current password. There should be two remaining text boxes under the password section; type your desired new password in the \"New Password\" and \"Verify New Password\" boxes. These new passwords must be identical to each other for your changes to be accepted.<p>
		<center><img width=\"281\" height=\"83\" src=\"images/help/ChangePswd.png\"></center>
		<p>
		When you have entered your current password and are satisfied with the new password, click on the <img align=\"middle\" width=\"88\" height=\"24\" src=\"images/help/EditProfile.png\"> button at the bottom of the page. Once the changes have successfully been made, the page will reload with a confirmation and will reflect your new information, the same as any changes made to the other profile information.<p>
		<center><img width=\"504\" height=\"50\" src=\"images/help/ChangesSaved.png\"></center>
		<p>
		In the event that the changes do not get saved or an error is displayed, you may want to try the process again. If it still is not working, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know the problem.<p>
		If you have forgotten your current password, refer to <a href=\"#pswdReset\">Resetting your Password</a> since a new password cannot be entered in the <a href=\"#profile>My Profile</a> page without a valid password entered in the \"Current Password\" box.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"pswdReset\"></a><b>Resetting your Password</b><br><br>

		In the event that you forget your Fusion password, you will receive an error and not be able to log in:
		<p>
		<center><img width=\"265\" height=\"190\" src=\"images/help/PswdWrong.png\"></center>
		<p>
		If you cannot remember your password, make sure your email is entered and click on the <img align=\"middle\" width=\"232\" height=\"17\" src=\"images/help/ForgotPswd.png\"> link underneath the <img align=\"middle\" width=\"63\" height=\"24\" src=\"images/help/Login.png\"> button. The system will prompt you for the answer to your secret question:
		<p>
		<center><img width=\"260\" height=\"266\" src=\"images/help/SecretQuest2.png\"></center>
		<p>
		f you do not want to reset your password, click <img align=\"middle\" width=\"63\" height=\"25\" src=\"images/help/Cancel.png\"> now. If you wish to reset it, enter your secret answer in the box and click <img align=\"middle\" width=\"67\" height=\"25\" src=\"images/help/Submit.png\"> to change your password in the system. At this point, the system will send you an email and display a confirmation:<p>
		<center><img width=\"258\" height=\"62\" src=\"images/help/forgotMsg.png\"></center>
		<p>
		When you receive the email, it will list your current login password and a new random password that the system generated for your account:
		<p>
		<center><img width=\"507\" height=\"250\" src=\"images/help/forgotEmail.png\"></center>
		<p>
		Follow the instructions in the email and click the link to return to the Fusion <a href=\"#login\">login page</a>. Enter your login email and the new password from your email to enter your system account. Once you have logged in, it is strongly recommended that you click on the <img width=\"73\" height=\"18\" src=\"images/help/ProfileBtn.png\"> button of the menu bar right away to set a different password of your choice. For more help, see the section on <a href=\"#pswdChange\">Changing your Password</a>.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>
		

		<a name=\"groups\"></a><b>My Groups</b><br><br>

		You can access the <img align=\"middle\" width=\"73\" height=\"18\" src=\"images/help/MyGroups.png\"> page after <a ref=\"#login\">logging in</a> to the system by clicking on the <img align=\"middle\" width=\"79\" height=\"18\" src=\"images/help/GroupsBtn.png\"> button in the top menu bar. The default page will show a <a href=\"#grpCreate\">Create Group</a> form, with a list of the groups you are currently a member of to the right:
		<p>
		<center><img width=\"590\" height=\"250\" src=\"images/help/GroupsPage.png\"></center>
		<p>
		On your <img align=\"middle\" width=\"73\" height=\"18\" src=\"images/help/MyGroups.png\"> page, you have the option to:
		<p>
		<ul>
			<li><a href=\"#grpInvite\">Respond to a Group Invitation</a></li>
			<li><a href=\"#grpCreate\">Create a Group</a></li>
			<li><a href=\"#grpEdit\">Edit a Group</a></li>
			<li><a href=\"#grpView\">View Group Details</a></li>
			<li><a href=\"#grpLeave\">Leave a Group</a></li>
		</ul>

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"grpInvite\"></a><b>Responding to a Group Invitation</b><br><br>

		If you have been <a href=\"#grpInvite\">invited to join any groups</a> that you have not accepted or rejected yet, there will be an additional dialogue at the top of your <a href=\"#groups\">My Groups</a> page:
		<p>
		<center><img width=\"529\" height=\"92\" src=\"images/help/GrpInviteMsg.png\"></center>
		<p>
		When you receive <a href=\"#grpInvite\">invitations</a> from other Fusion users, the system will send you an email with a link to your <a href=\"#groups\">My Groups</a> page so that you can accept or reject the invitation:
		<p>
		<center><img width=\"512\" height=\"128\" src=\"images/help/GrpInviteEmail.png\"></center>
		<p>
		Once you have an invitation that you wish to accept, click on the <img align=\"middle\" width=\"63\" height=\"23\" src=\"images/help/Accept.png\"> button below the message. You will be confirmed as a group member and the page will be refreshed with the new group added to your <a href=\"#groups\">My Groups</a> list on the right side:
		<p>
		<center><img width=\"192\" height=\"80\" src=\"images/help/MyGrpsForm.png\"></center>
		<p>
		If you choose to reject a group invitation, click the <img align=\"middle\" width=\"63\" height=\"23\" src=\"images/help/Reject.png\"> button below the message. The system will remove the invitation and not make any changes to your groups. The group information of the person who invited you will be updated, removing you from the list of non-confirmed members.:
		<p>
		<center><img width=\"186\" height=\"105\" src=\"images/help/MyGroupsForm.png\"></center>

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"grpCreate\"></a><b>Creating a Group</b><br><br>

		To create a new group, you can click on the <img align=\"middle\" width=\"79\" height=\"18\" src=\"images/help/GroupsBtn.png\"> button in the top menu bar of any Fusion page. The default page will show a <img align=\"middle\" src=\"images/help/CreateGrpTitle.png\"> form, with a list of the groups you are currently a member of to the right:
		<p>
		<center><img width=\"590\" height=\"250\" src=\"images/help/GroupsPage.png\"></center>
		<p>
		You must enter a name for your group first, followed by the Andrew ID's of any other members you wish to invite and choose a duration under the \"Until\" drop-down list. If any invited member does not have a valid Fusion account yet, the system will create a new account for the Andrew ID and email them with instructions on <a href=\"#profile\">creating their profile</a> and <a href=\"#grpInvite\">responding to the invitation</a>:
		<p>
		<center><img width=\"224\" height=\"205\" src=\"images/help/CreateGrp1.png\"></center>
		<p>
		To add more than five members, clicking on <img align=\"middle\" src=\"images/help/AddMembrs.png\"> will generate additional text boxes:
		<p>
		<center><img width=\"222\" height=\"197\" src=\"images/help/CreateGrp2.png\"></center>
		<p>
		You do not need to enter your own Andrew ID for any group; you are automatically a member of any group you create.<p>
		<center><img width=\"222\" height=\"199\" src=\"images/help/CreateGrp3.png\"></center>
		<p>
		
		When you are satisfied with the group information, click on the <img align=\"middle\" width=\"103\" height=\"30\" src=\"images/help/CreateGroup.png\"> button at the bottom of the page to submit your new group. The system will then redisplay the page with a confirmation message at the top and the group will be included in your My Groups list to the right:
		<p>
		<center><img width=\"711\" height=\"211\" src=\"images/help/CreateGrp4.png\"></center>
		<p>
		All members that you listed will receive an email from the system with an invitation they can now <a href=\"#grpInvite\">respond to</a>. Since you created the group, you are the only person with the option to <a href=\"#grpEdit\">edit this group</a>.<p>

		If you have any problems with group creation, or if the group is not reflected in your <a href=\"#groups\">My Groups page</a> after you have created it, try again. If that doesn't work, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know about the error.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"grpEdit\"></a><b>Editing a Group</b><br><br>

		You can only edit a group that you are the creator of. This is done by first clicking on the <img width=\"79\" height=\"18\" src=\"images/help/GroupsBtn.png\"> button of the top menu bar. From the <a href=\"#groups\">My Groups<a> page, click on any group name under the My Groups list that you are the creator for. For more information on this step, see <a href=\"#grpView\">Viewing Group Details</a>. The system will display the information for the group you chose: 
		<p>
		<center><img width=\"535\" height=\"319\" src=\"images/help/EditGroupForm3.png\"></center>
		<p>
		You have the option of entering a new group name and a new end date. By clicking on <img align=\"middle\" src=\"images/help/AddMembrs.png\">, the system will display text boxes where you can enter Andrew ID's of members who are not already in the group and who you would like to have invited.
		<p>
		When you are satisfied with any changes you've made, click on the <img align=\"middle\" width=\"108\" height=\"26\" src=\"images/help/EditGroup.png\"> button at the bottom of the <img align=\"middle\" width=\"74\" height=\"16\" src=\"images/help/EditGroupTitle.png\"> section. The system will save your new changes, send any new email invitations to additional members, and display the page to reflect the changes and a confirmation message:
		<p>
		<center><img width=\"529\" height=\"44\" src=\"images/help/ChangesSaved.png\"></center>
		<p>
		If you have any problems editing group information, check that the changes are valid and try again. If that doesn't work, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know about the error.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"grpView\"></a><b>Viewing Group Details</b><br><br>

		To view information on any group you are a member of, you need to click on the <img align=\"middle\" width=\"79\" height=\"19\" src=\"images/help/GroupsBtn.png\"> button in the top menu bar. Click on the group name under the <img align=\"middle\" width=\"73\" height=\"18\" src=\"images/help/MyGroups.png\"> list on the right-hand side of the page that you wish to see details on:<p>
		<center><img width=\"186\" height=\"105\" src=\"images/help/MyGroupsForm.png\"></center>
		<p>
		For a group that you have not created, the system will display details on the group that you chose, including the group name, creator, end date, and contact information for all group members who have either not responded or accepted the group invitation:<p>
		<center><img width=\"535\" height=\"268\" src=\"images/help/EditGroupForm2.png\"></center>
		<p>
		If you are the creator of the group, the name and duration will be displayed at the top, with the option of <a href=\"#grpEdit\">editing the group</a>. The group members who have either not responded or accepted the group invitation will also be listed, along with their contact information:<p>
		<center><img width=\"535\" height=\"268\" src=\"images/help/EditGroupForm3.png\"></center>
		<p>
		If you are the group creator, you cannot <a href=\"#grpLeave\">leave the group</a>.<p>
		To see information for a different group, simply click on the group's name under the <img align=\"middle\" width=\"73\" height=\"18\" src=\"images/help/MyGroups.png\"> list. If you wish to <a href=\"#grpCreate\">create a new group</a>, click on <img align=\"middle\" src=\"images/help/CreateGroupWord.png\"> to display a blank group form.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"grpLeave\"></a><b>Leaving a Group</b><br><br>

		You are able to leave any group that you are NOT the creator of. To do so, click on the <img align=\"middle\" width=\"79\" height=\"19\" src=\"images/help/GroupsBtn.png\"> button on the top menu bar. Then click the group name under your My Groups list on the right-hand side of the page to <a href=\"#grpView\">view the group details</a>. If the group is indeed one you have not created, you will see the information without an option of <a href=\"#grpEdit\">editing the group</a>:<p>
		<center><img width=\"535\" height=\"268\" src=\"images/help/EditGroupForm2.png\"></center>
		<p>
		To leave the group, click on the <img align=\"middle\" width=\"105\" height=\"30\" src=\"images/help/LeaveGroup.png\"> button in the bottom right corner of the page. The system will remove you from the group and refresh the page with a confirmation message. The My Groups list will no longer list the group:<p>
		<center><img width=\"534\" height=\"47\" src=\"images/help/LeftGrpMsg.png\"></center>
		<p>
		You will also be removed from that group's member list in the <a href=\"#groups\">My Groups</a> pages of other group members.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>



		<a name=\"grpCal\"></a><b>Group Schedule</b><br><br>

		The Group Calendar is your default page. It will be the first page you see after <a href=\"#login\">logging in</a>, and you can also reach it by clicking on the <img align=\"middle\" width=\"81\" height=\"37\" src=\"images/help/FusionLogo.png\"> or the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/RequestBtn.png\"> button, both in the top left of any Fusion page. To the right of the actual calendar will be the Calendar Navigation, Select Members form, and the <a href=\"#mtgRequest\">Request Meeting</a> form:<p>
		<center><img width=\"721\" height=\"554\" src=\"images/help/GroupSch.png\"></center>
		<p>
		The blocks of time on the Group Calendar are a combination of all the <a href=\"#schedule>Class, Work, and Personal events</a> of all the group members combined, plus any meeting times <a href=\"#mtgRequest\">requested</a> by anyone in your group. All the occupied time blocks will be gray on the <img align=\"middle\" src=\"images/help/GroupSchTitle.png\">. The Select Members box to the right allows you to choose which of your groups is displayed by clicking on the group name from the drop-down list. <p>
		<center><img width=\"706\" height=\"428\" src=\"images/help/GroupSchChecked.png\"></center>
		<p>
		After you have chosen the group you wish to view, the group members (including yourself) will appear under the group name. Checking and unchecking the boxes next to group member's names will add and take away their personal blocks of time from the <img align=\"middle\" src=\"images/help/GroupSchTitle.png\"> view. This feature allows you to only see the obligations of people you choose, making it easier to find available meeting times if you only need certain group members to meet:<p>
		<center><img width=\"701\" height=\"430\" src=\"images/help/GroupSchUnchecked.png\"></center>
		<p>
		The Calendar Navigation form on the top right of the page shows a monthly calendar with the current date highlighted. You can use this to look forward or ahead by month view. Clicking on any day of the Calendar Navigation will bring up that week view of the Group Calendar:<p>
		<center><img width=\"155\" height=\"163\" src=\"images/help/SelectMembers.png\"></center>
		<p>
		The Group Calendar page also has the <a href=\"#mtgRequest\">Request Meeting</a> form, which is located on the bottom right-hand side:<p>
		<center><img width=\"157\" height=\"274\" src=\"images/help/RequestMtgForm.png\"></center>
		<p>
		To learn more about meetings, refer to:<p>
		<ul>
			<li><a href=\"#mtgRequest\">Requesting a Meeting</a></li>
			<li><a href=\"#mtgRespond\">Responding to Meeting Times</a></li>
			<li><a href=\"#mtgDelete\">Deleting a Meeting</a></li>
		</ul>
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"mtgRequest\"></a><b>Requesting a Meeting</b><br><br>

		To request a meeting, you must be a member of at least one group and be looking at the <a href=\"#grpCal\">Group Schedule</a> page. You can reach this page by simply <a href=\"#login\">logging in</a>, clicking on the <img align=\"middle\" width=\"81\" height=\"37\" src=\"images/help/FusionLogo.png\">, or by clicking on the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/RequestBtn.png\"> button of any Fusion page. The Request Meeting form is on the bottom right-hand side of the <img align=\"middle\" src=\"images/help/GroupSchTitle.png\"> page:<p>
		<center><img width=\"157\" height=\"274\" src=\"images/help/RequestMtgForm.png\"></center>
		<p>
		First, you must enter a title, optional description, a number of hours to require <a href=\"#mtgResponse\">responses</a> from other group members, a meeting start date and time, and duration. If this meeting should have no recurrence, leave the box next to it unchecked: <img align=\"middle\" src=\"images/help/UncheckRecurr.png\">. In the case that you want <a href=\"#recurr\">recurrence</a>, check the box and follow the additional instructions.<p>

		Once you are satisfied with your first choice of a meeting time, click on the <img align=\"middle\" src=\"images/help/SetTime.png\"> button. The system will print \"Meeting Details\" and a \"Time 1\" label with the information for your first choice of a meeting time:<p>
		<center><img width=\"160\" height=\"402\" src=\"images/help/RqstMtgTime1.png\"></center>
		<p>
		Now you can enter additional start dates and times, durations, and <a href=\"#recurr\">recurrence</a> with any exceptions that you want. After you have entered the information for each additional meeting time, click on the <img align=\"middle\" src=\"images/help/AddTime.png\"> button and the system will print the Time information under the first time you already entered:<p>
		<center><img width=\"160\" height=\"375\" src=\"images/help/RqstMtgTime2.png\"></center>
		<p>
		After you are satisfied with the Meeting Details and Times, click on the <img align=\"middle\" src=\"images/help/Confirm.png\"> button at the bottom of the form to finalize your request. The system will refresh the page with a confirmation and the meeting times will appear on the <a href=\"#schedule\">My Schedule</a> pages of all members invited.
		<p>
		The system will also send an email to each group member that you selected from the <img align=\"middle\" src=\"images/help/GroupSchTitle.png\"> for your meeting request. Your group members will now have a link to <a href=\"#mtgRespond\">respond</a> to the meeting time options you chose.
		<p>
		The meeting will either be confirmed or <a href=\"#mtgDelete\">deleted</a> by the end of your chosen response time and you will receive an email from the system:<p>
		<center><img width=\"446\" height=\"315\" src=\"images/help/MtgConfirmEmail.png\"><p><img width=\"447\" height=\"214\" src=\"images/help/DeleteMtgEmail.png\"></center>

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"mtgRespond\"></a><b>Responding to Meeting Times</b><br><br>

		When a member from any of your groups <a href=\"#mtgRequest\">requests a meeting</a>, you will be alerted by an email from Fusion:<p>
		<center><img width=\"446\" height=\"391\" src=\"images/help/MtgRqstEmail.png\"></center>
		<p>
		Your <a href=\"#schedule\">My Schedule</a> page will also reflect any Unconfirmed Meetings or Confirmed Meetings you may have.<p>
		<center><img width=\"537\" height=\"374\" src=\"images/help/TimeRejected.png\"></center>
		<p>
		After you receive a Meeting Request email, you can either CONFIRM or REJECT any of the times. If you do not respond by the set deadline, the system will automatically confirm the optimal meeting time without your opinion. <p>

		By clicking the CONFIRM link, the <a href=\"#schedule\">My Schedule</a> page is brought up for you to accept times. A confirmation message will show in the Meeting Confirmation form on the right of the page.<p>
		Clicking the REJECT link will also display the <a href=\"#schedule\">My Schedule</a> page. Any times you have not already responded to will be listed, and you have the option of rejecting any unconfirmed meeting times.
		<p>
		<center><img width=\"160\" height=\"188\" src=\"images/help/TimeReject.png\"></center>
		<p>
		Once group members have responded to all meeting times or the response deadline is past, the Unconfirmed Meeting time blocks will be removed from your <a href=\"#schedule\">My Schedule</a> page, and the chosen time will turn into a Meeting, changing colors on the schedule according to the legend at the top:<p>
		<center><img width=\"691\" height=\"303\" src=\"images/help/SchMtgConfirm.png\"></center>
		<p>
		 The meeting creator and all other group members will be notified by the system when a meeting is finalized:<p>
		<center><img width=\"446\" height=\"315\" src=\"images/help/MtgConfirmEmail.png\"></center>
		<p>
		If all times of a meeting are rejected, the meeting is <a href=\"#mtgDelete\">deleted</a> automatically by the system.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"mtgDelete\"></a><b>Deleting a Meeting</b><br><br>

		Only the creator of a meeting has the option to delete it. However, if all meeting times are rejected when the group members <a href=\"#mtgRespond\">respond to the meeting times</a>, the system will delete the meeting automatically and notify the creator and other members by email:<p>
		<center><img width=\"447\" height=\"214\" src=\"images/help/DeleteMtgEmail.png\"></center>
		<p>
		If you are the meeting creator, you can delete it from your <a href=\"#schedule\">My Schedule</a> page in the same way that you would delete any event from your personal schedule. For more information on deleting a time block or meeting, see the section on <a href=\"#eventDelete\">Deleting an Event</a>.<p>
		When a meeting creator deletes a meeting time, all members involved will receive an email notification, and it will be removed from the calendars of all group members, the same as when the system automatically deletes a meeting.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>
		
		
		<a name=\"schedule\"></a><b>My Schedule</b><br><br>

		To view your My Schedule page, click on the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> button of the top menu bar from any Fusion page after <a href=\"#login\">logging in</a>. Your current personal schedule will be displayed, with a monthly calendar for navigation and the <a href=\"#eventAdd\">add event</a> form on the right side:<p>
		<center><img width=\"535\" height=\"455\" src=\"images/help/Schedule.png\"></center>
		<p>
		On the <img align=\"middle\" src=\"images/help/MySchedule.png\"> page, you have the option to:<p>
		<ul>
			<li>Add an event: <a href=\"#eventCourse\">Course,</a> <a href=\"#eventAdd\">Work, or Personal</a></li>
			<li><a href=\"#eventView\">View event details</a></li>
			<li><a href=\"#eventEdit\">Edit an event</a></li>
			<li><a href=\"#eventDelete\">Delete an event</a></li>
		</ul>
		<p>
		Before you have <a href=\"#eventAdd\">added any events</a>, your schedule will appear as a blank calendar with the current day highlighted:<p>
		<center><img width=\"595\" height=\"392\" src=\"images/help/Step3Sched.png\"></center>
		<p>
		As you <a href=\"#eventAdd\">add events</a>, including classes, work, and personal commitments, your schedule will change to reflect the new events according to the color legend:<p>
		<center><img width=\"535\" height=\"455\" src=\"images/help/Schedule.png\"></center>
		<p>
		You are the only user who is able to <a href=\"#eventAdd\">add</a> or <a href=\"#eventEdit\">make changes</a> to your My Schedule page.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"eventAdd\"></a><b>Adding an Event</b><br><br>

		To add an event, you must be looking at your <a href=\"#schedule\">My Schedule</a> page, accessed by clicking the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> button of the top menu bar anywhere in Fusion. The Add Event form is on the right side, under the Calendar Navigation:<p>
		<center><img width=\"174\" height=\"178\" src=\"images/help/CalNav.png\"><br><img width=\"175\" height=\"267\" src=\"images/help/AddPers.png\"></center>
		<p>
		There are three categories of events you can add to your personal schedule: Class, Work, and Personal. These categories are differentiated on the calendar with blocks of color, which are shown in the legend at the top of the calendar:<p>
		<center><img width=\"388\" height=\"25\" src=\"images/help/Legend.png\"></center>
		<p>
		To add a course, see the section on <a href=\"#eventCourse\">Adding a Course</a>. To add Work and Personal events, the forms are the same. Choose your category from the drop-down, then add a title, optional description, start date, and from and to times:<p> 
		<center><img width=\"173\" height=\"350\" src=\"images/help/AddPers2.png\"></center>
		<p>
		If this event is a one-time occurrence, leave the <img align=\"middle\" src=\"images/help/UncheckRecurr.png\"> box unchecked. For an event that should occur more than once, refer to the section on <a href=\"#recurr\">Recurrence</a>. After all information has been added, click on the <img align=\"middle\" width=\"70\" height=\"24\" src=\"images/help/Add.png\"> button. The system will refresh the page to reflect your new event:<p>
		<center><img width=\"808\" height=\"522\" src=\"images/help/SchAddWork.png\"></center>
		<p>
		You are the only user who can add events to your personal schedule. If the changes do not show up or you experience an error you do not understand, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"recurr\"></a><b>Recurrence</b><br><br>
		
		You have the option of recurrence when you either <a href=\"#eventAdd\">add</a> or <a href=\"#eventEdit\">edit an event</a>, or if you're <a href=\"#mtgRequest\">requesting a meeting time</a>. You must be looking at the <img align=\"middle\" src=\"images/help/MySchedule.png\"> page for events, which can be accessed by clicking on the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> button in the top menu bar of any Fusion page. For a meeting time, you must be on the <img align=\"middle\" src=\"images/help/GroupSchTitle.png\"> page, which can be accessed by clicking the <img align=\"middle\" width=\"81\" height=\"37\" src=\"images/help/FusionLogo.png\"> logo, the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/RequestBtn.png\"> button in the top menu bar, or by <a href=\"#login\">logging in</a>.<p>
		
		When you click the <img align=\"middle\" src=\"images/help/UncheckRecurr.png\"> checkbox in the <a href=\"#eventAdd\">Add Event</a>, <a href=\"#eventEdit\">Edit Event</a>, or <a href=\"#mtgRequest\">Request Meeting</a> form, the current form will repopulate with a recurrence section:<p>
		<center><img width=\"180\" height=\"301\" src=\"images/help/Recurr2.png\"></center>
		<p>
		Under recurrence, you can choose:<p>
		<ul>
			<li>If the event happens every day</li>
			<li>If the event happens every week, and if so, which days it happens on every week</li>
			<li>If the event doesn't happen every week, choose a number of weeks for the recurrence</li>
			<li>The duration from a drop-down list</li>
		</ul><p>
		You also have the option of entering specific dates as exceptions to the event recurrence. Enter the date you wish to except and click the <img align=\"middle\" width=\"70\" height=\"24\" src=\"images/help/Add.png\"> button. Your exception date will be added to the text box below. You have the option of adding as many exception dates as you like. If you want to remove an exception date you have already entered, click on the date in the list, then click on the <img align=\"middle\" width=\"70\" height=\"24\" src=\"images/help/Remove.png\"> button above the list box. The system should reflect your changes.<p>
		
		After you are finished with your recurrence and exception dates, finish <a href=\"#eventAdd\">adding</a> or <a href=\"#eventEdit\">editing the event</a> or <a href=\"#mtgRequest\">requesting the meeting times</a> by clicking on the <img align=\"middle\" width=\"70\" height=\"24\" src=\"images/help/AddEvent.png\"> button at the bottom of the form. For complete information on these steps, see the sections on <a href=\"#eventAdd\">Adding an Event</a>, <a href=\"#eventEdit\">Editing an Event</a>, or <a href=\"#mtgRequest\">Requesting a Meeting</a>.
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"eventCourse\"></a><b>Adding a Course</b><br><br>

		You must be looking at your <a href=\"#schedule\">My Schedule</a> page, accessed by clicking the <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> button of the top menu bar anywhere in Fusion. The Add Event form is on the right side, under the Calendar Navigation:<p>
		<center><img width=\"174\" height=\"178\" src=\"images/help/CalNav.png\"><br><img width=\"173\" height=\"350\" src=\"images/help/AddPers.png\"></center>
		<p>
		To add a course, choose \"Class\" from the \"Category\" drop-down list:<p>
		<center><img src=\"images/help/CategoryList.png\"></center>
		<p>
		The form will repopulate with instructions and a column of text boxes for courses:<p>
		<center><img width=\"164\" height=\"310\" src=\"images/help/AddCourseForm.png\"></center>
		<p>
		Enter either any word from the class name or the course number of your Carnegie Mellon classes in these text boxes. Once you have started typing, a pop-up list will appear with a list of the courses currently offered that include the words or course numbers that you've added:
		<p>
		<center><img width=\"167\" height=\"211\" src=\"images/help/AddClassList.png\"><img width=\"174\" height=\"310\" src=\"images/help/AddClassNum.png\"></center>
		<p>
		Either finish typing your course number, along with the lecture number or section letter or click on your course in the pop-up list. Once all your courses are entered, click on the <img align=\"middle\" src=\"images/help/AddClass.png\"> button at the bottom of the form. The system will populate the courss on your calendar with the correct days and times. The end date for the course is automatically set for the end of the current semester:
		<p>
		<center><img width=\"762\" height=\"407\" src=\"images/help/SchedEditClass.png\"></center>
		<p>
		You are the only user who can add courses to your personal schedule. If the changes do not show up or you experience an error you do not understand, click on the <img align-\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"eventView\"></a><b>Viewing Group Details</b><br><br>

		To view details for an event that you have already <a href=\"#eventAdd\">added</a> in your <img align=\"middle\" src=\"images/help/MySchedule.png\"> page, you must first click <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> on the top menu bar first. From the blocks of time on your schedule, click on the one you wish to see information on. The block will become highlighted and the system will display the current event information in the Edit Event form to the right:<p>
		<center><img width=\"755\" height=\"350\" src=\"images/help/SchEdit1.png\"></center>
		<p>
		You can now see all the details associated with the block of time, including its category, title, description, start date, start and end times, and any recurrence or exceptions. To make any changes to this information, see the section on <a href=\"#eventEdit\">Editing an Event</a>.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"eventEdit\"></a><b>Editing an Event</b><br><br>

		To edit an event that you have already <a href=\"#eventAdd\">added</a> in your <img align=\"middle\" src=\"images/help/MySchedule.png\"> page, you must first click <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> on the top menu bar first. From the blocks of time on your schedule, click on the one you wish to make changes to. The block will become highlighted and the system will display the current event information in the Edit Event form to the right:<p>
		<center><img width=\"755\" height=\"350\" src=\"images/help/SchEdit1.png\"></center>
		<p>
		You can now see all the details associated with the block of time, including its category, title, description, start date, start and end times, and any <a href=\"#recurr\">recurrence</a> or exceptions that you have already entered. For <a ref=\"eventCourse\">Class events</a>, you will see the Carnegie Mellon course information. 
		<p>
		<center><img width=\"762\" height=\"407\" src=\"images/help/SchedEditClass.png\"></center>
		<p>
		To make any changes to this information, simply replace the text or drop-down choice with the new information:<p>
		<center><img width=\"749\" height=\"362\" src=\"images/help/SchEdit2.png\"></center>
		<p>
		 Once you are satisfied with your changes, click on the <img align=\"middle\" width=\"107\" height=\"27\" src=\"images/help/EditEvent.png\"> button at the bottom of the form. Just like when you add a new event, the page will refresh, reflecting your changes:
		<p>
		<center><img width=\"750\" height=\"340\" src=\"images/help/SchEdit3.png\"></center>
		<p>
		You are the only user who can make changes to your personal schedule. If the changes do not show up or you experience an error you do not understand, click on the <img align=\"middle\" src=\"images/help/ContactUs.png\"> link at the bottom of the page to let us know.

		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>

		<a name=\"eventDelete\"></a><b>Deleting an Event</b><br><br>
		
		To delete an existing event (or <a href=\"#mtgDelete\">meeting time</a>) in your <img align=\"middle\" src=\"images/help/MySchedule.png\">, you must have clicked <img align=\"middle\" width=\"81\" height=\"18\" src=\"images/help/ScheduleBtn.png\"> on the top menu bar first. Choose the block of time that you wish to delete, and move your mouse pointer over the block. An \"X\" will appear in the top right-hand corner:
		<p>
		<center><img width=\"578\" height=\"214\" src=\"images/help/DeleteX.png\"></center>
		<p>
		Click on the X after it shows up, and the system will display a pop-up message for you to choose to \"Delete instance\" or to \"Delete all\":<p>
		<center><img width=\"577\" height=\"211\" src=\"images/help/DeleteList.png\"></center>
		<p>
		If the event has no recurrence, there will only be the option to \"Delete event\":
		<p>
		<center><img width=\"573\" height=\"214\" src=\"images/help/DeleteEvent.png\"></center>
		<p>
		When the pop-up is displayed, make sure that you have clicked on the correct time block; after you choose to delete the event(s), the system will reload your <img align=\"middle\" src=\"images/help/MySchedule.png\"> page with the event(s) deleted.<p>
		You are the only Fusion user who can delete blocks of time on your <img align=\"middle\" src=\"images/help/MySchedule.png\"> page.
		
		<p align=\"right\"><a href=\"#top\">Back to Top</a><hr><p>



";
 echo "</div>";
 echo "</div>";

 include ("includes/footer.php");
 
?>
