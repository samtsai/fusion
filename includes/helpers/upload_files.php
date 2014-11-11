<?php 

/*  This class is designed to make uploading files easier to manage
    (since it is a repeated task).  To use the class, you need to do 
    the following:

	1. Set the name of the uploaded file 'upfile' so it can be found in 
	   the $_FILES array by the constructor (or modify the constructor)

	2. Pass the type of extensions you want to view.  It is a very good idea 
	   to limit the file types that can be uploaded to a server (for security
	   purposes).  A couple of typical examples include:
	   
	   TEXT -- array(".txt",".htm",".html",".php") 
	   DOCS -- array(".doc",".rft",".pdf",".xls)
	   IMAGES -- array(".jpg",".gif",".jpeg",".png")
	   ARCHIVES -- array(".zip",".sit",".tar",".dmg")

       The default in the constructor is that all file types are ok.

	3. Set up other features including upload and upload log directories, 
	   banned user array, overwriting of files (default is 'yes'), setting 
	   pointers to databases (so only the reference has to be stored) and 
	   max file size.  The defaults in the constructor can be changed in 
	   this file or overridden in the main program via set functions.

    On to setting up the class...

    ======================================
    CREATING CLASS PROPERTIES
    ======================================    */

class Upload_Files { 

    var $temp_file_name; 
    var $file_name; 
    var $upload_dir; 
    var $upload_log_dir; 
    var $upload_file_size;
    var $max_file_size; 
    var $banned_array; 
    var $ext_array; 
    var $overwritable;


/*  ======================================
    METHOD SET 0:  CONSTRUCTOR 
    
    Note: Constructor is a special function that kicks in whenever an 
    instance of the class is created.  	

    Typically it is used to set initial values to zero or reset to whatever 
    the defaults should be.  In this case I am getting the array of banned 
    users and the extensions array to "" so that everyone is allowed to upload
    and all files are permissible.  The max file size is set to 10MB (should 
    be plenty) and files are overwritable.  The files themeselves will be 
    stored in a directory called "files/" (very creative name) and the 
    upload logs will be in the "files/upload_logs/" directory.
    
    ========================================    */

function Upload_Files() {

// Start by checking for any errors... (assumes PHP 4.2+)

    $upload_error = $_FILES['upfile']['error'];
	if ($upload_error > 0) {
    		echo "Error: $upload_error <BR>";
    		// echo 'Problem uploading file.<BR>';
    		die;
  	}

// if there are no errors, then we create the instance of the class
// first three attributes come from the $_FILES array...

    else {
    $this->temp_file_name = trim($_FILES['upfile']['tmp_name']); 
    $this->file_name = trim(strtolower($_FILES['upfile']['name']));
    $this->upload_file_size = $_FILES['upfile']['size']; 

/*   ---------------------------------------------------------------
     SET DEFAULTS HERE -- CAN BE CHANGED HERE OR WITHIN MAIN PROGRAM
     ---------------------------------------------------------------   */
    $this->banned_array = array("");   // i.e., no banned users yet
    $this->ext_array = array(".jpg",".gif",".jpeg",".png");  // i.e., only image-types ok 
    $this->upload_dir = "images/items/"; 
    $this->upload_log_dir = "images/items/upload_logs/"; 
    $this->max_file_size = 2048000;
    $this->overwritable = "yes";
    }
}   // End of Constructor



/*  =====================================================================
    METHOD SET 1:  SET FUNCTIONS  -- TO OVERRIDE DEFAULTS IN MAIN PROGRAM	
    =====================================================================   */

function setBanned_Array($array) {$this->banned_array = $array;}

function setExt_Array($array) {$this->ext_array = $array;}

function setUpload_Dir($string) {$this->upload_dir = strtolower($string);}

function setUpload_Log_Dir($string) {$this->upload_log_dir = strtolower($string);}

function setMax_File_Size($value) {$this->max_file_size = $value;}

function setOverwritable($string) {$this->overwritable = strtolower($string);}



/*  =====================================
    METHOD SET 2:  OTHER FUNCTIONS	
    =====================================    */


/* -----------------------------
   FUNCTION: PRINT ERROR REPORTS
   -----------------------------   */

function print_errors($errno) {

    switch ($errno) {
      case 1:  echo "File has an invalid extension. ";  break;

      case 2:  echo "File is larger than the system allows. ";  break;

      case 3:  echo "The upload directory could not be opened. ";  break;

      case 4:  echo "The upload directory is invalid. ";  break;
      
      case 5:  echo "A file with this name already exists in the system. ";  break;

      case 6:  echo "The upload log directory could not be opened. ";  break;

      case 7:  echo "The upload log directory is invalid. ";  break;

      case 8:  echo "This user is banned from uploading files. ";  break;

      case 9:  echo "File could not be moved to upload directory. ";  break;

      case 10:  echo "File could not be uploaded. ";  break;

      case 11:  echo "Database was not updated with file information. ";  break;
      
      case 12:  echo "Database could not be opened. ";  break;
      
      }
    
}  // End of print_errors()



/* -----------------------------------
   FUNCTION: CHECK FOR VALID EXTENSION
   -----------------------------------   */

function validate_extension() { 
    
/* We begin by getting the file's extension by finding the last instance of 
   the . and getting the characters that follow (can't take last 3 characters 
   because some extensions have four chars, e.g., .html)  */

    $file_name = $this->file_name; 
    $file_ext = strrchr($file_name,"."); 
    $ext_array = $this->ext_array; 
    $ext_count = count($ext_array); 


    // Now a few checks before we try to match the extension
    
    if (!$file_name) { // a double check -- should have caught this earlier...
	  $errno = 9;
	  //$this->print_errors($errno);
        return false; 
    } else { 
        if (!$ext_array) { // if no ext_array specified, any extension is acceptable
            return true; 
        } else { 

		/* just in case extension in array are not formatted right, we'll 
		   correct any potential problem here by adding . and making lower case */

            foreach ($ext_array as $value) {
                $first_char = substr($value,0,1); 
                    if ($first_char <> ".") { 
                        $extensions[] = ".".strtolower($value); 
                    } else { 
                        $extensions[] = strtolower($value); 
                    } 
            } 

    // Now onto the business of matching the extension with values in the array

		$valid_extension = false; // initially assume it is false...

            foreach ($extensions as $value) { 
                if ($value == $file_ext) { 
                    $valid_extension = true; // set to true if a match...
                }                 
            } 
 
            if ($valid_extension) { 
                return true;   // return true if any matches...
            } else { 
		    $errno = 1;
		    //$this->print_errors($errno);
                return false; 
            } 
        } 
    } 
} // End of validate_extension()



/* -----------------------------------
   FUNCTION: CHECK FOR VALID FILE SIZE
   -----------------------------------   */

function validate_size() { 
    $temp_file_name = $this->temp_file_name; 
    $max_file_size = $this->max_file_size; 

    if ($temp_file_name) { 
        $size = filesize($temp_file_name); 
            if ($size > $max_file_size) { 
	  	    $errno = 2;
	  	    //$this->print_errors($errno);
                return false;                                                         
            } else { 
                return true; 
            } 
    } else { 
	  $errno = 10;
	  //$this->print_errors($errno);
        return false; 
    }     
} // End of validate_size()



/* ------------------------------
   FUNCTION: FORMAT FILE SIZE
   ------------------------------   */

function format_file_size() { 
    
// Again, we start by defining vars...

    $temp_file_name = $this->temp_file_name; 
    $kb = 1024; 
    $mb = 1024 * $kb; 
    $gb = 1024 * $mb; 
    $tb = 1024 * $gb; 

    // Find right way to format file size...

       if ($temp_file_name) { 
            $size = filesize($temp_file_name); 
            if ($size < $kb) { 
                $file_size = "$size Bytes"; 
            } 
            elseif ($size < $mb) { 
                $final = round($size/$kb,2); 
                $file_size = "$final KB"; 
            } 
            elseif ($size < $gb) { 
                $final = round($size/$mb,2); 
                $file_size = "$final MB"; 
            } 
            elseif($size < $tb) { 
                $final = round($size/$gb,2); 
                $file_size = "$final GB"; 
            } else { 
                $final = round($size/$tb,2); 
                $file_size = "$final TB"; 
            } 
        } else { 
            $file_size = "ERROR"; 
        } 
        return $file_size; 

} // End of format_file_size()



/* ------------------------------------
   FUNCTION: CHECK FOR UPLOAD DIRECTORY
   ------------------------------------   */

function get_upload_directory() { 

    $upload_dir = $this->upload_dir; 

    if ($upload_dir) { // Still check if directory exists in case 
                       // developer screwed up and reset to nothing

	// Make sure the directory is a valid format type & fix if needed

        $ud_len = strlen($upload_dir); 
        $last_slash = substr($upload_dir,$ud_len-1,1); 
            if ($last_slash <> "/") { 
                $upload_dir = $upload_dir."/"; 
            } else { 
                    $upload_dir = $upload_dir; 
            } 

      /* Here we set a handle for opening the directory. Then we see if 
         the handle will indeed open. If it does open we close the directory 
         and return the upload directory name, otherwise we return an error 
         to tell the user that the directory is invalid.

         Important to surpress the error with a @ because if the directory 
         won't open then an error would appear and confuse the user.  */

        $handle = @opendir($upload_dir); 
            if ($handle) { 
                $upload_dir = $upload_dir; 
                closedir($handle); 
            } else { 
		     $errno = 3;
		     //$this->print_errors($errno);
                $upload_dir = "ERROR"; 
            } 
    } else { 
	  $errno = 4;
        //$this->print_errors($errno);
        $upload_dir = "ERROR"; 
    } 
    return $upload_dir; 

} // End of get_upload_dir()



/* --------------------------------------
   FUNCTION: CHECK IF FILE ALREADY EXISTS
   --------------------------------------   */

function validate_filename() { 
    $file_name = $this->file_name; 
    $upload_dir = $this->get_upload_directory();
    $test = $this->overwritable;

	if ($test == "yes") { return true; }
	
	else {
    	if (file_exists($upload_dir . $file_name)) { 
	  		$errno = 5;
	  		//$this->print_errors($errno);
        	return false; 
        }
        else { return true; }
    }
} // End of validate_filename()



/* ----------------------------------------
   FUNCTION: CHECK FOR UPLOAD LOG DIRECTORY
   ----------------------------------------   */

/* This function is similar to the one above and needs little
   commentary.  It will check the log file directory to be sure
   it is valid and can be accessed.  Wait... log file?  What in 
   the world is a log file and why do I need it?

   Everytime a user uploads a file, a .txt file is written to the 
   server with today's date. If a file already exists for today's 
   date we just append the data to the end, otherwise we start a 
   new file. The reason for doing this is to see who is uploading 
   what and when. If somebody uploads a virus you will be able to 
   track that user down and enter him/her onto your banned users 
   list.  Having a log file and storing them in a separate 
   directory is a good idea.   */

function get_upload_log_directory() { 
    $upload_log_dir = trim($this->upload_log_dir); 
    if ($upload_log_dir) { 
        $ud_len = strlen($upload_log_dir); 
        $last_slash = substr($upload_log_dir,$ud_len-1,1); 
            if ($last_slash <> "/") { 
                $upload_log_dir = $upload_log_dir."/"; 
            } else { 
                $upload_log_dir = $upload_log_dir; 
            } 
            $handle = @opendir($upload_log_dir); 
                if ($handle) { 
                    $upload_log_dir = $upload_log_dir; 
                    closedir($handle); 
                } else { 
	  		  $errno = 6;
	  		  //$this->print_errors($errno);
                    $upload_log_dir = "ERROR"; 
                } 
    } else { 
	  $errno = 7;
	  //$this->print_errors($errno);
        $upload_log_dir = "ERROR"; 
    } 
    return $upload_log_dir; 

} // End of get_upload_log_directory()



/* -------------------------------
   FUNCTION: CHECK FOR BANNED USER
   -------------------------------   */

function validate_user() { 

/* This function gets the user's IP address from the SERVER array
   and uses gethostbyaddr() to get the Internet host name 
   corresponding to a given IP address.  If the person's IP address + 
   host is on our banned user list (uploading viruses in the past, for
   instance) then they can be blocked from uploading  */

    $banned_array = $this->banned_array; 
    $ip = trim($_SERVER['REMOTE_ADDR']); 
    $cpu = gethostbyaddr($ip); 
    $count = count($banned_array); 

    if ($count < 1) { 
        return true; 
    } else { 
        foreach($banned_array as $key => $value) { 
            if ($value == $ip ."-". $cpu) { 
	 	    $errno = 8;
	 	    //$this->print_errors($errno);
                return false; 
            } else { 
                return true; 
            } 
        } 
    } 
} // End of validate_user()



/* -------------------------
   FUNCTION: UPLOAD FILE NOW
   -------------------------   */

function upload_file_now() { 

/* There are two parts to this function.  In the first part, we 
   just get all the information on the file that we will need 
   later.  Then the uploaded file is transfered from a temp 
   directory on the server to the designated directory.  If the 
   move is successful, the log file is updated.    */

    // Just getting the info we need...

    $temp_file_name = $this->temp_file_name; 
    $file_name = $this->file_name; 
    $upload_dir = $this->get_upload_directory(); 
    $upload_log_dir = $this->get_upload_log_directory(); 
    $file_size = $this->format_file_size(); 
    $ip = trim($_SERVER['REMOTE_ADDR']); 
    $cpu = gethostbyaddr($ip); 
    $m = date("m"); 
    $d = date("d"); 
    $y = date("Y"); 
    $date = date("m/d/Y"); 
    $time = date("h:i:s A"); 

    // Now time to validate...
    $valid_user = $this->validate_user();         
    $valid_size = $this->validate_size();         
    $valid_ext = $this->validate_extension();     
    $valid_name = $this->validate_filename();   
 
    if (($upload_dir == "ERROR") || ($upload_log_dir == "ERROR")) { 
        return false; 
    } 
    elseif ((((!$valid_user) || (!$valid_name) || (!$valid_size) || (!$valid_ext)))) { 
        return false; 
    } else { 
        if (is_uploaded_file($temp_file_name)) { 
            if (move_uploaded_file($temp_file_name,$upload_dir . $file_name)) { 
                $log = $upload_log_dir.$y."_".$m."_".$d.".txt"; 
                $fp = fopen($log,"a+"); 
                fwrite($fp,"$ip-$cpu | $file_name | $file_size | $date | $time \n"); 
                fclose($fp); 
                return true; 
            } else { 
			$errno = 9;
	  		//$this->print_errors($errno);
                  return false; 
            } 
        } else { 
	 	$errno = 10;
	  	//$this->print_errors($errno);
            return false; 
        } 
    } 
} // End of upload_file_now()



/* ----------------------------------
   FUNCTION: WRITE FILE POINTER TO DB
   ----------------------------------   */

function write_file_to_db($Table, $PointerField, $IDField, $ID) { 

    $file_name = $this->file_name; 
    $upload_dir = $this->get_upload_directory(); 
    $pointer = $upload_dir . $file_name;

	// Set up a link to the database 
	
		$db = new DB();
		
	// Create query to add file referenece to database

		$query = "UPDATE $Table SET $PointerField = '$pointer' WHERE $IDField = '$ID'";
						
	// Execute the query and test to see that it worked

		$Result = $db->updateRecord($query);
		if ($Result) {  
			return true;
		}

		else {   
	 		$errno = 11;
	  		//$this->print_errors($errno);
			return false;
		}

}  // end of write_file_to_db()

} // end of upload_files class
?> 