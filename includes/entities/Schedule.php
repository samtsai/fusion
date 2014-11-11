<?php
class Schedule //Schedule Class start

{

	/* This class is a bit different from most in that it is an encapsulation of most 		
	 of the functionality of the site, in that it contains the functions necessary		
	 for the aggregation and display of visual schedule information.  Thus said it is 	
	 generalized in function so as to allow an individual, group, or set of individual's  */

	private $person_ids; //Variable containing an array of individual person_id's whose information
	//this innstance of the class is currently responsible for displaying

	private $group_id; //Variable containing the group ID, if this schedule is currently
	//displaying the merged schedules of a particular group

	private $display_start; //variable containing a timestamp representing 1 second after midnight
	//the Sunday that the selected week starts on

	private $display_end; //variable containing a timestamp representing 1 second before midnight
	//the Saturday that the selected week ends on

	/* A constructor which takes one of two arguments, and acts accordingly. 		*/
	/* If passed a group_id, the class will set the group_id accordingly 			*/
	/* If the class is passed only a person_id, the class sets the group_id to 		*/
	/* NULL and adds the appropriate person_id to its list							*/
	/* In either of these cases, the date is assumed to be the current date.		*/
	/* If the constructor is called without either a person_id or a group_id		*/
	/* then the constructor sets all internal variables to NULL, and returns true	*/
	/* If the constructor is passed two values, it sets all internal values to NULL */
	/* and returns false, signifying impoper construction							*/

	function __construct($group_id = NULL, $person_id = NULL) {

		if ($group_id == NULL && $person_id == NULL) {
			$this->person_ids = NULL;
			$this->group_id = NULL;
		}

		if ($group_id != NULL && $person_id != NULL) {
			$this->person_ids = NULL;
			$this->group_id = NULL;
			$this->display_start = NULL;
			$this->display_end = NULL;
			return false;
		}

		$this->person_ids = array ();
		if ($group_id != NULL) {
			//echo "making a schedule for group $group_id <br/>";
			$this->switchGroupTo($group_id);
		} else
			if ($person_id != NULL) {
				$this->group_id = NULL;
				$this->addPerson($person_id);
			}
		$this->setVisibleWeek(date("n"), date("j"), date("Y"));
		return true;
	}

	public function getWeekStart() {
		return $this->display_start;
	}

	public function getWeekEnd() {
		return $this->display_end;
	}

	public function setGroup($group_id) {
		$this->group_id = $group_id;
	}

	function printWeekHeader() {

		//get the dates for the days of the current week
		//figure out the dates for the starting sunday and the ending saturday
		$sun = getdate($this->display_start + (86400 * 0));
		$sat = getdate($this->display_start + (86400 * 6));
		//echo $this->display_start.'<br>';
		//echo $this->display_end.'<br>'; 
		//echo $sun.'<br>';
		//echo $sat.'<br>';
		

		$sun_date = $sun["mday"];
		$sat_date = $sat["mday"];
		//echo $sun_date.'<br>';
		//echo $sat_date.'<br>';

		$start_month = $sun["month"];
		$end_month = $sat["month"];

		if (CommonFunctions :: inAgent("MSIE")) {
			$leftbutton = "<div id=\"navLeft\" onmouseover='document.getElementById(\"navLeft\").className=\"navIEOver\";' onmouseout='document.getElementById(\"navLeft\").className=\"navIEDefault\";' onclick='javascript:backWeek(".$this->display_start.")'><</div>";
			$rightbutton = "<div id=\"navRight\" onmouseover='document.getElementById(\"navRight\").className=\"navIEOver\";' onmouseout='document.getElementById(\"navRight\").className=\"navIEDefault\";' onclick='javascript:forwardWeek(".$this->display_end.")'>></div>";
		} else {
			$leftbutton = "<div id=navLeft onclick='javascript:backWeek(".$this->display_start.")'><</div>";
			$rightbutton = "<div id=navRight onclick='javascript:forwardWeek(".$this->display_end.")'>></div>";
		}

		//output the navigation buttons and the date header
		$output .= "<div id=\"calendarNav\">";
		$output .= $leftbutton;
		$output .= "<div id=nav> $start_month $sun_date to $end_month $sat_date</div>";
		$output .= $rightbutton;
		$output .= "</div>";

		$output .= "<div id=\"calendarHeader\">";
		if ($this->group_id) {
			$output .= "<input type=\"hidden\" id=\"group_id\" value=\"".$this->group_id."\" />";
		}

		$field = "<input type=\"hidden\" id=\"person_ids\" value=\"";

		foreach ($this->person_ids as $person_id)
			$field .= "$person_id|";

		$field .= "\" />";
		$field .= "<input type=\"hidden\" name=\"start_time\" id=\"start_time\" value=\"".$this->display_start."\" />";

		$output .= $field;

		$curYear = $sun["year"];
		$output .= "<div id=\"spacer\" class=\"everyOtherDay\">$curYear</div>";

		for ($i = 0; $i <= 6; $i ++) {
			
			$unix_time = ($this->display_start + (86400 * $i));
			
			if ((date("I", ($this->display_start + (86400 * $i)))) != 1)
				$unix_time += 3600;
			
			$start = getdate($unix_time);
			
			$start_date = $start["mday"];
			$start_name = substr($start["weekday"], 0, 3);
			$month = substr($start["month"], 0, 3);

			$cur = getdate();
			$cur_date = $cur["mday"];
			$cur_name = substr($cur["weekday"], 0, 3);
			$cur_month = substr($cur["month"], 0, 3);

			if ($start_date == $cur_date && $start_name == $cur_name && $month == $cur_month) {
				$bg = "today";
			} else {
				$bg = "everyOtherDay";
			}
			//$output .= "<div class=\"$bg\">$start_unix</div>";
			$output .= "<div class=\"$bg\">$start_name, $month $start_date</div>";
		}
		$output .= "</div>";

		return $output;

	}

