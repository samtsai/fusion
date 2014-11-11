<?php
/**
 * Manages all form elements including validation on the page.
 * 
 * Property of Team Fusion 
 * 
 * This nifty class is the backbone to any pages that have any sort
 * of form functionality. Through Form Manager the user can output
 * every kind of <input> element (ie textbox, radio, checkbox, etc.)
 * The Form Manager also handles validation and gives each element
 * a validation type. It can process the POST variables and spit
 * back any errors found in the form.
 * 
 * @author		Sam Tsai
 * @version		1.0
 * @since		1.0
 */
class FormManager {

	private $formID; /** ID to keep track of what form this is */
	private $formData; /** Array storing all the form data retrieved from POST or prepopulated */
	private $formErrors; /** Array storing all the form errors */
	private $formName; /** Name of the form */
	private $formValidationTypes; /** Array storing each element's validation type */
	private $formValidationGroups; /** Array that groups together elements for special validation */
	private $formElementNames; /** Array that stores all the elements' names in an array */
	private $formErrorSummary; /** Array that stores the error summary on the page */

	/**
	 *  FormID will keep track of what page you are on,
	 * Each page with a form, will have a specific static ID
	 * Page Name		ID
	 * Index.php		1
	 * Profile.php		2
	 * Groups.php		3
	 * Schedule.php		4
	 * Search.php		5
	 * Request.php		6
	 * Course.php		7
	 * Etc.
	 */

	/************************
	FormManager Constructor
	************************/ 
	function __construct($id = 0) {
		$this->formID = $id;
		$this->formData = array ();
		$this->formErrors = array ();
		$this->formName = "sampleForm";
		$this->formValidationTypes = array ();
		$this->formValidationGroups = array ();
		$this->formElementNames = array ();
		$this->formErrorSummary = array ();
	}


	/************************
	Basic Getter Methods
		
		In general use singular name to set just one item and plural to set the whole array.
		ie setFormValidationGroup($name,$array) vs. setFormValidationGroups($array)	
	************************/ 
	public function getFormID() {
		return $this->formID;
	}

	public function getFormData() {
		return $this->formData;
	}

	public function getFormElementNames() {
		return array_keys($this->formElementNames);
	}

	public function getFormElementType($name) {
		
		$fen = $this->formElementNames;
		if (isset($fen[$name])) {
			return $fen[$name];
		}
		else {
			return FALSE;	
		}
	}
	
	/****
	 * Consider setting formErrors on the ID and not the name?????
	 */
	public function getFormError($name) {
		
		$error = "";

		if (ereg("[0-9]+$", $name)) {
			$num = range(0,9);
			$realName = str_replace($num,"",$name);
			$length = strlen($realName);
			$idNum = substr($name,$length);

			$error = $this->formErrors[$realName][$idNum-1];
		}
		else {
			if (array_key_exists($name, $this->formErrors)) {
				$error = $this->formErrors[$name];
			}
		}

		return $error;							
	}

	public function getFormErrors() {
		return $this->formErrors;
	}

	public function getFormErrorSummary() {
		return $this->formErrorSummary;
	}

	public function getFormName() {
		return $this->formName;
	}

	public function getFormValidationGroups() {
		return $this->formValidationGroups;
	}

	public function getFormValidationGroup($name) {
		return $this->formValidationGroups[$name];
	}

	public function getFormValidationType($name) {
		return $this->formValidationTypes[$name];
	}

	public function getFormValidationTypes() {
		return $this->formValidationTypes;
	}

	public function getFormValue($name) {
		
		$name = $this->getRealName($name);
		$pieces = explode("|", $name);
			
		if (count($pieces) > 1) {
			$key = $pieces[1];
			$name = $pieces[0];
			$valueArray = $this->formData[$name];
			$value = $valueArray[$key];
		} else {
			$value = $this->formData[$name];
		}
		
		return $value;
	}

	public function getPostValue($name) {
		
		$name = $this->getRealName($name);
		$pieces = explode("|", $name);
		$postValue = "";
		
		if (count($pieces) > 1) {
			$postValue = $_POST[$pieces[0]][$pieces[1]];
		}
		else {
			if (isset($_POST[$name]))
				$postValue = $_POST[$name];				
		}
		
		return $postValue;
	}
	
	public function getStoredValue($name) {
		
		$storedValue = $this->getPostValue($name);

		if (!isset($storedValue) || $storedValue == "") {

			$storedValue = $this->getFormValue($name);
						
			if (!isset($storedValue)) {
				$storedValue = "";	
			}
		}
				
		return $storedValue;
	}
	
	/************************
	Basic Setter Methods
	*************************/
	public function setFormID($int) {
		$this->formID = $int;
	}

	public function setFormData($array) {
		$this->formData = $array;
	}

	public function setFormElementName($name,$type="unknown") {

		$name = $this->getRealName($name);

		$fen = $this->getFormElementNames();
	
		if (!in_array($name, $fen)) {
			$this->formElementNames[$name] = $type;
		}
	}

	public function setFormElementNames($array) {
		$this->formElementNames = $array;
	}

	public function setFormError($name, $value, $custom = 0) {

		// future: store this data in the database 
		$customErrorMessages = array ("firstName" => "Please enter a valid first name", "lastName" => "Please enter a valid last name", "address1" => "Please enter a valid address line 1", "address2" => "Please enter a valid address line 2", "zip" => "Please enter a valid zip code", "city" => "Please enter a valid city name", "phone" => "Please enter a valid 10-digit phone number", "password1" => "Please do not leave the password field blank", "password2" => "Please do not leave the password field blank", "email" => "Please enter a valid email address", "email1" => "Please enter a valid email address", "state" => "Please select a valid state name", "expdate" => "Please enter a valid non-expired expiration date", "student" => "Please enter a valid name", "tutor" => "Please enter a valid name", "course" => "Please enter a valid course number", "location" => "Please enter a valid location", "name" => "Please enter a valid name", "comments" => "Please enter a description", "count" => "Please enter a valid count", "major" => "Please enter a valid major", "qpa" => "Please enter a valid QPA", "secretAnswer" => "Please answer one of the secret questions", "aim" => "Please enter a valid AIM screenname", "loginEmail" => "Please enter a valid email address", "groupName" => "Please enter a valid group name", "UntilDate" => "Recurrence end date must be after the until date.");

		$pieces = explode("|", $name);
		if (empty ($value)) {
			if (count($pieces) > 1) {
				unset($this->formErrors[$pieces[0]][$pieces[1]]);
				if (count($this->formErrors[$pieces[0]]) == 0) {
					unset($this->formErrors[$pieces[0]]);	
				}
			} else {			
				unset($this->formErrors[$name]);
			}
			return;
		}
		elseif ($custom == 1) {
			if (!empty ($customErrorMessages[$name]))
				$value = $customErrorMessages[$name];
			if (!empty ($customErrorMessage[$pieces[0]])) 
				$value = $customErrorMessages[$pieces[0]];
		}

		if (count($pieces) > 1)
			$this->formErrors[$pieces[0]][$pieces[1]] = $value;
		else
			$this->formErrors[$name] = $value;

	}

	public function setFormErrors($array) {
		$this->formErrors = $array;
	}

	public function setFormErrorSummary($array) {
		$this->formErrorSummary = $array;
	}

