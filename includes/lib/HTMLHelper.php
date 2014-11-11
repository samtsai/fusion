<?php

class HTMLHelper {

    public static function insertSpacer() {
        
		$output = "<div class=\"spacer\">&nbsp;</div>";
		
		return $output;
		
    }
       
    public static function insertLine($style = "") {

    	if ($style != "") {
    		$style = "style =\"$style\"";	
    	}
    	
    	$output = "<div class=\"line\" $style>&nbsp;</div>";
    	
    	return $output;	
    }
    
    public static function checkVisibility($id) {
    	
    	if (empty($id)) {
    		return "";	
    	}
    	
		$pieces = explode("|",$id);

		if (isset($pieces[1])) {
			if ($pieces[1] == 0) {
				$id = $pieces[0];				
				return "id=\"$id\" style=\"display: none;\"";
			}
			else {
				$id = $pieces[0];
				return "id=\"$id\"";	
			}	
		}
		else {
			return "id=\"$id\"";	
		}
		    	
    }
    
	public static function startDiv($id = "", $title = "", $class = "") {
		
    	$id = HTMLHelper::checkVisibility($id);
    				
		if ($class) {
			$class = "class = \"$class\"";	
		}		
		
		$output = "<div $id $class>";
		
		if ($title) {
			$output .= "<h3 class=\"title\">$title</h3>";
		}
		
		return $output;
	}
	
    public static function startCollapsibleDiv($name="myDiv", $title="", $class="collapsibleDiv") {
               
        $output = "<div id=\"collapseHeader\">";
        //echo "<a onclick=\"hideShow('$name');\" title=\"Collapse Menu\">Hide :: Show</a>";
        $output .= "<div id=\"plusMinus\" onclick=\"switchSign('plusMinus');hideShow('$name');\">-</div>";
        $output .= $title;        
        $output .= "</div>";
        $output .= "<div class=\"$class\" id=\"$name\">";
        
        return $output;
    }
    
    public static function endDiv() {
        
        $output = "</div>";
        
        return $output;
    }
    
    public static function startPanel($name = "", $id = "panel", $extraHeader = "") {
    	
    	$output = "<div class=\"floatLeft\">";
    	
    	if (!empty($extraHeader))
    		$output .= $extraHeader;
    	
    	if (!empty($name))
			$output .= "<div id=\"$name\"></div>";
			
		if (!empty($id))
			$output .= "<div id=\"$id\">";
		
		return $output;
    }    
    
    public static function endPanel($name = "panelBottom") {
    	$output = "</div>";
    	if (!empty($name))
			$output .= "<div id=\"$name\"></div>";
		$output .= "</div>";
		
		return $output;
    }
    
    public static function startWidget ($name = "", $cal = 0) {
    	
    	if (!empty($name))
    		$output = "<div id=\"$name\"></div>";
    	
    	if ($cal)
			$output .= "<div id=\"calWidgetBox\">";
		else
			$output .= "<div class=\"widgetBox\">";
						
		$output .= "<div class=\"widgetContent\">";	
		
		return $output;
    }
    
    public static function endWidget ($cal = 0) {
    	$output = "</div>";
    	$output .= "</div>";
		if ($cal)
			$output .= "<div id=\"calWidgetBottom\"></div>";
		else
			$output .= "<div class=\"widgetBottom\"></div>";
		
		return $output;
    }
    
    /*****************************************
    Methods for Tables    
    ******************************************/
    public static function startTable($id="", $class="") {    
    	
    	$id = HTMLHelper::checkVisibility($id);
    	    		
    	if ($class)
    		$class = "class=\"$class\"";	    
    	
        $output = "<table cellspacing=\"0\" cellpadding=\"0\" $id $class>";
        
        return $output;
    }
    
    public static function endTable() {
    	
    	$output = "</table>";
    	
    	return $output;
    }
         
    public static function startTR($emptyTD = 0) {     	        	
        
        $output = "<tr>";
        if ($emptyTD) {
        	$output .= HTMLHelper::insertEmptyTD();
        }
        
        return $output;
    }
    
    public static function endTR() {
        
        $output = "</tr>";
        
        return $output;
    }
    
    public static function startTD($align="left",$colspan=1,$valign="middle") {     
        
        $output = "<td colspan=\"$colspan\" valign=\"$valign\" align=\"$align\">";
        
        return $output;
    }
    
    public static function endTD() {
    	
    	$output = "</td>";
    	
    	return $output;	
    }
          
	public static function startNestedTable($id="", $emptyTD = 1, $class="") {

    	$id = HTMLHelper::checkVisibility($id);
			
    	if ($class)
    		$class = "class=\"$class\"";	
    			
		$output = "<tr>";

		if ($emptyTD)
			$output .= HTMLHelper::insertEmptyTD();
		    	    	
		// colspan should = 0
		$output .= "<td colspan=\"5\">";
		$output .= "<table cellspacing=\"0\" cellpadding=\"0\" $id $class>";
		
		return $output;

	}

