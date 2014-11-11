<?php
class Meeting {

	private $meeting_id;
	private $creator_id;
	private $response_time;
	private $meeting_name;
	private $description;
	private $attendees;
	private $exceptions;
	private $recurrence_start;
	private $recurrence_end;
	private $recurrence_weeks;
	private $recurrence_days;

	private $times; //associative array containing the 
	//information related to particular
	//potential meeting times: key (event_id) or increments 
	// (start_datetime, end_datetime, exceptions, recurrence_start, 
	//  recurrence_end, recurrence_weeks, recurrence_days, rejected)

	private $accepted; // boolean variable to be set to true or false depending
	// on if all times have been rejected, or if all users
	//have responded, otherwise null to indicate that we can't tell

	function __construct($meeting_id = NULL) {

		$this->meeting_id = $meeting_id;
		$this->creator_id = NULL;
		$this->response_time = NULL;
		$this->meeting_name = "";
		$this->description = "";
		$this->attendees = array ();
		$this->times = array ();
		$this->accepted = NULL;
		$this->exceptions = array ();
		$this->recurrence_start = NULL;
		$this->recurrence_end = NULL;
		$this->recurrence_weeks = 0;
		$this->recurrence_days = "";

		if ($this->meeting_id != NULL)
			$this->initialize();

	}

	public function getId() {
		return $this->meeting_id;
	}

	public function getCreatorId() {
		return $this->creator_id;
	}

	public function getResponseTime() {
		return $this->response_time;
	}

	public function getMeetingName() {
		return $this->meeting_name;
	}

	public function getDescription() {
		return $this->description;
	}
	public function getTimes() {
		return $this->times;
	}
	public function getAttendees() {
		return $this->attendees;
	}
	public function getExceptions() {
		return $this->exceptions;
	}
	public function getRecurrenceStart() {
		return $this->recurrence_start;
	}

	public function getRecurrenceEnd() {
		return $this->recurrence_end;
	}
	public function getReccurenceWeeks() {
		return $this->reccurence_weeks;
	}
	public function getRecurrenceDays() {
		return $this->recurrence_days;
	}
	public function isAccepted() {
		$this->buildAcceptance();
	//	echo "thisaccepted: ".$this->accepted."<br>";
		return $this->accepted;
	}

	public function setCreator($int) {
		$this->creator_id = $int;
	}

	public function setId($int) {
		$this->meeting_id = $int;
	}

	public function setResponseTime($int) {
		$this->response_time = (mktime() + ($int * 3600));
	}

	public function setName($str) {
		$this->meeting_name = $str;
	}

	public function setDescription($str) {
		$this->description = $str;
	}

	public function setAttendees($array) {
		$this->attendees = $array;
	}
	public function setExceptions($array) {
		$this->exceptions = $array;
	}
	public function setRecurrenceStart($datetime) {
		$this->recurrence_start = $datetime;
	}

	public function setRecurrenceEnd($datetime) {
		$this->recurrence_end = $datetime;
	}

	public function setRecurrenceWeeks($num_weeks) {
		$this->recurrence_weeks = $num_weeks;
	}

	public function setRecurrenceDays($daystring) {
		$this->recurrence_days = $daystring;
	}

