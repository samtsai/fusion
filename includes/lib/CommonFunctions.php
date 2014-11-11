<?php
/**
 * Static function library for some of the functionality that is called throughout the system.  
 * 
 * Property of Team Fusion 
 * 
 * These functions are relatively simplistic in nature, 
 * and vary little in how they are used across different segments of the site.
 * 
 * @author		Matt Snider, Sam Tsai
 * @version		1.0
 * @since		1.0
 */
class CommonFunctions {

	/**
	 *	Sets a new or existing form object to the session array.
	 *	This is used to make sure that this page's form object
	 *	refers to the correct page. 
	 *	If no session is set, a new FormManager object is created and set to the session.
	 *	If a session is already set, check if the ID of the form matches,
	 *	if it matches than we are ok, simply return the session object,
	 *	Otherwise reset the FormManager object for the session and return the session object.
	 *	
	 *	@param	formID	the integer that is the form ID number
	 *	@return			the array that holds the session object
	 */
	public static function setFormObj($formID) {

		// set the Form Object	
		if (!isset ($_SESSION["formObj"])) {
			$_SESSION["formObj"] = new FormManager($formID);
		}
		// so Form Object session is set
		// Check if it's ID matches the current form
		// If it doesn't reset the Form Object	
		elseif ($_SESSION["formObj"]->getFormID() != $formID) {
			$_SESSION["formObj"] = new FormManager($formID);
		}

		return $_SESSION["formObj"];
	}
	
	/**
	 *	Completely resets the FormManager object.
	 *	
	 *	@param	formID	the integer that is the form ID number
	 *	@return			the array that holds the session object
	 */	
	public static function resetFormObj($formID) {
		
		unset($_POST);
		$_SESSION["formObj"] = new FormManager($formID);
		
		return $_SESSION["formObj"];	
	}

	/**
	 *	Sets a form object to the session array.
	 *	
	 *	@param	formObj	the object that is the FormManager object
	 *	@return			void
	 */
	public static function setFormObjSession($formObj) {
		$_SESSION["formObj"] = $formObj;
	}
	
	/**
	 *	Checks if the current agent matches the agent that was passed.
	 *	
	 *	@param	agent	the string that is the agent name
	 *	@return			the boolean that is TRUE if we are in this agent, FALSE otherwise
	 */	
	public static function inAgent($agent) {
		$notAgent = strpos($_SERVER["HTTP_USER_AGENT"], $agent) === false;
		return !$notAgent;
	}

	/**
	 *	Attaches a calendar widget to any part of the page.
	 *	
	 *	@param	field	the string that is the id of the input
	 *	@param	img		the image that is the image button of the input
	 *	@return			the javascript that prints out a calendar widget
	 */
	public static function attachCalendarWidget($field, $img) {
		return "<script type=\"text/javascript\">
			Calendar.setup({
			inputField : \"$field\",
			ifFormat : \"%m/%d/%Y\",
			button : \"$img\",
			align : \"Tl\",
			singleClick : false
		});
		</script>";
	}
	

	/**
	 *	Prints out the script to open up a calendar on date change.
	 * 	As a bug, for some reson the $location value cannot have an underscore in it
	 * 	or the calendar code will fail to find the parent into which it is to be inserted
	 *	@param	location		the string that is the id of the location
	 *	@param	onDateChange	the string that holds the javascript action
	 *	@return					the javascript that prints out a calendar widget
	 */	
	public static function standaloneCalendar($location, $onDateChange, $date = false)
	{
		if(!$date)
			$date = mktime();
			
		$date = $date*1000;
		
		echo "<script type=\"text/javascript\">
		function dateChanged(calendar) {
			if (calendar.dateClicked) {
			// OK, a date was clicked, redirect to /yyyy/mm/dd/index.php
				var y = calendar.date.getFullYear();
				var m = calendar.date.getMonth(); // integer, 0..11
				var d = calendar.date.getDate(); // integer, 1..31
				$onDateChange;
			}
		};
		Calendar.setup(
		{
		flat : '$location', // ID of the parent element
		flatCallback : dateChanged, // our callback function
		date: new Date($date) //set the visible date
		}
		); 
		
		</script>";		

	}
	
	/**
	 *	Prints out the actual page.
	 *	
	 *	@param	output	the string that is the page output
	 *	@param	formObj	the object that holds the FormManager object
	 *	@return			void
	 */	
	public static function printPage($output, $formObj="") {
		
		echo $output;
		
		if ($formObj) {
			CommonFunctions::setFormObjSession($formObj);
		}  
	}
	
	public static function showLoginMessage($pageName) {
		$_SESSION["goto"] = $pageName.".php";	
		Redirect::gotoPage("index.php?msg=1");	
	}
	
	public static function sessionCleanUp() {
		/** unset known sessions that should only exist for one page 
		 *  I know I'm probably going to upset matt with this unnecessary session
		 *  apologies...
		 * */
		
		$pageName = basename($_SERVER["PHP_SELF"]);
		if ($pageName != "index.php")
			unset($_SESSION["goto"]);	
	}
	
	
	public static function showErrorMessage($msgText)
	{
	
		return "<div id = \"editMSG\"><div class=\"roundcont-f\">
								   <div class=\"roundtop-f\">
									 <img src=\"images/tl-f.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\"display: none\" />
								   </div>
								   
									<span class=\"bigTitle\">$msgText</span>
							  		
								   <div class=\"roundbottom-f\">
									 <img src=\"images/bl-f.gif\" 
									 width=\"15\" height=\"15\" class=\"corner\" 
									 style=\display: none\" />
								   </div>
								</div></div>";
	}
}
?>