	public static function endNestedTable() {
		$output = "</table>";
		$output .= "</td>";
		$output .= "</tr>";
		
		return $output;
	}
	
	public static function insertEmptyRow() {
		
		// for now until we can fix the colspan=0, i'll make the colspan larger than the form
		$output = "<tr><td colspan=\"5\">&nbsp;</td></tr>";
		
		return $output;
	} 
	
	public static function insertEmptyTD() {
		
		$output = "<td>&nbsp;</td>";
		
		return $output;	
	}
	
	/**************************************************
	Name: 	Insert Description 
	Desc:	Inserts a row of text
	
	@param  string[description]	Description
	@param	string[class]		Class
	@return   void
	
	****************************************************/
	public static function insertDescription($description, $class = "", $displayType=1) {
		
		if ($displayType == 1) {
			$output = "<div class=\"description $class\">$description</div>";	
		}
		else {
			if ($class) {
				$class = "class=\"$class\"";	
			}			
			// colspan should be 0	
			$output = "<tr><td>&nbsp;</td>
			      <td colspan=\"5\" $class>$description</td>
			      </tr>";		      
		}
	
		return $output;
	}
	
	public static function insertTitle($title,$displayType=1,$class="") {
				
		if ($displayType == 1) {			
			$output = "<div class=\"title $class\">$title</div>";
		}
		else {
			// for now until we can fix the colspan=0, i'll make the colspan larger than the form
			$output = "<tr><td colspan=\"5\" class=\"title $class\">$title</td></tr>";
		}
		
		return $output;
	}
		
	public static function insertSmallTitle($name) {
		
		$output = "<span class=\"smallTitle\">$name</span>";
		
		return $output;
	}
	
	/**************************************************
	Name: 	Insert Submit Button
	Desc:	Inserts a submit button somewhere before the form is closed 
			and is not the final submit button
	
	@param  string[name]	Name of the submit button (DEFAULT: Submit)
	@param	array[object]	Object array that has data for inserting another element
	@return   void
	
	****************************************************/
	public static function insertSubmitButton($name = "Submit", $id = "", $values = "Submit", $displayType = 1, $align = "left", $class = "submitForm", $onclick="") {

		$input = HTMLHelper::insertButton($name,$id,$values,$onclick,1);
		
		if ($displayType == 1) {
			$output = "<div class=\"$class\">";
			$output .= $input;
			$output .= "</div>";
		}
		else {
			$output = "
			            <tr>
			                <td>&nbsp;</td>
			                <td colspan=\"5\" align=\"$align\">
			                $input
			                </td>		
			            </tr>";
		}        
		
		return $output;
	}		
		
	public static function insertButton($name, $id = "", $values, $onclick="",$submit=0) {
			
		if (!empty($onclick)) {
			$onclick = "onclick=\"$onclick\"";	
		}
		
		if ($submit) {
			$type = "submit";	
		}
		else {
			$type = "button";
		}
		
		if ($id) {
			$id = "id=\"$id\"";	
		}	
		
		if (is_array($values)) {
			foreach($values as $value) {
				if (strlen($value) > 9) {
					$class = "inputSubmitWide";
				}
				else {
					$class = "inputSubmit";	
				}
				
				$output .= "<input type=\"$type\" $id $onclick name=\"$name\" class=\"$class\" value=\"$value\" />";	
			}				
		}
		else {
			if (strlen($values) > 9) {
				$class = "inputSubmitWide";
			}
			else {
				$class = "inputSubmit";	
			}			
			$output = "<input type=\"$type\" $id $onclick name=\"$name\" class=\"$class\" value=\"$values\" />";	
		}		
		
		return $output;
		
	}
	
	public static function startFieldset($legend="",$id,$collapse=0,$nestedTable=0,$class="") {

		if ($collapse == 1) {
			$output = HTMLHelper :: startCollapsibleDiv("createGroup", $legend);
		}
			
		if ($class) {
			$class = "class=\"$class\"";	
		}		
				
		$output .= "<fieldset id=\"$id\" $class>";
		
		if ($collapse != 1)
			$output .= "<legend>$legend</legend>";

		if ($nestedTable == 1) {
			$output .= HTMLHelper :: startTable("");
		}
		
		return $output;
	}

	public static function endFieldset($collapse = 0, $nestedTable = 0) {

		if ($nestedTable == 1) {
			$output = HTMLHelper :: endTable();
		}

		$output .= "</fieldset>";

		if ($collapse == 1) {
			$output .= HTMLHelper :: endDiv();
		}
		
		return $output;
		
	}		
	             
}