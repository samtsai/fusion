<?
define("MAX_PACKLENGTH",65535);
define("SFLAP_TYPE_SIGNON",1);
define("SFLAP_TYPE_DATA",2);
define("SFLAP_TYPE_ERROR",3);
define("SFLAP_TYPE_SIGNOFF",4);
define("SFLAP_TYPE_KEEPALIVE",5);
define("SFLAP_MAX_LENGTH",4096);
define("SFLAP_SUCCESS",0);
define("SFLAP_ERR_UNKNOWN",1);
define("SFLAP_ERR_ARGS",2);
define("SFLAP_ERR_LENGTH",3);
define("SFLAP_ERR_READ",4);
define("SFLAP_ERR_SEND",5);
define("SFLAP_FLAP_VERSION",1);
define("SFLAP_TLV_TAG",1);
define("SFLAP_HEADER_LEN",6);
ini_set("max_execution_time", "0"); 


$ERROR_MSGS = array(1 => 'AOLIM Error: Unknown Error',
2 => 'AOLIM Error: Incorrect Arguments',
3 => 'AOLIM Error: Exceeded Max Packet Length (1024)',
4 => 'AOLIM Error: Reading from server',
5 => 'AOLIM Error: Sending to server',
6 => 'AOLIM Error: Login timeout',
901 => 'General Error: $ERR_ARG not currently available',
902 => 'General Error: Warning of $ERR_ARG not currently available',
903 => 'General Error: A message has been dropped, you are exceeding the server speed limit',
950 => 'Chat Error: Chat in $ERR_ARG is unavailable',
960 => 'IM and Info Error: You are sending messages too fast to $ERR_ARG',
961 => 'IM and Info Error: You missed an IM from $ERR_ARG because it was too big',
962 => 'IM and Info Error: You missed an IM from $ERR_ARG because it was sent too fast',
970 => 'Dir Error: Failure',
971 => 'Dir Error: Too many matches',
972 => 'Dir Error: Need more qualifiers',
973 => 'Dir Error: Dir service temporarily unavailble',
974 => 'Dir Error: Email lookup restricted',
975 => 'Dir Error: Keyword ignored',
976 => 'Dir Error: No keywords',
977 => 'Dir Error: Language not supported',
978 => 'Dir Error: Country not supported',
979 => 'Dir Error: Failure unknown $ERR_ARG',
980 => 'Auth Error: Incorrect nickname or password',
981 => 'Auth Error: The service is temporarily unavailable',
982 => 'Auth Error: Your warning level is too high to sign on',
983 => 'Auth Error: You have been connecting and disconnecting too frequently. Wait 10 minutes and try again.',
989 => 'Auth Error: An unknown signon error has occurred $ERR_ARG' );

$socket = null;
$clientSequenceNumber = 0;

$tocHost = "toc.oscar.aol.com";
$tocPort = 5190;
$authHost =  "login.oscar.aol.com";
$authPort = 5159;


$screenName="PreludeOracle";
$password = "shirley";
$controller="BITBIT";


function readFlap() {
     global $socket;

     $header = fread($socket, SFLAP_HEADER_LEN);
     if(strlen($header) < SFLAP_HEADER_LEN) 
		return strlen($header);

     $headerArray = unpack ("aasterisk/CframeType/nsequenceNumber/ndataLength", $header);
     $packet = fread($socket, $headerArray['dataLength']);

     if($headerArray['frameType'] == SFLAP_TYPE_SIGNON) 
          $packetArray = unpack("Ndata", $packet);
     else
          $packetArray = unpack("a*data", $packet);

     $data = array_merge($headerArray, $packetArray);

     return $data;
}

function sendRaw($data) {
     global $socket;
     
     if(fwrite($socket, $data) == FALSE)
          return 0;
     
     return 1;
}


function sendFlap($frameType, $data) {
    global $socket, $clientSequenceNumber;

    if( strlen($data) > SFLAP_MAX_LENGTH)
    {
         $data = substr($data, 0, (SFLAP_MAX_LENGTH-2) );
         $data .= '"';
    }
    
   $data = rtrim($data);
   $data .= "\0";
    $header = pack("aCnn", '*', $frameType, $clientSequenceNumber, strlen($data));

    
    $packet = $header . $data;

    if(fwrite($socket, $packet) == FALSE)
         die("Unable to send the packet.<BR>\n");

    $clientSequenceNumber++;
    
     return $packet;
} 


function sendTocFlapSignon() {
     global $screenName;
     
     $data = pack("Nnna".strlen($screenName), 1, 1, strlen($screenName), $screenName);
     $result = sendFlap(SFLAP_TYPE_SIGNON, $data);
     return $result;
}