	public function setFormValidationGroup($name, $array) {
		if (strcasecmp("all", $array) != 0) {
			$this->formValidationGroups[$name] = $array;
		} else {
			$fen = $this->getFormElementNames();
			$this->formValidationGroups["thisForm"] = array ("none" => $fen);
		}
	}

	public function setFormValidationGroups($array) {
		$this->formValidationGroups = $array;
	}

	public function setFormValidationType($name, $valType) {
		$this->formValidationTypes[$name] = $valType;
	}

	public function setFormValidationTypes($array) {
		$this->formValidationTypes = $array;
	}

	public function setFormValue($name, $value) {

		$name = $this->getRealName($name);
		$pieces = explode("|", $name);

		if (empty ($value)) {
			$value = "";
		}
		
		if (count($pieces) > 1) {
			$this->formData[$pieces[0]][$pieces[1]] = $value;
		} else {
			$this->formData[$name] = $value;
		}

	}

	public function unsetFormValidationGroup($name) {
		unset($this->formValidationGroup[$name]);	
	}
	
	/**
	 * Tables and Other Misc Form Stuff
	 */
	public function insertTitle($title, $displayType = 1, $class = "") {
		return HTMLHelper :: insertTitle($title, $displayType, $class);
	}

	public function insertDescription($desc, $class = "") {
		return HTMLHelper :: insertDescription($desc, $class);
	}

	public function insertSubmitButton($name = "Submit", $id = "", $value = "Submit", $displayType = 1, $align = "left", $class = "submitForm", $onclick = "") {
		return HTMLHelper :: insertSubmitButton($name, $id, $value, $displayType, $align, $class, $onclick);
	}

	public function insertButton($name = "Submit", $id = "", $value = "Submit", $onclick = "", $submit = 0) {
		return HTMLHelper :: insertButton($name, $id, $value, $onclick, $submit);
	}

	public function insertSpacer() {
		return HTMLHelper :: insertSpacer();
	}

	public function startFieldset($title = "", $id, $collapse = 0, $nestedTable = 0, $class = "") {
		return HTMLHelper :: startFieldset($title, $id, $collapse, $nestedTable, $class);
	}

	public function endFieldset($collapse = 0, $nestedTable = 0) {
		return HTMLHelper :: endFieldset($collapse, $nestedTable);
	}

	public function startDiv($id = "", $title = "", $class = "") {
		return HTMLHelper :: startDiv($id, $title, $class);
	}

	public function endDiv() {
		return HTMLHelper :: endDiv();
	}
	
	public function insertLine($style = "") {
		return HTMLHelper :: insertLine($style);	
	}

	public function insertSmallTitle($name) {
		return HTMLHelper :: insertSmallTitle($name);
	}
	/**
	 *	Starts the Form and includes an opening formBox <div>. 
	 *	This method should be used to start any form and you
	 *	can choose what display type to start with (table or div)
	 *	Default is DIV
	 *	Note:	Use multipart/form-data for inputting files
	 *	
	 *	@param  id			the string that is the id of the form (DEFAULT: "sampleForm")
	 *	@param	action		the string that is the action of the form (DEFAULT: "")
	 *	@param	displayType	the integer that decides if it is a <div> or a <table> (DEFAULT: 1 = div)
	 *	@param	method		the string that is the method type (DEFAULT: POST)
	 *	@param	enctype 	the string that is the encypte type (DEFAULT: application/x-www-form-urlencoded) 
	 *	@return				the string that holds all of the start form stuff
	 */
	public function startForm($id = "sampleForm", $action = "", $displayType = 1, $method = "post", $enctype = "application/x-www-form-urlencoded") {

		$this->formName = $id;

		if (empty ($action)) {
			$action = $_SERVER["PHP_SELF"];
		}

		$output = "<form action=\"$action\" method=\"$method\" id=\"$id\" enctype=\"$enctype\">";

		if ($displayType) {
			$output .= "<div class=\"formBox\">";
		} else {
			$output .= "<table cellpadding=\"0\" cellspacing=\"0\">";
		}

		return $output;
	}

	/**
	 *	Ends the Form with and optional submit button and closes the <div> or <table>. 
	 *	This method should be used to end any form and you
	 *	must choose what display type to end with (table or div)
	 *	Default is DIV
	 *	
	 *	@param	name		the string that is the name of the submit button (DEFAULT: "Submit")
	 *	@param  displayType the integer that is the display type (DEFAULT: 1 = div)
	 *	@param  submit	 	the integer that decides to show or not show the submit button (DEFAULT: 1 = show)
	 *	@param	cancel		the integer to show or not show a cancel button (DEFAULT: 0 = don't show)
	 *	@param  reset 		the integer to show or not show a reset button (DEFAULT: 0 = don't show) 
	 *	@return				the string that holds all of the end form stuff
	 */
	public function endForm($name = "Submit", $displayType = 1, $submit = 1, $cancel = 0, $reset = 0) {

		if ($submit == 1) {
			if ($displayType == 1) {
				$output = "<div class=\"submitForm\">";
			} else {
				// colspan should be 0
				$output = "<tr><td>&nbsp;</td><td colspan=\"5\">";
			}

			$output .= $this->insertSubmitButton("Submit", "", $name);

			if ($cancel == 1) {
				$output .= "<input type=\"submit\" name=\"Submit\" class=\"inputSubmit\" value=\"Cancel\" />";
			}
			if ($reset == 1) {
				$output .= "<input type=\"reset\" name=\"Submit\" class=\"inputSubmit\" value=\"Reset\" />";
			}

			if ($displayType == 1) {
				$output .= "</div>";
			} else {
				$output .= "</td></tr>";
			}
		}

		if ($displayType == 1) {
			$output .= "</div>";
		} else {
			$output .= "</table>";
		}

		$output .= "</form>";

		return $output;
	}

	/**
	 * Returns a variable name without the brackets.
	 * ie getRealName("people[]") returns: people
	 * 
	 * @param	name	the string that is to be stripped of its brackets
	 * @return			the string without the brackets
	 * 
	 */
	private function getRealName($name) {
		$array = Arrays :: stripBracket($name);

		if ($array) {
			$realName = $array[1];
		} else {
			$realName = $name;
		}

		return $realName;
	}

