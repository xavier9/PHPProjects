<?php	
	require_once("includes/startsession.php");	
	$page_title = "User Management";
	require_once("includes/header.php");

	$msg = "";
	
	if(!isset($_POST["show"]) && !isset($_POST["save"]) && !isset($_POST["get_pwd"]))
		unset($_SESSION["user_manage"]);
	
	if(isset($_POST["save_new_user"])) {		
		if($_POST["Password"] != "" && $_POST["Confirm_password"] != "") {
			if($_POST["Password"] == $_POST["Confirm_password"]) {
				$err = check_password_rules($_POST["Confirm_password"]);
				if($err=="ok") {	
					$user["ID"] = $_POST["ID"];
					$user["Last_name"] = strtoupper ($_POST["Last_name"]);
					$user["First_name"] = ucfirst ($_POST["First_name"]);
					$user["Nationality"] = $_POST["Nationality"];
					$user["Date_of_birth"] = change_date_format_db($_POST["Date_of_birth"]);
					$user["Sex"] = $_POST["Sex"];
					$user["Category"] = implode (",",$_POST["Category"]);
					$user["Email"] = $_POST["Email"];
					$user["Password"] = encrypt_password($_POST["Password"]);
					add_user ($conn, $user);
					$msg = "User: ".$user["Last_name"]." ".$user["First_name"]." added to extra users!";
				}
				else
					return $err;
			}
			else
				$err = "Passwords don't match!</br>";
		}					
	}
	
	if(isset($_POST["save"])) {
		$user = get_user ($conn, $_POST["ID"]);
		if($_POST["Password"] != "" && $_POST["Confirm_password"] != "") {
			if($_POST["Password"] == $_POST["Confirm_password"]) {
				$msg = check_password_rules($_POST["Confirm_password"]);
				if($msg=="ok") {	
					if(!is_extra_user($conn, strtolower($user["First_name"].".".$user["Last_name"]))) {
						$user["userpassword"]="{MD5}".base64_encode(pack("H*",md5($_POST["Password"])));						
						$user ["sambantpassword"]= strtoupper(hash('md4', iconv("UTF-8","UTF-16LE",$_POST["Password"])));
						update_password($conn, $_POST["ID"], encrypt_password($_POST["Password"]));
						$msg = user_update_ldap ($conn, $user);
					}
					else {
						update_password($conn, $_POST["ID"], encrypt_password($_POST["Password"]));
						$msg = "successfully updated!";
					}
				}
				else
					return $msg;
			}
			else
				$msg = "Passwords don't match!</br>";
		}
	}
	
	if(isset($_POST["delete"])) 
		delete_user ($conn, $_POST["delete"]);
	
	if(isset($_POST["show"]))
		$_SESSION["user_manage"]=get_user($conn, $_POST["user"]);
	
	if(isset($_POST["get_pwd"]))
		$msg = "Password: ".decrypt_password(get_password($conn, $_POST["ID"]));
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	$users = get_all_users($conn);
	echo "<select name='user'>";
	foreach ($users as $user) {
		echo "<option value='".$user["ID"]."' ";
		if(isset($_SESSION["user_manage"]) && $_SESSION["user_manage"]["ID"]==$user["ID"])
			echo "selected";
		echo ">".$user["Last_name"]." ".$user["First_name"]."</option>";
	}
	echo "</select> ";	
	echo "<input type='submit' value='Show' name='show' class='text_button'/> ";
	echo "<input type='submit' value='Add new user' name='add_user' class='text_button'/> ";
	echo "<input type='submit' value='Show Extra Users' name='show_extra_users' class='text_button'/> ";
		
	if(isset($_POST["add_user"]))
		add_new_user ();	
	
	if(isset($_POST["show_extra_users"]) || isset($_POST["delete"]))
		show_extra_users ($conn);	
		
	if(isset($msg))
		echo "<div class='err'></br>".$msg."</div>";
		
	if(isset($_SESSION["user_manage"]))
		print_user_info ($conn, $_POST["user"]);
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function user_update_ldap ($conn, $user) {
		$ldapconn = get_ldap_connection();
		$ldap_user = get_ldap_user($ldapconn,$_SESSION["user_manage"]["ID"]);
		$entry["uid"]=$ldap_user["uid"][0];
		$entry["userpassword"]=$user["userpassword"];						
		$entry ["sambantpassword"]= $user["sambantpassword"];	
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3); 	
		if(ldap_bind($ldapconn, "cn=root,dc=eeb2,dc=be", "vaexaewi")) {	
			if (ldap_mod_replace($ldapconn,"uid=".$entry["uid"].",ou=Users,dc=eeb2,dc=be",$entry))
				return "successfully updated!";
			else
				return "failed to update!";
		}
		ldap_close($ldapconn);
	}
	
	function print_user_info ($conn, $id) {
		$user = get_user($conn, $id);	
		$ldapconn = get_ldap_connection();
		$ldap_user = get_ldap_user($ldapconn,$user["ID"]);
		$last_login = get_last_login ($conn, $user);
		echo "<input type='hidden' name='ID' value='".$user["ID"]."'/>";
		echo "</br>";			
		echo "<table class='table_no_border'>"
			."<tr><td rowspan='8'><a href='search_teachers.php?id=".$user["ID"]."'><img class= 'Picture' src='".get_photo($user["ID"])."' width='126' height='190'></a></tr>"
			."<tr>"
			."<td><strong>ID:</strong></td>"
			."<td>".$user["ID"]."</td>"
			."<td><strong>Username:</strong></td>"
			."<td>".(isset($ldap_user)?$ldap_user["uid"][0]:str_replace (" ", "_",  strtolower($user["First_name"].".".$user["Last_name"])))."</td></tr>"
			."<td><strong>Surname:</strong></td>"
			."<td>".$user["Last_name"]."</td>"
			."<td><strong>Name:</strong></td>"
			."<td>".$user["First_name"]."</td>"
			."</tr><tr>"
			."<td><strong>Nationality:</strong></td>"
			."<td>".$user["Nationality"]."</td>"
			."<td><strong>Date of Birth:</strong></td>"
			."<td>".change_date_format($user["Date_of_birth"])."</td>"
			."</tr>"
			."<tr>"
			."<td><strong>Sex:</strong></td>"
			."<td>".$user["Sex"]."</td>"
			."<td><strong>Category:</strong></td>"
			."<td><input type='checkbox' name='Category[]' value='M' ".(isset($user["Category"])?(strpos($user["Category"],"M")!==false?"checked":""):"")."/>Mat "
			."<input type='checkbox' name='Category[]' value='P' ".(isset($user["Category"])?(strpos($user["Category"],"P")!==false?"checked":""):"")."/>Prim "
			."<input type='checkbox' name='Category[]' value='Sec' ".(isset($user["Category"])?(strpos($user["Category"],"S")!==false?"checked":""):"")."/>Sec "
			."<input type='checkbox' name='Category[]' value='Admin' ".(isset($user["Category"])?(strpos($user["Category"],"A")!==false?"checked":""):"")."/>Admin</td>"
			."</tr>"
			."<tr>"
			."<td><strong>Gidnumber:</strong></td>"
			."<td>".$ldap_user["gidnumber"][0]."</td>"
			."<td><strong>Old ID:</strong></td>"
			."<td>".$ldap_user["postalcode"][0]."</td>"
			."</tr>"
			."<tr>"
			."<td><strong>E-mail:</strong></td>"
			."<td><input type='text' name='email' size='30' value='".$ldap_user["mail"][0]."'/></td>"
			."<td><strong>Last Login:</strong></td>"
			."<td>".($last_login!=""?change_date_format(substr($last_login["Last_login"],0,10))." ".substr($last_login["Last_login"],11):"")."</td>"
			."</tr>"
			."<tr>"
			."<td><strong>Password:</strong></td>"
			."<td><input type='password' name='Password' size='25'/></td>"
			."<td><strong>Confirm Password:</strong></td>"
			."<td><input type='password' name='Confirm_password' size='25'/></td>"
			."</tr>"
			."</table>";
		echo "</br></br>";
		echo "<input type='submit' value='Save' name='save' class='text_button'/> ";
		echo "<input type='submit' value='Show Password' name='get_pwd' class='text_button'/> ";
	}	

	function add_new_user () {
		echo "</br></br><table class='table_no_border'>"
			."<tr><td><strong>ID:</strong></td>"
			."<td><input type='text' name='ID' size='25' /></td>"
			."<td><strong>E-mail:</strong></td>"
			."<td colspan='3'><input type='text' name='Email' size='25' /></td>"
			."</tr>"
			."<td><strong>Surname:</strong></td>"
			."<td><input type='text' name='Last_name' size='25' /></td>"
			."<td><strong>Name:</strong></td>"
			."<td><input type='text' name='First_name' size='25' /></td>"
			."</tr><tr>"
			."<td><strong>Nationality:</strong></td>"
			."<td><input type='text' name='Nationality' size='25' /></td>"
			."<td><strong>Date of Birth:</strong></td>"
			."<td><input type='text' name='Date_of_birth' size='9' /> (DD/MM/YYYY)</td>"
			."</tr>"
			."<tr>"
			."<td><strong>Sex:</strong></td>"
			."<td><input type='text' name='Sex' size='1'/></td>"
			."<td><strong>Category:</strong></td>"
			."<td><input type='checkbox' name='Category[]' value='M' />Mat "
			."<input type='checkbox' name='Category[]' value='P' />Prim "
			."<input type='checkbox' name='Category[]' value='Sec' />Sec "
			."<input type='checkbox' name='Category[]' value='Admin' />Admin</td>"
			."</tr>"
			."<tr>"
			."<tr>"
			."<td><strong>Password:</strong></td>"
			."<td><input type='password' name='Password' size='25'/></td>"
			."<td><strong>Confirm Password:</strong></td>"
			."<td><input type='password' name='Confirm_password' size='25'/></td>"
			."</tr>"
			."</table>";
		echo "</br><input type='submit' value='Save' name='save_new_user' class='text_button'/> ";
	}
	
	function show_extra_users ($conn) {
		$users = get_extra_users ($conn);
		echo "</br></br><table class='table'>";
		echo "<th>Surname</th><th>Name</th><th>Nationality</th><th>Date Of Birth</th><th>Gender</th><th>Category</th><th>Email</th><th></th>";
		foreach ($users as $user) {
			echo "<tr>"
				."<td>".$user["Last_name"]."</td>"
				."<td>".$user["First_name"]."</td>"
				."<td class='center small'>".$user["Nationality"]."</td>"
				."<td class='center small'>".change_date_format($user["Date_of_birth"])."</td>"
				."<td class='center small'>".$user["Sex"]."</td>"
				."<td class='center small'>".$user["Category"]."</td>"
				."<td>".$user["Email"]."</td>"
				."<td class='center small'><button class='text_button' name='delete' value='".$user["ID"]."'>Delete</button></td>"
				."</tr>";
		}
		echo "</table>";
	}
?>