function RoastPassword($password) {
     $roastString = 'Tic/Toc';
     $roastedPassword = '0x';

     for ($i = 0; $i < strlen($password); $i++) 
          $roastedPassword .= bin2hex($password[$i] ^ $roastString[($i % 7)]);

     return $roastedPassword;
}

function tocSignon() {
     global $screenName, $password;
     global $authHost, $authPort;
     
     $roastedPassword = RoastPassword($password);
     
     $tocSignon = 'toc_signon '.$authHost.' '.$authPort.' '.$screenName. ' '.$roastedPassword;
     $tocSignon .= ' "english" "AOLIM:\$Version 1.1\$"'."\0";

     $result = sendFlap(SFLAP_TYPE_DATA, $tocSignon);
     
     return $result;
}     

function normalize($screenName) { 
     return eregi_replace("[[:space:]]+","",strtolower($screenName)); 
}

function signOn() {
     global $screenName, $roastedPassword, $socket, $clientSequenceNumber, $ERROR_MSGS;
     global $tocHost, $tocPort;     
     
     echo "Signing on using $screenName<BR>\n";

     if( !($socket = fsockopen($tocHost, $tocPort, $errorno, $errorstr, 30)) )
          die("$errorstr ($errorno)");

     echo "Connected to the server<BR>\n";

     if( !sendRaw("FLAPON\r\n\r\n") )
               die("Error sending the FLAPON packet.");

     echo "FLAPON packet sent<BR>\n";

     if( !( $result = readFlap() ) )
          die("No response from server.<BR>\n");

     if($result['asterisk']!='*' && $result['frameType']!=1 && $result['dataLength']!=4 && $result['data']!=1)
          die("Invalid FLAP SIGNON response from the server.<BR>\n");          
	else
		print_r($result);

     sendTocFlapSignon();
     
     tocSignon();

     $result = readFlap();
     
     if($result['asterisk'] != '*' && $result['frameType'] != 2 )
          die("Invalid response from server.<BR>\n");          

     if($result['data'] == "SIGN_ON:TOC1.0")
          echo "toc_signon success SIGN_ON ";
     else if(substr($result['data'], 0, 6) == "ERROR:")
     {
          $where = strpos( $result['data'], ":");
          die($ERROR_MSGS[chop(substr($result['data'], $where+1))] . "<br>\n");
     }
	 
	 echo $result['data'];

     sendFlap(SFLAP_TYPE_DATA, "toc_add_permit");
     sendFlap(SFLAP_TYPE_DATA, "toc_add_deny");
     
     $tocAddBuddy = "toc_add_buddy " . $screenName;
     sendFlap(SFLAP_TYPE_DATA, $tocAddBuddy);
	echo $screenName." Added";

     sendFlap(SFLAP_TYPE_DATA, "toc_init_done");
     echo "sent toc_init_done<BR>\n";
}

	signOn();
/*
	$x=false;
	while($x==false) 
	{
		$result = readFlap(); //grab the data
		//echo "<br/><br/> $result <br/><br/>";
	
		if(ereg('^IM_IN',$result['data'])) 
		{
			$nick = explode(':',$result['data']);
			$nick = normalize($nick[1]);
	
			//$buzzwords[0]="hello"; // generic test response
			//$buzzwords[1]="shit";
			//$buzzwords[2]="fuck"; // warn 'em for bad words
	
			//$response[0]='toc_send_im ' . $nick . ' "Hello! How are you?"';
			//$response[1]='toc_evil ' . $nick . ' norm';
			//$response[2]='toc_evil ' . $nick . ' norm';
	
			$keepsearching=true;
			foreach($buzzwords as $key => $value) {
				if(ereg($value,$result['data'])) {
					sendFlap(SFLAP_TYPE_DATA,$response[$key]);
					$keepsearching=false;
				}
			}
	
	/*
			if ($keepsearching) {
				$nick = explode(':',$result['data']);
				$search_result=strip_tags($nick[3]);
				$nick = normalize($nick[1]);
				$dbpass="PASSWORD";
				$chosenDB="DATABASE";
				mysqli_connect("localhost","root",$dbpass); 
				mysqli_select_db($chosenDB); 
				$SQL="Select Response FROM Responses WHERE Input='$search_result'";
				$query=mysqli_query($SQL);
				list($response) = mysqli_fetch_row($query);
				sendFlap(SFLAP_TYPE_DATA,'toc_send_im ' . $nick . ' "'.$response.'"');
			}
		
		}
	}
*/
?>