	public function initialize() {
		$meeting_id = $this->getId();

		if ($meeting_id == "") { // event ID hasn't been set yet...
			return FALSE;
		}

		// Step 2: Get all the data from tblEvent for that event

		// Specify a particular event from tblEvent

		$condition = "WHERE meeting_id = $meeting_id";
		// Set up a link to the database and get the record

		$db = new DB();
		//MODIFIED PARAMETERS

		$meeting_data = $db->getOneRecord('tblMeeting', $condition);

		if (!$meeting_data) { // No results were found
			return FALSE;
		} else {

			$arrayQuery = "SELECT event_id FROM tblMeetingEvent WHERE meeting_id = $meeting_id";
			$event_array = $db->getArray($arrayQuery);
			if ($event_array) {

				foreach ($event_array as $key => $eventID) {
					$priorityQuery = "SELECT priority FROM tblMeetingEvent WHERE event_id = $eventID";
					$priority = $db->getScalar($priorityQuery);
					$rejected = $db->getScalar("SELECT rejected FROM tblMeetingEvent WHERE event_id = $eventID");

					$condition = "WHERE event_id = $eventID";
					$eventRecord = $db->getOneRecord('tblEvent', $condition);

					$start_datetime = $eventRecord["start_datetime"];
					$end_datetime = $eventRecord["end_datetime"];

					if ($recurrence_data = $db->getOneRecord('tblRecurrence', $condition)) {

						$recurrence_start = $recurrence_data["start_datetime"];
						$recurrence_end = $recurrence_data["end_datetime"];
						$recurrence_weeks = $recurrence_data["weeks"];
						$recurrence_days = $recurrence_data["days"];
					}

					if ($exception_data = $db->getArray("SELECT exception_datetime FROM tblException $condition")) {
						$exceptions = $exception_data;
					}

					$this->addMeetingtime($priority, $start_datetime, $end_datetime, $exceptions, $recurrence_start, $recurrence_end, $recurrence_weeks, $recurrence_days, $rejected);

				}
			}

			$this->creator_id = $meeting_data["person_id"];
			$this->response_time = $meeting_data["responseTime"];

			$description_id = $db->getScalar("SELECT description_id FROM tblEventDescription $condition");

			$condition = "WHERE description_id = $description_id";
			$description_data = $db->getOneRecord('tblDescription', $condition);

			$this->meeting_name = stripslashes($description_data["name"]);
			$this->description = stripslashes($description_data["description"]);

			$arrayQuery = "SELECT person_id FROM tblPersonEvent WHERE event_id = $eventID";
			$this->attendees = $db->getArray($arrayQuery);

			$this->buildAcceptance();

		} // end of else
		return TRUE;

	}

	public function updateDB() {

	}

	private function buildAcceptance() {

		$db = new DB();
		$meeting_id = $this->meeting_id;
				
/*		$event_id = $db->getScalar("SELECT event_id FROM tblMeetingEvent WHERE meeting_id = $meeting_id");
		$db->query("SELECT event_id FROM tblMeetingEvent WHERE rejected = 0 AND meeting_id = $meeting_id");
		
		$accepted_count = $db->numReturned();
*/

		//echo "meeting id $meeting_id<br>";
		$total_events = $db->getScalar("SELECT COUNT(event_id) FROM tblMeetingEvent WHERE meeting_id = $meeting_id");		
		//echo "totalevents: $total_events<br>";
		if($total_events > 1)
		{
			//echo "first case<br>";
			$this->accepted = FALSE;
			return;		
		}
		else
		{
			$rejected = $db->getScalar("SELECT rejected FROM tblMeetingEvent WHERE meeting_id = $meeting_id");			
			if($rejected==0){
				
				//echo "second case<br>";
				$this->accepted = TRUE;
				
				return;
			}
			//echo "third case<br>";
			$this->accepted = FALSE;
			return;
		}
		//echo "SELECT COUNT(event_id) FROM tblMeetingEvent WHERE meeting_id = $meeting_id";
		//$rejected = $total_events - $accepted_count;
		
		//echo "$accepted_count Accepted Events, $total_events Total Events, meaning $rejected Rejected events for this meeting<br/>";
/*//
//		if (($accepted_count == $total_events) && $accepted_count == 1) {
//			$responseArray = $db->getArray("SELECT response from tblPersonEvent WHERE event_id = $event_id");
//			if ($responseArray) {
//				foreach ($responseArray as $response) {
//					if ($response != 0 && $response != null) {
//						$this->accepted = FALSE;
//						return;
//					}
//				}
//			}
//			$this->accepted = TRUE;
//		}
//		elseif ($rejected == $total_events) {
//			$this->accepted = FALSE;
//		} else {
//			$this->accepted = NULL;
//		}
*/
	}

