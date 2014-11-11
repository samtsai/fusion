<?php


/**
 * This class represents all events within the system.
 * 
 * Property of Team Fusion 
 * 
 * This class handles the abstraction of all information regarding 
 * events stored in the system into a single entity, the Event.  Events
 * are the core component that drives the system.  This class contains some
 * complicated functionality, including the computation of occurrence
 * of events and the ability to tell whether or not one event conflicts with
 * another event at any point in the past or future. It also sends the directives
 * to the database class in order to deal with the creation, update, or deletion
 * of event entities.
 * 
 * @author		Matthew Snider & Trey Sisson
 * @version		1.0
 * @since		1.0
 */

class Event {

	private $event_id; /** ID to track particular event entities */
	private $event_name; /** The name attached to the particular event */
	private $person_id; /** The person_id of the individual to which the event is attached */
	private $type_id; /** A numeric code representing the "type" of event in question (Class, Work, Personal, etc) */
	private $description_id; /** Numeric ID linking the event to a particular description, so that one description can be linked to multiple events */
	private $event_description; /** The description attached to this event entity */
	private $start_datetime; /** Unix timestamp representing the start date and time of the base event entry */
	private $end_datetime; /** Unix timestamp representing the end date and time of the base event entry */
	private $recurrence_start; /** Unix timestamp representing the first day on which an event begins recurring: Is currently always equal to start_datetime */
	private $recurrence_end; /** Unix timestamp representing the day on which an event stops recurring */
	private $recurrence_weeks; /** Numeric entry representing the number of weeks between recurrences of this event */
	private $recurrence_days; /** (U|M|T|W|R|F|S) Pipe delimited string representing the days on which an event recurrs */
	private $exceptions; /** Array of Unix timesamps representing days on which the event will not occur, regardless of other characteristics */
	private $dates; /** Constructed array of unix timestamps giving the days during a particular period on which the event will occur, given its start, end, recurrence, and exception characteristics */
	private $conflictMessage; /** String message containing the output of the getConflicts function, for easy access by outer-class entities */

	/** 
	 * Event Constructor
	 * Initializes all internal variables to their default values, and calls init() if an event_id is provided.
	 */
	function __construct($event_id = NULL) {

		$this->event_id = $event_id;
		$this->event_name = "";
		$this->person_id = NULL;
		$this->type_id = NULL;
		$this->description_id = 0;
		$this->event_description = "";
		$this->start_datetime = NULL;
		$this->end_datetime = NULL;
		$this->recurrence_start = NULL;
		$this->recurrence_end = NULL;
		$this->recurrence_weeks = 0;
		$this->recurrence_days = "";
		$this->exceptions = array ();
		$this->dates = array ();
		$this->conflictMessage = "";

		if ($this->event_id != NULL)
			$this->initialize();
	}

	/********************************
	 * Basic Setter Methods For Event
	 ********************************/
	public function setEventID($int) {
		$this->event_id = $int;
	}

	public function setEventName($string) {
		$this->event_name = $string;
	}

	public function setPersonId($int) {
		$this->person_id = $int;
	}

	public function setEventType($int) {
		$this->type_id = $int;
	}

	public function setEventDescription($string) {
		$this->event_description = $string;
	}

	public function setStartDatetime($datetime) {
		$this->start_datetime = $datetime;
	}

	public function setDescriptionId($int) {
		$this->description_id = $int;
	}

	public function setEndDatetime($datetime) {
		$this->end_datetime = $datetime;
	}

	public function addException($datetime) {
		$this->exceptions[] = $datetime;
	}

	public function setExceptions($datetimes) {
		$this->exceptions = $datetimes;
	}

