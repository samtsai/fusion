<?php
/*
 * Created on Apr 4, 2006
 * This is a stub file I'm making in order to test the actual course_form
 */
 
 	require_once ("includes/common.php");

 	$pageTitle = "Add Courses";
 	include("includes/header.php");
 
 	$formObj = CommonFunctions :: setFormObj(7);
 	
 	$curForm = "course";
	include ("event_process.php");
 	
	include("includes/views/course_form.php");
 
 	include("includes/footer.php");
 
 
?>
