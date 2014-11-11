<?php
include ("../common.php");

$db = new DB();
$meeting_ids = $db->getArray("SELECT meeting_id FROM tblMeeting");

//mail("msnider@andrew.cmu.edu", "hello", "ran auto meeting confirm");
//mail("tsisson@andrew.cmu.edu", "hello", "ran auto meeting confirm");
//dump($meeting_ids);
foreach ($meeting_ids as $meeting_id) {
	$meeting = new Meeting($meeting_id);
	$accepted = $meeting->isAccepted();
	/*if($accepted===FALSE)
		echo "HOORAY! FALSE!<br>";
	else
		echo "DIEEE nothing<br>";
    echo "meeting id: $meeting_id<br>";
    echo "accepted: $accepted <br>";
    */
	//if meeting hasn't already been accepted/set
	if ($accepted !== true && $accepted !=1) {
		$response = $meeting->getResponseTime();
		$curTime = mktime();

		//checks to see if response time is after the current time
		//echo "response: $response<br>";
		$temp = 1162360800;
        if ($response <=$curTime){
		//nif ($response <= $curTime) {

			$meetingQuery = "Select priority from tblMeetingEvent where meeting_id = $meeting_id AND rejected = 0";

			$db = new DB();
			$priorityArray = $db->getArray($meetingQuery);
               
			//print_r($priorityArray);
			
			if ($priorityArray) {
				$highPriority = min($priorityArray);
				
				$eventIDQuery = "SELECT event_id from tblMeetingEvent where meeting_id = $meeting_id and priority = $highPriority";
				$event_id = $db->getScalar($eventIDQuery);
				if ($event_id) {
					//changes temp meeting to meeting
					
					$meetingEvent = new Event($event_id);
					//echo "event type: ".$meetingEvent->getTypeID()."<br>";
					if ($meetingEvent->getTypeID() == 4)
						continue;

					$meetingEvent->setEventType(4);
					$meetingEvent->updateDB();
				}

				$query = "Select event_id from tblMeetingEvent WHERE meeting_id = $meeting_id AND event_id <> $event_id";
				$eventIDArray = $db->getArray($query);

				if ($eventIDArray) {
					//iterate through those event id's and delete them using event class
					foreach ($eventIDArray as $id) {
						$event = new Event($id);
						$event->deleteEvent();
						$db->deleteRecord('tblMeetingEvent', 'event_id', $id);
					}
				} //end of if eventIDArray
				//retrieve people to send email to
				$query = "Select person_id from tblPersonEvent where event_id = $event_id";
				$personArray = $db->getArray($query);

				//email each person confirmation
				if ($personArray && $meeting_id && $highPriority) {
					foreach ($personArray as $person_id) {
						$meeting = new Meeting($meeting_id);
						if($person_id==138||$person_id==139||$person_id==226)
							$meeting->emailConfirmation($person_id, $highPriority);
					}
				}
				//end of if personArray   	

			} //end of if priorityArray
			//else
			//echo "meetingID: ".$id;
		} //if response > curTime

	} // end of accepted != true

	//echo "<br/><br/><br/>";
} //end of foreach meeting_ids
?>