	/**
	 *	Looks to see if a checkbox is checked or not. 
	 *	This method will return a 1 for yes and 0 for no
	 *	and is mostly used outside of the class. 
	 *	
	 *	@param  name 	the string that is the name of the element
	 *	@param	value	the string that is the value of the element
	 *	@return		 	the integer result (1 for checked, 0 for not checked) 
	 */
	public function checkCheckbox($name, $value = "") {

		//$storedValue = $this->getFormValue($name);
		$storedValue = $this->getStoredValue($name);

		if (!empty ($value)) {

			if ($storedValue == $value) {
				return 1;
			} else {
				return 0;
			}
		} else {
			if (!empty ($storedValue)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	/**
	 *	Looks to see if the value has special instructions.
	 *	Special instructions might include specifying a default value
	 *	or inserting a Date Widget. 
	 *	These are appended with a "|" to separate the two actions
	 *	
	 *	@param  name	the string that is the name of the element
	 *	@param	value	the value being checked for special instructions
	 *	@return		 	the output of the special instructions if any
	 */
	private function checkSpecial($name, $value) {

		if (empty ($value)) {
			return $value;
		}

		$pieces = explode("|", $value);
		$dateWidget = substr($pieces[1], 0, 10);
		if (isset ($pieces[1])) {
			if ($pieces[1] == "default") {				
				$storedValue = $this->getStoredValue($name);
				
				if (empty($storedValue)) {
					$this->setFormValue($name, $pieces[0]);	
				}
				
				/*
				else {
					$this->setFormValue($name, $storedValue);	
				}*/				
			}			
			elseif ($dateWidget == "dateWidget") {
				$widgetName = substr($pieces[1], 10);
				$widget = $this->insertDateWidget($widgetName);
				$pieces[1] = $widget;
				return $pieces;
			}
			
			$value = $pieces[0];
			
			return $value;
		} else {
			return $value;
		}

	}

	/**
	 *	Looks to see if the value matches an already stored value. 
	 *	This method will return an output according to the type
	 *	and types include: checkbox, dropdown, etc. 
	 *	
	 *	@param name 	the string that is the name of the element
	 *	@param value	the string that is the value of the element
	 *	@param type		the string that is the type of the element
	 *	@param output	the integer that specifies the type of output 
	 *	@return			the string or boolean result
	 */
	private function isSelected($name, $value, $type = "", $output = 1) {
		
		$storedValue = $this->getStoredValue($name);

		$found = FALSE;
		
		if (is_array($storedValue)) {
			if (in_array($value,$storedValue)) {
				$found = TRUE;	
			}	
		}
		elseif ($storedValue === $value) {
			$found = TRUE;
		}

		if ($output == 1 && $found) {
			switch ($type) {
				// checkbox
				case 1 :
					return "checked=\"checked\"";
				// dropdown
				case 2 :
					return "selected=\"selected\"";
				default :
					}
		}
		elseif ($found) {
			return TRUE;
		} else {
			return "";
		}
	}
	
	/**
	 * Checks if a name belongs to a validation group.
	 * Simple function that searches the all the Form Validation Groups
	 * to see if this particular name is in any of them.
	 * 
	 * @param	name	the string that is the name of the element
	 * @return			the boolean will either be true or false
	 */
	private function isInGroup($name) {

		$name = $this->getRealName($name);

		$fvg = $this->getFormValidationGroups();

		if (Arrays :: in_multi_array($name, $fvg))
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Checks if a variable is a name of a group.
	 * Simple function that looks in all the Form Validation Groups
	 * to see if this variable is one of the group names.
	 * 
	 * @param	name	the string that is the name of the element
	 * @return			the boolean will either be true or false
	 */
	private function isGroup($name) {

		$name = $this->getRealName($name);

		$fvg = $this->getFormValidationGroups();

		if (in_array($name, $fvg)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Returns the name of the group this variable belongs to.
	 * Searches the form validation groups to see what group this variable is a part of.
	 * 
	 * @param	name	the string that is the name of the element
	 * @return			the string will be the name of the group
	 */
	private function getGroupName($name) {

		$fvg = $this->getFormValidationGroups();

		$keyPath = Arrays :: multi_array_search($name, $fvg);
		$groupName = $keyPath[0];

		return $groupName;
	}

	/**
	 *	Looks to see if a value passed signifies read only. 
	 *	This method will return a 1 for yes and 0 for no
	 *	
	 *	@param  readOnly	the integer value specifying whether or not the element is read only
	 *	@return				the string result (1 for read only, 0 for not read only) 
	 */
	private function isReadOnly($readOnly) {
		if ($readOnly == 1) {
			return "disabled=\"disabled\"";
		} else {
			return "";
		}
	}

	/**
	 *	Looks to see the element is a required element. 
	 *	This method takes in a name and checks if it is set to required
	 *	by looking at the validation type for that element.
	 *	Optional elements have "opt_" appended to the front of the validation.
	 *
	 *	@param  name	the string that is the name of the element
	 *	@return			the boolean result 
	 */
	private function isRequired($name) {

		$valType = $this->getFormValidationType($name);
		// check for the optional tag in the front of valType
		$opt = substr($valType, 0, 3);

		if (strcasecmp("opt", $opt) == 0) {
			return FALSE;
		} else {
			return TRUE;
		}

	}

	/**
	 *	Starts the element with an appropriate tag. 
	 *	This method should be used to start all form elements
	 *	because several elements have similar beginnings.
	 *	In general elements start off with an open <div> or <table>
	 *	and a <span class="label"> to label the element
	 *	
	 *	@param	id			the string that is the id of the element
	 *	@param	name		the string that is the name of the element
	 *	@param	label		the string that is the label of the element
	 *	@param  formType	the integer that decides what type of form display to use
	 *	@param	displayType	the integer that decides what type of display to use
	 *	@param	class		the string that is the class of the element
	 *	@param	style		the string that is the style of the element
	 *	@return				the string that represents the start of the element
	 */
	private function startElement($id, $name, $label, $formType = 1, $displayType = 1, $class = "", $style = "") {

		$name = $this->getRealName($name);

		if ($this->isRequired($name)) {
			$required = "<span class=\"requiredField\"><abbr title=\"Required field: you must not leave this blank\">*</abbr></span>";
		} else {
			$required = "";
		}
		
		$type = $this->getFormElementType($name);
		if ($type) {
			$classes = $type;
			if ($class != "") {
				$classes .= " ".$class;
			}			
			$class = "class=\"$classes\"";
		}
		else {
			$class = "";	
		}
		
		if ($style != "") {
			$style = "style=\"$style\"";
		}

		// Textbox/Textarea/Dropdown
		if ($formType == 1) {
			// Div Display
			if ($displayType == 1) {
				$output = "<div $class>";
				if (!empty ($label))
					$output .= "<label for=\"$id\">$required $label:</label>&nbsp;";
			}
			// Table Display (label and input)
			else {
				$output = "<tr>";
				if (!empty ($label))
					$output .= "<td>$required $label:&nbps;</td>";
				$output .= "</tr>";
				$output .= "<tr>";
			}
		}
		// Radios/Checkboxes
		else {
			// Div Display
			if ($displayType == 1) {
				$output = "<div $class>";
				$output .= "<fieldset>";
				$output .= "<legend>$required $label</legend>";
			}
			// Table Display
			elseif ($displayType == 2) {
				$output = "<tr>";
				$output .= "<td>&nbsp;</td>";
				$output .= "<td>";
				$output .= "<div>";
				if (!empty ($label))
					$output .= "<p>$required $label</p>";
			}
			// Multilined Radio/Checkbox Div/Table    	
			elseif ($displayType == 3) {
				$output = "<div $class>";
				$output .= "<fieldset>";
				$output .= "<legend>$required $label</legend>";
				$output .= "<table>";
				$output .= "<tr>";
			}
			elseif ($displayType == 4) {
				$output = "<div $class $style>";
				if (!empty ($label))
					$output .= "<label for=\"$id\">$required $label</label>&nbsp;";
			} else {
				$output = "<div $class $style>";
				if (!empty ($label))
					$output .= "<label for=\"$id\">$required $label</label>&nbsp;";
			}
		}

		return $output;
	}

	/**
	 *	Ends the element with an appropriate tag. 
	 *	This method should be used to start all form elements
	 *	because several elements have similar beginnings.
	 *	In general elements start off with an open <div> or <table>
	 *	and a <span class="label"> to label the element
	 *	
	 *	@param	name		the string that is the name of the element
	 *	@param	id			the string that is the id of the element
	 *	@param	postLabel	the string that is the label after the <input> element
	 *	@param	displayType	the integer that decides the display type 
	 *	@param	showErrors	the integer that decides to show or not show errors
	 *	@return				the string that represents the end of the element
	 */
	private function endElement($name, $id, $postLabel = "", $displayType, $showErrors = 1) {

		$output = "";
		
		if (!empty ($postLabel)) {
			if ($displayType > 3) {
				$label = "<label for=\"$id\">$postLabel</label>";

				if ($displayType == 6) {
					$output .= "<div class=\"underInput\">$label</div>";
				} else {
					$output .= $label;
				}
			} else {
				$output .= "<span class=\"postLabel\">$postLabel</span>";
			}
		}
		
		if ($showErrors == 1) {
			$output .= $this->populateFormErrors($id);
		}
		elseif ($showErrors == 2) {
			$this->addToFormErrorSummary($name);
		}

		if ($displayType == 3)
			$output .= "</fieldset>";

		$output .= "</div>";

		return $output;
	}

	/**
	 *	Prints out the input in its appropriate display. 
	 *	This just wraps the <input> in its appropriate tag if any.	
	 *	
	 *	@param	input		the string that is the input of the element
	 *	@param	formType	the integer that decides what type of form display to use
	 *	@param	displayType	the integer that decides the display type	
	 *	@return				the string that represents the input of the element
	 */
	private function printInput($input, $formType, $displayType) {

		if ($formType == 1) {
			if ($displayType == 1) {
				$output = $input;
			} else {
				$output = "<td>$input</td>";
			}
		} else {
			if ($displayType == 1) {
				//$output = "<p class=\"radio\">$input</p>";
				$output = "<span>&nbsp;$input</span>";
			}
			elseif ($displayType == 2) {
				$output = "<div>$input</div>";
			} else {
				$output = "<td>$input</td>";
			}
		}

		return $output;
	}

	/**
	 * Prints a value with a label.
	 * 
	 * @param	value	the string that is the value
	 * @return			the output that contains the label
	 */
	private function printLabel($value) {

		$label = ucwords($value);
		$output = $label;
		return $output;
	}

	/**
	 *	Prints out any errors found for the specified element(s).
	 *	Takes in the element name(s) and checks if there is an error
	 *	stored in the formErrors array of the FormObj 
	 *	
	 *	@param	name		the string that is the name of the element	
	 *	@param	showLabel	the integer that decides to show or not show a label (DEFAULT: 0 = no)
	 *	@return				the string that represents the errors for that element
	 */
	private function populateFormErrors($name, $showLabel = 0) {

		if (strcasecmp("errorSummary", $name) == 0) {
			$errorSummary = $this->getFormErrorSummary();
			$elementType = "div";
			$classVisibility = "";
			$class = "";
			
			/** add any extra errors like group stuff */
			$allFormErrors = key($this->formErrors);
			$fen = $this->getFormElementNames();

			if (isset($allFormErrors) && is_array($fen)) {
				if (!is_array($allFormErrors)) {
					$allFormErrors = array($allFormErrors);	
				}
				
				foreach ($allFormErrors as $value) {
					if (!in_array($value, $fen)) {
						$errorSummary[] = $value;
					}
				}
			}			
		} else {
			$errorSummary = array ($name);
			$elementType = "span";
			$outputTitle = "";
			$classVisibility = array("class=\"visible\"","class=\"invisible\"");
		}
		
		if (!empty ($errorSummary)) {

			$errorCount = 0;
			$outputInnerStuff = "";
			$outputInner = "";
			
			foreach ($errorSummary as $value) {
				$errorID = $value."Error";

				if ($this->getFormError($value)) {
					if (!empty($classVisibility)) 
						$class = $classVisibility[0];									
												
					if ($showLabel == 1)
						$outputInnerStuff .= $this->printLabel("$value: ");
					$outputInnerStuff .= $this->getFormError($value);
					$errorCount ++;
				}
				else {
					$outputInnerStuff = "";
					if (!empty($classVisibility)) 
						$class = $classVisibility[1];	
				}
								
				$outputInnerStart = "<$elementType id=\"$errorID\" $class>";
				$outputInnerEnd = "</$elementType>";
				
				$outputInner .= $outputInnerStart.$outputInnerStuff.$outputInnerEnd;				
			}
							
			if (strcasecmp("errorSummary", $name) == 0) {
								
				if ($errorCount > 0) {
					$visibility = "visible";
				} else {
					$visibility = "invisible";
				}

				$spacer = $this->insertSpacer();
				$outputStart = "<div id=\"errorSummary\" class=\"errorSummaryBox $visibility\">";				
				$outputEnd = "</div><div id=\"extraSpace\" class=\"$visibility\">$spacer</div>";
				
				$output = $outputStart.$outputInner.$outputEnd;
				
			} else {
				$outputStart = "<span class=\"error\">";
				$outputEnd = "</span>";
				$output = $outputStart.$outputInner.$outputEnd;				
			}
		} else {
			$output = "";
		}

		return $output;
	}

	/**
	 * Adds this name to the list of errors shown in the summary.
	 * 
	 * @param	name	the string that is the name of the element
	 * @return			void
	 */
	private function addToFormErrorSummary($name) {

		$fes = $this->formErrorSummary;

		if (!in_array($name, $fes)) {
			$this->formErrorSummary[] = $name;
		}
	}

	/**
	 * Prints the current form error summary.
	 * 
	 * @param	showLabel	the integer that determines whether or not to show labels (DEFAULT: 1 - yes)
	 * @return				the output of the errors in a summary form
	 */
	public function printErrorSummary($showLabel = 1) {

		$fes = $this->formErrorSummary;

		$output = "";

		if (!empty ($fes)) {
			$output = $this->populateFormErrors("errorSummary", $showLabel);
		}

		return $output;
	}
	/**
	 *	Checks to see if the form is valid. 
	 *	Takes in the element names and checks if there is an error
	 *	stored in the formErrors array of the FormObj.
	 *	Also does a check of any groups that need to be validated.
	 *	
	 *	@param	formData	the array that is the name of the element
	 *	@return				the boolean that tells if the form is valid or not
	 */
	public function validateForm($formData) {

		foreach ($formData as $name => $value) {			
			$this->validateFormElement($name, $value);
		}
		
		if ($this->validateGroups()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 *	Validates a group of elements.
	 *	Pulls the validation type and value of all the items in the 
	 *	group array and runs it through the validation machine.
	 *	
	 *	@param	key		the string that is the name associated with the error
	 *	@param	group	the array that holds the different element names
	 *	@return			void
	 */
	private function validateGroup($key, $group) {

		$valType = key($group);
		$names = $group[$valType];
				
		foreach ($names as $value) {			
				$values[] = $this->formData[$value];
		}
		
		/** If the values are empty, then we assume we don't need to validate this group */
		if (Arrays::noEmptyValues($values)) {
			
			$result = Validate :: isValid($valType, $values);
		
			if ($result) {
				$this->setFormError("$key", "");
			} else {
				$this->setFormError("$key", "Please check that all your fields are valid.", 1);
			}
		}

	}

	/**
	 * Validates all of the groups.
	 * 
	 * @return	the boolean will be TRUE if errors exist and FALSE otherwise
	 */
	public function validateGroups() {
		$fvg = $this->formValidationGroups;

		if (!empty($fvg)) {
			foreach ($fvg as $key => $value) {
				$this->validateGroup($key, $value);
			}
		}
		
		if (empty($this->formErrors)) {
			return TRUE;	
		}
		else {
			return FALSE;	
		}
	}

	/**
	 *	Runs the element through a validation process. 
	 *	Takes in the value and pulls the validation type
	 *	assigned to that name and checks if the value is valid.
	 *	
	 *	@param	name	the string that is the name of the element
	 *	@param	value	the string that is the value of the element
	 *	@return			void
	 */
	public function validateFormElement($name, $value) {

		/**************************************************
		Detailed Description: 	
			1) Grab the validation type from the FormObj
			2) Check for special cases: passwords
			3) Check if the validation is optional
				If it is but something was submitted for that element, 
				then continue with validation
				Else we ignore it
			4) Check if the value is empty
				If it is return an error
				Else send it through the Validate machine
			5) Set the formData[$name] to the value	
		****************************************************/
		if (is_array($value)) {
			$array = $value;
		}
		else {
			$array = array($value);		
			$newName = $name;	
		}
		
		$this->setFormValue($name, $value);
		foreach($array as $key => $arrayValue) {
			$valType = $this->getFormValidationType($name);
			if (is_array($value))
				$newName = $name."|".$key;
			
			$opt = substr($valType, 0, 3);
			if (strcmp($opt, "opt") == 0) {
				if (isset($arrayValue) && !empty ($arrayValue)) {
					$valType = substr($valType, 4);
				}
				elseif (empty($arrayValue)) {
					$this->setFormError($newName, "");
					$this->setFormValue($newName, $arrayValue);
					continue;
				}
			}
	
			if ($valType == "passwordVerification") {
				$this->formData[$newName] = $arrayValue;
				$this->checkPasswordVerification();
				return;
			}
	
			if ($valType == "" || empty ($valType)) {
				$this->setFormError($newName, "");
				$this->setFormValue($newName, $arrayValue);
				continue;
			} else {
				if ((empty ($arrayValue) && !is_numeric($arrayValue)) || $arrayValue < 0) {
					$this->setFormError($newName, "Required field");
				} else {
					if (Validate :: isValid($valType, $arrayValue)) {
						$this->setFormError($newName, "");
					} else {
						$this->setFormError($newName, "Please enter a valid entry for this field", 1);
					}
					continue;
				}
			}
		}
		
	}

	/**
	 *	Special validation for verifying passwords. 
	 *	
	 *	@return		void
	 */
	private function checkPasswordVerification() {

		if (empty ($this->formData["password1"])) {
			$this->formErrors["password1"] = "Please enter a password";
			unset ($this->formErrors["password2"]);
		} else {
			if (empty ($this->formData["password2"])) {
				$this->formErrors["password2"] = "Please verify your password";
				unset ($this->formErrors["password1"]);
			}
			elseif ($this->formData["password1"] != $this->formData["password2"]) {
				$this->formErrors["password2"] = "Passwords must match.";
				unset ($this->formErrors["password1"]);
			} else {
				unset ($this->formErrors["password1"]);
				unset ($this->formErrors["password2"]);
			}
		}
	}

	/**
	 * Inserts a textbox with its appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	type		the string that is the type of the input (DEFAULT: text)
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	id			the string that is the id of the input
	 * @param	label		the string that is the label going before the input
	 * @param	postLabel	the string that is the label going before the input
	 * @param	value		the string that is the value of the input
	 * @param	class		the string that is the class of the input
	 * @param	size		the integer that is the size of the input
	 * @param	maxlength	the integer that is the maxlength of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	public function insertTextbox($displayType = 1, $type = "text", $valType, $name, $id, $label = "", $postLabel = "", $value = "", $class = "", $size = "", $maxlength = "", $readOnly = 0, $showErrors = 1, $interaction = "") {
		
		$realName = $this->getRealName($name);
		$this->setFormValidationType($realName, $valType);

		if ($type == "dateWidget") {
			$input = $this->insertDateWidget($name, $value, "", $interaction, $showErrors);
		} else {
			$input = $this->insertQuickTextbox($type, $name, $id, $value, $readOnly, $size, $maxlength, $interaction, $showErrors);
		}

		$output = $this->startElement($id, $realName, $label, 1, $displayType, $class);
		$output .= $this->printInput($input, 1, $displayType);
		$output .= $this->endElement($realName, $id, $postLabel, 1, $showErrors);

		return $output;
	}

	/**
	 * Inserts a dropdown with its appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the group
	 * @param	label		the string that is the label going before the input
	 * @param	postLabel	the string that is the label going before the input
	 * @param	values		the array that holds all the <option> fields, the key is the name of the individual input
	 * @param	message		the string that is the message to be shown by default
	 * @param	class		the string that is the class of the input
	 * @param	size		the integer that is the size of the input
	 * @param	multiple	the boolean that determines whether or not to show multiple values or not
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErrors	the integer that decides to show or not show errors 
	 * @param	onSelect	the string that holds JavaScript onChange instructions 
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	public function insertDropdown($displayType = 1, $valType = "dropdown", $name = "", $label = "", $postLabel = "", $values, $message = "", $class = "", $size = "", $multiple = false, $readOnly = 0, $showErrors = 1, $onSelect = "", $validate = true) {

		if ($name) {
			$keys = array_keys($values);
			$this->setFormElementName($name,"dropdown");
		} else {
			$name = key($values);
		}
		
		if (!is_array($label)) {
			$labelArray = array($label);	
		}
		else {
			$labelArray = $label;	
		}
		if (!is_array($postLabel)) {
			$postLabelArray = array($postLabel);	
		}
		else {
			$postLabelArray = $postLabel;	
		}
		
		$labels = array($labelArray,$postLabelArray);
		$firstLabel = $labelArray[0];
		$lastLabel = end($postLabelArray);
		
		$input = $this->insertQuickDropdown($name, $valType, $labels, $values, $message, $size, $multiple, $readOnly, $onSelect, $validate, $showErrors);

		$output = $this->startElement("", $name, $firstLabel, 1, $displayType, $class);
		$output .= $this->printInput($input, 1, $displayType);
		$output .= $this->endElement($name, $name, $lastLabel, 2, $showErrors);

		return $output;
	}

	/**
	 * Inserts a checkbox with its appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	id			the string that is the id of the input 
	 * @param	label		the string that is the label going before the input
	 * @param	postLabel	the string that is the label going before the input
	 * @param	value		the string that is the value of the input
	 * @param	class		the string that is the class of the input
	 * @param	style		the string that is the style of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	public function insertCheckbox($displayType = 4, $valType = "checkbox", $name, $id, $label = "", $postLabel = "", $value, $class = "", $style = "", $readOnly = 0, $showErrors = 1, $interaction = "") {

		$array = Arrays :: stripBracket($name);
		if ($array) {
			$realName = $array[1];
		} else {
			$realName = $name;
		}

		$this->setFormValidationType($realName, $valType);

		$checkbox = $this->insertQuickCheckbox($name, $id, $value, $readOnly, $interaction);

		$output = $this->startElement($id, $name, $label, 2, $displayType, $class, $style);
		$output .= $this->printInput($checkbox, 1, 1);
		$output .= $this->endElement($realName, $id, $postLabel, $displayType, $showErrors);

		return $output;

	}

	/**
	 * Inserts a radio with its appropriate surrounding tags.
	 * 
	 * @param	displayType	integer that is the display type of the input
	 * @param	valType		string that is the validation type of the input
	 * @param	name		string that is the name of the input
	 * @param	id			string that is the id of the input 
	 * @param	label		string that is the label going before the input
	 * @param	postLabel	string that is the label going before the input
	 * @param	value		array that holds all the possible options
	 * @param	class		string that is the class of the input
	 * @param	readOnly	integer that decides if the input is readonly or not
	 * @param	showErrors	integer that decides to show or not show errors 
	 * @param	interaction	string that holds any JavaScript interaction
	 * @return				string that includes the <input> and any surrounding tags.
	 */
	public function insertRadio($displayType = 4, $valType = "radio", $name, $id, $label = "", $postLabel = "", $value, $class = "", $style = "", $readOnly = 0, $showErrors = 1, $interaction = "") {

		$array = Arrays :: stripBracket($name);
		if ($array) {
			$realName = $array[1];
		} else {
			$realName = $name;
		}
		
		$this->setFormValidationType($realName, $valType);
		
		$specialStuff = "";
		if (is_array($value)) {
			$value = $this->checkSpecial($name, $value);
	
			if (is_array($value)) {
				$specialStuff = $value[1];
				$value = $value[0];
			}
		}

		$input = $this->insertQuickRadio($valType, $name, $id, $value, $readOnly, $showErrors, $interaction);

		$output = $this->startElement($id, $name, $label, 2, $displayType, $class, $style);
		$output .= $this->printInput($input, 2, 1);
		$output .= $this->endElement($realName, $id, $postLabel.$specialStuff, $displayType, $showErrors);

		return $output;

	}
	
	/**
	 * Inserts a textarea with its appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	label		the string that is the label going before the input
	 * @param	postLabel	the string that is the label going before the input
	 * @param	value		the string that is the value of the input
	 * @param	class		the string that is the class of the input
	 * @param	cols		the integer that is the number of columns for the input
	 * @param	rows		the integer that is the number of rows for the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	function insertTextarea($displayType, $valType, $name, $label = "", $postLabel = "", $value = "", $class = "", $cols = 10, $rows = 4, $readOnly = 0, $showErrors = 1, $interaction = "") {

		$this->setFormValidationType($name, $valType);

		$input = $this->insertQuickTextarea($name, $value, $readOnly, $cols, $rows, $class, $interaction);
		
		$id = $name;
		
		$output = $this->startElement($id, $name, $label, 1, $displayType, $class);
		$output .= $this->printInput($input, 1, $displayType);
		$output .= $this->endElement($name, $name, $postLabel, 1, $showErrors);

		return $output;

	}

	/**
	 * Special case where a checkbox enables/disables an associated dropdown.
	 * 
	 * @param	checkboxName 	the string that is the name of the checkbox
	 * @param	checkboxLabel	the string that is the label of the checkbox
	 * @param	checkboxClass	the string that is the class of the checkbox
	 * @param	checkboxAction	the string that is the action of the checkbox
	 * @param	checkboxValue	the string that is the value of the checkbox
	 * @param	dropdownValType	the string that is the validation type of the dropdown
	 * @param	dropdownLabel	the string that is the label that goes before the dropdown
	 * @param	dropdownMessage	the string that is the message inside the dropdown
	 * @param	dropdownValues	the string that is the value 
	 * @param	readOnly		the integer that decides if the input is readonly or not
	 * @param	showErrors		the integer that decides to show or not show errors
	 * @return					the string that includes the <input> and any surrounding tags.
	 */
	public function insertCheckboxDropdown($checkboxName, $checkboxLabel = "", $checkboxClass = "", $checkboxAction = 1, $checkboxValue, $dropdownValType = "dropdown", $dropdownLabel = "", $dropdownMessage = "", $dropdownValues, $readOnly = 0, $showErrors = 0) {

		if ($checkboxClass) {
			$checkboxClass = "class = \"$checkboxClass\"";
		}
		
		$dropdownName = key($dropdownValues);
		$interaction = "onclick=\"enableDisable('$dropdownName');\"";

		$checkboxOutput = $this->insertCheckbox(4, "opt_checkbox", $checkboxName, $checkboxName, "", $checkboxLabel, $checkboxValue, $checkboxClass, "", $readOnly, $showErrors, $interaction);

		if ($this->checkCheckbox($checkboxName)) {
			$readOnly = 0;
			$showErrors = 1;
		} else {
			$readOnly = 1;
			$showErrors = 0;
		}

		$dropdownOutput = $this->insertDropdown(1, "opt_dropdown", "", "", $dropdownLabel, $dropdownValues, $dropdownMessage, "", "", false, $readOnly, $showErrors);

		$output = "<div class=\"checkboxDropdown\">";
		$output .= $checkboxOutput;
		$output .= $dropdownOutput;
		$output .= "</div>";
		
		return $output;
	}

	/**
	 * Inserts multiple checkboxes with their appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	label		the string that is the label going before the input
	 * @param	values		the array that is all the possible options 
	 * @param	perField	the integer that is the number of inputs to show per row or column
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	public function insertCheckboxes($displayType = 4, $valType = "checkbox", $name, $label = "", $values, $class = "", $perField = 3, $showErrors = 0, $interaction = "") {

		// start the counter for elements in the row
		$currentRow = 1;
		$countInRow = 0;
		$count = 1;
		$total = count($values);

		if ($perField == 0) {
			$perField = $total;
		}

		$array = Arrays :: stripBracket($name);
		if ($array) {
			$id = $array[1];
		}

		//display type = 4 -> rows one per line
		//display type = 5 -> rows with multiple per row
		//display type = 6 -> columns with label under		

		if ($displayType == 5) {
			$direction = "rows";
			$addWidth = 30;
		}
		elseif ($displayType == 6) {
			$direction = "columns";
			$addWidth = 20;
		} else {
			$addWidth = 100;
		}
		// use longest to determine the width of each field
		$longest = Arrays :: longestString($values) + $addWidth;
		$width = $longest."px";

		$output = "<div class=\"multipleInputs $class\">";

		if ($label) {
			$output .= "<span>$label</span>";
		}

		if ($perField > 1 || $displayType == 6) {
			$output .= "<div class=\"$direction\">";
		}

		foreach ($values as $value => $labels) {

			$value = $this->checkSpecial($name, $value);

			$splitLabels = explode("|", $labels);
			$label = $splitLabels[0];
			$postLabel = $splitLabels[1];

			if ($array) {
				$curID = $id.$count;
			}

			if ($perField == 1 && $displayType != 6) {
				$output .= $this->insertCheckbox($displayType, $valType, $name, $curID, $label, $postLabel, $value, "", "width: $width", 0, $showErrors, $interaction);
			} else {
				if ($countInRow < $perField) {
					$output .= $this->insertCheckbox($displayType, $valType, $name, $curID, $label, $postLabel, $value, "", "width: $width;", 0, $showErrors, $interaction);

					$countInRow ++;
				} else {
					$output .= "</div>";
					$output .= "<div class=\"$direction\">";
					$output .= $this->insertCheckbox($displayType, $valType, $name, $curID, $label, $postLabel, $value, "", "width: $width", 0, $showErrors, $interaction);

					$countInRow = 1;
				}
			}

			$count ++;
		}

		if ($perField > 1 || $displayType == 6) {
			$output .= "</div>";
		}
		
		$output .= "</div>";

		return $output;
	}
	
	/**
	 * Inserts multiple radios with their appropriate surrounding tags.
	 * 
	 * @param	displayType	the integer that is the display type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	label		the string that is the label going before the input 
	 * @param	values		the array that is all the possible options
	 * @param	perField	the integer that is the number of inputs to show per row or column
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */	
	public function insertRadios($displayType = 4, $valType = "radio", $name, $label = "", $values, $class = "", $readOnly = 0, $showErrors = 1, $interaction = "") {

		$array = Arrays :: stripBracket($name);
		if ($array) {
			$id = $array[1];
		}
		
		$output = "";
		
		if ($label) 
			$output .= "<div><span>$label</span></div>";
			
		$output .= "<div class=\"multipleInputs $class\">";

		$count = 1;
		
		foreach ($values as $value => $labels) {

			$value = $this->checkSpecial($name, $value);

			$splitLabels = explode("|", $labels);
			$label = $splitLabels[0];
			$postLabel = $splitLabels[1];

			if ($array) 
				$curID = $id.$count;	
			else
				$curID = $name.$count;
			
			$curInteraction = $interaction[$count];
			$output .= $this->insertRadio($displayType,$valType,$name,$curID,$label,$postLabel,$value, "", "", 0, $showErrors, $curInteraction);
			
			$count++;
		}

		$output .= "</div>";

		return $output;

	}	

	/**
	 * Inserts multiple textboxes with their appropriate surrounding tags.
	 * 
	 * @param	type		the string that is the type of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	label		the string that is the label going before the input
	 * @param	postLabel	the string that is the label going after the input
	 * @param	total		the integer that is the total number of inputs
	 * @param	perRow		the integer that is the number of inputs to show per row
	 * @param	size		the integer that is the size of the input
	 * @param	maxlength	the integer that is the maxlength of the input
	 * @param	class		the string that is the class of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input> and any surrounding tags.
	 */
	public function insertTextboxes($type = "text", $valType, $name, $label = "", $postLabel = "", $total = 2, $perRow = 3, $size = "", $maxlength = 25, $class = "", $readOnly = 0, $showErrors = 1, $interaction = "") {

		// start the counter for elements in the row
		$count = 1;
		$errorLabels = array ();
		$countInRow = 0;
		$boxName = $name."[]";

		for ($i = 1; $i <= $total; $i ++) {
			$boxID = $name.$i;
			if (!empty ($label))
				$boxLabel = $label." ".$i;
			else
				$boxLabel = "";

			if (!empty ($postLabel))
				$boxPostLabel = $postLabel." ".$i;
			else
				$boxPostLabel = "";

			if ($countInRow < $perRow) {
				$output .= $this->insertTextbox(1, $type, $valType, $boxName, $boxID, $boxLabel, $boxPostLabel, "", $size, $maxlength, $class, $readOnly, $showErrors, $interaction);
				$countInRow ++;
			} else {
				$output .= $this->insertTextbox(1, $type, $valType, $boxName, $boxID, $boxLabel, $boxPostLabel, "", $size, $maxlength, $class, $readOnly, $showErrors, $interaction);
				$countInRow = 1;
			}
		}

		return $output;
	}

	/**
	 * Outputs a textbox.
	 * 
	 * @param	type		the string that is the type of the input (DEFAULT: text)
	 * @param	name		the string that is the name of the input
	 * @param	id			the string that is the id of the input
	 * @param	value		the string that is the value of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	size		the integer that is the size of the input
	 * @param	maxlength	the integer that is the maxlength of the input
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @return				the string that includes the <input>.
	 */
	public function insertQuickTextbox($type = "text", $name, $id, $value = "", $readOnly = 0, $size = "", $maxlength = "", $interaction = "", $showErrors = 1) {

		$realName = $this->getRealName($name);
		/**** Add this element to the array of elements on the page ****/
		$this->setFormElementName($realName,"textbox");			
		
		if (isset ($_POST["$realName"])) {
			if (is_array($_POST["$realName"])) {
				$length = strlen($realName);
				$idNum = substr($id, $length);
				if (isset($_POST["$realName"][$idNum-1])) 
					$value = $_POST["$realName"][$idNum-1];
			}
			else {
				$value = $_POST["$realName"];
			}
		}
		
		$readOnly = $this->isReadOnly($readOnly);

		// if it's a hidden type then we need to specify less attributes
		if ($type == "hidden") {
			$output = "<input type=\"$type\" name=\"$realName\" id=\"$id\" value=\"$value\" />";
		} else {
			
			$valType = $this->getFormValidationType($realName);

			//This is a method of preserving the returned email address
			//recieved from an andrewID validation procedure.			
			if ((strpos($valType, "course") === false) && (strcasecmp("exceptionDate", $name) != 0)) {
				//course validation is handled after the selection from the dropdown
				//that is generated for course fields					
				$validate = "validate(this, '$valType', $showErrors);";
			} else {
				$validate = "";
			}

			if ($type == "date") {
				$class = "inputTextboxDate";
				$type = "text";
			} else {
				$class = "inputTextbox";
			}

			$interactionType = substr($interaction, 0, 6);
			if ($interactionType == "onblur") {
				$interaction = substr($interaction, 8, -1);
				$javascript = "onblur=\"$validate $interaction\"";
			} else {
				$onblur = "onblur=\"$validate\"";
				$javascript = $interaction." ".$onblur;
			}

			$output = "<input autocomplete=\"off\" type=\"$type\" name=\"$name\" id=\"$id\" class=\"$class\" size=\"$size\" maxlength=\"$maxlength\" $javascript value=\"$value\" $readOnly />";

		}

		//Add in a div specified to the id of the box + "results";
		//This is currently to serve as a place to drop the course auto_complete results
		//Its probably going to make everything look horrid in the course form, but we need a specific
		//drop point generated per text box, and this is about the only way to get it.

		if (strpos($id, "course") !== false) {
			$id = $id."Results";
			$output .= "<div id='$id'></div>";
		}
		// for now keep this only for courses

		return $output;
	}

	/**
	 * Outputs a textarea.
	 * 
	 * @param	name		the string that is the name of the input
	 * @param	value		the string that is the value of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	cols		the integer that is the number of columns
	 * @param	rows		the integer that is the number of rows
	 * @param	class		the string that is the class of the input
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input>.
	 */
	public function insertQuickTextarea($name, $value = "", $readOnly = 0, $cols = 20, $rows = 4, $class = "", $interaction = "") {

		/**** Add this element to the array of elements on the page ****/
		$this->setFormElementName($name,"textarea");

		$storedValue = $this->getStoredValue($name);
		
		if (!empty($storedValue)) {
			$value = $storedValue;
		}
		
		$readOnly = $this->isReadOnly($readOnly);

		$output = "<textarea name=\"$name\" id=\"$name\" wrap=\"physical\" cols=\"$cols\" rows=\"$rows\" class=\"inputTextarea\" $readOnly $interaction />$value</textarea>";

		return $output;
	}

	/**
	 * Outputs a textarea.
	 * 
	 * @param	name		the string that is the name of the input
	 * @param	valType		the string that is the validation type of the input
	 * @param	values		the array that is the values of the input
	 * @param	message		the string that is the message shown by default
	 * @param	size		the integer that is the size of the input
	 * @param	multiple	the boolean that decides to show or not show multiple values
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	onSelect	the string that holds the JavaScript onChange instructions
	 * @param	validate	the boolean to specify whether or not to ever validate this input
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @return				the string that includes the <input>.
	 */
	public function insertQuickDropdown($name = "", $valType = "dropdown", $labels, $values, $message = "", $size = "", $multiple = false, $readOnly = 0, $onSelect = "", $validate = true, $showErrors = 1) {

		$readOnly = $this->isReadOnly($readOnly);

		if (count($values) > 1) {
			$showLabels = "|1";
			$register = false;
			$ddName = $name."[]";
		} else {
			$showLabels = "";
			$register = true; // register means that this is a single dropdown
		}

		$count = 0;
		$lastLabelCount = count($labels[1])-1;

		foreach ($values as $dropdownKey => $options) {

			$array = Arrays :: stripBracket($dropdownKey);

			if (count($array) == 2) {
				$dropdownName = $dropdownKey;
				$id = $array[1];
			} else {
				$id = $dropdownKey;
			}

			if (isset ($ddName)) {
				$dropdownName = $ddName;
				$array = Arrays :: stripBracket($dropdownName);
				$justDropdownName = $array[1];
			} else {
				$dropdownName = $dropdownKey;
			}

			if ($register) {
				$this->setFormValidationType($id, $valType);
				$this->setFormElementName($id,"dropdown");
			}

			if (!$validate)
				$blur = "";
			else {
				$blur = "onblur=\"validate(this, '$valType$showLabels',$showErrors);\"";
			}

			if ($size) {
				$size = "size = \"$size\"";
			}
			if ($multiple) {
				$multiple = "multiple";
			}
			
			if ($count != 0 && $labels[0][$count] != "") 
				$output .= "<span class=\"padLR\">".$labels[0][$count]."</span>";

			$output .= "<select name=\"$dropdownName\" id=\"$id\" $size $multiple $blur $readOnly onchange=\"$onSelect\">";

			if (!empty ($message)) {
				if (is_array($message)) {
					$selectMessage = $message[$id];
				} else {
					$selectMessage = $message;
				}

				$output .= "<option value=\"\">$selectMessage</option>";

				if (!empty ($selectMessage) && $selectMessage != " ") {
					$filler = "-";
					$output .= "<option value=\"\">";
					for ($i = 0; $i < strlen($selectMessage); $i ++) {
						$output .= $filler;
					}
					$output .= "</option>";
				}
			}

			foreach ($options as $key => $value) {
				if ($justDropdownName) {
					$id = $justDropdownName;
				}

				if ($register) {
					$selectName = $id;
				} else {
					$selectName = $id."|$count";
				}
				
				$key = $this->checkSpecial($selectName, $key);
				$selected = $this->isSelected($selectName, "$key", 2);
				
				// Add leading 0 to single digit numbers
				// ie Time (3 becomes 03)                
				if (ereg("^[0-9]+$", $value)) {
					if ($value >= 0 && $value < 10) {
						$value = substr($value +100, 1);
					}
				}
				$output .= "<option value=\"$key\" $selected>$value</option>";
			}

			$output .= "</select>";
			if ($count != $lastLabelCount && $labels[1][$count] != "")
				$output .= "<span class=\"padLR\">".$labels[1][$count]."</span>";
			$count ++;
		}
		
		return $output;
	}

	/**
	 * Outputs a radio button.
	 * 
	 * @param	valType		the string that is the validation type of the input
	 * @param	name		the string that is the name of the input
	 * @param	id			the string that is the id of the input
	 * @param	value		the string that is the value of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	showErorrs	the integer that decides to show or not show errors
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input>.
	 */
	public function insertQuickRadio($valType, $name, $id, $value, $readOnly = 0, $showErrors = 1, $interaction = "") {

		/**** Add this element to the array of elements on the page ****/
		$this->setFormElementName($name,"radio");

		$checked = $this->isSelected($name, "$value", 1);

		$readOnly = $this->isReadOnly($readOnly);

		$blur = "onblur=\"validate(this, '$valType',$showErrors);\"";

		$output = "<input type=\"radio\" name=\"$name\" id=\"$id\" value=\"$value\" class=\"inputRadio\" $readOnly $interaction $blur $checked />";

		return $output;
	}

	/**
	 * Outputs a checkbox.
	 * 
	 * @param	name		the string that is the name of the input
	 * @param	id			the string that is the id of the input
	 * @param	value		the string that is the value of the input
	 * @param	readOnly	the integer that decides if the input is readonly or not
	 * @param	interaction	the string that holds any JavaScript interaction
	 * @return				the string that includes the <input>.
	 */
	public function insertQuickCheckbox($name, $id, $value, $readOnly = 0, $interaction = "") {

		/**** Add this element to the array of elements on the page ****/
		$this->setFormElementName($name,"checkbox");

		$checked = $this->isSelected($name, "$value", 1);

		$readOnly = $this->isReadOnly($readOnly);

		$output = "<input type=\"checkbox\" name=\"$name\" id=\"$id\" value=\"$value\" class=\"inputCheckbox\" $interaction $onclick $checked $readOnly />";

		return $output;

	}

	/**
	 * Outputs a date widget.
	 * 
	 * @param	name		the string that is the name of the input
	 * @param	value		the string that is the value of the input
	 * @param	class		the string that is the class of the input
	 * @param	interaction	the string that is the javascript actions to take
	 * @param	showErrors	the integer that decides to show or not show errors
	 * @return				the string that includes the <input>.
	 */
	public function insertDateWidget($name, $value = "", $class = "", $interaction = "", $showErrors = 1) {

		$dateButtonImage = "images/calbtn.gif";
		
		/***********************************************
		 * Sam, for some reason date validation on the 
		 * request form was firing as invalid on a normal
		 * valid date.  I switched this to false on show
		 * errors in order to force the form to get the
		 * form to submit without errors.  Not sure what the
		 * deal is.
		 ***********************************************/
		
		$output = $this->insertQuickTextbox("date", $name, $name, $value, 0, "", "", $interaction, $showErrors);

		$btnID = $name."_button";

		$output .= "&nbsp;<img src=\"$dateButtonImage\" class=\"calButton\" id=\"$btnID\" title=\"Specify Date\" />";

		$output .= CommonFunctions :: attachCalendarWidget($name, $btnID);

		return $output;

	}
} 
?>