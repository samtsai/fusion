<?php
include ("../common.php");

$db = new DB();

$meeting_ids = $db->getArray("SELECT meeting_id FROM tblMeeting");

foreach ($meeting_ids as $id) {
	$meeting = new Meeting($id);
	
	if ($meeting->isAccepted() == TRUE) {
		$meeting_event = new Event($db->getScalar("SELECT event_id FROM tblMeetingEvent WHERE meeting_id = $id"));
		$schedule = new Schedule();

		$meeting_name = $meeting_event->getEventName();

		$week_start = $schedule->getWeekStart();
		$week_end = $schedule->getWeekEnd();
		$times = array ();
		$days = $meeting_event->getDates($week_start, $week_end);
		if ($days) {
			foreach ($days as $day) {
				$stime = $meeting_event->getStartDatetime();
				$sdata = getdate($stime);
				$ddata = getdate($day);

				$event_time = mktime($sdata["hours"], $sdata["minutes"], 0, $ddata["mon"], $ddata["mday"], $ddata["year"]);
				$times[] = $event_time;
			}
		}

		$people = $meeting->getAttendees();
		if ($people) {
			foreach ($people as $person_id) {

				$person = new Person($person_id);

				$time = $person->getEmailTime();
				if ($time != 0) {
					$t = $person->getFirstName();

					foreach ($times as $key => $event_time) {

						//This dictates that we need to run the script every 15 minutes
						//if the script is run more or less frequently these times must
						//be adjusted accordingly
						$spot_start = $event_time - ($time * 3600) - (7.5 * 60);
						$spot_end = $event_time - ($time * 3600) + (7.5 * 60);

						$now = mktime();

						$stime = date("g:i A", $spot_start);
						$etime = date("g:i A", $spot_end);
						$ntime = date("g:i A", $now);

						if ($event_time > $now && ($now >= $spot_start && $now < $spot_end)) {
							sendReminderEmail($person_id, $meeting_name, $event_time);
						} else {
						
						}

					}
				}
			}
		}
	} else {

	}		
}

function sendReminderEmail($person_id, $meeting_name, $date) {

	$person = new Person($person_id);
	$name = $person->getFirstName();
	$email = $person->getEmail();

	$date_info = date("l, F jS Y", $date);
	$time_info = date("g:i A", $date);

	$subject = "Meeting Reminder";
	$message = "Dear $name,\n\n"."This is a reminder that you have your meeting '$meeting_name' at $time_info on $date_info.\n\n\n"."--FUSION";
	$from = "From: Fusion Administrator <support.fusion@gmail.com>";
	mail($email, $subject, $message, $from);

}
?>