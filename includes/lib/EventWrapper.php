<?php


/** The event wrapper is accessed through 
 * the js file through various functions. 
 * This is utilizing AJAX. PHP is compiled
 * 'on the fly' and functions are completed
 * without reloading the page. 
 */
include_once ("../common.php");

if (isset ($_GET["name"])) {
	$name = stripslashes(trim($_GET["name"]));
	$dateValue = date("m/d/Y");
	$datePost = "";
	$formObj = CommonFunctions :: setFormObj(4);
	echo $formObj->insertTextbox(1, "dateWidget", "opt_date", $name, $name, "", $datePost, $dateValue, "untilDateStuff", "", "", 0, 2, $interaction);
	return;
}

/** This if statement is used when a user clicks on
* an event on their schedule. This is accessed 
* through the JS populateForm() function and
* is used to pre-populate the form when the
* specified event's details.
*/
if (isset ($_GET["event_id"])) {
	$event_id = $_GET["event_id"];

	$event = new Event($event_id);
	$name = $event->getEventName();
	$description = $event->getEventDescription();
	$typeID = $event->getTypeID();

	if ($typeID == 4 || $typeID == 5) {
		$meeting_query = "SELECT meeting_id from tblMeetingEvent where event_id = $event_id";
		$db = new DB();

		$meeting_id = $db->getScalar($meeting_query);

		$meeting = new Meeting($meeting_id);
		$person_array = $meeting->getAttendees();
		$creator = $meeting->getCreatorId();

	}

	$startDatetime = $event->getStartDatetime();
	$startTime = getdate($startDatetime);
	$startHour = $startTime["hours"];
	if ($startHour > 12) {
		$startAMPM = "PM";
		$startHour = $startHour -12;
	} else {
		if ($startHour == 0)
		{
			$startHour = 12;
			$startAMPM = "AM";
		}	
		elseif ($startHour == 12)
			$startAMPM = "PM";
		else
			$startAMPM = "AM";
	}
	$startMinutes = $startTime["minutes"];
	for ($i = 0; $i <= 9; $i ++) {
			if ($startMinutes == $i) {
				$startMinutes = "0".$i;
				break;
		}
	}
	$endDatetime = $event->getEndDatetime();
	$endTime = getdate($endDatetime);
	$endHour = $endTime["hours"];
	if ($endHour > 12) {
		$endAMPM = "PM";
		$endHour = $endHour -12;
	} else {
		if ($endHour == 0)
			$endHour = 12;
		$endAMPM = "AM";
		if ($endHour == 12)
			$endAMPM = "PM";
	}
	$endMinutes = $endTime["minutes"];
	for ($i = 0; $i <= 9; $i ++) {
			if ($endMinutes == $i) {
				$endMinutes = "0".$i;
				break;
		}
	}
	$recurrenceStart = $event->getRecurrenceStart();
	$recurrenceEnd = $event->getRecurrenceEnd();
	$recurrenceWeeks = $event->getRecurrenceWeeks();
	$recurrenceDays = $event->getRecurrenceDays();
	$exceptions = $event->getExceptions();
	
	if ($recurrenceEnd) {
		if ($recurrenceEnd == 1146884459)
			$until = "SEMESTER";
		elseif ($recurrenceEnd == 1142049659) $until = "MINI";
		elseif ($recurrenceEnd == 2147482800) $until = "FOREVER";
		else {
			$until = "DATE";
			$specificDate = getdate($recurrenceEnd);
			$specMonth = $specificDate["mon"];
			$specDay = $specificDate["mday"];
			$specYear = $specificDate["year"];
			$specDate = $specMonth."/".$specDay."/".$specYear;
		}
		if ($recurrenceDays == "U|M|T|W|R|F|S")
			$frequency = "ed";
		else
			$frequency = "we";
		$recDays = explode("|", $recurrenceDays);
	} /** end of if($recurrenceEnd) */

	$month = $startTime["mon"];
	$day = $startTime["mday"];
	$year = $startTime["year"];
	$startDate = $month."/".$day."/".$year;

	$formType = "Edit Event";

	if ($typeID == 4 || $typeID == 5)
		echo HTMLHelper :: startWidget("meetingTop");
	else
		echo HTMLHelper :: startWidget("editEventTop");
	echo "<div id = \"eventForm\">";

	if ($event->getTypeID() == 4 || $event->getTypeID() == 5) {
		$db = new DB();
		$creator = $db->getScalar("SELECT tblMeeting.person_id FROM tblMeeting, tblMeetingEvent WHERE tblMeetingEvent.event_id = $event_id AND tblMeetingEvent.meeting_id = tblMeeting.meeting_id");

		$specificDate = getdate($startDatetime);
		$specMonth = $specificDate["mon"];
		$specDay = $specificDate["mday"];
		$specYear = $specificDate["year"];
		$specDate = $specMonth."/".$specDay."/".$specYear;

		if ($startMinutes == 0)
			$startMinutes = "00";

		if ($endMinutes == 0)
			$endMinutes = "00";

		$meeting_info .= "<a href = 'javascript:showEventForm();'>:: Return to Add Event</a><p>";
		
		$meeting_info .= "<div class=\"chunk\">";
		$creatorCondition = "WHERE person_id = '$creator'";
		$creatorQuery = $db->getOneRecord("tblPerson", $creatorCondition);
		$creator_fn = $creatorQuery["first_name"];
		$creator_ln = $creatorQuery["last_name"];
		
		$meeting_info .= "<span style=\"color:#999;\">Creator:</span> $creator_fn $creator_ln<br/>";

		$meeting_info .= "<div class=\"recurrenceLabel\">Invited:</div>";
		$meeting_info .= "<div class=\"indent\">";

		foreach ($person_array as $id) {

			$condition = "WHERE person_id = '$id'";
			$invited = $db->getOneRecord("tblPerson", $condition);
			$invited_fn = $invited["first_name"];
			$invited_ln = $invited["last_name"];

			//$confirmedCondition = "WHERE person_id = '$id' AND event_id = '$event_id'";
			$confirmedStatus = $db->getScalar("SELECT response FROM tblPersonEvent WHERE person_id = '$id' AND event_id = '$event_id'");
			//echo "SELECT response FROM tblPersonEvent WHERE person_id = '$id' AND event_id = '$event_id'";
			//echo $confirmedStatus;

			//$confirmed = $confirmedStatus["response"];
			
			if($confirmedStatus == "0") {
				$meeting_info .= "-$invited_fn $invited_ln<br/>";
			}
			else {
				$meeting_info .= "<span class=\"gray\">-$invited_fn $invited_ln</span><br/>";
			}

		}

		$meeting_info .= "</div></div>";
		
		//$meeting_info .= "<div class=\"chunk\">";
		$meeting_info .= "<span class=\"requested\">Title: </span> $name<br/>";
		if($description != "") {
			$meeting_info .= "<span class=\"requested\">Description: </span><div class=\"indent\">$description</div>";
		}
		$meeting_info .= "<span class=\"requested\">Date: </span> $specDate<br/>";
		$meeting_info .= "<span class=\"requested\">Start Time: </span> $startHour:$startMinutes $startAMPM<br/>";
		$meeting_info .= "<span class=\"requested\">End Time: </span> $endHour:$endMinutes $endAMPM<br/>";

		if ($recurrenceEnd) {

			$meeting_info .= "<div class=\"recurrenceLabel\">Recurrence:</div>";
			$meeting_info .= "<div class=\"indent\">";

			$specificDate = getdate($recurrenceEnd);
			$specMonth = $specificDate["mon"];
			$specDay = $specificDate["mday"];
			$specYear = $specificDate["year"];
			$specDate = $specMonth."/".$specDay."/".$specYear;

			if ($recurrenceDays == "U|M|T|W|R|F|S") {
				$recur = "day";
				//$meeting_info .= "-Every day";
			} else {

				$fulldays = array ("U" => "Sunday", "M" => "Monday", "T" => "Tuesday", "W" => "Wednesday", "R" => "Thursday", "F" => "Friday", "S" => "Saturday");

				$days = explode("|", $recurrenceDays);
				for ($i = 0; $i < count($days); $i ++) {
					if ($i == count($days) - 1) {
						$recur .= $fulldays[$days[$i]];
						break;
					} else
						$recur .= $fulldays[$days[$i]].", ";
				}

			}

			$meeting_info .= "-Every $recur<br/>";

			if ($recurrenceWeeks == 2) {
				$meeting_info .= "-Every other week<br/>";
			}
			elseif ($recurrenceWeeks == 3) {
				$meeting_info .= "-Every 3 weeks<br/>";
			}
			elseif ($recurrenceWeeks == 4) {
				$meeting_info .= "-Every 4 weeks<br/>";
			}

			$meeting_info .= "-Until $specDate";
			$meeting_info .= "</div>";

			if ($exceptions) {
				$meeting_info .= "<div class=\"recurrenceLabel\">Exceptions:</div>";
				$meeting_info .= "<div class=\"indent\">";

				if (count($exceptions) > 1)
					$date = "dates";
				else
					$date = "date";

				//$meeting_info .= "<div class =\"requested2\"><span class=\"requested\">Except for the $date of:</span>";
				foreach ($exceptions as $excepts) {
					
					
					
					$specificDate = getdate($excepts);
					$specMonth = $specificDate["mon"];
					$specDay = $specificDate["mday"];
					$specYear = $specificDate["year"];
					$specDate = $specMonth."/".$specDay."/".$specYear;
					$meeting_info .= "-$specDate<br/>";
				}

				$meeting_info .= "</div>";
				$meeting_info .= "</div>";
			}

		}

		//$meeting_info .= "</div>"; //end chunk div
		
		//$meeting_info .= "<div>Meetings may only be edited by their creator.</div>";
		echo $meeting_info;

	} else
		include ("../views/event_form.php");

	echo "</div>";
	echo HTMLHelper :: endWidget();

}
/** This if is used when a user clicks on the 'X'
* in the corner of an event. It is used for
* deleting the instance of the event, or the
* event entirely. 
*/
elseif (isset ($_GET["del_id"])) {
	$event_id = $_GET["del_id"];
	$display_start = $_GET["date"];
	$event_day = $_GET["day"];

	$display_start += ($event_day * 86400);
	$event = new Event($event_id);

	$box = "<span class=\"delete_dialog\">";

	if ($event->getRecurrenceStart()) {
		$box .= "<div id=\"instance$event_id\" onClick='addException($event_id, $display_start);' onmouseover=\"document.getElementById('instance$event_id').className='hover';\" onmouseout=\"document.getElementById('instance$event_id').className='no_hover'\";> Delete instance</div>";
		$box .= "<div id=\"all$event_id\" onClick='delEvent($event_id, $display_start);' onmouseover=\"document.getElementById('all$event_id').className='hover';\" onmouseout=\"document.getElementById('all$event_id').className='no_hover'\";> Delete all</div>";
	} else {
		$box .= "<div id=\"$event_id\" onClick='delEvent($event_id, $display_start);' onmouseover=\"document.getElementById('$event_id').className='hover';\" onmouseout=\"document.getElementById('$event_id').className='no_hover'\";> Delete Event</div>";
	}

	$box .= "</span>";

	echo $box;
}
/** This is used when a user decides to delete
* all instances of an event. 
*/
elseif (isset ($_GET["delete"])) {
	$event = new Event($_GET["delete"]);
	$date = ($_GET["date"]);
	$event_id = $event->getEventID();
	if ($event->getTypeID() == 4) {
		$event_start = $event->getStartDatetime();
		$start_hours = date("g", $event_start) * 3600;
		$start_minutes = date("i", $event_start) * 60;

		$message = ($event->getRecurrenceStart() == NULL) ? (true) : (false);

		$db = new DB();
		$meeting_id = $db->getScalar("SELECT meeting_id FROM tblMeetingEvent WHERE event_id = $event_id");
		$meeting = new Meeting($meeting_id);
		$meeting->emailDeleteMeeting(($date + $start_hours + $start_minutes), $message);
		$meeting->deleteMeeting();
	} else {
		$event->deleteEvent();
	}

}
/** This is used when a user decides to delete
* one instance of an event. An exception for
* that is added. 
*/
elseif (isset ($_GET["exception"])) {
	$event = new Event($_GET["exception"]);
	$date = $_GET["date"];

	$event->addException($date);
	$event->updateDB();
	$event_id = $event->getEventID();

	/** check to see if event is a meeting. If so, send an email*/
	if ($event->getTypeID() == 4) {

		$event_start = $event->getStartDatetime();
		$start_hours = date("g", $event_start) * 3600;
		$start_minutes = date("i", $event_start) * 60;

		$db = new DB();
		$meeting_id = $db->getScalar("SELECT meeting_id FROM tblMeetingEvent WHERE event_id = $event_id");
		$meeting = new Meeting($meeting_id);
		$meeting->emailDeleteMeeting(($date + $start_hours + $start_minutes), true);
	}

}
/** This is used when a user decides to return
* to the original add event form. 
*/
elseif (isset ($_GET["showEvent"])) {
	echo "<div id=\"addEventTop\"></div>";
	echo "<div class=\"widgetBox\">";
	echo "<div class=\"widgetContent\">";
	echo "<div id = \"eventForm\">";
	include ("../views/event_form.php");
	echo "</div>";

}
?>