	public function removeException($datetime) {
		unset ($this->exceptions[$datetime]);
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

	/*********************************
	 * Basic Getter Methods For Event
	 *********************************/
	public function getEventID() {
		return $this->event_id;
	}

	public function getEventName() {
		return $this->event_name;
	}

	public function getTypeID() {
		return $this->type_id;
	}

	public function getConflictsMessage() {
		return $this->conflictMessage;
	}

	public function getDescriptionId() {
		return $this->description_id;
	}

	public function getEventDescription() {
		return $this->event_description;
	}

	public function getStartDatetime() {
		return $this->start_datetime;
	}

	public function getEndDatetime() {
		return $this->end_datetime;
	}

	public function getStart() {
		return $this->getReadable($this->start_datetime);
	}

	public function getEnd() {
		return $this->getReadable($this->end_datetime);
	}

	public function getExceptions() {
		return $this->exceptions;
	}

	public function getDates($start, $end) {
		$this->updateDates($start, $end);
		return $this->dates;
	}

	public function getRecurrenceStart() {
		return $this->recurrence_start;
	}

	public function getRecurrenceEnd() {
		return $this->recurrence_end;
	}

	public function getRecurrenceWeeks() {
		return $this->recurrence_weeks;
	}

	public function getRecurrenceDays() {
		return $this->recurrence_days;
	}

	/**
	 *	Initialize the event class, given the provided Event_id.
	 *	This pulls the necessary information out of the database
	 *	and assigns it to the internal variables as appropriate.
	 */
	public function initialize() {

		// Step 1: Get the ID of the event we want to get data on

		$this->event_id = $this->getEventID();

		if ($this->event_id == "") { // event ID hasn't been set yet...
			return FALSE;
		}
		$event_id = $this->event_id;
		// Step 2: Get all the data from tblEvent for that event

		// Specify a particular event from tblEvent

		$condition = "WHERE event_id = $event_id";
		// Set up a link to the database and get the record

		$db = new DB();
		//MODIFIED PARAMETERS

		$event_data = $db->getOneRecord('tblEvent', $condition);

		if (!$event_data) { // No results were found
			return FALSE;
		} else {

			$this->type_id = $event_data["type_id"];

			$this->start_datetime = $event_data["start_datetime"];
			$this->end_datetime = $event_data["end_datetime"];

			if ($recurrence_data = $db->getOneRecord('tblRecurrence', $condition)) {

				$this->recurrence_start = $recurrence_data["start_datetime"];
				$this->recurrence_end = $recurrence_data["end_datetime"];
				$this->recurrence_weeks = $recurrence_data["weeks"];
				$this->recurrence_days = $recurrence_data["days"];
			}

			if ($exception_data = $db->getArray("SELECT exception_datetime FROM tblException $condition")) {
				$this->exceptions = $exception_data;
			}

			//retrieve ID from eventDescription table

			$description_id = $db->getScalar("SELECT description_id FROM tblEventDescription $condition");
			$this->description_id = $description_id;

			$condition = "WHERE description_id = $description_id";
			$description_data = $db->getOneRecord('tblDescription', $condition);
			$this->event_name = stripslashes($description_data["name"]);
			$this->event_description = stripslashes($description_data["description"]);

		} // end of else
		return TRUE;

	} // End of initialize()

	// -----------------------------------
	// FUNCTION: UPDATE DB

	public function updateDB() {

		// Get info needed to update database
		//divided these up into the tables they are associated with

		$id = $this->getEventID();
		$type = $this->getTypeID();
		$start = $this->getStartDatetime();
		$end = $this->getEndDatetime();

		$description = addslashes($this->getEventDescription());
		$name = addslashes($this->getEventName());

		$current_exceptions = $this->getExceptions();

		//start, end, and week are numbers, and recurrence days is controlled by the system.
		//so we don't need to add slashes to these either.	
		$recurrence_start = $this->getRecurrenceStart();
		$recurrence_end = $this->getRecurrenceEnd();
		$recurrence_weeks = $this->getRecurrenceWeeks();
		$recurrence_days = $this->getRecurrenceDays();

		if ($id == "") { // event ID hasn't been set yet...
			return FALSE;
		} else {
			$conflicts = $this->getConflicts();
			if ($conflicts) {

				$message .= "This event (".$this->event_name.") could not be rescheduled because it conflicts with $conflicts";
				//$message .= "Please correct the dates of this new event, add an exception, or edit the conflicting event.";
				$this->conflictMessage = $message;
				return false;
			}
		}
		// Update database

		// Set up a query to update the event information 
		$query3 = "UPDATE tblEvent SET type_id = $type, start_datetime = $start, end_datetime = $end WHERE event_id = '$id'";

		$db = new DB();

		if ($old_exceptions = $db->getArray("SELECT exception_datetime FROM tblException WHERE event_id = $id")) {

			//dump($current_exceptions);
			//dump($old_exceptions);
			if (!empty ($current_exceptions)) {
				//this produces a list of new exception dates
				$new_exceptions = array_diff($current_exceptions, $old_exceptions);
				//this provides a list of exception dates that should be removed
				$deleted_exceptions = array_diff($old_exceptions, $current_exceptions);

				foreach ($deleted_exceptions as $date) {
					//echo "getting rid of the old exception $date";
					$db->query("DELETE FROM tblException WHERE event_id = $id AND exception_datetime = $date");
				}

				foreach ($new_exceptions as $date) {
					$db->query("INSERT INTO tblException VALUES($id, $date)");
				}
			} else {
				foreach ($old_exceptions as $date) {
					$db->query("DELETE FROM tblException WHERE event_id = $id AND exception_datetime = $date");
				}
			}
		} else {
			if ($current_exceptions) {
				foreach ($current_exceptions as $date) {
					$db->query("INSERT INTO tblException VALUES($id, $date)");
				}
			}

		} //end of if old exceptions
		if ($db->getScalar("SELECT start_datetime FROM tblRecurrence WHERE event_id = $id")) {
			$query5 = "UPDATE tblRecurrence SET start_datetime = $recurrence_start,";
			$query5 .= "end_datetime = $recurrence_end,";
			$query5 .= "weeks = $recurrence_weeks,";
			$query5 .= "days = '$recurrence_days' WHERE event_id = $id";
			$Result5 = $db->updateRecord($query5);
		} else {
			$db->query("INSERT INTO tblRecurrence VALUES($id, $recurrence_start, $recurrence_end, $recurrence_weeks, '$recurrence_days')");

		}
		$description_id = $db->getScalar("SELECT description_id FROM tblEventDescription WHERE event_id = $id");
		if ($this->description_id != $description_id) {
			$description_id = $this->description_id;
			$db->updateRecord("UPDATE tblEventDescription SET description_id = $description_id WHERE event_id = $id");

		} else {
			$query6 = "UPDATE tblDescription SET name = '$name', description = '$description' WHERE description_id = $description_id";
			$Result6 = $db->updateRecord($query6);
		}
		// Execute the query and return the result

		$Result3 = $db->updateRecord($query3);

		//not all tables are necessarily updated, not sure how we should test for execution
		if ($Result3) {
			return TRUE;
		} else {
			return FALSE;
		}

		/** check to see if event is a meeting. If so, send an email*/
		$this->initialize();
		$eventType = $this->getTypeID();
		if ($eventType == 4) {
			$meeting = new Meeting();
			$meeting->emailEditMeeting($id);
		}
	}
	// End of editEvent()

	// ---------------------------
	//   FUNCTION: ADD EVENT

	public function addEvent() {

		// Get info needed to insert into database

		//Divided according to the tables they are associated with	
		$id = ($this->getEventID());
		$type = ($this->getTypeID());
		$start = ($this->getStartDatetime());
		$end = ($this->getEndDatetime());

		$description = addslashes($this->getEventDescription());
		$name = addslashes($this->getEventName());

		$exceptions = $this->getExceptions();

		$recurrence_start = ($this->getRecurrenceStart());
		$recurrence_end = ($this->getRecurrenceEnd());
		$recurrence_weeks = ($this->getRecurrenceWeeks());
		$recurrence_days = ($this->getRecurrenceDays());

		// Insert records into database

		// Set up a query to insert the user information

		//query for Event table
		// Execute the query and return the result

		//need to execute this first to obtain the event id
		$db = new DB();
		$Result = $db->insertRecord("INSERT INTO tblEvent VALUES (NULL,'$type','$start','$end')");

		if ($this->getEventName() != "Busy")
			$conflicts = $this->getConflicts();

		if (!$conflicts) {

			$db = new DB();
			$Result = $db->insertRecord("INSERT INTO tblEvent VALUES (NULL,'$type','$start','$end')");

			if (is_numeric($Result)) {
				$this->event_id = $Result; // set the id property of the event
				$id = $Result;
				if ($this->description_id) {
					$Result = $this->description_id;
				} else {
					$Result = $db->insertRecord("INSERT INTO tblDescription VALUES (NULL, '$name', '$description')");
					$this->description_id = $Result;
				}
				//need to execute this 2nd to obtain description id
				if (is_numeric($Result)) {
					//echo "new description id $Result <br/>";
					$description_id = $Result; // set the id property of the event

					$db->insertRecord("INSERT INTO tblEventDescription VALUES ($id, $description_id)");

					$pid = $this->person_id;

					$val = "NULL";

					$db->insertRecord("INSERT INTO tblPersonEvent VALUES ($pid, $id, $val)");

					if ($this->recurrence_start != NULL) {
						if (!$recurrence_weeks)
							$recurrence_weeks = 1;

						$db->insertRecord("INSERT INTO tblRecurrence VALUES ($id, $recurrence_start, $recurrence_end, $recurrence_weeks, '$recurrence_days')");

						if (!empty ($exceptions)) {
							foreach ($exceptions as $key => $date) {
								$db->insertRecord("INSERT INTO tblException VALUES($id, $date)");
							}
						}
					}
				} else
					return false;

				return $this->event_id;
			}

		} else {

			$message .= "Your new event (".$this->event_name.") could not be scheduled because it conflicts with $conflicts";
			//$message .= "Please correct the dates of this new event, add an exception, or edit the conflicting event.";
			$this->conflictMessage = $message;
			return false;
		}

	} // End of addEvent()

	// ---------------------------
	//   FUNCTION: DELETE EVENT

	public function deleteEvent() {

		// Get event ID to delete, description id needed also

		$id = $this->getEventID();
		$description_id = $this->getDescriptionID();

		if ($id == "") { // event ID hasn't been set yet...
			return FALSE;
		}

		// Delete record from database, 5 different tables
		$db = new DB();
		$Result = $db->deleteRecord("tblEvent", "event_id", $id);

		//need to get the description ID from the EventDescription table
		$description_id = $db->getScalar("SELECT description_id FROM tblEventDescription WHERE event_id = $id");
		$Result2 = $db->deleteRecord("tblEventDescription", "event_id", $id);

		//then we check to see if the description is being used for other events (so we don't delete it)	
		$event_ids = $db->getArray("SELECT event_id FROM tblEventDescription WHERE description_id = $description_id");

		if (empty ($event_ids)) //safe to delete the actual description
			$Result3 = $db->deleteRecord("tblDescription", "description_id", $description_id);

		$Result4 = $db->deleteRecord("tblException", "event_id", $id);
		$Result5 = $db->deleteRecord("tblRecurrence", "event_id", $id);
		$db->deleteRecord("tblPersonEvent", "event_id", $id);

		//there may or may not be info in all of the tables, how to test?
		if ($Result) {
			return TRUE;
		} else {
			return FALSE;
		}

	} // End of deleteEvent()

	// ---------------------------
	//   FUNCTION: UPDATE DATES
	/**********************************************
	 * This function retrieves the information    *
	 * for a specific week so it can be displayed * 
	 * on the calendar accordingly                *
	 **********************************************/
	//pass in a function to limit the dates returned
	private function updateDates($start_date, $end_date) {

		/******************************************************
			Get the detailed information regarding the start
			and end period.
		 ******************************************************/
		$start_date_array = getdate($start_date);
		$end_date_array = getdate($end_date);

		//go and get all of the recurrence information associated with this event

		/********************************************
		Rebuild the object to make sure we 
		are working with the most recent information
		 *******************************************/

		//$this->initialize();
		$dates = array ();

		$start_datetime = $this->getStartDatetime();
		$end_datetime = $this->getEndDatetime();
		$exceptions = $this->getExceptions();

		/******************************************************** 
		 If there are no exceptions, make sure we have an array, so
		 that we can still do the array_diff at the end of the function
		 *************************************************************/

		if (!$exceptions)
			$exceptions = array ();

		$date = getdate($start_datetime);

		if ($this->recurrence_start == NULL) {

			/*************************************************
			This event is non-recurring, so all we have to worry 
			about is whether or not it falls during this week.
			If it does, add its date to the list of days to print out.
			 *************************************************/
			
			if (($end_datetime < $start_date) || ($start_datetime > $end_date)) {
				$this->dates = array();
				return;
			}
			
			//echo "<br/>this non-recurring event ".$this->event_name ." occurs during the specified week <br/>";
			//simple mode, all we have to do is see if it falls within the start and end dates
			//if the event ends before the start of the viewing period
			//or starts after the end of it, then we can ignore it

			//if the event does actually fall somewhere in our visible week
			//we need to figure out what day it starts and ends on
			// *** events that span multiple days? ****
			// *** events that start in one week and end on another (meeting across midnight Saturday? ****
			// currently I don't think our interface supports the creation of these types of events, 
			// but its something we should probably consider

			$time = mktime(0, 0, 0, $date["mon"], $date["mday"], $date["year"]);
			array_push($dates, $time);
			//echo "This event has no recurrence and happens during the specified week.<br/><br/>";
			//dump($dates);

		} else {
			/************************************************
			This event is recurring, so populate the fields with
			the recurrence information.
			 ************************************************/
			
			$recurrence_start = $this->getRecurrenceStart();
			$recurrence_end = $this->getRecurrenceEnd();
			$recurrence_weeks = $this->getRecurrenceWeeks();
			$recurrence_days = $this->getRecurrenceDays();
			//echo $this->getEventName() . "AND set to reoccur on $recurrence_days<br/>";		

			$date = getdate($recurrence_end);
			$recurMon = $date['mon'];
			$recurDate = $date['mday'];

			/****************************************************
			  get the days of the week that the event recurrs on 
			 (recurrence_days), and plug them into an array.
			  (M|W|F) --> [0]=>M, [1]=>W, [2]=>F
			 ****************************************************/

			$days = explode("|", $recurrence_days);
			//dump($recurrence_days);
			//Arrays::clearEmptyValues($days);
			//dump($days);
			/**************************************************
			   Build backwards associative arrays to translate
			   between the days from the recurrence_days list and the
			   results of the wday array for the purposes of figuring out
			   what days to start building the weeks on.
			*****************************************************/

			$associative_days = array (0 => "U", 1 => "M", 2 => "T", 3 => "W", 4 => "R", 5 => "F", 6 => "S");
			$rassociative_days = array_flip($associative_days);

			/***********************************************
			 Get the detailed information regarding the actual
			 starting day of the recurrence.	
			 ***********************************************/

			$date = getdate($recurrence_start);
			$startMonth = $date['mon'];
			$startYear = $date['year'];
			$startDate = $date['mday'];
			$startDay = $date['wday'];

			foreach ($days as $day) {
				/**************************************
				We need to figure out during which days of the week,
				during the original week for which an event is scheduled
				to start, that that event occurs.
				 ***************************************/

				//echo "checking $day<br/>";
				// generate a timestamp for each of those occurrences during the original week
				if ($rassociative_days[$day] < $startDay) {

					//echo "<br/>the recurrence day $day comes before ". $associative_days[$startDay]."<br/>";
					/****************************************************
					If the day occurs before the start of the recurrence, 
					then we need to subtract the difference between the two
					days in order to build the correct starting day from which
					to iterate over the weeks.
					 ****************************************************/

					$current = mktime(0, 0, 0, $startMonth, $startDate - ($startDay - $rassociative_days[$day]), $startYear);

				} else
					if ($rassociative_days[$day] > $startDay) {

						/****************************************************
						If the day occurs after the start of the recurrence, 
						then we need to add the difference between the two
						days in order to build the correct starting day from which
						to iterate over the weeks.
						 ****************************************************/

						$current = mktime(0, 0, 0, $startMonth, $startDate + ($rassociative_days[$day] - $startDay), $startYear);
					} else {

						/****************************************************
						If the day is the same, then just keep its timestamp
						 ****************************************************/

						$current = $recurrence_start;
					}

				/***************************************************************
				Now we need to start at the specified point in the original week, 
				and increment the timestamp in question until it either falls in the
				currently visible week period, or is after either the end of the visible
				period or the end of the recurrence period.
				 ****************************************************************/

				while (($current < $end_date) && ($current < $recurrence_end)) {

					/***************************
					If the incremented timestamp falls
					in the currently visible period, then 
					add the date to the list of dates to
					display, and then break out of the loop.
					 ***************************/


					if (($current >= $recurrence_start) && ($current >= $start_date) && ($current <= $end_date)) {
						
						$date = getdate($current);

						array_push($dates, $current);
						break;
					}

					/****************************************
					 If we haven't hit the current week or the
					 end of the recurrence, jump the timestamp
					 by a week * the number of the value of $recurrence
					_weeks, so that we skip the number of weeks necessary
					for values > 1
					 ***************************************/

					$current += (604800 * $recurrence_weeks);

				}

			}

		}

		$date_compares = array ();
		$exception_compares = array ();

		foreach ($dates as $occurrence) {
			
			if ((date("I", $occurrence) != 1))
				$occurrence += 3600;

			$date_compares[$occurrence] = date("m j Y", $occurrence);
			//print_r($date_compares);
		}

		foreach ($exceptions as $occurrence) {
			
			if ((date("I", $occurrence) != 1))
				$occurrence += 3600;

			$exception_compares[$occurrence] = date("m j Y", $occurrence);
		}

		$this->dates = array_keys(array_diff($date_compares, $exception_compares));

	}

	// ---------------------------
	//   FUNCTION: GET CONFLICTS
	/**This function determines whether events are
	 * being scheduled at the same time as existing
	 * events. It is used both when adding events or
	 * requesting meetings. 
	 */

	public function getConflicts() {
		$db = new DB();

		if ($this->event_id)
			$except = "AND tblEvent.event_id != ".$this->event_id;

		$other_events = $db->getArray("SELECT tblEvent.event_id FROM tblPersonEvent, tblEvent WHERE tblPersonEvent.person_id = ".$this->person_id." $except AND tblEvent.event_id = tblPersonEvent.event_id ORDER BY tblEvent.start_datetime");
		$conflicts = array ();
		if ($other_events) {

			foreach ($other_events as $event_id) {
				$event = new Event($event_id);
				$name = $event->getEventName();
				if ($date = $this->Conflicts($this, $event)) {
					return "$name on $date";
				}

			}
		}
		return false;
	}

	public function Conflicts($event1, $event2) {

		$Ndays = array(0=>"U", 1=>"M", 2=>"T", 3=>"W", 4=>"R", 5=>"F", 6=>"S");
		
		if ($event1->getRecurrenceStart() != NULL)
			$recur1 = explode("|",$event1->getRecurrenceDays());
		else
			$recur1 = array($Ndays[date("w",$event1->getStartDatetime())]);
			
		if ($event2->getRecurrenceStart() != NULL)
			$recur2 = explode("|",$event2->getRecurrenceDays());
		else
			$recur2 = array($Ndays[date("w",$event2->getStartDatetime())]);
		
			
		$n1 = $event1->getEventName();
		$n2 = $event2->getEventName();


		if (!array_intersect($recur1, $recur2))
			return false;

		
		$first_start = $event1->getStartDateTime();
		$first_end = $event1->getEndDateTime();
		$second_start = $event2->getStartDateTime();
		$second_end = $event2->getEndDateTime();

		$first_start_info = getdate($first_start);
		$second_start_info = getdate($second_start);
		$first_end_info = getdate($first_end);
		$second_end_info = getdate($second_end);

		if ($second_start_info["hours"] > 12) {
			$realStartInfo["hours"] = $second_start_info["hours"] - 12;
			$startAMPM = "PM";
		}
		elseif ($second_start_info["hours"] == 0) {
			$realStartInfo["hours"] = 12;
			$startAMPM = "AM";
		} else {
			$realStartInfo["hours"] = $second_start_info["hours"];
			$startAMPM = "AM";
		}

		if ($second_end_info["hours"] > 12) {
			$realEndInfo["hours"] = $second_end_info["hours"] - 12;
			$endAMPM = "PM";
		}
		elseif ($second_end_info["hours"] == 0) {
			$realEndInfo["hours"] = 12;
			$endAMPM = "AM";
		} else {
			$realEndInfo["hours"] = $second_end_info["hours"];
			$endAMPM = "AM";
		}
		for ($i = 0; $i <= 9; $i ++) {
			if ($second_start_info["minutes"] == $i) {
				$realStartInfo["minutes"] = "0".$i;
				break;
			} else {
				$realStartInfo["minutes"] = $second_start_info["minutes"];
			}
		}

		for ($i = 0; $i <= 9; $i ++) {
			if ($second_end_info["minutes"] == $i) {
				$realEndInfo["minutes"] = "0".$i;
				break;
			} else {
				$realEndInfo["minutes"] = $second_end_info["minutes"];
			}
		}

		if ($first_start == $second_start) {
			
			return $second_start_info["weekday"].", ".$second_start_info["month"]." ".$second_start_info["mday"]." at ".$realStartInfo["hours"].":".$realStartInfo["minutes"]." $startAMPM - ".$realEndInfo["hours"].":".$realEndInfo["minutes"]." $endAMPM";
		}

		if (($first_start > $second_start && $first_start < $second_end) || ($second_start > $first_start && $second_start < $first_end)) {
			return $second_start_info["weekday"].", ".$second_start_info["month"]." ".$second_start_info["mday"]." at ".$realStartInfo["hours"].":".$realStartInfo["minutes"]."$startAMPM - ".$realEndInfo["hours"].":".$realEndInfo["minutes"].$endAMPM;
		}
		

		if($event1->getRecurrenceStart() == NULL && $event2->getRecurrenceStart() == NULL)
				return false;
			
		if ((($event1->getStartMil() >= $event2->getStartMil() && $event1->getStartMil() < $event2->getEndMil()) || ($event2->getStartMil() >= $event1->getStartMil() && $event2->getStartMil() < $event1->getEndMil()))) {
			//echo "events happen during the same time of day, so we have to make sure they never overlap<br/>";

			$start = max($first_start, $second_start);
			$first_recurrence = ($event1->getRecurrenceStart() == NULL) ? ($event1->getEndDateTime()) : $event1->getRecurrenceEnd();
			$second_recurrence = ($event2->getRecurrenceStart() == NULL) ? ($event2->getEndDateTime()) : $event2->getRecurrenceEnd();

			if ($event1->getRecurrenceStart() != NULL) {
				if ($first_recurrence < $second_start)
					return false;

			}

			if ($event2->getRecurrenceStart() != NULL) {
				if ($second_recurrence < $first_start)
					return false;

			}

			$end = max(max($first_end, $second_end), min($first_recurrence, $second_recurrence));

			//reset the start to one second after midnight at the start of whatever week
			$sday = getdate($start);
			$real_start = ($start - (($sday["wday"] * 86400) + ($sday["hours"] * 3600) + ($sday["minutes"] * 60)));

			//reset the end to one second before midnight at the end of whatever week
			$eday = getdate($end);
			$real_end = $end + (((6 - $eday["wday"]) * 86400) + ((23 - $eday["hours"]) * 3600) + ((60 - $sday["minutes"]) * 60)) - 1;
			//echo "RS: $real_start || RE: $real_end<br/>";
			for ($week_start = $real_start; $week_start < $real_end; $week_start += 604800) {
				$start_period = $week_start;
				$end_period = $week_start +604799;

				$info_start = getdate($start_period);
				$info_end = getdate($end_period);

				$ev1 = $event1->getDates($start_period, $end_period);
				$ev2 = $event2->getDates($start_period, $end_period);

				$ev1days = array ();
				$ev2days = array ();
				
				foreach ($ev1 as $key => $occur) {
				
					$info = getdate($occur);
					$ev1days[] = $info["wday"];
				}

				foreach ($ev2 as $key => $occur) {

					$info = getdate($occur);
					$ev2days[] = $info["wday"];
				}
			
				$conflicts = array_intersect($ev1days, $ev2days);
				if (!empty ($conflicts)) {
					//echo "these events both happen on ".dump($conflicts)."<br/>";
					$conflicted_info_start = getdate($second_start);
					$conflicted_info_end = getdate($second_end);

					$time = $start_period + (array_pop($conflicts) * 86400) + $sday["hours"] * 3600 + $sday["minutes"] * 60;

					$conflict_time = getdate($time);
					if ($second_start_info["hours"] > 12) {
						$second_start_info["hours"] = $second_start_info["hours"] - 12;
						$startAMPM = "PM";
					}
					elseif ($second_start_info["hours"] == 0) {
						$second_start_info["hours"] = 12;
						$startAMPM = "AM";
					} else {
						$startAMPM = "AM";
					}

					if ($second_end_info["hours"] > 12) {
						$second_end_info["hours"] = $second_end_info["hours"] - 12;
						$endAMPM = "PM";
					}
					elseif ($second_end_info["hours"] == 0) {
						$second_end_info["hours"] = 12;
						$endAMPM = "AM";
					} else {
						$endAMPM = "AM";
					}
					return $conflict_time["weekday"].", ".$conflict_time["month"]." ".$conflict_time["mday"]." at ".$realStartInfo["hours"].":".$realStartInfo["minutes"]." $startAMPM - ".$realEndInfo["hours"].":".$realEndInfo["minutes"]." $endAMPM";

				}

			}
			return false;

		} else
			return false; //the events happen at different times, so we don't have to worry about them overlapping;

	}

	// ---------------------------
	//   FUNCTION: GET START HOUR
	public function getStartHour() {
		$info = getdate($this->start_datetime);
		return $info["hours"];
	}

	// ---------------------------
	//   FUNCTION: GET END HOUR
	public function getEndHour() {
		$info = getdate($this->end_datetime);
		return $info["hours"];
	}

	// ---------------------------
	//   FUNCTION: GET END QUARTER
	public function getEndQuarter() {
		//echo "event ends at  ". $this->end_datetime. "<br/>";
		$info = getdate($this->end_datetime);
		$min = $info["minutes"];
		//echo "event ends at $min <br/>";

		if (($min >= 0) && ($min <= 15))
			return 1;
		else
			if (($min > 15) && ($min <= 30))
				return 2;
			else
				if (($min > 30) && ($min <= 45))
					return 3;
				else
					if (($min > 45) && ($min <= 59))
						return 4;

	}

	// ---------------------------
	//   FUNCTION: GET START QUARTER
	public function getStartQuarter() {
		//echo "event starts at  ". $this->start_datetime. "<br/>";
		$info = getdate($this->start_datetime);
		$min = $info["minutes"];
		//echo "event starts at $min <br/>";

		if (($min >= 0) && ($min < 15))
			return 1;
		else
			if (($min >= 15) && ($min < 30))
				return 2;
			else
				if (($min >= 30) && ($min < 45))
					return 3;
				else
					if (($min >= 45) && ($min < 59))
						return 4;

	}

	// ---------------------------
	//   FUNCTION: GET READABLE DATE

	private function getReadable($timestamp) {
		$date = getdate($timestamp);
		return $date["hours"].":".$date["minutes"];
	}

	public function getShortName() {
		if (strlen($this->event_name) > 11)
			return substr($this->event_name, 0, 8)."...";
		else
			return $this->event_name;

	}

	public function toprint() {
		echo $this->event_name." ".$this->event_description." ".$this->getStart()." ".$this->getEnd();
		echo "<br/>".$this->recurrence_start."<br/>".$this->recurrence_end."<br/>".$this->recurrence_days."<br/>".$this->recurrence_weeks."<br/>";
	}

	public function buildMil($hh, $mm) {
		if ($hh < 10)
			$hh = "0$hh";

		if ($mm < 10)
			$mm = "0$mm";

		return $hh.$mm;
	}

	function getStartMil() {
		$start = $this->start_datetime;
		$sinfo = getdate($start);

		return $this->buildMil($sinfo["hours"], $sinfo["minutes"]);
	}

	function getEndMil() {
		$end = $this->end_datetime;
		$einfo = getdate($end);

		return $this->buildMil($einfo["hours"], $einfo["minutes"]);

	}

} // END OF EVENT CLASS
?>