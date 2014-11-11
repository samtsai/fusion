<?php


/*
 * Created on Mar 8, 2006
 */
function LDAPwrapper($name) {
	$ds = ldap_connect("ldap.andrew.cmu.edu"); // must be a valid LDAP server!
	$rsp = false;
	if ($ds) {
		$r = ldap_bind($ds); // this is an "anonymous" bind, typically
		$dn = "ou=Person, dc=cmu, dc=edu";
		$filter = "(|(mail=$name*))";
		$justthese = array ("mail", "mailforwardingaddress", "mailalternateaddress");
		$sr = ldap_search($ds, $dn, $filter, $justthese);
		$info = ldap_get_entries($ds, $sr);
		for ($i = 0; $i < $info["count"]; $i ++) {
			/*
			$rsp .= "first email entry is: " . $info[$i]["mail"][0] . "<br />";
			$rsp .= "second email entry is: " . $info[$i]["mail"][1] . "<br/>";
			$rsp .= "first alternate email entry is: " . $info[$i]["mailalternateaddress"][0] . "<br/>";
			$rsp .= "first forward email entry is: " . $info[$i]["mailforwardingaddress"][0] . "<br /><hr />";
			*/
			if ($info[$i]["mail"][0] == "$name@andrew.cmu.edu" || $info[$i]["mail"][0] == "$name@cmu.edu") {
				//echo "hi!";
				$rsp = $info[$i]["mail"][0];
				break;
			}

		}
		ldap_close($ds);
	}
	if ($rsp)
		return $rsp;
	else
		return FALSE;

}
?>


