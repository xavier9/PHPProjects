<?php
	require_once("includes/startsession.php");	
	$page_title = "Change Password";
	require_once("includes/header.php");
	
	$msg = "";
	
	if(isset($_POST["submit"])) {
		if($_POST["new_passw"] != "" && $_POST["conf_passw"] != "" && $_POST["curr_passw"] != "") {
			if($_POST["new_passw"] == $_POST["conf_passw"]) {
				$msg = check_password_rules($_POST["conf_passw"]);
				if($msg=="ok") 	
					$msg = change_password($conn, $_SESSION["user"], $_POST["curr_passw"], $_POST["conf_passw"]);
			}
			else
				$msg = "Passwords don't match!</br>";
		}
		else 
			$msg = "Please fill in all fields!</br>";
	}
	
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content_with_title'>";	
	
	if($msg!="")
		echo "<div class='err'><h3>" . $msg . "</h3></div>";
	
	echo "<h3>Change your password</h3>";
	echo "<p>This will change your password to login to: <strong>Windows (all the computers is school), Zimbra and Extranet.</strong></br></br>";
	echo "It will <strong>not change</strong> your password for <strong><a href='http://sms.eursc.eu' target='_blank'>SMS</a></strong>, to change your password for SMS: login to SMS then click <strong>edit my account</strong> on the right top corner  and change your password.</p>";
	echo "<p><strong>Password requirements: </strong></p>";
	echo "<ul>"
		."<li>At least 8 characters</li>"
		."<li>At least 1 uppercase</li>"
		."<li>At least 1 lowercase</li>"
		."<li>At least 1 digit</li>"
		."</ul>";
	echo "<table  class='table_no_border'>";
	echo "<tr><td>Current password:</td><td><input type='password' name='curr_passw' placeholder='Current password' size='25'/></td><tr>";
	echo "<tr><td>New password:</td><td><input type='password' name='new_passw' placeholder='New password' size='25'/></td><tr>";
	echo "<tr><td>Confirm password:</td><td><input type='password' name='conf_passw' placeholder='Confirm password' size='25'/></td><tr>";
	echo "</table></br>";
	echo "<input type='submit' name='submit' value='Change password' class='text_button'/>";	
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function change_password ($conn, $user, $password, $new_password) {			
		$ldapconn = get_ldap_connection();
		$ldap_user = get_ldap_user($ldapconn, $user["ID"]);
		$entry["uid"]=$ldap_user["uid"][0];		
		$format = "uid=%s,ou=Users,dc=eeb2,dc=be";
		$ldap_user = sprintf($format,$entry["uid"]);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); 	
		if(ldap_bind($ldapconn, $ldap_user, $password)) {
			$entry["userpassword"]="{MD5}".base64_encode(pack("H*",md5($new_password)));						
			$entry ["sambantpassword"]= strtoupper(hash('md4', iconv("UTF-8","UTF-16LE",$new_password)));
			update_password($conn, $user["ID"], encrypt_password($new_password));
			return user_update_ldap ($conn, $entry);
		}
		else
			return "Current password is incorrect!";
	}	
	
	function user_update_ldap ($conn, $user) {
		$ldapconn = get_ldap_connection();
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); 	
		if(ldap_bind($ldapconn, "cn=root,dc=eeb2,dc=be", "vaexaewi")) {	
			if (ldap_mod_replace($ldapconn,"uid=".$user["uid"].",ou=Users,dc=eeb2,dc=be",$user))
				return "Password successfully changed!";
			else
				return "Failed to change password!";
		}
		ldap_close($ldapconn);
	}	
?>