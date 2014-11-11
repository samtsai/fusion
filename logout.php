<?php	
	//$pageTitle = "Log Out";
	require_once ("includes/common.php");
	include ("includes/header.php");
	
	 //  Log out by first unsetting each session var
    foreach($_SESSION as $key => $value) { unset($_SESSION[$key]); }
	
	//  Now destroy the session
	session_destroy();
	
	Redirect :: gotoPage("index.php");
	
	//echo "<p>You have been logged out of the system.</p>";
	include ("includes/footer.php");
?>
