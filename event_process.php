<?php
if ($_POST["Submit"] === "Add Class") {

	$events = array ();
	$db = new DB();

	if($_POST["Semester"]{0} == "F")
	{
		$semester_start = FALL_SEMESTER_START;
		$semester_end = FALL_SEMESTER_END;
		$mini_start = FALL_MINI_START;
		$mini_end = FALL_MINI_END;
	}
	elseif ($_POST["Semester"]{0} == "S")
	{
		$semester_start = SPRING_SEMESTER_START;
		$semester_end = SPRING_SEMESTER_END;
		$mini_start = SPRING_MINI_START;
		$mini_end = SPRING_MINI_END;
		
	}
	$semester = $_POST["Semester"];
	$semester_start_info = getdate($semester_start);


	$semester_start_day = $semester_start_info["wday"];
	$convert_day = array (
		"U" => 0,
		"M" => 1,
		"T" => 2,
		"W" => 3,
		"R" => 4,
		"F" => 5,
		"S" => 6
	);

	/**Determines if the course form is being submitted/displayed */
	if (isset ($_POST["course"]) && $_POST["course"] != "" && !empty ($_POST["course"])) {
		foreach ($_POST["course"] as $name) {
			if ($name != "") {
				if (strstr($name, "-") !== false)
					$name = str_replace("-", "", $name);

				$selectedCourse = explode(" ", $name);
				$courseNumber = $selectedCourse[0];
				$courseSection = $selectedCourse[1];

				//HARDCODED until fix form	
				
				$qry = "SELECT tblCourse.course_name, tblSection.start_datetime, tblSection.end_datetime, tblSection.days FROM tblCourse, tblSection WHERE tblSection.course_number = '$courseNumber' AND tblSection.section = '$courseSection' AND tblSection.semester = '$semester' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester";
				$db->query($qry);
				if ($row = $db->getRow("SELECT tblCourse.course_name, tblSection.start_datetime, tblSection.end_datetime, tblSection.days FROM tblCourse, tblSection WHERE tblSection.course_number = '$courseNumber' AND tblSection.section = '$courseSection' AND tblSection.semester = '$semester' AND tblSection.course_number = tblCourse.course_number AND tblSection.semester = tblCourse.semester")) {

					$description = $row["course_name"];
					$start_time = $row["start_datetime"];
					$end_time = $row["end_datetime"];
					$days = $row["days"];
					//var_dump($row);

					$start_minutes = (substr($start_time, 0, 2) * 3600) + (substr($start_time, 2) * 60);
					$end_minutes = (substr($end_time, 0, 2) * 3600) + (substr($end_time, 2) * 60);

					$chars = str_split($days);
					$recur_days = implode("|", $chars);

					if (strlen($courseSection) == 2) {
						$char = substr($courseSection, -1, 1);
						if ($courseSection{1} == "2" || $courseSection{1} == "4") {
							$basis = $mini_start;
						} else
							$basis = $semester_start;
					} else
						$basis = $semester_start;

					foreach ($chars as $day) {
						if ($convert_day[$day] >= $semester_start_day) {
							$base = ($basis + (($convert_day[$day] - $semester_start_day) * 86400));
							break;
						}
					}

					if (!$base) {
						$base = ($basis - (($semester_start_day - $convert_day[$chars[0]]) * 86400)) + 604800;
					}

					$start_datetime = $base + $start_minutes;
					$end_datetime = $base + $end_minutes;

					$recurrence_start = $start_datetime;

					if (strlen($courseSection) == 2) {
						$char = substr($courseSection, -1, 1);
						if ($courseSection{1} == "1" || $courseSection{1} == "3") {
							$recurrence_end = $mini_end;
						} else
							$recurrence_end = $semester_end;
					} else
						$recurrence_end = $semester_end;

					$recurrence_weeks = 1;
					$recurrence_days = $recur_days;

					//$description = "$description: Course: $courseNumber Section: $courseSection, start: $start_datetime, end: $end_datetime, RS: $recurrence_start, RE: $recurrence_end, RW: $recurrence_weeks, Days: $recurrence_days :: $days :: $qry";

					$event = new Event();
					$event->setEventName($name);
					$event->setEventDescription($description);
					$event->setEventType(1);
					$event->setPersonId($_SESSION["person_id"]);
					$event->setStartDatetime($start_datetime);
					$event->setEndDatetime($end_datetime);
					$event->setRecurrenceStart($recurrence_start);
					$event->setRecurrenceEnd($recurrence_end);
					$event->setRecurrenceWeeks($recurrence_weeks);
					$event->setRecurrenceDays($recurrence_days);

					if (!$event->addEvent())
						$conflict_msg = $event->getConflictsMessage();

				}
			}
		}

		CommonFunctions :: resetFormObj(4);
	}

}
/**Determines if add event/edit event form is being submitted */
elseif ($_POST["Submit"] == "Add Event" || $_POST["Submit"] == "Edit Event") {

	if ($_POST["Submit"] == "Add Event") {
		$formType = "Add Event";
	} else {
		$formType = "Edit Event";
	}

	$eventData = Arrays :: getPostVars("");

	$eventRecurrence = array (
		"frequency",
		"weekdays",
		"weekly",
		"until",
		"untilDate"
	);
	$eventExceptions = array (
		"exceptions",
		"exceptionDate",
		"exceptionsListBox",
		"exCount"
	);

	/** If the recurrence box is not checked then unset everything in recurrence**/
	if (!isset ($eventData["recurrence"])) {

		$eventStuff = array_merge($eventRecurrence, $eventExceptions);

		foreach ($eventStuff as $value) {
			unset ($eventData["$value"]);
		}

		$formObj->setFormValue("recurrence", "");
		$formObj->setFormError("frequency", "");
		$formObj->setFormError("UntilDate", "");
		$formObj->unsetFormValidationGroup("UntilDate");

	} else { /** recurrence is checked */

		foreach ($eventRecurrence as $value) {
			if (!isset ($eventData["$value"])) {
				$eventData["$value"] = "";
			}
		}
		if (isset ($eventData["frequency"]) && $eventData["frequency"] == "ed") {
			unset ($eventData["weekdays"]);
		}
		/** If the Exceptions checkbox is not checked then do not validate "exceptionDate"*/
		if (!isset ($eventData["exceptions"])) {
			foreach ($eventExceptions as $value) {
				unset ($eventData["$value"]);
			}
		}

		if (isset ($recurrenceEnd))
			$formObj->setFormValue("recurrenceEnd", $recurrenceEnd);
		else {
			
			if(mktime() > FALL_SEMESTER_END)
			{
				$sem_end = SPRING_SEMESTER_END;
				$min_end = SPRING_MINI_END;	
			}
			else
			{
				$sem_end = FALL_SEMESTER_END;
				$min_end = FALL_MINI_END;			
			}
		
			if ($eventData["until"] === "SEMESTER")
				$recurrence_end = $sem_end;
			elseif ($eventData["until"] === "MINI") $recurrence_end = $min_end;
			elseif ($eventData["until"] === "FOREVER") $recurrence_end = FOREVER;
			else {
				$recurrence_end = $eventData["untilDate"]; //need to work on the custom end-date
				$pieces = explode("/", $recurrence_end);
				$untilMonth = $pieces[0];
				$untilDay = $pieces[1];
				$untilYear = $pieces[2];
				$recurrence_end = mktime(0, 0, 0, $untilMonth, $untilDay, $untilYear);
			}

			$formObj->setFormValue("recurrenceEnd", $recurrence_end);
		}

		$timeAndDate = array (
			"userDate",
			"recurrenceEnd"
		);
		$formObj->setFormValidationGroup("UntilDate", array (
			"futureDate" => $timeAndDate
		));
	}

	/** Validate form */
	if ($formObj->validateForm($eventData)) {

		$fromDate = $eventData["userDate"];
		$toDate = $fromDate;

		$pieces = explode("/", $fromDate);
		$fromMonth = $pieces[0];
		$fromDay = $pieces[1];
		$fromYear = $pieces[2];

		$fromHH = $eventData["fromTime"][0];
		$fromMM = $eventData["fromTime"][1];
		$fromAMPM = $eventData["fromTime"][2];

		if ($fromHH == 12) {
			if ($fromAMPM == "AM")
				$fromHH = 0;
		}
		elseif ($fromAMPM == "PM") {
			$fromHH += 12;
		}
		$pieces = explode("/", $toDate);
		$toMonth = $pieces[0];
		$toDay = $pieces[1];
		$toYear = $pieces[2];

		$toHH = $eventData["toTime"][0];
		$toMM = $eventData["toTime"][1];
		$toAMPM = $eventData["toTime"][2];

		if ($toHH == 12) {
			if ($toAMPM == "AM")
				$toHH = 0;
		}
		elseif ($toAMPM == "PM" && $toHH != 12) {
			$toHH += 12;
		}

		$fromTime = mktime($fromHH, $fromMM, 0, $fromMonth, $fromDay, $fromYear);

		if ($toHH < $fromHH) {
			if ($toHH == 0 && $toMM == 0)
				$split_event = false;
			else
				$split_event = true;
			$toHH = 23;
			$toMM = 59;
		}

		$toTime = mktime($toHH, $toMM, 0, $toMonth, $toDay, $toYear);

		$name = $eventData["title"];
		$desc = $eventData["desc"];
		$type = $eventData["category"];

		if ($_POST["Submit"] == "Add Event")
			$event = new Event();
		if ($_POST["Submit"] == "Edit Event") {
			$id = $eventData["eventID"];
			$event = new Event($id);
		}

		$event->setEventName($name);
		$event->setEventDescription($desc);
		$event->setEventType($type);
		$event->setPersonId($_SESSION["person_id"]);
		$event->setStartDatetime($fromTime);
		$event->setEndDatetime($toTime);

		/** Determines is recurrence is checked */
		if (!empty ($eventData["recurrence"])) {
			$recurrence_start = $fromTime;

			if(mktime() > FALL_SEMESTER_END)
			{
				$sem_end = SPRING_SEMESTER_END;
				$min_end = SPRING_MINI_END;	
			}
			else
			{
				$sem_end = FALL_SEMESTER_END;
				$min_end = FALL_MINI_END;			
			}
		
			if ($eventData["until"] === "SEMESTER")
				$recurrence_end = $sem_end;
			elseif ($eventData["until"] === "MINI") $recurrence_end = $min_end;
			elseif ($eventData["until"] === "FOREVER") $recurrence_end = FOREVER;
			else {
				$recurrence_end = $eventData["untilDate"]; //need to work on the custom end-date
				$pieces = explode("/", $recurrence_end);
				$untilMonth = $pieces[0];
				$untilDay = $pieces[1];
				$untilYear = $pieces[2];
				$recurrence_end = mktime(0, 0, 0, $untilMonth, $untilDay, $untilYear);
			}

			$recurrence_weeks = $eventData["weekly"];

			if ($eventData["frequency"] == "ed") {
				$selectedDays = array (
					"U",
					"M",
					"T",
					"W",
					"R",
					"F",
					"S"
				);
				$recurrence_days = implode("|", $selectedDays);
			} else {
				$recurrence_days = implode("|", $eventData["weekdays"]);
			}

			$count = $eventData["exCount"];
			for ($num = 0; $num < $count; $num++) {
				$exArray[] = $eventData["exception$num"];
			}

			if (!empty ($exArray)) {
				$exception_dates = $exArray;
				$exceptions_array = array ();

				foreach ($exception_dates as $date) {

					$pieces = explode("/", $date);
					$fromMonth = $pieces[0];
					$fromDay = $pieces[1];
					$fromYear = $pieces[2];

					$exception_date = mktime(0, 0, 0, $fromMonth, $fromDay, $fromYear);
					array_push($exceptions_array, $exception_date);
				}
			}

			$event->setRecurrenceStart($recurrence_start);
			$event->setRecurrenceEnd($recurrence_end);
			$event->setRecurrenceWeeks($recurrence_weeks);
			$event->setRecurrenceDays($recurrence_days);
			$event->setExceptions($exceptions_array);
		}

		if ($_POST["Submit"] == "Add Event") {
			if (!$event->addEvent()) {
				$conflict_msg = $event->getConflictsMessage();
			}
		}
		elseif ($_POST["Submit"] == "Edit Event") {
			if (!$event->updateDB()) {
				$conflict_msg = $event->getConflictsMessage();
			}
		}

		if ($split_event) {
			$description_id = $event->getDescriptionId();

			$event = new Event();
			$event->setEventType($type);
			$event->setPersonId($_SESSION["person_id"]);
			$event->setDescriptionId($description_id);
			$fromTime = mktime(0, 0, 0, $fromMonth, $fromDay +1, $fromYear);

			$event->setStartDatetime($fromTime);

			$toHH = $eventData["toTime"][0];
			$toMM = $eventData["toTime"][1];
			$toAMPM = $eventData["toTime"][2];

			if ($toHH == 12) {
				if ($toAMPM == "AM")
					$toHH = 0;
			}
			elseif ($toAMPM == "PM" && $toHH != 12) {
				$toHH += 12;
			}

			$toTime = mktime($toHH, $toMM, 0, $fromMonth, $fromDay +1, $fromYear);

			$event->setEndDatetime($toTime);
			if ($recurrence_start) {
				$recurrence_start = $recurrence_start +86400;
				$recurrence_end = $recurrence_end +86400;
				if ($exceptions_array) {
					foreach ($exceptions_array as $exception) {
						$exception = $exception +86400;
						$exception_temp[] = $exception;
					}
					$exceptions_array = $exception_temp;
				}

				$days_array = explode("|", $recurrence_days);
				foreach ($days_array as $day) {
					if ($day == "U")
						$day = "M";
					if ($day == "M")
						$day = "T";
					if ($day == "T")
						$day = "W";
					if ($day == "W")
						$day = "R";
					if ($day == "R")
						$day = "F";
					if ($day == "F")
						$day = "S";
					if ($day == "S")
						$day = "U";
					$newDaysArray[] = $day;
				}
				$recurrence_days = implode("|", $newDaysArray);

				$event->setRecurrenceStart($recurrence_start);
				$event->setRecurrenceEnd($recurrence_end);
				$event->setRecurrenceWeeks($recurrence_weeks);
				$event->setRecurrenceDays($recurrence_days);
				$event->setExceptions($exceptions_array);
			} /** end of if recurrence*/
			if ($event->addEvent() == false) {
				$conflict_msg = $event->getConflictsMessage();
			}
		}

		if (empty ($conflict_msg)) {
			/** Reset to Add Event and reset the FormObject and the Post */
			$formType = "Add Event";
			$formObj = CommonFunctions :: resetFormObj(4);
			$formObj->setFormValue("category", "$type");
		}

	} else {
		/** Show Errors */

	}
}
?>