	public function requestMeeting() {

		//create the meeting
		//add the person_meeting references
		//create the events for each possible time
		//recurrence
		//exceptions
		//get the list of event_ids
		//insert those into the meeting_event information
		//insert those into each members list of events

		$db = new DB();
		$pid = $this->creator_id;
		$time = $this->response_time;
		$name = addslashes($this->meeting_name);
		$description = addslashes($this->description);

		$this->meeting_id = $db->insertRecord("INSERT INTO tblMeeting VALUES(NULL, $pid, $time)");
		$description_id = $db->insertRecord("INSERT INTO tblDescription VALUES(NULL, '$name', '$description')");
		
		
		
		//since this is creation, the $event_ids will not be set to particular values
		//also the meeting is temporary until 
		//Meeting = array("start_datetime => 1")
		//array_push($times, $meeting); 
		$i = 0;
		$list = array ();
		foreach ($this->times as $priority => $information) {

			$start_datetime = $information["start_datetime"];
			$end_datetime = $information["end_datetime"];

			$start = getdate($start_datetime);
			$end = getdate($end_datetime);

			$startHours = $start["hours"];
			$startMinutes = $start["minutes"];

			$endHours = $end["hours"];
			$endMinutes = $end["minutes"];

			if ($endHours < $startHours) {
				$endHoursTemp = 23;
				$endMinutesTemp = 59;
				$split_event = true;
			}
			$startMonth = $start["mon"];
			$startDay = $start["mday"];
			$startYear = $start["year"];

			$endMonth = $end["mon"];
			$endDay = $end["mday"];
			$endYear = $end["year"];
			if ($endHours < $startHours)
				$end_datetime = mktime($endHoursTemp, $endMinutesTemp, 0, $startMonth, $startDay, $startYear);
			else
				$end_datetime = mktime($endHours, $endMinutes, 0, $endMonth, $endDay, $endYear);

			${ "event$i" } = new Event();
			${ "event$i" }->setEventType(5);
			${ "event$i" }->setDescriptionId($description_id);
			${ "event$i" }->setPersonId($pid);
			${ "event$i" }->setStartDatetime($start_datetime);
			${ "event$i" }->setEndDatetime($end_datetime);

			if (isset ($information["recurrence_start"])) {
				$recurrence_start = $information["recurrence_start"];
				$recurrence_end = $information["recurrence_end"];
				$recurrence_weeks = $information["recurrence_weeks"];
				$recurrence_days = $information["recurrence_days"];
				$exceptions_array = $information["exceptions"];

				${ "event$i" }->setRecurrenceStart($information["recurrence_start"]);
				${ "event$i" }->setRecurrenceEnd($information["recurrence_end"]);
				${ "event$i" }->setRecurrenceWeeks($information["recurrence_weeks"]);
				${ "event$i" }->setRecurrenceDays($information["recurrence_days"]);

				if (isset ($information["exceptions"])) {
					${ "event$i" }->setExceptions($information["exceptions"]);
				}
			}

			/**Adding times to array to be used while checking conflicts */
			$list[] = ${"event$i"};
			$i ++;

		} ////END OF FOR EACH

		foreach ($this->attendees as $person_id) {
			//if ($person_id == $pid) {
			foreach ($list as $listEvent) {
				$listEvent->setPersonId($person_id);
				if ($conflictInfo = $listEvent->getConflicts())
					return $conflictInfo;
			}
			//}
		}
		$priority = 1;
		foreach ($list as $listEvent) {
			$listEvent->setPersonId($pid);
			$listEventID = $listEvent->addEvent();

			foreach ($this->attendees as $key => $person_id) {
				if ($person_id != $this->creator_id) {
					$val = "NULL";
					$db->insertRecord("INSERT INTO tblPersonEvent VALUES ($person_id, $listEventID,$val)");
				}

			}

			$db->insertRecord("INSERT INTO tblMeetingEvent VALUES (".$this->meeting_id.", $listEventID, $priority, 0)");
			$priority ++;
		}
		
		$this->attendees = array_reverse($this->attendees);
		$this->emailRequest(444);
		foreach ($this->attendees as $person_id) {
			$this->emailRequest($person_id);
		}
		//emails dump account - hopefully pushes out emails
		$this->emailRequest(394);
		
	} /** end of request meeting */

	public function deleteMeeting() {

		$meeting_id = $this->meeting_id;
		$db = new DB();

		$events = $db->getArray("SELECT event_id FROM tblMeetingEvent WHERE meeting_id = $meeting_id");
		/** 
		 * Technically this should only be 1, 
		 * but lets make sure that we get all
		 * of the associated events.
		 */
		if ($events) {
			foreach ($events as $event_id) {
				$event = new Event($event_id);
				$event->deleteEvent();
				$db->query("DELETE FROM tblPersonEvent WHERE event_id = $event_id");
			}
		}

		/** Delete the other associated records **/

		$db->query("DELETE FROM tblMeetingEvent WHERE meeting_id = $meeting_id");
		$db->query("DELETE FROM tblMeeting WHERE meeting_id = $meeting_id");

	}

