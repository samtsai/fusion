<?php

require_once ("includes/common.php");

$test[] = "";
$test[] = "";

var_dump($test);
var_dump(empty($test));
var_dump(Arrays::isEmptyArray($test));

//phpinfo();
/*
require_once ("includes/common.php");

$pageTitle = "Testing the Design";
include ("includes/header.php");

$formObj = CommonFunctions :: setFormObj(10);


// Set the Session FormObj
$_SESSION["formObj"] = $formObj;       
    
  include ("includes/footer.php");

*/
?>