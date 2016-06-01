<?php
	require_once("includes/startsession.php");	
	$page_title = "Invite Visitors";
	require_once("includes/header.php");
	
	if(!$_POST) {
		$_SESSION["date"]="0000-00-00";
		unset($_SESSION["course"]);
	}
	
	if(isset($_POST["date1"]))
		$_SESSION["date"]=$_POST["date1"];
	
	if(isset($_POST["course"]))
		$_SESSION["course"]=get_course($conn, $_POST["course"]);
	
	
	if(isset($_POST["remove_contact"])) 		
		remove_contacts ($conn, get_contacts ($conn, $_POST["remove_contact"]), $_SESSION["date"]);
		
	if(isset($_POST["begin_hour"]))
		$_SESSION["begin_hour"] = $_POST["begin_hour"];
		
	if(isset($_POST["end_hour"]))
		$_SESSION["end_hour"] = $_POST["end_hour"];
		
	if(isset($_POST["begin_minutes"]))
		$_SESSION["begin_minutes"] = $_POST["begin_minutes"];
	
	if(isset($_POST["end_minutes"]))
		$_SESSION["end_minutes"] = $_POST["end_minutes"];
		
	if(isset($_POST["contact"])) 		
		save_contacts ($conn, get_contacts ($conn, $_POST["contact"]), $_SESSION["date"], $_SESSION["begin_hour"].":".$_SESSION["begin_minutes"].":00", $_SESSION["end_hour"].":".$_SESSION["end_minutes"].":00");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					

	add_visitors ($conn);

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function add_visitors ($conn) {
		echo "<table class='table'>";
		echo "<tr><td><strong>Date: </strong></td><td>";
		require_once("includes/calendar_2.php");
		echo "</td></tr>";
		echo "<tr><td><strong>Time: </strong></td><td>From ";
		$minutes = array ("00","05","10","15","20","25","30","35","40","45","50","55");
		$hours = array ("08","09","10","11","12","13","14","15","16");
		echo "<select name='begin_hour'><option value='-1'>-</option>'";
		for($i=0; $i<count($hours);$i++)
			echo "<option value='".$hours[$i]."' ".($_SESSION["begin_hour"]==$hours[$i]?"selected":"").">".$hours[$i]."</option>";
		echo "</select> ";		
		echo "<select name='begin_minutes'><option value='-1'>-</option>'";
		for($i=0; $i<count($minutes);$i++)
			echo "<option value='".$minutes[$i]."' ".($_SESSION["begin_minutes"]==$minutes[$i]?"selected":"").">".$minutes[$i]."</option>";
		echo "</select> till ";		
		echo "<select name='end_hour'><option value='-1'>-</option>'";
		for($i=0; $i<count($hours);$i++)
			echo "<option value='".$hours[$i]."' ".($_SESSION["end_hour"]==$hours[$i]?"selected":"").">".$hours[$i]."</option>";
		echo "</select> ";		
		echo "<select name='end_minutes'><option value='-1'>-</option>'";
		for($i=0; $i<count($minutes);$i++)
			echo "<option value='".$minutes[$i]."' ".($_SESSION["end_minutes"]==$minutes[$i]?"selected":"").">".$minutes[$i]."</option>";
		echo "</select></td></tr> ";
		echo "</table></br>";	
		
		$gen_courses = array ("art","mat","l1-","mus","ddm", "ep-");
		$class = false;
		$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
		foreach ($courses as $course) {			
			if(substr($course["Code"],2,3)=="mat")
				echo "<button name='course' value='".$course["Code"]."' class='text_button'>".substr($course["Code"],0,2).substr($course["Code"],5,3)."</button> ";
			if(!in_array(substr($course["Code"],2,3),$gen_courses))
				echo "<input type='submit' value='".$course["Code"]."' name='course' class='text_button'/> ";		
		}
			
		if(isset($_SESSION["course"])) {		
			$students = get_all_students_course($conn,$_SESSION["course"]);
			show_list($conn, $students);
		}
		else
			echo "</br></br></br></br></br></br></br></br></br></br></br></br></br>";
	}
	
	function show_list ($conn, $data) {
		echo "</br></br>";
		echo "<h3>Visitors: ".change_date_format($_SESSION["date"])."</h3>";
		echo "<table class='table'>";		
		echo "<th>Surname</th><th>Name</th><th>Time</th><th></th>";
		foreach ($data as $value) {
			$color = "red";
			if (in_list ($conn, $value["ID"], $_SESSION["date"]))
				$color = "green";
			echo "<tr style='background-color:$color;'><td>".$value["Last_name"]."</td>"
				."<td>".$value["First_name"]."</td>"
				."<td class='small center'>".get_time($conn, $value["ID"], $_SESSION["date"])."</td>";
			if($color != "red")
				echo "<td class='center small'><button type='submit' name='remove_contact' value='".$value["ID"]."' class='text_button'>Remove</button></td></tr>";
			else
				echo "<td class='center small'><button type='submit' name='contact' value='".$value["ID"]."' class='text_button'>Add</button></td></tr>";		
		}
		echo "</table>";		
	}
	
	function save_contacts ($conn, $contacts, $date, $begin, $end) {
		foreach ($contacts as $contact)
			add_visitor ($conn, $_SESSION["user"]["ID"], $contact["ID"], $date, $begin, $end);
	}
	
	function remove_contacts ($conn, $contacts, $date) {
		foreach ($contacts as $contact)
			delete_visitor ($conn, $_SESSION["user"]["ID"], $contact["ID"], $date);
	}
	
	function in_list ($conn, $student_id, $date) {
		$contacts = get_contacts ($conn, $student_id);
		$visitors = get_visitors ($conn, $date);
		foreach ($contacts as $contact) {
			if(!find_contact($contact, $visitors))
				return false;
		}
		return true;
	}
	
	function find_contact ($contact, $visitors) {
		foreach ($visitors as $visitor) 
			if($visitor["Contact_id"]==$contact["ID"])
				return true;
		return false;
	}
	
	function get_time ($conn, $student_id, $date) {
		$list = array ();
		$contacts = get_contacts ($conn, $student_id);
		foreach ($contacts as $contact)
			array_push($list,$contact["ID"]);
		$visitors = get_visitors ($conn, $date);
		foreach ($visitors as $visitor)
			if (in_array($visitor["Contact_id"],$list))
				return substr($visitor["Begin"],0,5)." - ".substr($visitor["End"],0,5);
		return false;
	}
?>