<?php

class Arrays {      // ARRAYS CLASS

/*	A more detailed set of notes exists for each method within the method itself,
	but this list gives a good overview of what is in this set of methods.  All 
	methods here are static (for obvious reason).
		
	LIST OF FUNCTIONS INCLUDED
	--------------------------
	printArray($array,$border)				-- commonly used for diagnostics on basic arrays
	
	print2DArray($array, $border)			-- commonly used for diagnostics on assoc arrays 

	getPostVars($pattern)					-- puts selected $_POST vars into array; 
										       if pattern == "", gets all $_POST vars
	writePostVarsToSession($pattern)		-- same as above, but adds to $_SESSION as well
	
	getSessionVars($pattern) 				-- puts selected $_SESSION vars into array; 
										   	   if pattern == "", gets all $_POST vars 
	clearEmptyValues($array)				-- removes any empty values from assoc array
	
	addedToArray($old_array, $new_array)	-- determines which values were added to new array
	
	removedFromArray($old_array, $new_array) -- determines which values were removed from new array

	checkInArray($needle, $haystack) -- determines if the needle is in the haystack
*/

// -----------------------
// FUNCTION: PRINT ARRAY

public static function printArray($array, $border) {

/* This function loops through a simple (1D)
   array and prints out all the values in a 
   table.  This is used for diagnostic
   purposes primarily, although there 
   can be other applications.  */
   
   // Check that the array exists
   if (!$array) {
        echo "No array specified.<BR><BR>";
        return;
   }
   
   else {
        // Start table
        echo "<TABLE BORDER=$border>";
   
        // Print the array in table cells
        foreach ($array as $value)  {
            echo "<TR><TD>$value</TD></TR>"; 
        }  // end of while loop
   
        // End table and return
        echo "</TABLE>";
        return;
   }  // end of else

} // End of printArray()


// -----------------------
// FUNCTION: PRINT 2D ARRAY

public static function print2DArray($array, $border) {

/* This function loops through an associative
   array and prints out all the values in a 
   table.  This is used for diagnostic
   purposes primarily, although there 
   can be other applications.  */
   
   // Check that the array exists
   if (!$array) {
        echo "No array specified.<BR><BR>";
        return;
   }
   
   else {
        // Start table
        echo "<TABLE BORDER=$border>";
   
        // Print the array in table cells
        while (list($key, $value) = each ($array))  {
            echo "<TR><TD>$key</TD><TD>$value</TD></TR>"; 
        }  // end of while loop
   
        // End table and return
        echo "</TABLE>";
        return;
   }  // end of else

} // End of print2DArray()


// -----------------------
// FUNCTION: GET POST VARS
public static function getPostVars($pattern) {

/* This function loops through the $_POST
   array and extracts all variables that 
   match the pattern passed.  All values 
   are cleaned using trim and strip slashes
   if magic_quotes are on.   */
   
   if (!$_POST) {
        //echo "The POST Array is empty<BR>";
        return FALSE;
   }
   
   if (!$pattern || $pattern == "") { $pattern = "^[a-z0-9_%-]+$"; }
   
   foreach ($_POST as $key => $value) {

		if (is_array($value)) {
			$postvars["$key"] = $value;
		}
		elseif (eregi($pattern, $key) && $value != 9999 && $key != "Submit") {
		 
		    // Trim data
		    $key = trim($key);
		    $value = trim($value);
		    
		    // Strip slashes if magic quotes in effect
		    if (get_magic_quotes_gpc()) {
		        $key = stripslashes($key);
		        $value = stripslashes($value);
		    }
		
		    // enter into the post vars to be returned
		    
		    $postvars["$key"] = $value;
		   
		} // End of if loop
   	} // End of foreach loop
   
   if (!$postvars) { return FALSE; }
   else { return $postvars; }

} // End of getPostVars()


// -------------------------
// FUNCTION: WRITE POST VARS TO SESSION

public static function writePostVarsToSession($pattern) {

/* This function is just like getPostVars 
   except that it also writes the values 
   to the $_SESSION array.   */
   
   if (!$_POST) {
        // echo "The POST Array is empty<BR>";
        return FALSE;
   }
   
   if (!$pattern || $pattern == "") { $pattern = "^[a-zA-Z0-9_%-]+$"; }
   
   foreach ($_POST as $key => $value) {
   
     if (ereg("$pattern", "$key") && $value != 9999 && $key != "Submit") {
   
        // Trim data
        $key = trim($key);
        $value = trim($value);
        
        // Strip slashes if magic quotes in effect
        if (get_magic_quotes_gpc()) {
            $key = stripslashes($key);
            $value = stripslashes($value);
        }
        
        // enter into the post vars to be returned
        
        if ($value != "") {    // no blank values should be passed
            $_SESSION["$key"] = $value;
            $postvars["$key"] = $value;
        }
   
      } // End of if loop
   } // End of foreach loop
   
   if (!$postvars) { return FALSE; }   // no array was generated
   else { return $postvars; }

} // End of writePostVarsToSession()


// ----------------------------------
// FUNCTION: GET SESSION VARS

public static function getSessionVars($pattern) {

/* This function loops through the $_SESSION
   array and extracts all variables that 
   match the pattern passed.  After cleaning
   the data (should be clean -- just a 
   precaution) the values are all placed
   into an associative array.  */
   
   if (!$_SESSION) {
        // echo "The SESSION Array is empty<BR>";
        return FALSE;
   }
   
   if (!$pattern || $pattern == "") { $pattern = "^[a-zA-Z0-9_%-]+$"; }
   
   foreach ($_SESSION as $key => $value) {
   
     if (ereg("$pattern", "$key") && $value != 9999 && $key != "Submit") {
   
        // Trim data
        $key = trim($key);
        $value = trim($value);
        
        // Strip slashes if magic quotes in effect
        if (get_magic_quotes_gpc()) {
            $key = stripslashes($key);
            $value = stripslashes($value);
        }

        // enter into the post vars to be returned
        
        $sessionvars["$key"] = $value;
   
      } // End of if loop
   } // End of foreach loop
   
   if (!$sessionvars) { return FALSE; }  // no array was generated
   else { return $sessionvars; }

} // End of getSessionVars()


// ----------------------------------
// FUNCTION: CLEAR EMPTY VALUES

public static function clearEmptyValues($array) {

/*	This function unsets any empty values in an associate array
	and returns the array back.  This is useful, perhaps, if we 
	expect some $_POST values are unset and we need to clear 
	those empty values out of the array.  */

   foreach ($array as $key => $value) {
    	if ($value == "") { unset($array[$key]); }
    }
    
    return $array;
   
}	// End of clearEmptyValues()



/*	The following two functions are little more than the array_diff 
	function applied, but I often get array_diff messed up, so these
	functions help me keep it straight.  There is no function for 
	array_intersect, b/c I rarely make mistakes with that one.   */

// ----------------------------------
// FUNCTION: ADDED TO ARRAY

public static function addedToArray($old_array, $new_array) {

/*	This function returns those values that exist in the 
	new array, but not in the old one.  Those values have 
	essentially been "added" to the old array.   */

   $added = array_diff($new_array, $old_array);
   return $added;

}  // End of addedToArray()



// ----------------------------------
// FUNCTION: REMOVED FROM ARRAY

public static function removedFromArray($old_array, $new_array) {

/*	This function returns those values that exist in the old 
	array, but are no longer found in the new one.  Those 
	values have essentially been "removed" to the old array.   */

   $removed = array_diff($old_array, $new_array);
   return $removed;
   
}	// End of removedFromArray()
 
// ----------------------------------
// FUNCTION: ARE STRINGS FROM ARRAY IN ARRAY

public static function checkArrayInArray($needle, $haystack) {

    // we lowercase the haystack because the needle is lowercased, without altering the original query
    foreach ($needle AS $value) {
        if (!in_array($value, $haystack)) {
            return FALSE;
        }
    } 
    
    return TRUE;
}   // End of checkInArray

// ----------------------------------
// FUNCTION: ARE STRINGS IN ARRAY

public static function checkInArray($needle, $haystack) {
    
    // we lowercase the haystack because the needle is lowercased, without altering the original query
    $haystack = Arrays::arrayToLower($haystack);
    $needle = strtolower(trim($needle));

    if (in_array($needle, $haystack)) {
        return TRUE;
    }
    
    return FALSE;
}   // End of checkInArray

// ----------------------------------
// FUNCTION: ARRAY TO LOWER

public static function arrayToLower($array) {
    foreach ($array AS $key => $value) {
        if (is_string($value)) {
            $array[$key] = strtolower(trim($value));
        }
    }
    return $array;
}   // End of arraytolower

// ----------------------------------
// FUNCTION: ARRAY TO UPPER

public static function arrayToUpper($array) {
    foreach ($array AS $key => $value) {
        if (is_string($value)) {
            $array[$key] = strtoupper(trim($value));
        }
    }
    return $array;
}   // End of arraytoupper

// ----------------------------------
// FUNCTION: ARRAY TO UPPERCASE

public static function arrayucwords($array) {
    foreach ($array as $key => $value) {
        if (is_string($value)) {
            $array[$key] = ucwords(trim($value));
        }
    }
    return $array;
}   // End of arrayucwords

public static function hasNext($array) {
   if (is_array($array)) {
       if (next($array) === false) {       		
           return false;
       } else {
       		echo next($array);
           return true;
       }
   } else {
       return false;
   }
}

public static function stripBracket($arrayName) {
	
	if (empty($arrayName))
		return FALSE;
		
	$arrayString = $arrayName;				
	$parts = explode("[",$arrayString);
	if (count($parts) == 1) {
		return FALSE;	
	}
	
	$level = 1;

	foreach ($parts as $value) {
		$stuff = array ("]","\"","'");
		$array[$level] = str_replace($stuff,"",$value);
		$level++; 	
	}
	
	return $array;
}

public static function KeyName($array,$pos) {
  // $pos--;
  /* uncomment the above line if you */
  /* prefer position to start from 1 */

  if ( ($pos < 0) || ( $pos >= count($array) ) )
        return "NULL";  // set this any way you like

  reset($array);
  for($i = 0;$i < $pos; $i++) next($array);

  return key($array);
} 

public static function countdim($array)
{
	if (is_array(reset($array))) 
		$return = Arrays::countdim(reset($array)) + 1;
	else
		$return = 1;
 
	return $return;
}

public static function longestString($array) {
	
	// current longest string length
	$i = 0;
	
	foreach ($array as $value) {	
		if (strlen($value) > $i) {	
			$i = strlen($value);
		}			
	}
	
	return $i;
}

public static function in_multi_array($value, $array) {
   foreach($array as $item) {
       if(!is_array($item)) continue;
       if(in_array($value, $item)) return true;
       else if(Arrays::in_multi_array($value, $item)) return true;
   }
  
   return false;
}

public static function isParent($item, $array) {
	
	$path = Arrays::multi_array_search($item, $array);
	$levels = count($path);
	
	if ($levels <= 1) {
		return false;	
	}
	else {
		return $path;
		//$path[$levels] = 
	}
	
}
	
public static function array_searchMultiOnKeys($searchKeysArray, $multiArray) {
   // Iterate through searchKeys, making $multiArray smaller and smaller.
   foreach ($searchKeysArray as $keySearch) {
       $multiArray = $multiArray[$keySearch];
       $result = $multiArray;
   }
  
   // Check $result.
   if (is_array($multiArray)) {
       // An array was found at the end of the search. Return true.
       $result = true;
   }
   elseif ($result == '') {
       // There was nothing found at the end of the search. Return false.
       $result = false;
   }

   return $result;
// End of function,
}
/*
public static function multi_array_search($needle, $haystack) {
	
	foreach($haystack as $value) {
		$search = array_search($needle, $value);
		if ($search) {
			return $value[$search];	
		}		
	}
	
	return false;	
}
*/
public static function multi_array_search($search_value, $the_array)
{
   if (is_array($the_array))
   {
       foreach ($the_array as $key => $value)
       {
           $result = Arrays::multi_array_search($search_value, $value);
           if (is_array($result))
           {
               $return = $result;
               array_unshift($return, $key);
               return $return;
           }
           elseif ($result == true)
           {
               $return[] = $key;
               return $return;
           }
       }
       return false;
   }
   else
   {
       if ($search_value == $the_array)
       {
           return true;
       }
       else return false;
   }
}

public static function clearBlanks($array) {
	
	foreach($array as $key => $value) {
		if (empty($value)) {
			unset($array[$key]);	
		}	
	}
	return $array;
}

public static function isEmptyArray($array) {
	
	$clearedArray = Arrays::clearEmptyValues($array);
	if (empty($clearedArray)) {
		return TRUE;	
	}
	else {
		return FALSE;	
	}
}

public static function noEmptyValues($array) {

	foreach($array as $value) {
		if ($value == "" || $value == NULL) {
			return FALSE;
		}	
	}	
	
	return TRUE;
}

} //  END OF ARRAYS CLASS

?>