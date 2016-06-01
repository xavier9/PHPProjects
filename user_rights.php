<?php	
	require_once("includes/startsession.php");	
	$page_title = "User Rights";
	require_once("includes/header.php");

	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";	
	
	if(isset($_POST["submit"])) {
		$_SESSION["admin_user"]=$_SESSION["user"];
		$_SESSION["user"]=get_user($conn,$_POST["user"]);
		header("Location: home.php");
	}
	
	if(isset($_POST["search"]))
		$_SESSION["user_rights"] = get_user ($conn, $_POST["user"]);		
	
	if(isset($_POST["yes"])) 
		update_rights($conn,get_user_rights($conn, $_SESSION["user_rights"]["ID"]),$_POST["yes"],false);

	if(isset($_POST["no"])) 
		update_rights($conn,get_user_rights($conn, $_SESSION["user_rights"]["ID"]),$_POST["no"],true);
		
	if(isset($_POST["next"])) 
		$_SESSION["user_rights"] = get_next_user ($conn);		
		
	if(isset($_POST["previous"])) 
		$_SESSION["user_rights"] = get_previous_user ($conn);		
	
	if(isset($_POST["search"]) || isset($_POST["yes"]) || isset($_POST["no"]) || isset($_POST["next"]) || isset($_POST["previous"])) {		
		print_select_user ($conn);
		echo "<table class='table_no_border'>"
			."<tr>"
			."<td><button name='previous' value='".$_SESSION["user_rights"]["ID"]."' class='text_button'><</button></td>"
			."<td class='center'><h3>".$_SESSION["user_rights"]["Last_name"]." ".$_SESSION["user_rights"]["First_name"]."</h3></td>"	
			."<td><button name='next' value='".$_SESSION["user_rights"]["ID"]."' class='text_button float_right'>></button></td>"
			."</tr>"
			."</table>";
		$menus = get_menu($conn);
		$counter = 0;
		foreach ($menus as $menu) {
			if($counter==0)
				echo "<div class='column'>";
			echo "<div class='rights'>";
			echo "<table class='table_no_border'>";
			echo "<h4>".$menu["Description"]."</h4>";
			$items = get_menu_items($conn, $menu["ID"]);
			foreach ($items as $item) {
				echo "<tr><td>".$item["Description"]."</td>";
				if(has_access ($item["Rights"], get_user_rights ($conn,$_SESSION["user_rights"]["ID"])))
					echo "<td class='small right'><button type='submit' value='".$item["Rights"]."' name='yes' class='on_button'>Yes</button></td>";
				else
					echo "<td class='small right'><button type='submit' value='".$item["Rights"]."' name='no' class='off_button'>No</button></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "</div>";			
			$counter++;
			if($counter>2) {
				$counter=0;
				echo "</br></div>";
			}
		}
	}
	else {
		unset ($_SESSION["user_rights"]);
		print_select_user ($conn);
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function print_select_user ($conn) {
		$users = get_all_users($conn);
		echo "<select name='user'>";
		foreach ($users as $user) {
			echo "<option value='".$user["ID"]."' ";
			if(isset($_SESSION["user_rights"]) && $_SESSION["user_rights"]["ID"]==$user["ID"])
				echo "selected";
			echo ">".$user["Last_name"]." ".$user["First_name"]."</option>";
		}
		echo "</select> ";	
		echo "<input type='submit' value='Show Rights' name='search' class='text_button'/> ";
		echo "<input type='submit' value='Impersonate' name='submit' class='text_button'/> ";
	}
	
	function update_rights ($conn, $rights, $right, $activate) {
		if(!$activate) 
			$rights["Rights"] = str_replace ($right, "", $rights["Rights"]);
		else {
			$rights["Rights"].=$right;
			$stringParts = str_split($rights["Rights"]);
			sort($stringParts);
			$rights["Rights"] = implode('', $stringParts);
		}
			
		$query = "UPDATE b2_user_rights SET Rights = '".$rights["Rights"]."' WHERE ID = '".$rights["ID"]."';";
		mysqli_query($conn,$query);
	}
	
	function get_next_user ($conn) {
		$users = get_all_users($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["user_rights"]) {
				if($i+1==count($users))
					return $users[0];
				else 
					return $users[$i+1];
			}
		}
	}
	
	function get_previous_user ($conn) {
		$users = get_all_users($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["user_rights"]) {
				if($i==0)
					return $users[count($users)-1];
				else 
					return $users[$i-1];
			}
		}
	}
?>
