<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php 
	
	   if($_SESSION["person_id"]){
	   		$db = new DB();
	   		$condition = "WHERE person_id = ".$_SESSION["person_id"];
            $data = $db->getOneRecord("tblPerson", $condition);
            $logEmail = $data["email"];
	   		$time = time();
		    $cookie_string = $logEmail; 
		    setcookie ("cookie_data",$cookie_string, $time+3600*24*100);
	   }
	   elseif ($_COOKIE["cookie_data"]){
	   $cookie_string = $_COOKIE["cookie_data"];
	   $time = time();
	   setcookie ("cookie_data",$cookie_string, $time+3600*24*100);
	   $_POST["loginEmail"]=$cookie_string;
	   }
	?>
	
	
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="description" content="Description" />
    <meta name="keywords" content="fusion" />
    
    <title>Fusion :: <?php echo $pageTitle; ?></title>
    <link rel="shortcut icon" href="images/favicon.gif" type="image/x-icon" />
    
    <link rel="stylesheet" href="css/styles.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/forms.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/tables.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/schedule.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/calendar.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/autocomplete.css" type="text/css" media="all" />
	<link rel="stylesheet" href="css/aqua/theme.css" type="text/css" media="all" />
	
	<script type="text/javascript" src="js/ajax.js"></script>	
    <script type="text/javascript" src="js/fusion.js"></script>
    <script type="text/javascript" src="js/styles.js"></script>
    <script type="text/javascript" src="js/fusion.js"></script>
    <script type="text/javascript" src="js/dialog.js"></script>
    <script type="text/javascript" src="js/calendar.js"></script>
    <script type="text/javascript" src="js/calendar-setup.js"></script>
    <script type="text/javascript" src="js/lang/calendar-en.js"></script>
  
</head>

<body <?php if ($pageTitle == 'Home') { echo "onload=\"document.getElementById('loginEmail').focus();\""; }?> onClick='hideCurrentPopup();'>

<div id="wrapper">
<div id="container">
<div id="header">
	<div id="headerBanner"><a href="index.php"><img src="images/fusion1.jpg" height="74" alt="Home" /></a></div>
</div>
<div id="navigation">
<div id="spacer"></div>
<?php

if ($pageTitle === "Home") {
	echo "<span>Welcome to Fusion</span>";
}
if ($pageTitle === "Log Out" || $pageTitle === "Please Log In") {
	echo "<span><a href=\"index.php\">Log In</a></span>";
}
elseif ($pageTitle === "Set Profile") {
	echo "<span>Step 1 of 4 :: Set Profile</span>";
}
elseif ($pageTitle === "Activation") {
	echo "<span>Step 2 of 4 :: Activation</span>";
}
elseif ($pageTitle === "Set Weekly Schedule") {
	echo "<span>Step 3 of 4 :: Set Weekly Schedule</span>";
}
elseif ($pageTitle === "Set Groups") {
	echo "<span>Step 4 of 4 :: Set Groups</span>";
}
elseif (!isset($_SESSION["status"]) && $pageTitle === "Privacy Policy") {
	echo "<span>Privacy Policy</span>";
}
elseif (!isset($_SESSION["status"]) && $pageTitle === "About Us") {
	echo "<span>About Team Fusion</span>";
}

if($_SESSION["status"] == 2 && $pageTitle != "Log Out") {
	echo "<div id=\"buttons\">";
	
	if ($pageTitle === "Request Meeting")
		echo "<a href=\"request.php\"><img width=\"110\" height=\"30\" src=\"images/requestOn.gif\" class=\"buttonLink\"></a>";
	else
		echo "<a href=\"request.php\"><img width=\"110\" height=\"30\" src=\"images/requestOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/requestHover.gif'\" onmouseout=\"this.src='images/requestOff.gif'\"></a>";
	
	/*if ($pageTitle === "Search for Meeting")
		echo "<img width=\"110\" height=\"30\" src=\"images/searchOn.gif\">";	
	else 
		echo "<a href=\"search.php\"><img width=\"110\" height=\"30\" src=\"images/searchOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/searchHover.gif'\" onmouseout=\"this.src='images/searchOff.gif'\"></a>";*/
		
	if ($pageTitle === "My Schedule")
		echo "<a href=\"schedule.php\"><img width=\"110\" height=\"30\" src=\"images/scheduleOn.gif\" class=\"buttonLink\"></a>";
	else
		echo"<a href=\"schedule.php\"><img width=\"110\" height=\"30\" src=\"images/scheduleOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/scheduleHover.gif'\" onmouseout=\"this.src='images/scheduleOff.gif'\"></a>";
		
	if ($pageTitle === "My Profile")	
		echo "<a href=\"profile.php\"><img width=\"110\" height=\"30\" src=\"images/profileOn.gif\" class=\"buttonLink\"></a>";
	else
		echo"<a href=\"profile.php\"><img width=\"110\" height=\"30\" src=\"images/profileOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/profileHover.gif'\" onmouseout=\"this.src='images/profileOff.gif'\"></a>";
		
	if ($pageTitle === "My Groups")
	echo "<a href=\"groups.php\"><img width=\"110\" height=\"30\" src=\"images/groupsOn.gif\" class=\"buttonLink\"></a>";
	else
		echo "<a href=\"groups.php\"><img width=\"110\" height=\"30\" src=\"images/groupsOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/groupsHover.gif'\" onmouseout=\"this.src='images/groupsOff.gif'\"></a>";
	
	if ($pageTitle === "Help")
	echo "<a href=\"help.php\"><img width=\"110\" height=\"30\" src=\"images/helpOn.gif\" class=\"buttonLink\"></a>";
	else
		echo "<a href=\"help.php\"><img width=\"110\" height=\"30\" src=\"images/helpOff.gif\" class=\"buttonLink\" onmouseover=\"this.src='images/helpHover.gif'\" onmouseout=\"this.src='images/helpOff.gif'\"></a>";
	
	echo "</div>";
	
	echo "<div id=\"spacer\"></div>";
	
	//show andrew ID if logged in
	echo "<div id='rightAlign'>".$_SESSION["andrew_id"]." | <a href=\"logout.php\">Log Out</a></div>";
}
?>
	
</div>

<div id="contents">
<div id="main">