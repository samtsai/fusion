<?php

require_once ("includes/common.php");

$formObj = CommonFunctions::setFormObj(7);	

$pageTitle = "Events Tester";
include ("includes/header.php");


$curForm = "event";

			$event = new Event();
			$event->setEventName("test event" . date("g i", mktime()));
			$event->setEventDescription("HELP");
			$event->setEventType(4);
			$event->setPersonId($_SESSION["person_id"]);
			$event->setStartDatetime(1144062000);  //4/3/06 7 AM
			$event->setEndDatetime(1144069200); // 4/3/06 9AM
			
			$event->setRecurrenceStart(1144062000); //4/3/06 7 AM
			$event->setRecurrenceEnd(SEMESTER_END); //SEMESTER_END 5/5/06
			$event->setRecurrenceWeeks(1);
			$event->setRecurrenceDays("M|W|F");
			
			//exceptions on the 7th
			$exceptions = array(1144386000);
			$event->setExceptions($exceptions);
			//echo "here is the exception information <br/>";
			//print_r ($event->getExceptions());

			//$info  = $event->getDates(1143954401, 1144415201);
			$event->addEvent();
			//print_r ($info);
	
include ("includes/views/event_form.php");

?>