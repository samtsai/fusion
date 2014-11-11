<?php
/*
 * Created on Mar 18, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 require_once ("includes/common.php");
 
//$person = new Person();
//$person->setEmail("gstsai@andrew.cmu.edu");
//$person->emailResetPassword();
/*
$pswd = "hpzytrx3";
$questionAnswer = "johns";

echo $pswdHash = Login :: generateHash($pswd);
echo "<br/><br/>";		
echo $questionAnswerHashed = Login :: generateHash($questionAnswer);
*/
phpinfo();

////deletes all temp meetings
/*	$db = new DB();

	$query = "Select tblEvent.event_id FROM tblPersonEvent, tblEvent WHERE tblPersonEvent.event_id = tblEvent.event_id AND tblEvent.type_id = 5 AND tblPersonEvent.person_id = ".$_SESSION["person_id"];
	$ids = $db->getArray($query);
		
	foreach($ids as $id){
		echo "deleting $id... ";
		$event = new Event($id);
		
		//if($event->getTypeID() != 5)
			$event->deleteEvent();
			
		echo "done. <br/>";
	}
	*/


?>