	function groupDropdown() {
		$person = new Person($_SESSION["person_id"]);
		$list = $person->getGroups();
		$formObj = CommonFunctions::setFormObj(4);
		$output .= "<div id=\"veil\" class=\"invisible\">&nbsp;</div><div id=\"groupSelect\">";

		if (!empty ($list)) {
			foreach ($list as $id => $name) {
				if ($id == $this->group_id) {
					$formObj->setFormValue("groups", "$id");
					break;
				}
			}

			$time = $this->display_start + 1000;
			$pid = $_SESSION["person_id"];
			$onSelect = "document.getElementById('group_id').value = this.options[this.selectedIndex].value; changeGroup($pid);";
			$values = array ("groups" => $list);

			$output .= "Group: ";

			//$formObj->setFormData(array("Select Group" => $gid));
			$output .= $formObj->insertQuickDropdown("", "", "", $values, "", "", false, 0, $onSelect, false);
			$output .= "</div>";
			$output .= $this->memberSelector();
		} else {
			$output .= "You are not currently in any groups.</div>";
		}

		return $output;

	}

	public function getGroupId() {
		return $this->group_id;
	}
	
	public function setGroupId($int)
	{
		$this->group_id = $int;
	}

	function Display() 
	{

		/** Print the calendar header **/

		$output .= $this->printWeekHeader();
		if (ereg('class="today"',$output))
			$current_week_visible = true;
		else
			$current_week_visible = false;
		
		
		
		
		/*******************************************************
		set up default values for the earliest and latest hours we have to display
		we set them to 24 and 0 initially so that any hour will be earlier/later
		and thus we can move the marker in the correct direction.  As we encounter earlier
		start times we can continue to move the start time closer to 0, and as we run into 
		later end times we can move the other counter towards 24
		**********************************************************/
		$first_hour = 24;
		$last_hour = 0;

		/*****************************************************
		This is the array which is going to be used to hold
		The IDs of the divs that have events within them.  The values to these
		ID keys will be another associative array containing references
		to all of the specific parameters we will need to print out
		This way adding a parameter to a div will be as easy as pushing it 
		into the internal array for the DIV and then reading it back out on display
		**********************************************************/

		$ids_array = array ();
		if (!empty ($this->person_ids)) {

			if ($this->group_id != NULL)
				$category = "Group";

			foreach ($this->person_ids as $id) {
				$db = new DB();

				//echo "<br/> getting events for person_id $id";
				//if ($events = $db->getArray("SELECT event_id FROM tblPersonEvent WHERE person_id = $id")) {
				if ($events = $db->getArray("SELECT event_id FROM tblPersonEvent WHERE person_id = $id")) {
					//keeps track of how many types of an event there are
					$classcount = 0;
					$workcount = 0;
					$personalcount = 0;
					$meetingcount = 0;
					$tempcount = 0;
					//echo "person $person_id has these events:<br/>";
					foreach ($events as $event_id) {
						//echo "$event_id for person $person_id<br/>";
						/*******************************************************
						for right now the colors are simply cycled through.  
						in this space we can use the type of the event and set
						the eventcount accordingly to trigger the right style
						presumably we can also just set the eventcount to a default
						value for when we want all of the evnents to be the same color
						for the group calendar 
						**********************************************************/

						/** construct the event in question. **/
						$event = new Event($event_id);
						//$event->toprint();
						$type = $event->getTypeID();

						if ($this->group_id == NULL||$this->group_id == "special") {
							if ($type == 1) {
								$classcount ++;
								$category = "Class";
							}
							elseif ($type == 2) {
								$workcount ++;
								$category = "Work";
							}
							elseif ($type == 3) {
								$personalcount ++;
								$category = "Personal";
							}
							elseif ($type == 4 || $type == 6) {
								$meetingcount ++;
								$category = "Meeting";
							}
							elseif ($type == 5) {
								$tempcount ++;
								$category = "TempMeeting";
							}
						}

						//echo "<br/> getting event dates for event $event_id <br/>";

						/*******************************************************
						User the event object to build a list of the times it is visible, given
						the start and end times of the currently displayed week
						**********************************************************/

						$visible = $event->getDates($this->display_start, $this->display_end);

						//dump($visible);
						$eventStart = $this->display_start;
						if (!empty ($visible)) {
							//echo "event_id is $event_id occurs on <br/>";
							//dump($visible);
							foreach ($visible as $occur) {
								//echo $occur;
								/** get information regarding the period during which the event occurs **/
								$temp = getdate($occur);

								/** figure out during which hour the event starts **/
								$start_hour = $event->getStartHour();

								/************************************************
								  figure out on which quarter hour the event starts
								  this will be used to do fadeins/outs on event times,
								  so that we're not always rounding to the nearest half hour
								*************************************************************/

								$squarter = $event->getStartQuarter();

								//echo "$event_id starts during $squarter <br/>";

								/** keep track of the earliest we have to start the calendar **/
								if ($start_hour < $first_hour)
									$first_hour = $start_hour;

								/****************************************************************
								  begin building the list of ids that we're going to have to use
									  first unique param on the id is the day number followed by an underscore
								**************************************************************/

								$daynum = $temp["wday"];
								$idbase = $daynum."_";

								//echo "event starts in $squarter<br/>";

								/**********************************************
									build the id of the span for the delete icon
									of a particular event, this must be particular
									to the day that the event is being shown on
									in order to distinguish one particular instance
									of an event from another, and so that the X only
									shows up on one when we hover on it
								****************************************************/

								$spanid = "del_$event_id"."_".$this->display_start."_".$daynum;
								$delspan = $event_id."_$daynum";

								/****************************************************
								Now we have to build the parameters that each particular
								hourly/half-hourly div is going to be established with depending
								on the event that fall on it.
								
								To do this we will build an associative array of parameters
								params have the following fields:
								 class -> class describing the background image/color to be CSS'd onto the div
								 text -> the text to be within the div, if none then &nbsp;
								 content -> other content that needs to be go into the div	("delete button")						
								 onclick -> the onclick js
								 onmouseover -> the onmouseover js
								 onmouseout -> the onmouseout js 
								
								Now lets's build the default values for this particular event
								**************************************************************/
								if ($type == 1) {
									$extraclass = $category.$classcount.$daynum;
								}
								elseif ($type == 2) {
									$extraclass = $category.$workcount.$daynum;
								}
								elseif ($type == 3) {
									$extraclass = $category.$personalcount.$daynum;
								}
								elseif ($type == 4 || $type == 6) {
									$extraclass = $category.$meetingcount.$daynum;
								}
								elseif ($type == 5) {
									$extraclass = $category.$tempcount.$daynum;
								}

								/*** Now generate the 5 unique classnames for this particular event **/
								$extras = $extraclass."Start";
								$extram = $extraclass."Middle";
								$extrae = $extraclass."End";
								$extrafin = $extraclass."FadeIn";
								$extrafout = $extraclass."FadeOut";
								$extrahalf = $extraclass."HalfHour";

								$dclass = "middle$category $extram"; //class
								$txt = "&nbsp;"; //text
								$dcontent = ""; //content

								if ($this->group_id == NULL) {
									$oclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\")'"; //onclick
									$omover = "onmouseover='show(\"$spanid\"); eventOnMouseOver(\"$extraclass\", \"$type\")'"; //onmouseover
									$omout = "onmouseout='hide(\"$spanid\"); eventOnMouseOut(\"$extraclass\", \"$type\")'"; //onmouseout
								}

								/**************************************************************** 
								 Put the default values into the array.  These will be the values attached
								 to the intermediate div id's (the ones after the first div but before the
								 last div) for a particular event.								 
								 **************************************************************/
								$defaults = array ("text" => $txt, "content" => $dcontent, "onmouseover" => $omover, "onmouseout" => $omout, "onclick" => $oclick, "class" => $dclass);

								/********************************************************
								Now we need to build the actual starting and ending ids, so that
								we know where the event actually goes and can display the calendar
								accordingly.  The starting point is the hour the event starts in, 
								but modified slightly depending on when.  If the event starts between
								15 and 45 minutes after the hour, we don't want to round to the half hour to display
								the start time. Thus we designate a fade in, to visually show the user
								that the actual start time is variable.  The eventcount variable triggers
								between particular classes for the purposes of giving events different
								visaual styles depending on either type or looping through a set of colors
								 *******************************************************/
								$start_id = $idbase.$start_hour;
								$start_params = array ();

								if ($squarter == 1) {
									$start_id .= "00";
									$start_params["class"] = "start$category $extras";
								} else
									if ($squarter == 2) {
										$start_id .= "00";
										$start_params["class"] = "start$category fadeIn$category $extras $extrafin";
									} else
										if ($squarter == 3) {
											$start_postfix = "30";
											$start_id .= "30";
											$start_params["class"] = "start$category $extras";
										} else
											if ($squarter == 4) {
												$start_postfix = "30";
												$start_id .= "30";
												$start_params["class"] = "start$category fadeIn$category $extras $extrafin";
											}

								/*********************************************************
								Build the event id parameters for the first div.  We already have
								the correct class given the actual starting time.  The text is the
								event name, special content is the "delete button" and associated
								onclick functionality, as well as the onmouseover/out effects to
								hide and show that span.
									*********************************************************/

								$title_text = "<span class=\"title\" onclick='populateForm(\"$event_id\");'>".$event->getShortName()."</span>";
								if($this->group_id != NULL&&$this->group_id!="special")
								{
									$title_text = "";
									$delete = "";
									$event_onclick = "";
									$event_omover = "";
									$event_omout = "";
								}
								elseif($this->group_id == "special"){
										$title_text = "<span class=\"title\">".$event->getShortName()."</span>";
								}
								else
								{
									if ($type == 5) {
										$title_text = "<span class=\"title\">".$event->getShortName()."</span>";
										$delete = "";
										$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
										$event_omover = "onmouseover='eventOnMouseOver(\"$extraclass\", \"$type\")'";
										$event_omout = "onmouseout='eventOnMouseOut(\"$extraclass\", \"$type\")'";
									}
									elseif ($type == 4) {
										$creator_id = $db->getScalar("SELECT tblMeeting.person_id FROM tblMeeting, tblMeetingEvent WHERE tblMeeting.meeting_id = tblMeetingEvent.meeting_id AND tblMeetingEvent.event_id = $event_id");
										if ($_SESSION["person_id"] == $creator_id && $this->group_id == NULL) {
											$delete = "<span style='display: none;' id='$spanid' class='delete'><a href='javascript:delPopup(\"$spanid\", \"$delspan\");'>X</a></span><span id=\"$delspan\"></span>";
											$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
											$event_omover = $omover;
											$event_omout = $omout;
										} else {
											$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
											$delete = "";
											$event_omover = "onmouseover='eventOnMouseOver(\"$extraclass\", \"$type\")'";
											$event_omout = "onmouseout='eventOnMouseOut(\"$extraclass\", \"$type\")'";
										}
	
									} else {
										$delete = "<span style='display: none;' id='$spanid' class='delete'><a href='javascript:delPopup(\"$spanid\", \"$delspan\");'>X</a></span><span id=\"$delspan\"></span>";
										$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
										$event_omover = $omover;
										$event_omout = $omout;
									}
								}
								//$title_text = "Mins: ".($event->getEndDatetime() - $event->getStartDatetime());
								//if ($this->group_id == NULL) {
								if (ereg("fadeIn", $start_params["class"])) {
									$title_text = str_replace("title", "title titleSpan ", $title_text);
									$delete = str_replace("class='delete'", "class='delete titleSpan'", $delete);
								}
	
								$start_params["text"] = $title_text;
								$start_params["content"] = $delete;

								if ($this->group_id != NULL && $this->group_id!="special")
									$start_params["text"] = $txt;

								$start_params["onclick"] = $event_onclick;
								$start_params["onmouseover"] = $event_omover;
								$start_params["onmouseout"] = $event_omout;

								$defaults["onclick"] = $event_onclick;
								$defaults["onmouseover"] = $event_omover;
								$defaults["onmouseout"] = $event_omout;

								//echo "start: $start_id";

								/*********************************************************
								 Now perform the same set of operations for the ending div:
								 get the ending hour and quarter
								 *********************************************************/
								$end_hour = $event->getEndHour();
								$equarter = $event->getEndQuarter();

								//echo "$event_id ends during $equarter <br/>";
								//echo "$event_id ends at $end_hour";

								/** Build the ending id and keep track of the parameters **/
								$end_params = array ();
								$offset = 0;
								$end_id = $idbase.$end_hour;
								if ($equarter == 1) {

									//ugly case, if we're ending on the hour, we need to
									//rewind a bit and set the block to actually end in
									//the last block of the previous hour
									//this requires resetting the initial end_id that was 
									//created, as well as upping the offset for end times
									//to ensure that we still get the 1 hour of padding
									if ($event->getEndDatetime() % 3600 == 0) {
										$end_hour --;
										$end_id = $idbase.$end_hour;
										$end_id .= "30";
										$equarter = 4;
										$offset = 1;
										$end_params["class"] = "end$category $extrae";

									} else {
										$end_id .= "00";
										$end_params["class"] = "end$category fadeOut$category $extrae $extrafout";
									}

								} else
									if ($equarter == 2) {
										$end_id .= "00";
										$end_params["class"] = "end$category $extrae";
									} else
										if ($equarter == 3) {
											$end_id .= "30";
											$end_params["class"] = "end$category fadeOut$category $extrae $extrafout";
										} else
											if ($equarter == 4) {
												$end_id .= "30";
												$end_params["class"] = "end$category $extrae";
											}

								/** keep track of the latest we have to end the calendar **/
								if ($end_hour > $last_hour)
									$last_hour = $end_hour;

								/***********************************************************
								 put the parameters into an array and attach that array to the
								  primary list of ids
								 **********************************************************/

								//$end_params["text"] = $event->getEnd()." EQ: $equarter";
								$end_params["content"] = "$content";
								$end_params["onclick"] = $event_onclick;

								$end_params["onmouseover"] = $event_omover;
								$end_params["onmouseout"] = $event_omout;

								/***********************************************************
								Then add this array and its associated id to the primary  
								array of event associated divs.
								 *********************************************************/

								$half_hour = false;
								if (($event->getEndDatetime() - $event->getStartDatetime() <= 1800) && (($squarter == 1 && (($equarter == 1) || ($equarter == 2))) || ($squarter == 3 && (($equarter == 3) || ($equarter == 4)))))
									{
									if ($this->group_id == NULL||$this->group_id=="special")
										$whole["text"] = $event->getShortName();
									else
										$whole["text"] = "";

									$whole["content"] = $delete;
									$whole["onmouseover"] = $event_omover;
									$whole["onmouseout"] = $event_omout;
									$whole["onclick"] = $event_onclick;
									$whole["class"] = "halfHour$category $extrahalf";

									$ids_array[$start_id] = $whole;
									$half_hour = true;

								} else {

									if (!$this->group_id || ($this->group_id && (!isset ($ids_array[$end_id]) || !strpos($end_params["class"], "fadeOut") || strpos($ids_array[$end_id]["class"], "fadeOut"))))
										$ids_array[$end_id] = $end_params;

									if (!$this->group_id || ($this->group_id && (!isset ($ids_array[$start_id]) || !strpos($start_params["class"], "fadeIn") || strpos($ids_array[$start_id]["class"], "fadeIn"))))
										$ids_array[$start_id] = $start_params;

								}

								//echo "end $end_id";
								//build the intermediate-ids

								/***********************************************************
								Now that we have the start and end divs, we need to build
								a correct listing of the divs that fall between them.
								***********************************************************/

								/***********************************************************
								First of all determine whether or not we know when we're actually
								going to start and stop printing out hours.  If the envent ends in the 3rd
								or 4th quarter, or ends on the top of the hour, we need to make 
								sure that the hour buffer at the bottom of the calendar page is
								displayed correctly.
								***********************************************************/

								if (($equarter == 3) || ($equarter == 4)) {
									$offset ++;
								}

								if (($squarter == 3) || ($squarter == 4)) {
									$start = false;
								} else
									$start = true;

								/***********************************************************
								if the end time is evenly divisible by the number of secons in an hour
								then it starts on the hour.  This means that we're going to need to display
								the extra hour buffer.  We don't want to extend this to all of qarter 1 (right?)
								***********************************************************/
								//if (($event->getEndDatetime() % 3600) == 0)
								//$offset = 1;

								/***********************************************************
								Now lets actually build the ids of the divs that this event affects
								by looping between the start and end hours and flipping between the two
								possible end prefixes as necessary.
								***********************************************************/
								/*
								echo "$event_id starth: $start_hour endh: $end_hour<br/>";
								echo "$event_id start: $start_id startq: $squarter<br/>";
								echo "$event_id end: $end_id endq: $equarter<br/>";
								//echo $start." $start_postfix <br/>";
								if($start == false)
									echo "start is false <br/>";
								else
									echo "start is true<br/>";
									*/
								if (!$half_hour) {

									for ($begin = $start_hour; $begin <= $end_hour; $begin ++) {

										$idstr = ""; /** reset the current Id being built **/

										$idstr = $idbase.$begin; /** append the current hour in question **/
										if ($start) {
											$add = $idstr."00";

											if ($add !== $start_id) {

												if ($add !== $end_id) {
													$ids_array[$add] = $defaults; /** if its new, add it **/
												} else
													break;
											}
										}

										$add = $idstr."30"; /** make the other tag **/
										$start = true;
										if ($add !== $start_id) {
											if ($add !== $end_id) {
												$ids_array[$add] = $defaults; /** if its new, add it **/
												//echo "$add<br/>";
											} else
												break;
										}

									}
								}
								//echo "end $end_id <br/>";
							}
						}
					}

				}
			}
		} else {
			//echo "no people to worry about?<br/>";
		}

		/************************************************
		Now that we have built all the information for all of the events
		that need to be shown on this current calendar week, we can go ahead
		and actually spit out the calendar.  This starts by determining and
		printing out the list of hours on the left side.  This list is limited
		to one hour before the earliest start time and one hour after the latest
		end time.  In the case where there are no events during a particular week,
		the display defaults to showing the entire 24 hour period.  (Maybe we should move
		this to just the daylight hours?) 8-6pm.
		 *************************************************/

		if ($first_hour == 24) {
			$first_hour = 8;
			$hours_start_limit = 8;
			$halfs_start_limit = 8;
		} else {
			$hours_start_limit = $first_hour -1;
			$halfs_start_limit = $first_hour -1;
		}

		if ($last_hour == 0) {
			$last_hour = 16;
			$hours_end_limit = 18;
			$halfs_end_limit = 18;
		} else {
			$hours_end_limit = $last_hour +1 + $offset;
			$halfs_end_limit = $last_hour +1 + $offset;
		}

		/*
				echo "First: $first_hour Last: $last_hour Offset: $offset<br/>";
				echo "Hours start: $hours_start_limit  Hours end: $hours_end_limit<br/>";
				echo "Halfs start: $halfs_start_limit  Halfs end: $halfs_end_limit<br/>";
		*/
		/************************************************************************
		 if extending the end hours to accomdate for late-ending events has
		 caused us to overextend the available hours in a day, lets reset them to the bounds.
		 **********************************************************************/

		if ($hours_end_limit > 24)
			$hours_end_limit = 24;

		if ($halfs_end_limit > 24)
			$halfs_end_limit = 24;

		if ($hours_start_limit < 0) {
			$hours_start_limit = 0;
			$halfs_start_limit = 0;
		}

		/** Print out the hours based on the max and min limits **/

		$output .= "<div class=\"timeColHours\">";
		for ($i = $hours_start_limit; $i < $hours_end_limit; $i ++) {
			if ($i < 12)
				$time = "<span>AM</span>";
			else
				if ($i > 12)
					$time = "<span>PM</span>";
				else
					$time = "";

			if ($i == 0) {
				$output .= "<div class=\"time\">12:00 AM</div>";
			} else
				if ($i == 12) {
					$output .= "<div class=\"time\">12:00 PM</div>";
				} else {
					$hour = $i % 12;
					$output .= "<div class=\"time\">$hour:00 ".$time."</div>";
				}

		}
		$output .= "</div>";

		/*****************************************************
		Now we have to go through all of the available days and print out all of the divs associated
		with the (limited) hours we displayed in the previous function.
		 *****************************************************/

		for ($days = 0; $days < 7; $days ++) {
			$output .= "<div id=\"day_$days\"class=\"timeColEvents\">";

			for ($halfs = $halfs_start_limit; $halfs < $halfs_end_limit; $halfs ++) {

				/** Shade the divs based on whether they are daylight or not **/

				if ($halfs < 8 || $halfs > 17) {
					$shaded = "shaded";
				} else {
					$shaded = "";
				}

				$now = getdate();
				$mins = $now['minutes'];
				$hours = $now['hours'];	

				/*********************************************************
				 Here's where all the work pays off.  If we encounter a div for which
				 we have generated and stored display information, we have to fetch that
				 information from the array and use it to build the div that we are display.
				 If not, then just display a standard tagged div without the necessary
				 style or interaction information.
				 ********************************************************/

				$id = $days."_".$halfs."00";
				if(($mins >= 0 && $mins < 30) && $hours == $halfs && $current_week_visible)
					$current = "current_time";
				else
					$current = "";


				if (!empty ($ids_array[$id])) {
					$class = $ids_array[$id]["class"];
					$text = $ids_array[$id]["text"];
					$content = $ids_array[$id]["content"];
					$onclick = $ids_array[$id]["onclick"];
					$onmouseover = $ids_array[$id]["onmouseover"];
					$onmouseout = $ids_array[$id]["onmouseout"];
					
					//check to see if event is fading out and add bottom border
					if (ereg("fadeIn", $class)) {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $shaded $current\" style=\"margin-top: 0px\">$text $content</div>";
					}
					elseif (ereg("fadeOut", $class)) {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $shaded $current\" style=\"border-bottom: 1px #d8deeb solid\">$text $content</div>";
					} else {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $current\">$text $content</div>";
					}

				} else {
					   
					$output .= "<div id=\"$id\" class=\"half $shaded $current\">&nbsp;</div>";
				}

				/** do the same thing for the 30-59 minute div **/
				if(($mins >= 30 && $mins <= 59 ) && $hours == $halfs && $current_week_visible)
					$current = "current_time";
				else
					$current = "";

				$id = $days."_".$halfs."30";
				if (!empty ($ids_array[$id])) {
					$class = $ids_array[$id]["class"];
					$text = $ids_array[$id]["text"];
					$content = $ids_array[$id]["content"];
					$onclick = $ids_array[$id]["onclick"];
					$onmouseover = $ids_array[$id]["onmouseover"];
					$onmouseout = $ids_array[$id]["onmouseout"];

					if (ereg("fadeIn", $class)) {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $shaded $current\" style=\"margin-top: 0px\">$text $content</div>";
					}
					elseif (ereg("fadeOut", $class)) {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $shaded $current\" style=\"border-bottom: 1px #d8deeb solid\">$text $content</div>";
					} else {
						$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $current\">$text $content</div>";
					}

				} else {

					$output .= "<div id=\"$id\" class=\"hour $shaded $current\">&nbsp;</div>";
				}

			}
			$output .= "</div>";
		}
		/** DISPLAY THE OUTPUT! **/

		//return $output."<div class=\"clear\"></div>";
		$clear = HTMLHelper::insertSpacer();
		return $output.$clear;
	}

