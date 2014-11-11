<?php
/*
 * Created on Apr 27, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 require_once ("includes/common.php");
 $pageTitle = "About Us";
 include ("includes/header.php");
 
 echo "<div class=\"block\">";
 
 echo "<img src=\"images/aboutus.jpg\">
Dear User,";
 
 echo "<div class=\"paragraph\">";
 echo "In the spring semester of 2006, six Information Systems majors (Natalie Freed, Christina Kung, David Park, Trey Sisson, Matt Snider, and Sam Tsai) decided to fix the problem of group scheduling for their software development project. Group members often waste time figuring out when everyone is available to meet.
The more people there are in a group, the more difficult the problem becomes. Throughout the course of this project, we believe that we have developed a system that will benefit anyone who is effected by group efficiency.<p>-Team Fusion";
 echo "</div>";
 
 echo "<p>Special thanks to:<br>" .
 		"-<a href=\"http://www.backbase.com\" target=\"_blank\">www.backbase.com</a> for the Create Profile and Loading graphics<br>" .
 		"-<a href=\"http://www.apple.com/macosx/\" target=\"_blank\">www.apple.com</a> for the panel and side widget graphics and other lovely design influences<br>" .
 		"-<a href=\"http://www.dynarch.com/\" target=\"_blank\">www.dynarch.com</a> for the calendar widget";
 
 echo "</div>";
 
 include ("includes/footer.php");
?>
