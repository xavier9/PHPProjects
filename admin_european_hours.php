<?php
	require_once("includes/startsession.php");	
	$page_title = "Manage European Hours";
	require_once("includes/header.php");
	require_once("includes/show_info.php");	
	
	if(isset($_POST["update"])) 
		update_all_eur($conn);
	
	if(isset($_POST["show"])) 
		$_SESSION["european_hours"] = get_user($conn,$_POST["teacher"]);
		
	if(isset($_POST["next"])) 
		$_SESSION["european_hours"] = get_next_user ($conn);		
		
	if(isset($_POST["previous"])) 
		$_SESSION["european_hours"] = get_previous_user ($conn);	
	
	if(isset($_POST["save"])) 
		save_european_hours ($conn, $_POST["courses"], $_POST["begin_date"], $_POST["end_date"]);
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	$users = get_eur_teachers ($conn, get_all_users($conn));
	show_eur_teachers ($conn, $users);
		
	if(isset($_POST["show"]) || isset($_POST["previous"]) || isset($_POST["next"]) || isset($_POST["save"])) {
		$courses = get_eur_teacher_courses($conn,$_SESSION["european_hours"]);
		show_eur($conn, $_SESSION["european_hours"], $courses);
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function get_eur_teachers ($conn) {
		$teachers = array ();
		$ids = array ();
		$courses = get_eur_courses ($conn);
		foreach ($courses as $course) {
			$teacher_ids = explode(",",$course["Teacher_id"]);
			foreach ($teacher_ids as $teacher_id) {
				if(!in_array($teacher_id, $ids)) {}
					array_push($ids, $teacher_id);
			}
		}
		foreach ($ids as $id)
			array_push ($teachers, get_user($conn, $id));
		return sort_array($teachers,"Last_name");		
	}
	
	function show_eur_teachers ($conn, $users) {
		echo "<select name='teacher'>";
		foreach ($users as $user) {			
			echo "<option value='".$user["ID"]."' ";
			if(isset($_SESSION["european_hours"]) &&  $user["ID"]==$_SESSION["european_hours"]["ID"])
				echo "selected";
			echo ">".$user["Last_name"]." ".$user["First_name"]."</option>";
		}
		echo "</select> ";
		echo "<input type='submit' value='Show' name='show' class='text_button'/> ";
		echo "<input type='submit' value='Update European Hours' name='update' class='text_button'/> ";
	}
	
	function show_eur ($conn, $user, $courses) {
		echo "<table class='table_no_border'>";
		echo "<tr><td class='small'><input type='submit' value='<' name='previous' class='text_button float_left'/></td>"
			."<td class='center'><h3>".$user["Last_name"]." ".$user["First_name"]."</h3></td>"
			."<td class='small'><input type='submit' value='>' name='next' class='text_button float_right'/></td></tr>"
			."</table>";
		echo "<table class='table'>";
		echo "<th>Course</th><th>Begin</th><th>End</th>";
		foreach ($courses as $course) {
			$begin = get_begin_eur ($conn, $user, $course["Code"]);
			$end = get_end_eur ($conn, $user, $course["Code"]);
			echo "<tr><td class='center'>".$course["Code"]."</td>"
			."<td class='hidden'><input type='text' name='courses[]' value='".$course["Code"]."' /></td>"
			."<td class='center'><input type='text' name='begin_date[]' value='";
			if($begin!="0000-00-00" && $begin!=0) 
				echo change_date_format($begin);
			echo "' class='small'/></td>"
			."<td class='center'><input type='text' name='end_date[]' value='";
			if($end!="0000-00-00" && $end!=0) 
				echo change_date_format($end);
			echo"' class='small'/></td>"
			."</tr>";
		}
		echo "</table></br>";
		echo "<input type='submit' value='Save' name='save' class='text_button'/> ";
	}
	
	function save_european_hours ($conn, $courses, $begin, $end) {
		for($i=0; $i<count($courses);$i++) {
			if(eur_exists ($conn, $_SESSION["european_hours"], $courses[$i]))
				update_eur ($conn, $_SESSION["european_hours"], $courses[$i], change_date_format_db($begin[$i]), change_date_format_db($end[$i]));
			else
				insert_eur ($conn, $_SESSION["european_hours"], $courses[$i], change_date_format_db($begin[$i]), change_date_format_db($end[$i]));
		}	
	}
	
	function get_next_user ($conn) {
		$users = get_eur_teachers($conn, get_all_users($conn));
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["european_hours"]) {
				if($i+1==count($users))
					return $users[0];
				else 
					return $users[$i+1];
			}
		}
	}
	
	function get_previous_user ($conn) {
		$users = get_eur_teachers($conn, get_all_users($conn));
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["european_hours"]) {
				if($i==0)
					return $users[count($users)-1];
				else 
					return $users[$i-1];
			}
		}
	}
	
	function update_all_eur ($conn) {
		$data = get_eur_timetable_template($conn);
		$counter = 0;
		foreach ($data as $value) {
			$teacher = get_user($conn, $value["Teacher_id"]);
			$eur_courses = get_current_eur($conn,$teacher);
			if(count($eur_courses)==1) {
				update_timetable_template_teacher_course($conn, $value["ID"],$eur_courses[0]["Course_id"]);
				$counter=0;
			}
			else {
				update_timetable_template_teacher_course($conn, $value["ID"],$eur_courses[$counter]["Course_id"]);
				$counter++;
				if($counter>=count($eur_courses))
					$counter=0;
			}
		}		
	}
?>