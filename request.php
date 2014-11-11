<?php

require_once ("includes/common.php");

$formObj = CommonFunctions::setFormObj(6);	
if (isset ($_POST["Submit"]) && ($_POST["Submit"] == "Cancel")) {
	Redirect :: gotoPage("request.php");
	
}
if(isset($_GET["login"])){
	$_SESSION["status"]= 2;
	
	$id = $_GET["person_id"];
	$person = new Person($id);
	$person->setActive("1");
	$person->updateDB();
	
	Redirect :: goToPage("request.php");	
}

if (isset($_SESSION["status"]) && $_SESSION["status"] == 2) {
	$pageTitle = "Request Meeting";
	include ("includes/header.php");
	
	$user = new Person($_SESSION["personID"]);
	$groups = $user->getLastGroup();
	if($groups)
	{
		 $schedule = new Schedule($groups);
	}
	elseif ($groups = $user->getGroups())
	{
		 $schedule = new Schedule(array_pop(array_keys($groups)));
	}
	else
	{
		//echo "<p>setting personal schedule</p><br/>";
		$schedule = new Schedule(NULL, $_SESSION["personID"]);
		$schedule->setGroupID(-1);		
	}
	
	echo HTMLHelper::startPanel("calTop");
		
	//echo $schedule->Display2();
	echo $schedule->Display();
	echo HTMLHelper::endPanel();
	
	echo "<div class=\"floatLeft\">";
	
	echo "<div id=\"calWidgetTop\"></div>";
	echo "<div id=\"calWidgetBox\">";
		echo "<div class=\"widgetContent\">";
		echo "<div id=\"calendar-container\"></div>";
		echo "</div>";
	echo "</div>";
	echo "<div id=\"calWidgetBottom\"></div>";
	
	echo HTMLHelper::startWidget("selectMembersTop");
		echo "<div id=\"member_selector\">";
		echo "<div id=\"veil\"></div>";
		echo $schedule->groupDropdown();
		echo "</div>";
	echo HTMLHelper::endWidget();
	
	echo "<div id = 'requestDiv'>";
	echo HTMLHelper::startWidget("requestMeetingTop");
	if($groups = $user->getGroups())	
		include ("request_process.php");
	else
		echo "<h7>You are not currently in any groups.<br/>Please join or create a group if you wish to send out group meeting requests.</h7>";
	echo HTMLHelper::endWidget();
	echo "</div>";
	/*echo "<div id=\"searchTop\"></div>";
	echo "<div class=\"widgetBox\">";
		echo "<div class=\"widgetContent\">";
		include ("includes/views/search_form.php");
		echo "</div>";
	echo "</div>";
	echo "<div class=\"widgetBottom\"></div>";*/
	
	echo "</div>";
	
	CommonFunctions::standaloneCalendar("calendar-container", "setVisibleWeek(y+\",\"+eval(m+1)+\",\"+d, false)",$fromTime);
	
}
else {
	CommonFunctions::showLoginMessage("request");
}


include ("includes/footer.php");

?>