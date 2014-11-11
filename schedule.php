<?php

require_once ("includes/common.php");

$formObj = CommonFunctions::setFormObj(4);	

if ($_SESSION["status"] == 2) {
	$pageTitle = "My Schedule";
	include ("includes/header.php");
	
	if($_POST["current_calendar"])
		$previous_viewdate = $_POST["current_calendar"];
	
	$curForm = "event";
	if($_POST["Submit"] == "Add Event" || $_POST["Submit"]=="Add Class" || $_POST["Submit"] == "Edit Event")
		include ("event_process.php");
	
	$schedule = new Schedule(NULL, $_SESSION["person_id"]);

	$extraHeader = "";
	
}
elseif($_SESSION["status"] == 1) {

	$pageTitle = "Set Weekly Schedule";
	include ("includes/header.php");
	
	$curForm = "event";
	if($_POST["Submit"] == "Add Event" || $_POST["Submit"]=="Add Class" || $_POST["Submit"] == "Edit Event")
	{
		include ("event_process.php");
	}
	else {
		$category = 1;	
	}
	
  	$schedule = new Schedule(NULL, $_SESSION["person_id"]);
  
  	if($_POST["Submit"] == "Add Event") {	
  		if ($formObj->validateForm($_POST)) {
			$pieces = explode("/", $_POST["userDate"]);
	  		$schedule->setVisibleWeek($pieces[0], $pieces[1], $pieces[2]);
  		}
  	}

	$extraHeader = "<div class=\"roundcont\">
   <div class=\"roundtop\">
	 <img src=\"images/tl.gif\" 
	 width=\"15\" height=\"15\" class=\"corner\" 
	 style=\"display: none\" />
   </div>" .
   		"<div>
	<span class=\"bigTitle\">This is where you make your schedule!</span>
   <p style=\"height: 100px\">1. Use the Add Event form on the right to start.<br />" .
   		"2. Change the category and the form will change.<br />" .
   		"3. To edit, click on events on the calendar.<br />" .
   		"4. To delete, click on the X in the top-right hand corner of an event.<br />" .
   		"<span class=\"right\">Once you're done, click here: <a href=\"groups.php\"><img src=\"images/next.gif\" class=\"noBorder\"></a></span></p>" .
   		"</div>
   <div class=\"roundbottom\">
	 <img src=\"images/bl.gif\" 
	 width=\"15\" height=\"15\" class=\"corner\" 
	 style=\display: none\" />
   </div>
</div>";

}
else {
	CommonFunctions::showLoginMessage("schedule");
}

if($previous_viewdate)
	$schedule->setVisibleWeekStarting($previous_viewdate);

echo HTMLHelper::startPanel("schedTop", "panel", $extraHeader);


//toggle between old and new display functionality
echo $schedule->Display();
//echo $schedule->Display2();

echo HTMLHelper::endPanel();

echo HTMLHelper::startPanel("calWidgetTop", "");
	echo HTMLHelper::startWidget("calWidgetBox",1);
	echo "<div id=\"calendar-container\"></div>";
	echo HTMLHelper::endWidget(1);
	echo "<div id=\"eventDiv\">";

		echo HTMLHelper::startWidget("addEventTop");
			echo "<div id = \"eventForm\">";
				if($conflict_msg)
					echo "<div class=\"error\">$conflict_msg</div>";
				
				if (!isset($category)) 
					$category = $formObj->getFormValue("category");
					
				if($category == 1)
					include ("includes/views/course_form.php");
				else
					include ("includes/views/event_form.php");
			echo "</div>";
	echo HTMLHelper::endWidget();	
	echo "</div>";	
echo HTMLHelper::endPanel("");	
	
//calendar widget javascript
	CommonFunctions::standaloneCalendar("calendar-container", "setVisibleWeek(y+\",\"+eval(m+1)+\",\"+d, false)",$fromTime);

include ("includes/footer.php");
?>



