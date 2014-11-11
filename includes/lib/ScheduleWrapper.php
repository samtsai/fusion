<?php


/*
 * Created on Mar 10, 2006
 *
 * This file created to server as a functional wrapper for the Schedule class.
 * Intended to provide access to Schedule functions through AJAX
 */

include_once ("../common.php");

$selected_date = stripslashes(trim($_GET["start"]));
$ids = stripslashes(trim($_GET["ids"]));
$switch = stripslashes(trim($_GET["getMembers"]));
$person_id = stripslashes(trim($_GET["pid"]));

if ($switch) {
	$group = new Group($switch);
	$db = new DB();
	$db->updateRecord("UPDATE tblPerson SET group_preference = $switch WHERE person_id = $person_id");
	$members = $group->getPersonArray();
	foreach ($members as $member => $conf) {
		if ($conf) {
			$response .= "$member|";
		}
	}
	echo $response;
} else if (isset($_GET["ListMembers"])) {
		
		$group_id = stripslashes(trim($_GET["ListMembers"]));	
		$schedule = new Schedule($group_id);
		echo $schedule->groupDropdown();

} else { 
	
	if (isset ($_GET["gid"])) {
		$group_id = stripslashes(trim($_GET["gid"]));
	} else
		$group_id = false;
		//build the new schedule item
		$schedule = new Schedule();
		//determine whose ID's the calendar is currently referencing'
	if ($group_id)
		$schedule->setGroup($group_id);
	
	$array = explode("|", $ids);
	$schedule->setPeople($array);
		//set the visible week to the newly determined time
	
	if (strpos($selected_date, ",") !== false) {
		$date = explode(",", $selected_date);
		$schedule->setVisibleWeek($date[1], $date[2], $date[0]);
	} else
		$schedule->setVisibleWeekStarting($selected_date);
		//build the calendar output and return it to the handler
	//echo $schedule->Display2();
	echo $schedule->Display();
}
?>