	/*This is a helper method to take each meeting and put them into an array. 
	 * The key is the priority of the meeting*/
	public function addMeetingTime($priority, $start_datetime, $end_datetime, $exceptions, $recurrence_start, $recurrence_end, $recurrence_weeks, $recurrence_days, $rejected) {

		$meetingInfo = array ("start_datetime" => $start_datetime, "end_datetime" => $end_datetime, "exceptions" => $exceptions, "recurrence_start" => $recurrence_start, "recurrence_end" => $recurrence_end, "recurrence_weeks" => $recurrence_weeks, "recurrence_days" => $recurrence_days, "rejected" => $rejected);
		$this->times["$priority"] = $meetingInfo;

	}

	public function emailRequest($pid) {
		$person = new Person($pid);
		$email = $person->getEmail();
		$name = $person->getFirstName();

		$responseTime = $this->getResponseTime();

		$response_time = getdate($responseTime);

		$weekday = $response_time["weekday"];
		$month = $response_time["month"];
		$day = $response_time["mday"];
		$year = $response_time["year"];
		$hours = $response_time["hours"];
		$AMPM = "AM";
		if ($hours == 0) {
			$hours = 12;
		}
		if ($hours >= 12) {
			$AMPM = "PM";
		}
		if ($hours > 12) {
			$hours = $hours -12;
		}
		$minutes = $response_time["minutes"];
		if ($minutes == 0) {
			$minutes = "00";
		}
		if ($minutes == 5) {
			$minutes = "05";
		}
		for ($i = 0; $i <= 9; $i ++) {
			if ($minutes == $i)
				$minutes = "0".$i;
		}
		$responseTimeBy = $hours.":".$minutes." ".$AMPM;
		$responseDate = $weekday." ".$month." ".$day;

		$creatorPerson = new Person($this->creator_id);
		$creatorName = $creatorPerson->getFirstName();

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";

		$message = "Dear $name,\n\n"."A meeting has been requested by: ".$creatorName;
		$message .= "\n\nMeeting name: ".$this->getMeetingName();
		$message .= "\n\nDescription: ".$this->getDescription();
		$message .= "\n\n\nThe following members have been invited : \n";
		$message .= $creatorName."\n";
		
		
		foreach ($this->attendees as $key => $person_id) {
			$member = new Person($person_id);
			$memberName = $member->getFirstName();
			if ($memberName != $creatorName)
				$message .= $memberName."\n";
		}

		$message .= "\n\n\nThe following potential times have been requested:\n\n";
		$midnight = false;
		$specialCase = false;
		foreach ($this->times as $priority => $information) {
			$start_datetime = $information["start_datetime"];
			$end_datetime = $information["end_datetime"];
			$new_start = getdate($start_datetime);
			$new_end = getdate($end_datetime);

			$weekday = $new_start["weekday"];
			$month = $new_start["month"];
			$day = $new_start["mday"];
			$year = $new_start["year"];

			$hours = $new_start["hours"];
			$endHours = $new_end["hours"];
			$minutes = $new_start["minutes"];
			$endMinutes = $new_end["minutes"];

			if ($endHours == 0 && $endMinutes == 0 && $specialCase!=true)
				$specialCase = true;
			else
				$specialCase = false;

			if ($endHours < $hours && $midnight!=true)
				$midnight = true;
			else
				$midnight = false;

			$AMPM = "AM";
			if ($hours == 0) {
				$hours = 12;
			}
			if ($hours >= 12) {
				$AMPM = "PM";
			}
			if ($hours > 12) {
				$hours = $hours -12;
			}

			if ($minutes == 0) {
				$minutes = "00";
			}
			if ($minutes == 5) {
				$minutes = "05";
			}
			$startTime = $hours.":".$minutes." ".$AMPM;

			$endAMPM = "AM";
			if ($endHours == 0) {
				$endHours = 12;
			}
			if ($endHours >= 12) {
				$endAMPM = "PM";
			}
			if ($endHours > 12) {
				$endHours = $endHours -12;
			}

			if ($endMinutes == 0) {
				$endMinutes = "00";
			}
			if ($endMinutes == 5) {
				$endMinutes = "05";
			}

			if ($midnight == true)
				$endTime = "Midnight";
			else
				$endTime = $endHours.":".$endMinutes." ".$endAMPM;

			$startTime = $hours.":".$minutes." ".$AMPM;

			$message .= $weekday." ".$month." ".$day.", ".$year."  ".$startTime."  to  ".$endTime."\n";

			if (isset ($information["recurrence_start"])) {
				$weeks = $information["recurrence_weeks"];
				$days = $information["recurrence_days"];
				$days = explode("|", $days);
				$dayString = "";
				foreach ($days as $key => $dayValue) {
					if ($dayValue == "U")
						$dayString = "Sunday ";
					if ($dayValue == "M")
						$dayString .= "Monday ";
					if ($dayValue == "T")
						$dayString .= "Tuesday ";
					if ($dayValue == "W")
						$dayString .= "Wednesday ";
					if ($dayValue == "R")
						$dayString .= "Thursday ";
					if ($dayValue == "F")
						$dayString .= "Friday ";
					if ($dayValue == "S")
						$dayString .= "Saturday ";
				}
				//if($days{1}=="|" &&  $days{2}==""){$days = $days{0};}
				$message .= "		On ".$dayString." every ".$weeks." week(s)\n\n";
			}

		}

		$meetingID = $this->getId();
		$meetingName = $this->getMeetingName();
		$meetingDesc = $this->getDescription();

		if ($this->creator_id == $pid && $specialCase == false && $midnight == true) {
			$message .= "\n*NOTE*: Fusion does not currently support meetings that span midnight.\n";
			$message .= "If you want to extend this time further, please schedule another meeting starting at midnight.";
		}

		$message .= "\n\nPlease reject or confirm these times by ".$responseTimeBy." on ".$responseDate.".";
		$message .= "\n\nIf a meeting has not be confirmed by this time, the system will automatically schedule the optimal time.";

		$message .= "\n\n\nTo REJECT one or more of these meeting times please click the following link : \n http://rook.hss.cmu.edu/~team04s06/meeting_confirmation.php?action=reject&meeting_id=".$meetingID."&person_id=".$pid."\n\n";
		//$message.="\n\n\nTo REJECT one or more of these meeting times please click the following link : \n http://rook.hss.cmu.edu/~team04s06/index.php?action=reject&meeting_id=".$meetingID."&person_id=".$pid."\n\n";

		$message .= "\n\nTo CONFIRM all times please click the following link : \n http://rook.hss.cmu.edu/~team04s06/meeting_confirmation.php?action=confirm&meeting_id=".$meetingID."&person_id=".$pid."\n\n";
		//$message.="\n\nTo CONFIRM all times please click the following link : \n http://rook.hss.cmu.edu/~team04s06/index.php?action=confirm&meeting_id=".$meetingID."&person_id=".$pid."\n\n";

		$message .= "\n\n\nThanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";

		$subject = "Fusion Meeting Request";
	/*
		$mail = new PHPMailer();
		$mail->From = "fusion@admin.com";
		$mail->FromName = "Meeting Requester";
		$mail->AddAddress($email);		
		$mail->Subject = $subject;
		$mail->Body    = $message;
				
		if(!$mail->Send())
		{
		   echo "Message could not be sent. <p>";
		   echo "Mailer Error: " . $mail->ErrorInfo;
		}
	*/	
		//echo "$email, $subject, $from <br/><br/>";
		
		mail($email, $subject, $message, $from);
	}

