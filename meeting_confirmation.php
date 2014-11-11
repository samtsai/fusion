<?php
require_once ("includes/common.php");
$pageTitle = "Meeting Confirmation";
include ("includes/header.php");

$meeting_id = $_GET["meeting_id"];
$person_id = $_GET["person_id"];

/**IF statement to determine that user is logged in */
if (isset ($_SESSION["status"]) && $_SESSION["status"] == 2) {
	/**Makes sure the person id matches the user who is logged in */
	if ($person_id == $_SESSION["person_id"]) {
		$meeting = new Meeting($meeting_id);
		$tempTimes = $meeting->getTimes();
		//dump($tempTimes);
		
		$visStart = 99999999999;
		foreach($tempTimes as $key =>$timeArray){
			$visStart = min($timeArray["start_datetime"],$visStart);
		}
		if($visStart == 99999999999)
			$visStart = mktime();
		$name = $meeting->getMeetingName();
		$desc = $meeting->getDescription();
		$person_array = $meeting->getAttendees();
		$creator = $meeting->getCreatorId();
		
		
		$db = new DB();
		
		$creatorCondition = "WHERE person_id = '$creator'";
		$creatorQuery = $db->getOneRecord("tblPerson", $creatorCondition);
		$creator_fn = $creatorQuery["first_name"];
		$creator_ln = $creatorQuery["last_name"];
		
		$formObj = CommonFunctions :: setFormObj(8);

		$condition = "WHERE meeting_id = $meeting_id";
		$arrayQuery = "Select event_id from tblMeetingEvent $condition";
		$event_array = $db->getArray($arrayQuery);

		//echo "<div id='main'>";
		//echo "<div id='contents'>";


		if ($_GET['action'] == "reject") {
			$formType = "reject";

			$count = 1;


		}
		/**if statement if user clicks on 'confirm all' link in request email */
		if ($_GET['action'] == "confirm") {
			$formType = "confirm";


		if($event_array){
			foreach ($event_array as $key => $eventID) {

				$updateQuery = "UPDATE tblPersonEvent SET response = 0 WHERE event_id = $eventID AND person_id = $person_id";
				$db = new DB();
				$db->updateRecord($updateQuery);
				
				$responseQuery = "Select response from tblPersonEvent where event_id = $eventID";
				$responseArray = $db->getArray($responseQuery);
				$setMeeting = "no";
				if($responseArray){
					foreach ($responseArray as $response) {
			
						if ($response != "0") {
							$setMeeting = "no";
							break;
						} else {
							$setMeeting = "yes";
						}
					} //end of foreach	
				}//end of if response Array

				if ($setMeeting == "yes") {
					$meetingEvent = new Event($eventID);
					$meetingEvent->setEventType(4);
					$meetingEvent->updateDB();
		
					//retrieve meeting id for this event
					$db = new DB();
					$query = "SELECT meeting_id from tblMeetingEvent WHERE event_id = $eventID";
					$meeting_id = $db->getScalar($query);
		
					//might not need executed if other meeting already set
					if ($meeting_id) {
						//use the meeting id to access other event id's for that meeting
						$query = "Select event_id from tblMeetingEvent WHERE meeting_id = $meeting_id AND event_id <> $eventID";
						$eventIDArray = $db->getArray($query);
		
						//iterate through those event id's and delete them using event class

						if($eventIDArray){
							foreach ($eventIDArray as $id) {
								$event = new EVENT($id);
								$event->deleteEvent();
								$db->deleteRecord('tblMeetingEvent', 'event_id', $id);
							}
						} //end of if eventIDArray
						//retrieve people to send email to
						$query = "Select person_id from tblPersonEvent where event_id = $eventID";
						$personArray = $db->getArray($query);
		
						//email each person confirmation
						foreach ($personArray as $personID) {
							$meeting = new Meeting($meeting_id);
							$meeting->emailConfirmation($personID, $priority);
							
						}
						
					} //end of if meeting_id
				} //end of if setmeeting
				mail('fusion.dump@gmail.com', 'confirmation_dump', 'dump', 'Fusion!');
			} /**end of foreach */
		} //end of if event_array
			
		}
		$output = "<div class=\"floatLeft\">";
		$output .= "<div id=\"schedTop\"></div>";
		$output .= "<div id=\"panel\">";
			$schedule = new Schedule(NULL,$person_id);

			$schedule->setVisibleWeekStarting($visStart);
			$schedule->setGroup("special");
		//$output .= $schedule->Display2();
		$output .= $schedule->Display();
		$output .= "</div>";
		$output .= "<div id=\"panelBottom\"></div>";
		$output .= "</div>";
		$output .= "<div class=\"floatLeft\">";
			$output .= "<div id=\"calWidgetTop\"></div>";
			$output .= "<div id=\"calWidgetBox\">";
				$output .= "<div class=\"widgetContent\">";
				$output .= "<div id=\"calendar-container\"></div>";
				$output .= "</div>";
		$output .= "</div>";
		$output .= "<div id=\"calWidgetBottom\"></div>";
		

		echo $output;
		CommonFunctions::standaloneCalendar("calendar-container", "setVisibleWeek(y+\",\"+eval(m+1)+\",\"+d, false)",$visStart);
			
		include ("includes/views/meeting_confirmation_form.php");
		echo "</div>";
		
	} /** end of if person id - session id*/
	else
		echo "You are not authorized to view this page.<br/>Please log out and log in using your account";
} /**end of if session status */
else {
	if ($_GET["action"] == "reject")
		Redirect :: gotoPage("index.php?action=reject&meeting_id=".$meeting_id."&person_id=".$person_id);
	elseif ($_GET["action"] == "confirm") Redirect :: gotoPage("index.php?action=confirm&meeting_id=".$meeting_id."&person_id=".$person_id);
}
?>

