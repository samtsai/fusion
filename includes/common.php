<?php

include_once ("lib/arrays.php");
include_once ("lib/CommonFunctions.php");
include_once ("lib/DB.php");
include_once ("lib/FormManager.php");
include_once ("lib/FunctionLib.php");
include_once ("lib/HTMLHelper.php");
include_once ("lib/redirect.php");
include_once ("lib/validate.php");

	
include_once ("entities/Person.php");
include_once ("entities/Event.php");
include_once ("entities/Schedule.php");
include_once ("entities/Group.php");
include_once ("entities/Meeting.php");

include_once ("helpers/login.php");
include_once ("helpers/phpDump.php");

CommonFunctions::sessionCleanUp();
//semester start
define("SPRING_SEMESTER_START", 1168837200);

//semester end
define("SPRING_SEMESTER_END", 1179287999);

//mini one end
define("SPRING_MINI_END", 1173502799);

//mini two start
define("SPRING_MINI_START", 1174276800);

//semester_start
define("FALL_SEMESTER_START", 1159416000);

//semester end
define("FALL_SEMESTER_END", 1166590799);

//mini one end
define("FALL_MINI_END", 1161143999);

//mini two start
define("FALL_MINI_START", 1161576000);

//forever... kinda
define("FOREVER", 2147482800);


session_cache_limiter(nocache);
session_start();

?>