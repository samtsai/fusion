<?php

/*
 * Created on Apr 6, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once ("../common.php");

if (isset ($_GET["title"]) && isset ($_GET["title"]) && isset ($_GET["desc"]) && isset ($_GET["date"]) && isset ($_GET["fromHH"]) && isset ($_GET["fromMM"]) && isset ($_GET["fromAMPM"]) && isset ($_GET["durationHH"]) && isset ($_GET["durationMM"])) {

	$title = stripslashes(trim($_GET["title"]));
	$desc = stripslashes(trim($_GET["desc"]));
	$date = stripslashes(trim($_GET["date"]));
	$fromHH = trim($_GET["fromHH"]);
	$fromMM = trim($_GET["fromMM"]);
	$fromAMPM = trim($_GET["fromAMPM"]);
	$durationHH = trim($_GET["durationHH"]);
	$durationMM = trim($_GET["durationMM"]);

	//echo "$title $description $date $fromHH $duration";
	echo "wrapper reached";
	echo $formObj->insertSubmitButton("Add Time");
}

if (isset ($_GET["person_id"]) && isset ($_GET["event_id"]) && isset ($_GET["type"]) && isset ($_GET["priority"])) {

	$person_id = $_GET["person_id"];
	$event_id = $_GET["event_id"];
	$type = $_GET["type"];
	$priority = $_GET["priority"];

	if ($type == "reject") {
		$updateQuery = "UPDATE tblPersonEvent SET response = 1 WHERE event_id = $event_id AND person_id = $person_id";
		$updateQuery2 = "UPDATE tblMeetingEvent SET rejected = 1 WHERE event_id = $event_id";
		$db = new DB();
		$db->updateRecord($updateQuery);
		$db->updateRecord($updateQuery2);
		echo "<br/><div class=\"error\">This meeting time has been rejected.</div>";
		echo "<br/>There will be a personal event scheduled in this time slot. Refer to your <a href = 'http://rook.hss.cmu.edu/~team04s06/schedule.php'>Schedule</a> for any additional details.";
		$event = new Event($event_id);
		$startTime = $event->getStartDatetime();
		$endTime = $event->getEndDatetime();

		$newEvent = new Event();
		$newEvent->setPersonId($person_id);
		$newEvent->setStartDatetime($startTime);
		$newEvent->setEndDatetime($endTime);
		$newEvent->setEventType(3);
		// this ID was inserted into the database and will be used for all of these events
		$newEvent->setEventName("Busy");
		$newEvent->setEventDescription("This event was scheduled because you rejected a meeting at this time. You may edit this event.");
		$newEvent->setExceptions($event->getExceptions());
		$newEvent->setRecurrenceDays($event->getRecurrenceDays());
		$newEvent->setRecurrenceEnd($event->getRecurrenceEnd());
		$newEvent->setRecurrenceStart($event->getRecurrenceStart());
		$newEvent->setRecurrenceWeeks($event->getRecurrenceWeeks());
		
		$newEvent->addEvent();

		$query = "SELECT meeting_id from tblMeetingEvent WHERE event_id = $event_id";
		$meeting_id = $db->getScalar($query);
		if($meeting_id){
			$meetingRejectQuery = "SELECT rejected from tblMeetingEvent where meeting_id = $meeting_id";
			$meetingRejects = $db->getArray($meetingRejectQuery);
			//echo "<br/>meeting rejects array: ";
			//print_r($meetingRejects);
			$eliminateMeeting = "no";
			foreach ($meetingRejects as $reject) {
				if ($reject != "1") {
					$eliminateMeeting = "no";
					break;
				} else {
					$eliminateMeeting = "yes";
				}
	
			} //end of foreach meetingRejects
			//echo "<br/>eliminate meeting ? ";
			//echo $eliminateMeeting;
			if ($eliminateMeeting == "yes") {
				//retrieve people to send email to
				$query = "Select person_id from tblPersonEvent where event_id = $event_id";
				$personArray = $db->getArray($query);
				//retrieve meeting id for use in emails
				
				foreach ($personArray as $person_id) {
					$meeting = new Meeting($meeting_id);
					$meeting->emailRejection($person_id);
				}
				mail('fusion.dump@gmail.com', 'rejection_dump', 'dump', 'Fusion!');
	
				$query = "Select event_id from tblMeetingEvent WHERE meeting_id = $meeting_id";
				$eventIDArray = $db->getArray($query);
				if($eventIDArray){
					foreach ($eventIDArray as $id) {
						$event = new EVENT($id);
						$event->deleteEvent();
						$db->deleteRecord('tblMeetingEvent', 'event_id', $id);
					} //end of eliminate meeting
				}//end of if eventIDArray
			} //end of if eliminate meeting
		}//end of if meeting_id
		else
			echo "Conflicts have occurred and the meeting can longer be scheduled";
	}

	if ($type == "accept") {
		$updateQuery = "UPDATE tblPersonEvent SET response = 0 WHERE event_id = $event_id AND person_id = $person_id";
		$db = new DB();
		$db->updateRecord($updateQuery);
		echo "<br/><div class=\"accepted\">This meeting time has been accepted.</div><br/>Refer to your <a href = 'http://rook.hss.cmu.edu/~team04s06/schedule.php'>Schedule</a> for any additional meeting details.";

		$responseQuery = "Select response from tblPersonEvent where event_id = $event_id";
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
			$meetingEvent = new Event($event_id);
			$meetingEvent->setEventType(4);
			$meetingEvent->updateDB();

			//retrieve meeting id for this event
			$db = new DB();
			$query = "SELECT meeting_id from tblMeetingEvent WHERE event_id = $event_id";
			$meeting_id = $db->getScalar($query);

			//might not need executed if other meeting already set
			if ($meeting_id) {
				//use the meeting id to access other event id's for that meeting
				$query = "Select event_id from tblMeetingEvent WHERE meeting_id = $meeting_id AND event_id <> $event_id";
				$eventIDArray = $db->getArray($query);

				//iterate through those event id's and delete them using event class
				//echo "<br/>event array : ";
				//print_r($eventIDArray);
				//echo "<br/>";
				if($eventIDArray){
					foreach ($eventIDArray as $id) {
						$event = new EVENT($id);
						$event->deleteEvent();
						$db->deleteRecord('tblMeetingEvent', 'event_id', $id);
					}
				} //end of if eventIDArray
				//retrieve people to send email to
				$query = "Select person_id from tblPersonEvent where event_id = $event_id";
				$personArray = $db->getArray($query);

				//email each person confirmation
				foreach ($personArray as $person_id) {
					$meeting = new Meeting($meeting_id);
					$meeting->emailConfirmation($person_id, $priority);
				}
			} //end of if meeting_id
		} //end of if setmeeting

	} // end of if type = accept

} // end of if isset person_id, event_id

if (isset ($_GET["showRequest"])) {
	echo "<div id=\"requestMeetingTop\"></div>";
	echo "<div class=\"widgetBox\">";
	echo "<div class=\"widgetContent\">";
	include ("../../request_process.php");
	echo "</div>";
	echo "</div>";
	echo "<div class=\"widgetBottom\"></div>";

}
?>

