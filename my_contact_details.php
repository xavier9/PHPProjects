<?php
	require_once("includes/startsession.php");	
	$page_title = "My Contact Details";
	require_once("includes/header.php");
	
	if(isset($_POST["submit"])) {
		$details["Last_name"] = $_POST["last_name"];
		$details["First_name"] = $_POST["first_name"];
		$details["Address_1"] = $_POST["address_1"];
		$details["Address_2"] = $_POST["address_2"];
		$details["Address_3"] = $_POST["address_3"];
		$details["City"] = $_POST["city"];
		$details["Postalcode"] = $_POST["postalcode"];
		$details["Date_of_birth"] = change_date_format_db($_POST["date_of_birth"]);
		$details["Sex"] = $_POST["sex"];
		$details["Home_phone"] = $_POST["home_phone"];
		$details["Mobile_phone"] = $_POST["mobile_phone"];	
		save_details ($conn, $details);
	}
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	$details = get_contact_details ($conn, $_SESSION["user"]["ID"]);
	$changes = get_contact_details_changes ($conn, $_SESSION["user"]["ID"]);
	echo "<table class='table_no_border'>";
	echo "<tr><td class=''><strong>Surname:</td>"
		."<td><input type='text' name='last_name' size='60' ".(is_changed($changes,"Last_name")?"style='color:red;'":"")." "
		."value='".(is_changed($changes,"Last_name")?get_value($changes,"Last_name"):$_SESSION["user"]["Last_name"])."'/></td></tr>"
		."<tr><td><strong>Name:</strong></td>"
		."<td><input type='text' name='first_name' size='60' value='".$_SESSION["user"]["First_name"]."'/></td></tr>"
		."<tr><td><strong>Address:</strong></td>"
		."<td><input type='text' name='address_1' size='60' value='".$details["Address_1"]."'/></td></tr>"
		."<tr><td><strong></strong></td>"
		."<td><input type='text' name='address_2' size='60' value='".$details["Address_2"]."'/></td></tr>"
		."<tr><td><strong></strong></td><td><input type='text' name='address_3' size='60' value='".$details["Address_3"]."'/></td></tr>"
		."<tr><td><strong>Postalcode:</strong></td><td><input type='text' size='60' name='postalcode' value='".$details["Postalcode"]."'/></td></tr>"
		."<tr><td><strong>City:</strong></td><td><input type='text' name='city' size='60' value='".$details["City"]."'/></td></tr>"
		."<tr><td><strong>Date of Birth:</strong></td><td><input type='text' size='60' name='date_of_birth' value='".change_date_format($_SESSION["user"]["Date_of_birth"])."'/></td></tr>"
		."<tr><td><strong>Gender:</strong></td><td><input type='text' name='sex' size='60' value='".$_SESSION["user"]["Sex"]."'/></td></tr>"
		."<tr><td><strong>Nationality:</strong></td><td><input type='text' name='nationality' size='60' value='".$_SESSION["user"]["Nationality"]."'/></td></tr>"
		."<tr><td><strong>Home Phone:</strong></td><td><input type='text' name='home_phone' size='60' value=''/></td></tr>"
		."<tr><td><strong>Mobile Phone:</strong></td><td><input type='text' name='mobile_phone' size='60' value=''/></td></tr>";
		
	echo "</table>";	
	
	echo "<input type='submit' name='submit' value='Submit' class='text_button'/>";
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function save_details ($conn, $data) {
		$details = get_contact_details ($conn, $_SESSION["user"]["ID"]);
		$info = array("Address_1","Address_2","Address_3","Postalcode","City","Mobile_phone","Home_phone");
		$info_2 = array ("Last_name","First_name","Date_of_birth","Sex");
		foreach ($info as $value) {
			if($details[$value]!=$data[$value])
				add_contact_details_changes($conn, $_SESSION["user"]["ID"], $value, $data[$value]);
		}
		foreach ($info_2 as $value) {
			if($_SESSION["user"][$value]!=$data[$value])
				add_contact_details_changes($conn, $_SESSION["user"]["ID"], $value, $data[$value]);
		}
	}

	function is_changed ($data, $field) {
		foreach ($data as $value)
			if($value["Field"]==$field)
				return true;
		return false;
	}
	
	function get_value ($data, $field){
		foreach ($data as $value)
			if($value["Field"]==$field)
				return $value["Value"];
	}
	
	function get_contact_details ($conn, $id){
		$query = "SELECT * FROM b2_contact_details WHERE ID = '".$id."'";
		$data = mysqli_query($conn,$query);
		return mysqli_fetch_assoc($data);
	}
	
	function get_contact_details_changes ($conn, $id){
		$query = "SELECT * FROM b2_contact_details_changes WHERE Teacher_id = '".$id."'";
		return get_values(mysqli_query($conn,$query));
	}
	
	function add_contact_details_changes($conn, $teacher_id, $field, $value){
		$query = "INSERT INTO b2_contact_details_changes (Teacher_id, Field, Value) VALUES ('".$teacher_id."','".$field."','".$value."')";
		mysqli_query($conn,$query);
	}
?>