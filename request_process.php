<?php

if (isset ($_POST["Submit"]) && ($_POST["Submit"] == "Confirm")) {
	$requestData = Arrays :: getPostVars("");
	
	$title = $requestData["title"];

	$desc = $requestData["desc"];
	/** response must give users enough time */
	$response = $requestData["responseHH"];
	$count = $_POST["count"];
	$ids = $requestData["personID"];
	$creator = $_SESSION["person_id"];

	$ids = explode("|", $ids);

	foreach ($ids as $key => $value) {
		if (!$value)
			unset ($ids[$key]);
	}

	$meeting = new Meeting();
	$meeting->setName($title);
	$meeting->setDescription($desc);
	$meeting->setResponseTime($response);
	$meeting->setAttendees($ids);
	$meeting->setCreator($creator);
	
	
	for ($num = 1; $num <= $count; $num ++) {
	
		$string = $_POST["hiddenInput$num"];
		$tempMeetingArray = explode("%", $string);

		$number = 0;

		$keyChange = array (0 => "userDate", 1 => "fromHH", 2 => "fromMM", 3 => "fromAMPM", 4 => "durationHH", 5 => "durationMM", 6 => "recurrence", 7 => "frequency", 8 => "weeklyRecurrence", 9 => "weekly", 10 => "weekdays", 11 => "until", 12 => "exceptionDate");
		
		foreach ($tempMeetingArray as $key => $value) {			
			$new[$keyChange[$number]] = $value;
			$number ++;
		}

		$requestData = $new;	
		$requestData["title"] = $title;
		$requestData["desc"] = $desc;

		$requestRecurrence = array ("frequency", "weekdays", "weekly", "until", "untilDate");
		$requestExceptions = array ("exceptions", "exceptionDate", "exceptionsListBox", "exCount");
		// If the recurrence box is not checked then unset everything in recurrence
		//echo $_POST["recurrence"];
		if ($requestData["recurrence"] == "false" || !isset ($requestData["recurrence"])) {
			//echo "recurrence is false";
			$requestStuff = array_merge($requestRecurrence, $requestExceptions);

			foreach ($requestStuff as $value) {
				//echo "unsetting $value";
				unset ($requestData["$value"]);
			}

		} else { // recurrence is checked
			foreach ($requestRecurrence as $value) {
				if (!isset ($requestData["$value"])) {
					$requestData["$value"] = "";
				}
			}
			if (isset ($requestData["frequency"]) && $requestData["frequency"] == "ed") {
				unset ($requestData["weekdays"]);
			}
			// If the Exceptions checkbox is not checked then do not validate "exceptionDate"
			if (!isset ($requestData["exceptions"])) {
				foreach ($requestExceptions as $value) {
					unset ($requestData["$value"]);
				}
			}
		}
		/*
		$durationHH = $requestData["durationHH"];
		$durationMM = $requestData["durationMM"];
		$duration = $durationHH+$durationMM;
		if ($duration == 0) 
			$formObj->setFormError("duration","Must have a duration greater than 0 minutes");
		*/	
		
		if ($formObj->validateForm($requestData)) {
			
			$fromDate = $requestData["userDate"];
			$toDate = $fromDate;

			$pieces = explode("/", $fromDate);
			$fromMonth = $pieces[0];
			$fromDay = $pieces[1];
			$fromYear = $pieces[2];

			$fromHH = $requestData["fromHH"];
			$fromMM = $requestData["fromMM"];
			$fromAMPM = $requestData["fromAMPM"];

			if ($fromHH == 12) {
				if ($fromAMPM == "AM")
					$fromHH = 0;
			}
			elseif ($fromAMPM == "PM") {
				$fromHH += 12;
			}
			$fromTime = mktime($fromHH, $fromMM, 0, $fromMonth, $fromDay, $fromYear);

			$durationHH = $requestData["durationHH"];
			$durationMM = $requestData["durationMM"];

			$durationHHSec = $durationHH * 60 * 60;
			$durationMMSec = $durationMM * 60;
			$durationTotalSec = $durationHHSec + $durationMMSec;

			$toTime = $fromTime + $durationTotalSec;

			if ($requestData["recurrence"] == "true") {
				//echo "STRING TRUE";
				$recurrence_start = $fromTime;

				if ($requestData["until"] === "SEMESTER")
					$recurrence_end = SEMESTER_END;
				else
					if ($requestData["until"] === "MINI")
						$recurrence_end = MINI_END;
					else
						if ($requestData["until"] === "FOREVER")
							$recurrence_end = FOREVER;
						else {
							$recurrence_end = $_POST["untilDate"]; //need to work on the custom end-date
							$pieces = explode("/", $recurrence_end);
							$untilMonth = $pieces[0];
							$untilDay = $pieces[1];
							$untilYear = $pieces[2];
							$recurrence_end = mktime(0, 0, 0, $untilMonth, $untilDay, $untilYear);
						}
										
				$recurrence_weeks = $requestData["weekly"];

				if (!$recurrence_weeks)
					$recurrence_weeks = 1;

				if ($requestData["frequency"] == "ed") {
					$selectedDays = array ("U", "M", "T", "W", "R", "F", "S");
					$recurrence_days = implode("|", $selectedDays);
				} else {
					$recurrence_days = $requestData["weekdays"];
				}

				$exCount = $_POST['exCount'];
				for ($exNum = 0; $exNum < $exCount; $exNum ++) {
					$exArray[] = $_POST["exception$exNum"];
				}
	
				//if (!empty ($_POST["exceptionDate"])) {
				if (!empty ($exArray)) {

					//$exception_dates = $_POST["exceptionDate"];

					$exception_dates = $exArray;

					//$exception_dates = explode("|", $exception_dates);

					$exceptions_array = array ();

					foreach ($exception_dates as $date) {

						$pieces = explode("/", $date);
						$fromMonth = $pieces[0];
						$fromDay = $pieces[1];
						$fromYear = $pieces[2];

						$exception_date = mktime(0, 0, 0, $fromMonth, $fromDay, $fromYear);
						array_push($exceptions_array, $exception_date);
					} //end of foreach
				} //end of if post exception date
			} //end of if post recurrence
			//echo "<br/>addMeetingTime:  num: ".$num."  fromtime: ".$fromTime." totime: ".$toTime." excArray: ".$exceptions_array." recur_start: ".$recurrence_start." recur_end: ".$recurrence_end." recur_weeks: ".$recurrence_weeks." recur_days: ".$recurrence_days."<br/>";		
			$meeting->addMeetingTime($num, $fromTime, $toTime, $exceptions_array, $recurrence_start, $recurrence_end, $recurrence_weeks, $recurrence_days, null);
			
		} //end of if valid
		else{
		}
	}// end of for loop
	//echo "<br/>NUM: $num COUNT: $count"; 
	$errors = $formObj->getFormErrors();
	if (empty ($errors)) {
		if($conflicts = $meeting->requestMeeting()){
			echo "<div class = \"error\">This meeting could not be scheduled because one of the times conflicted with $conflicts</div><br/>";
			echo "<div><nobr><a href = 'javascript:showRequest();'>:: Request Another Meeting</nobr></a><br/><br/></div>";
		}
		else{
			echo "<div>The meeting has been requested.</div><br/>";
			echo "<a href = 'javascript:showRequest();'>:: Request Another Meeting</a><script> setVisibleWeek($fromTime); </script>";
		}
	} else {
		include("includes/views/request_form.php");
	}	
} else {
	include ("includes/views/request_form.php");
}
?>