	function Display2()
	{

		/** Print the calendar header **/

		$output .= $this->printWeekHeader();
		if (ereg('class="today"',$output))
			$current_week_visible = true;
		else
			$current_week_visible = false;
		
		
		
		
		/*******************************************************
		set up default values for the earliest and latest hours we have to display
		we set them to 24 and 0 initially so that any hour will be earlier/later
		and thus we can move the marker in the correct direction.  As we encounter earlier
		start times we can continue to move the start time closer to 0, and as we run into 
		later end times we can move the other counter towards 24
		**********************************************************/
		$first_hour = 24;
		$last_hour = 0;

		/*****************************************************
		This is the array which is going to be used to hold
		The IDs of the divs that have events within them.  The values to these
		ID keys will be another associative array containing references
		to all of the specific parameters we will need to print out
		This way adding a parameter to a div will be as easy as pushing it 
		into the internal array for the DIV and then reading it back out on display
		**********************************************************/

		$ids_array = array ();
		if (!empty ($this->person_ids)) {

			if ($this->group_id != NULL)
				$category = "Group";

			foreach ($this->person_ids as $id) {
				$db = new DB();

				//echo "<br/> getting events for person_id $id";
				//if ($events = $db->getArray("SELECT event_id FROM tblPersonEvent WHERE person_id = $id")) {
				if ($events = $db->getArray("SELECT event_id FROM tblPersonEvent WHERE person_id = $id")) {
					//keeps track of how many types of an event there are
					$classcount = 0;
					$workcount = 0;
					$personalcount = 0;
					$meetingcount = 0;
					$tempcount = 0;
					//echo "person $person_id has these events:<br/>";
					foreach ($events as $event_id) {
						//echo "$event_id for person $person_id<br/>";
						/*******************************************************
						for right now the colors are simply cycled through.  
						in this space we can use the type of the event and set
						the eventcount accordingly to trigger the right style
						presumably we can also just set the eventcount to a default
						value for when we want all of the evnents to be the same color
						for the group calendar 
						**********************************************************/

						/** construct the event in question. **/
						$event = new Event($event_id);
						//$event->toprint();
						$type = $event->getTypeID();

						if ($this->group_id == NULL||$this->group_id == "special") {
							if ($type == 1) {
								$classcount ++;
								$category = "Class";
							}
							elseif ($type == 2) {
								$workcount ++;
								$category = "Work";
							}
							elseif ($type == 3) {
								$personalcount ++;
								$category = "Personal";
							}
							elseif ($type == 4 || $type == 6) {
								$meetingcount ++;
								$category = "Meeting";
							}
							elseif ($type == 5) {
								$tempcount ++;
								$category = "TempMeeting";
							}
						}

						//echo "<br/> getting event dates for event $event_id <br/>";

						/*******************************************************
						User the event object to build a list of the times it is visible, given
						the start and end times of the currently displayed week
						**********************************************************/

						$visible = $event->getDates($this->display_start, $this->display_end);

						//dump($visible);
						$eventStart = $this->display_start;
						if (!empty ($visible)) {
							//echo "event_id is $event_id occurs on <br/>";
							//dump($visible);
							foreach ($visible as $occur) {
								//echo $occur;
								/** get information regarding the period during which the event occurs **/
								$temp = getdate($occur);

								/** figure out during which hour the event starts **/
								$start_hour = $event->getStartHour();
								$end_hour = $event->getEndHour();
								/************************************************
								  figure out on which quarter hour the event starts
								  this will be used to do fadeins/outs on event times,
								  so that we're not always rounding to the nearest half hour
								*************************************************************/

								

								//echo "$event_id starts during $squarter <br/>";

								/** keep track of the earliest we have to start the calendar **/
								if ($start_hour < $first_hour)
									$first_hour = $start_hour;

								if ($end_hour > $last_hour)
									$last_hour = $end_hour;
									
								/****************************************************************
								  begin building the list of ids that we're going to have to use
								first unique param on the id is the day number followed by an underscore
								**************************************************************/

								$daynum = $temp["wday"];
								$idbase = $daynum."_";
								$idbase .= $start_hour;
								$squarter = $event->getStartQuarter();
								
								
								if ($squarter == 1 || $squarter == 2) {
									$div_id = $idbase."00";
								} elseif ($squarter == 3 || $squarter == 4) {
									$div_id = $idbase."30";
								}

								/**********************************************
									build the id of the span for the delete icon
									of a particular event, this must be particular
									to the day that the event is being shown on
									in order to distinguish one particular instance
									of an event from another, and so that the X only
									shows up on one when we hover on it
								****************************************************/

								$spanid = "del_$event_id"."_".$this->display_start."_".$daynum;
								$delspan = $event_id."_$daynum";

								/****************************************************
								Now we have to build the parameters that each particular
								hourly/half-hourly div is going to be established with depending
								on the event that fall on it.
								
								To do this we will build an associative array of parameters
								params have the following fields:
								 class -> class describing the background image/color to be CSS'd onto the div
								 text -> the text to be within the div, if none then &nbsp;
								 content -> other content that needs to be go into the div	("delete button")						
								 onclick -> the onclick js
								 onmouseover -> the onmouseover js
								 onmouseout -> the onmouseout js 
								
								Now lets's build the default values for this particular event
								**************************************************************/
								if ($type == 1) {
									$extraclass = $category.$classcount.$daynum;
								}
								elseif ($type == 2) {
									$extraclass = $category.$workcount.$daynum;
								}
								elseif ($type == 3) {
									$extraclass = $category.$personalcount.$daynum;
								}
								elseif ($type == 4 || $type == 6) {
									$extraclass = $category.$meetingcount.$daynum;
								}
								elseif ($type == 5) {
									$extraclass = $category.$tempcount.$daynum;
								}
						
								$txt = "&nbsp;"; //text
								$dcontent = ""; //content

								if ($this->group_id == NULL) {
									$oclick = "onclick='populateForm(\"$event_id\"); eventOnClick2(\"$extraclass\", \"$type\")'"; //onclick
									$omover = "onmouseover='show(\"$spanid\"); eventOnMouseOver2(\"$extraclass\", \"$type\")'"; //onmouseover
									$omout = "onmouseout='hide(\"$spanid\"); eventOnMouseOut2(\"$extraclass\", \"$type\")'"; //onmouseout
								}
								
								
								//currently, a half hour block is 17 pixels tall, plus 1 px bordering.
								//meaning that each minute of time is represented by (30/18) or 1 and 2/3'rds pixels
								//taking the running time of the event, and dividing by 1 and 2/3'rds gives us the
								//height the event needs to be to represent its real length in time.
								$height_val = round(($event->getStartDatetime() - $event->getEndDatetime() / (1+(2/3))));
								
								//figure out how much off the nearerst half hour we're going to have to offset the top or start of the event
								//we're going to round this to the half hour even throgh that means we need to keep the logic to determine
								//which half hour the event technically "starts" in.  Having only hour granularity would probably
								//lead to complication with counting the number of currently "active" events, and thus the width 
								//percentages and layering logic.
								$offset_val = round(($event->getStartDateTime() % 1800) / (1+(2/3)));
	
								/**************************************************************** 
								 Put the default values into the array.  These will be the values attached
								 to the intermediate div id's (the ones after the first div but before the
								 last div) for a particular event.								 
								 **************************************************************/
								 
								$defaults = array ("text" => $txt, "content" => $dcontent, "onmouseover" => $omover, "onmouseout" => $omout, "onclick" => $oclick, "class" => $extraclass, "height" => $height_val, "offset" => $offset_val);

								/*********************************************************
								Build the event id parameters for the first div.  We already have
								the correct class given the actual starting time.  The text is the
								event name, special content is the "delete button" and associated
								onclick functionality, as well as the onmouseover/out effects to
								hide and show that span.
									*********************************************************/

								$title_text = "<span class=\"title\" onclick='populateForm(\"$event_id\");'>".$event->getShortName()."</span>";
								if($this->group_id != NULL&&$this->group_id!="special")
								{
									$title_text = "";
									$delete = "";
									$event_onclick = "";
									$event_omover = "";
									$event_omout = "";
								}
								elseif($this->group_id == "special"){
									$title_text = "<span class=\"title\">".$event->getShortName()."</span>";
								}
								else
								{
									if ($type == 5) {
										$title_text = "<span class=\"title\">".$event->getShortName()."</span>";
										$delete = "";
										$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick2(\"$extraclass\", \"$type\");'";
										$event_omover = "onmouseover='eventOnMouseOver2(\"$extraclass\", \"$type\")'";
										$event_omout = "onmouseout='eventOnMouseOut2(\"$extraclass\", \"$type\")'";
									}
									elseif ($type == 4 || $type == 6) {
										$creator_id = $db->getScalar("SELECT tblMeeting.person_id FROM tblMeeting, tblMeetingEvent WHERE tblMeeting.meeting_id = tblMeetingEvent.meeting_id AND tblMeetingEvent.event_id = $event_id");
										if ($_SESSION["person_id"] == $creator_id && $this->group_id == NULL) {
											$delete = "<span style='display: none;' id='$spanid' class='delete'><a href='javascript:delPopup(\"$spanid\", \"$delspan\");'>X</a></span><span id=\"$delspan\"></span>";
											$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
											$event_omover = $omover;
											$event_omout = $omout;
										} else {
											$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick(\"$extraclass\", \"$type\");'";
											$delete = "";
											$event_omover = "onmouseover='eventOnMouseOver2(\"$extraclass\", \"$type\")'";
											$event_omout = "onmouseout='eventOnMouseOut2(\"$extraclass\", \"$type\")'";
										}
	
									} else {
										$delete = "<span style='display: none;' id='$spanid' class='delete'><a href='javascript:delPopup(\"$spanid\", \"$delspan\");'>X</a></span><span id=\"$delspan\"></span>";
										$event_onclick = "onclick='populateForm(\"$event_id\"); eventOnClick2(\"$extraclass\", \"$type\");'";
										$event_omover = $omover;
										$event_omout = $omout;
									}
								}
	
								$defaults["text"] = $title_text;
								$defaults["content"] = $delete;

								if ($this->group_id != NULL && $this->group_id!="special")
									$defaults["text"] = $txt;
								
								$defaults["onclick"] = $event_onclick;
								$defaults["onmouseover"] = $event_omover;
								$defaults["onmouseout"] = $event_omout;

								$ids_array[$div_id] = $defaults;
							}//end occurrences loop
						}//end visible occurrences check
					}//end events loop
				}//end event check for this person
			}//end of person loop
		} //end if people selection

		/************************************************
		Now that we have built all the information for all of the events
		that need to be shown on this current calendar week, we can go ahead
		and actually spit out the calendar.  This starts by determining and
		printing out the list of hours on the left side.  This list is limited
		to one hour before the earliest start time and one hour after the latest
		end time.  In the case where there are no events during a particular week,
		the display defaults to showing the entire 24 hour period.  (Maybe we should move
		this to just the daylight hours?) 8-6pm.
		 *************************************************/

		if ($first_hour == 24) {
			$first_hour = 8;
			$hours_start_limit = 8;
			$halfs_start_limit = 8;
		} else {
			$hours_start_limit = $first_hour -1;
			$halfs_start_limit = $first_hour -1;
		}

		if ($last_hour == 0) {
			$last_hour = 16;
			$hours_end_limit = 18;
			$halfs_end_limit = 18;
		} else {
			$hours_end_limit = $last_hour +1;
			$halfs_end_limit = $last_hour +1;
		}

		/************************************************************************
		 if extending the end hours to accomdate for late-ending events has
		 caused us to overextend the available hours in a day, lets reset them to the bounds.
		 **********************************************************************/

		if ($hours_end_limit > 24)
			$hours_end_limit = 24;

		if ($halfs_end_limit > 24)
			$halfs_end_limit = 24;

		if ($hours_start_limit < 0) {
			$hours_start_limit = 0;
			$halfs_start_limit = 0;
		}

		/** Print out the hours based on the max and min limits **/

		$output .= "<div class=\"timeColHours\">";
		for ($i = $hours_start_limit; $i < $hours_end_limit; $i ++) {
			if ($i < 12)
				$time = "<span>AM</span>";
			else
				if ($i > 12)
					$time = "<span>PM</span>";
				else
					$time = "";

			if ($i == 0) {
				$output .= "<div class=\"time\">12:00 AM</div>";
			} else
				if ($i == 12) {
					$output .= "<div class=\"time\">12:00 PM</div>";
				} else {
					$hour = $i % 12;
					$output .= "<div class=\"time\">$hour:00 ".$time."</div>";
				}

		}
		$output .= "</div>";

		/*****************************************************
		Now we have to go through all of the available days and print out all of the divs associated
		with the (limited) hours we displayed in the previous function.
		 *****************************************************/

		for ($days = 0; $days < 7; $days ++) {
			$output .= "<div id=\"day_$days\"class=\"timeColEvents\">";
			$zindex = 1;
			$percentage = 100;
			for ($halfs = $halfs_start_limit; $halfs < $halfs_end_limit; $halfs ++) {

				/** Shade the divs based on whether they are daylight or not **/

				if ($halfs < 8 || $halfs > 17) {
					$shaded = "shaded";
				} else {
					$shaded = "";
				}

				$now = getdate();
				$mins = $now['minutes'];
				$hours = $now['hours'];	

				/*********************************************************
				 Here's where all the work pays off.  If we encounter a div for which
				 we have generated and stored display information, we have to fetch that
				 information from the array and use it to build the div that we are display.
				 If not, then just display a standard tagged div without the necessary
				 style or interaction information.
				 ********************************************************/

				$id = $days."_".$halfs."00";
				if(($mins >= 0 && $mins < 30) && $hours == $halfs && $current_week_visible)
					$current = "current_time";
				else
					$current = "";

				$output .= "<div id=\"$id\" class=\"half $shaded $current\">&nbsp;</div>";
				
				if (!empty ($ids_array[$id])) {
					$class = $ids_array[$id]["class"];
					$text = $ids_array[$id]["text"];
					$content = $ids_array[$id]["content"];
					$onclick = $ids_array[$id]["onclick"];
					$onmouseover = $ids_array[$id]["onmouseover"];
					$onmouseout = $ids_array[$id]["onmouseout"];
					$height = $ids_array[$id]["height"]."px;";
					$offset = $ids_array[$id]["offset"]."px;";
					$zindex = $zindex++;
					$width = ($percentage-($zindex*10))."px;";
					
					$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $current\"  style=position: relative; top: $offset; height: $height; z-index: $zindex; width: $width;'>$text $content</div>";

				}
				
				/** do the same thing for the 30-59 minute div **/
				if(($mins >= 30 && $mins <= 59 ) && $hours == $halfs && $current_week_visible)
					$current = "current_time";
				else
					$current = "";

				$id = $days."_".$halfs."30";
				
				$output .= "<div id=\"$id\" class=\"hour $shaded $current\">&nbsp;</div>";

				if (!empty ($ids_array[$id])) {
					$class = $ids_array[$id]["class"];
					$text = $ids_array[$id]["text"];
					$content = $ids_array[$id]["content"];
					$onclick = $ids_array[$id]["onclick"];
					$onmouseover = $ids_array[$id]["onmouseover"];
					$onmouseout = $ids_array[$id]["onmouseout"];
					$height = $ids_array[$id]["height"]."px;";
					$offset = $ids_array[$id]["offset"]."px;";
					$zindex = $zindex++;
					$width = ($percentage-($zindex*10))."px;";
					
					$output .= "<div id=\"$id\" $onclick $onmouseover $onmouseout class=\"$class $current\"  style=position: relative; top: $offset; height: $height; z-index: $zindex; width: $width;'>$text $content</div>";

				}

			}
			$output .= "</div>";
		}
		/** DISPLAY THE OUTPUT! **/
		$clear = HTMLHelper::insertSpacer();
		return $output.$clear;
	}

	public function memberSelector() {
		
		if ($this->group_id) {
			$group = new Group($this->group_id);
			
			$widget = "<div id=\"memberListBox\">";
			$widget .= "<ul id=\"memberList\" class=\"plain\">";
			foreach ($group->getPersonArray() as $member => $conf) {
				if ($conf) {
					$person = new Person($member);
					$first_name = $person->getFirstName();
					$last_name = $person->getLastName();
	
					if (in_array($member, $this->person_ids))
						$selected = "<img src=\"images/check.gif\">";
					else
						$selected = "&nbsp;";
	
					$time = $this->display_start;
	
					$widget .= "<li><span id =\"$member\" class=\"fakeCheckbox\" onclick='javascript:changeUsers(this);' onmouseover=\"this.style.background='url(images/checkHover.gif) no-repeat top center'\" onmouseout=\"this.style.background='url(images/checkBG.gif) no-repeat top center'\">$selected</span> $first_name $last_name</li>";
				}
			}
			/** Add "Select All" Checkbox **/
			$widget .= "<li><span id =\"selectAllCheckbox\" class=\"fakeCheckbox\" onclick='javascript:selectAllMembers(this);' onmouseover=\"this.style.background='url(images/checkHover.gif) no-repeat top center'\" onmouseout=\"this.style.background='url(images/checkBG.gif) no-repeat top center'\"> </span> Select All</li>";
			$widget .= "</ul>";			
			$widget .= "</div><br />";			
		}
		else {
			$widget = "";	
		}
		
		return $widget;

	}

	/* This function is used to set the visible week. 								*/
	/* It does so by determining what day of the week the selected date is			*/
	/* and then setting the display start time to 1 second after midnight on 		*/
	/* the preceeding Sunday, and the end of display time to one second before 		*/
	/* midnight on the following Saturday.  We use the times one second before and  */
	/* after in order to avoid some strange behavor surrounding midnight under 		*/
	/* certain conditions.  Thus events set to end at midnight will end at 1 second */
	/* before midnight, and those events set to "start" at midnight on a given day  */
	/* will actually be set to start 1 second after midnight on the following day.	*/
	//"$day": value should be the number day of the month (i.e. 22)
	//"$month": number of the current month, where January = 1 and December = 1
	//"$year": current 4 digit year (2006)

	function setVisibleWeek($month, $day, $year) {

		$timestamp = mktime(0, 0, 0, $month, $day, $year);
		$date = getdate($timestamp);
		$startDay = $date['wday'];
		$this->display_start = mktime(0, 0, 0, $month, $day - $startDay, $year);
		$this->display_end = mktime(23, 59, 59, $month, $day + (6 - $startDay), $year);
	}

	function setVisibleWeekStarting($timestamp) {
		$date = getdate($timestamp);

		$startDay = $date['wday'];
		$month = $date['mon'];
		$day = $date['mday'];
		$year = $date['year'];
		$this->display_start = mktime(0, 0, 0, $month, $day - $startDay, $year);
		$this->display_end = mktime(23, 59, 59, $month, $day + (6 - $startDay), $year);
	}

	//This function takes an array of person_id's and sets the internal 

	//person_ids array to the same

	function setPeople($people_ids) {
		$this->person_ids = array ();
		foreach ($people_ids as $person_id)
			$this->addPerson($person_id);
	}

	//This function will take a single 

	function addPerson($person_id) {
		if (!in_array($person_id, array_values($this->person_ids)) && $person_id != "" && $person_id != NULL) {
			//echo "adding $person_id <br/>";
			array_push($this->person_ids, trim($person_id));
		}
	}

	function removePerson($person_id) {
		unset ($this->person_ids[$person_id]);
		//reset the indicies so the array doesn't grow?
		//$this->person_ids = array_values($this->person_ids);
	}

	function switchGroupTo($group_id) {
		$this->group_id = $group_id;

		$db = new DB();
		if ($person_list = $db->getArray("SELECT tblPersonGroup.person_id FROM tblPersonGroup, tblGroup WHERE tblPersonGroup.group_id = $group_id AND tblPersonGroup.group_id = tblGroup.group_id AND tblPersonGroup.confirmed = 1")) {
			$this->setPeople($person_list);
		} else {
			//echo "group $group_id has no members?<br/>$qry";
		}
	}

}
?>