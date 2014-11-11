<?php
echo HTMLHelper :: startWidget("confirmTop");

$buttonName = "Submit";
$num = 0;
if ($formType == "reject") {
	echo $formObj->startForm("meetingConfirm");

	echo $formObj->startDiv("meetingConfirmTitle");
	echo $formObj->startDiv("meetingConfirmDesc");
	if ($tempTimes) {
		echo "<div>";
		echo "<b>Please accept or reject the times for the following meeting.</b><br/><br/>Note: You are unable to edit events while accepting / rejecting meetings.<br/><br/>";
		echo "<span class=\"requested\">Title: </span> $name<br/>";
		if ($desc != "") {
			echo "<span class=\"requested\">Description: </span> $desc<br/>";
		}
		echo "<span class=\"requested\">Creator: </span> $creator_fn $creator_ln<br/>";
		echo "<div class=\"requested\">Invited:</div><div class=\"indent\">";

		foreach ($person_array as $id) {

			$condition = "WHERE person_id = '$id'";
			$invited = $db->getOneRecord("tblPerson", $condition);
			$invited_fn = $invited["first_name"];
			$invited_ln = $invited["last_name"];

			echo "<br/>-$invited_fn $invited_ln";

		}

		echo "</div>";

		echo "</div>";
	}
	echo $formObj->endDiv();
	echo $formObj->endDiv();
	$countTimes = 0;

	foreach ($tempTimes as $priority => $information) {
		$event_id = $event_array[$num];
		$db = new DB();
		$query = "SELECT response from tblPersonEvent WHERE person_id = $person_id AND event_id = $event_id";
		$response = $db->getScalar($query);

		if ($response != "1" && $response != "0") {

			$start_datetime = $information["start_datetime"];
			$end_datetime = $information["end_datetime"];

			$new_start = getdate($start_datetime);
			$new_end = getdate($end_datetime);

			$weekday = $new_start["weekday"];
			$month = $new_start["mon"];
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

			echo $formObj->startDiv('tempTime'.$priority);
			echo "<div class=\"upchunk\">";
			echo "<span class=\"requested\">Date: </span> $weekday<br/>";
			echo "<span class=\"requested\">&nbsp;</span>$month/$day/$year<br/>";
			echo "<span class=\"requested\">Start Time: </span> $startTime<br/>";
			echo "<span class=\"requested\">End Time: </span> $endTime<br/>";

			//echo $weekday." ".$month." ".$day.", ".$year."  ".$startTime."  to  ".$endTime."<br/>";

			if ($information["recurrence_start"] != NULL) {
				$weeks = $information["recurrence_weeks"];
				$days = $information["recurrence_days"];
				$days = explode("|", $days);
				$dayString = "";
				foreach ($days as $key => $dayValue) {
					$dayString = "-Every ";
					if ($dayValue == "U")
						$dayString .= "Sunday ";
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

				echo "<div class=\"recurrenceLabel\">Recurrence:</div>";
				echo "<div class=\"indent\">";

				echo $dayString."<br/>";

				if ($weeks == 2) {
					echo "-Every other week<br/>";
				}
				elseif ($weeks == 3) {
					echo "-Every 3 weeks<br/>";
				}
				elseif ($weeks == 4) {
					echo "-Every 4 weeks<br/>";
				}

				$until_datetime = $information["recurrence_end"];
				echo "-Until ".date("n/j/Y", $until_datetime);

				echo "</div>";

				$exceptions = $information["exceptions"];
				if ($exceptions) {
					echo "<div class=\"recurrenceLabel\">Exceptions:</div>";
					echo "<div class=\"indent\">";
					foreach ($exceptions as $exception) {
						echo "-".date("n/j/Y", $exception)."<br/>";
					}
					echo "</div>";
				}
			}
			echo $formObj->startDiv($event_array[$num]."response");
			echo $formObj->insertButton("accept", "accept", "Accept", "javascript:rejectMeeting($person_id, $event_array[$num],'accept',$priority)");
			echo $formObj->insertButton("reject", "reject", "Reject", "javascript:rejectMeeting($person_id, $event_array[$num],'reject',$priority)");
			echo $formObj->endDiv();
			echo "</div>";
			echo $formObj->endDiv();
			$countTimes ++;
		} /**end of if response !=1 */
		$num ++;
	
	} /** end of foreach*/
	if ($countTimes == 0) {
		echo "<br/>All times have been accepted/rejected.";
	}
} /** end of if form type reject*/

if ($formType == "confirm") {
	echo $formObj->startForm("meetingConfirm");
	echo $formObj->startDiv("confirm");
	echo "You have accepted all times for this meeting. Refer to your <a href = 'http://rook.hss.cmu.edu/~team04s06/schedule.php'>Schedule </a> for any additional meeting details."; 
	echo $formObj->endDiv();

} /** end of if form type confirm */

echo $formObj->endForm($buttonName, 1, 0);
echo HTMLHelper :: endWidget();
?>