	public function emailConfirmation($pid, $priority) {
		$person = new Person($pid);
		$email = $person->getEmail();
		$name = $person->getFirstName();

		$creatorPerson = new Person($this->creator_id);
		$creatorName = $creatorPerson->getFirstName();

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";

		$message = "Dear $name,\n\n"."A meeting has been confirmed and scheduled. ";
		$message .= "\n\nMeeting name: ".$this->getMeetingName();
		$message .= "\n\nDescription: ".$this->getDescription();
		$message .= "\n\n\nThe following members have been invited : \n";
		$message .= $creatorName."\n";
		foreach ($this->attendees as $key => $person_id) {
			if ($person_id != $this->creator_id) {
				$member = new Person($person_id);
				$memberName = $member->getFirstName();
				$message .= $memberName."\n";
			}
		}

		$message .= "\n\nThe meeting has been set for the following time:\n\n";

		foreach ($this->times as $key => $information) {
			if ($key == $priority) {
				$start_datetime = $information["start_datetime"];
				$end_datetime = $information["end_datetime"];
				$new_start = getdate($start_datetime);
				$new_end = getdate($end_datetime);

				$weekday = $new_start["weekday"];
				$month = $new_start["month"];
				$day = $new_start["mday"];
				$year = $new_start["year"];
				$hours = $new_start["hours"];
				$AMPM = "AM";
				if ($hours == 0) {
					$hours = 12;
				}
				if ($hours >= 12) {
					$AMPM = "PM";
				}
				if ($hours > 12) {
					$hours = $hours -12;
				}
				$minutes = $new_start["minutes"];
				if ($minutes == 0) {
					$minutes = "00";
				}
				if ($minutes == 5) {
					$minutes = "05";
				}
				$startTime = $hours.":".$minutes." ".$AMPM;

				$endHours = $new_end["hours"];
				$endAMPM = "AM";
				if ($endHours == 0) {
					$endHours = 12;
				}
				if ($endHours >= 12) {
					$endAMPM = "PM";
				}
				if ($endHours > 12) {
					$endHours = $endHours -12;
				}
				$endMinutes = $new_end["minutes"];
				if ($endMinutes == 0) {
					$endMinutes = "00";
				}
				if ($endMinutes == 5) {
					$endMinutes = "05";
				}
				$endTime = $endHours.":".$endMinutes." ".$endAMPM;

				$message .= $weekday." ".$month." ".$day.", ".$year."  ".$startTime."  to  ".$endTime."\n";
				if (isset ($information["recurrence_start"])) {
					$weeks = $information["recurrence_weeks"];
					$days = $information["recurrence_days"];
					$days = explode("|", $days);
					$dayString = "";
					foreach ($days as $key => $dayValue) {
						if ($dayValue == "U")
							$dayString = "Sunday ";
						if ($dayValue == "M")
							$dayString .= "Monday ";
						if ($dayValue == "T")
							$dayString .= "Tuesday ";
						if ($dayValue == "W")
							$dayString .= "Wednesday ";
						if ($dayValue == "R")
							$dayString .= "Thursday ";
						if ($dayValue == "F")
							$dayString .= "Friday ";
						if ($dayValue == "S")
							$dayString .= "Saturday ";
					}
					//if($days{1}=="|" &&  $days{2}==""){$days = $days{0};}
					$message .= "		On ".$dayString." every ".$weeks." week(s)\n\n";
				} // end of isset information recur start
			} // end of if($key==$priority)
		} //end of foreach 

		$message .= "\n\n\nYou may view this meeting and other events by logging into Fusion at http://rook.hss.cmu.edu/~team04s06/index.php";

		$message .= "\n\n\nThanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";

		$subject = "Fusion Meeting Confirmation";

		mail($email, $subject, $message, $from);
	}

