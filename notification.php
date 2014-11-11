<?php
/*
 * Created on Mar 17, 2006
 */
require_once ("includes/common.php");
$pageTitle = "Activation";
include ("includes/header.php");
$email = $_GET["email"]; 
 
 echo "<p>Your email confirmation and activation code has been sent to $email and should arrive shortly.</p>
 	  <p>When it does, please click the activation link, at which point you will be directed
 	  to complete the creation of your personal schedule.</p> Thank you for your interest, and welcome to Fusion!";
 	  
 
include ("includes/footer.php");
 
?>
