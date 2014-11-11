<?php    

    class Validate {
    
        public static function isValid($type, $value) {
			if (empty($value) && !is_numeric($value)) {
				return FALSE;	
			}

            switch ($type) {
                case "name":
                    $pattern = "^[a-z][a-z,. '-]*$"; 
                break;
                case "address":
                    $pattern = "^[a-z0-9][a-z0-9#\&\'-,. ]*";
                break;
                /** implement +4 later */
                case "zip":
                    $pattern = "^[0-9]{5}$";
                break;
                case "phone":
                    /** strip the phone number of all dashes, spaces, or parenthesis */
                    $stuff = array ("-", "(", ")", " ", ".");
                    $value = str_replace($stuff, '', $value);
                    $pattern = "^[2-9][0-9]{9}$";
                break;
                case "email":
                    $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
                break;
                case "city":
                    $pattern = "^[a-z][a-z.\'-, ]*$";
                break;                 
                case "password":
                    $pattern = "^.{1,}";
                break;
                case "state": 
                    $pattern = "^[a-z]{2}$";
                break;          
                case "text":
                    $pattern = "[:alnum:]*[:space:]*[:punct:]*[#\&\'@*^$%-,.]*";
                break;            
                case "posinteger":
                    //$pattern = "^[1-9]+[0-9]*";
                    $pattern = "^[0-9]+$";
                break;
                case "creditcard":
                    $pattern = "^[0-9]{16}$";
                break;
                case "money":
                    $pattern = "^\$?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$";
                break;
                case "course":
                    $stuff = array ("-", "(", ")", " ", ".");
                    $value = str_replace($stuff, '', $value);
                	$pattern = "^[0-9]{5}[A-Za-z]?[1-4]?$";
                break;
                case "andrew":
                   return (false !== LDAPwrapper($value));         
                case "aim":
                    $pattern = "^[a-z0-9][a-z0-9 ]+$";
                break;
                case "dropdown":                	
                	if ((is_numeric($value) || is_string($value)) && (!empty($value) || $value >= 0)) {
                    	return TRUE;
                	}
                	else {
                		return FALSE;
                	}
                case "radio":
                	return TRUE;                
                case "checkbox":
                    return TRUE;
                case "LDAP":
					echo LDAPwrapper($value);
					return true;
				case "date":
					$pieces = explode ("/",$value);
					$month = $pieces[0];
					$day = $pieces[1];
					$year = $pieces[2];                                      
                    if(!is_numeric($month) || !is_numeric($day) || !is_numeric($year)) {
                    	/*echo "something wasn't a number<br/>";
                    	echo "Month: $month ".is_numeric($month)."<br/>";
                    	echo "Day: $day ".is_numeric($day)."<br/>";
                    	echo "Year: $year ".is_numeric($year)."<br/>";*/
                    	return FALSE;	
                    }
                    elseif ($month > 12 || $month < 1 || $day > 31 || $day < 1) {
                    	echo "some number out of range";
                    	return FALSE;	
                    }
                    else {
                    	$time = mktime(0,0,0,$month,$day,$year);
                    	$timeInfo = getdate($time);
                    	$testMonth = $timeInfo["mon"];
                    	if ($testMonth != $month) {
                    		echo "that day doesn't occur during that month";
                    		return FALSE;
                    	}	
                    }                      			
					return TRUE;
				case "futureDate":
					return Validate::isFutureDateTime($value);
				case "dropdownTextbox":
					/** $values (dropdown, textbox) */
					if (!empty($value[0]) && !empty($value[1])) {
						return TRUE;	
					}
					elseif (empty($value[0]) && empty($value[1])) {
						return TRUE;	
					}
					else {
						return FALSE;	
					}										
                default:
            }
            
            return eregi($pattern, $value);
        }
        
        // -----------------------
        // FUNCTION: IS VALID FUTURE DATETIME
        
        public static function isFutureDateTime($value)  {
        	        	
        	if (count($value) > 1) {
        		$dateTime1 = $value[0]; // date or time for now assume date
        		$dateTime2 = $value[1]; // date or time for now assume datetime   		
        	}
        	else {
        		$dateTime1 = mktime();
        		$dateTime2 = $value;        		  
        	}
        	
    		$pieces = explode("/", $dateTime1);
			$month = $pieces[0];
			$day = $pieces[1];
			$year = $pieces[2];
        	$dateTime1 = mktime(0,0,0,$month,$day,$year);
        	
        /* This function takes the month and year
           and checks to make sure this constitutes
           a date sometime in the future.  */
 
           if ($dateTime2 > $dateTime1) { 
           		return TRUE; 
           }  
           else { 
                return FALSE;
           }  // was not a valid future date time
            
        }  // End of isFutureDateTime()

    }
?>