	public function emailRejection($pid) {
		$person = new Person($pid);
		$email = $person->getEmail();
		$name = $person->getFirstName();

		$creatorPerson = new Person($this->creator_id);
		$creatorName = $creatorPerson->getFirstName();

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";

		$message = "Dear $name,\n\n"."The meeting that was requested cannot be scheduled. All times have been rejected for the following meeting:  ";
		$message .= "\n\nMeeting name: ".$this->getMeetingName();
		$message .= "\n\nDescription: ".$this->getDescription();

		if ($this->creator_id == $pid)
			$message .= "\n\nPlease log in to Fusion at http://rook.hss.cmu.edu/~team04s06/index.php and request a new meeting.";
		else
			$message .= "\n\n\nThe creator of this meeting has been notified and will take the necessary actions.\n You can request a meeting yourself by logging on to Fusion at http://rook.hss.cmu.edu/~team04s06/index.php";

		$message .= "\n\n\nThanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";

		$subject = "Meeting Could not be scheduled";

		mail($email, $subject, $message, $from);
	}
	public function emailDeleteMeeting($date_info, $exception) {

		/** find which meeting corresponds to the event id and get all attendees*/
		$meetingName = $this->meeting_name;
		$description = $this->description;
		$attendees = $this->getAttendees();

		$date = date("l, F jS", $date_info);
		$time = date("g:i A", $date_info);

		$from = "From: Fusion Administrator <support.fusion@gmail.com>";
		$subject = "Meeting Canceled";
		foreach ($attendees as $person_id) {
			$person = new Person($person_id);
			$email = $person->getEmail();
			$name = $person->getFirstName();

			$message = "Dear $name,\n\n"."The meeting '$meetingName' on $date at $time has been canceled.\n";

			if (!$exception)
				$message .= "All future meetings at this time have also been canceled.";

			$message .= "\n\n\nThanks!\n"."The Fusion Team\n\n"."This is an automated response, please do not reply!\n"."You may send any questions to support.fusion@gmail.com.";
			mail($email, $subject, $message, $from);
		}
		mail('fusion.dump@gmail.com', 'deleteMeeting_dump', 'dump', 'Fusion!');

	}

} //end meeting class
?>