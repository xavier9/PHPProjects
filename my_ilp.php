<?php
	require_once("includes/startsession.php");	
	$page_title = "My ILP";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
		
	if(isset($_POST["save"])) {
		save_ilp ($conn);
		show_ilp ($conn, get_student($conn,$_POST["save"]));
	}
	
	if(isset($_POST["save_2"])) {
		save_ilp_2 ($conn);
		show_ilp_2 ($conn, get_student($conn,$_POST["save_2"]));
	}
	
	if(isset($_POST["page_1"]))
		show_ilp ($conn, get_student($conn,$_POST["page_1"]));
	
	if(isset($_POST["page_2"]))
		show_ilp_2 ($conn, get_student($conn,$_POST["page_2"]));
	
	if(isset($_POST["show"]))
		show_ilp ($conn, get_student($conn,$_POST["show"]));
	
	if(isset($_POST["show_2"]))
		show_ilp_2 ($conn, get_student($conn,$_POST["show_2"]));
	
	if(isset($_GET["show"]))
		show_ilp ($conn, get_student($conn,$_GET["show"]));
	
	if(isset($_GET["show_2"]))
		show_ilp_2 ($conn, get_student($conn,$_GET["show_2"]));
	
	if(!isset($_POST["show"]) && !isset($_POST["save"])  && !isset($_POST["show_2"]) && !isset($_GET["show"]) && !isset($_GET["show_2"]) 
		&& !isset($_POST["page_1"]) && !isset($_POST["page_2"]) && !isset($_POST["save_2"]))
		show_students ($conn);
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_students ($conn) {				
		$students = get_students_ilp ($conn);
		if (count ($students) != 0) {
			echo "<table class='table'>";
			echo "<th>Surname</th><th>Name</th><th>Page 1</th><th>Page 2</th>";
			foreach ($students as $student) {
				echo "<tr>"
					."<td>".$student["Last_name"]."</td>"
					."<td>".$student["First_name"]."</td>"
					."<td class='center small'><button type='submit' class='text_button' value='".$student["ID"]."' name='show'>Show</button></td>"
					."<td class='center small'><button type='submit' class='text_button' value='".$student["ID"]."' name='show_2'>Show</button></td>"
					."</tr>";
			}
			echo "</table>";
		}
		else 
			echo "<p class='err'>You don't have any LS Moderate students.</p>";
	}
	
	function save_ilp_2 ($conn) {
		if(!($_POST["add_objectives"] == "" && $_POST["add_responsible"] == "" && $_POST["add_methods"] =="" & $_POST["add_assessment"] == "" 
				&& $_POST["add_february"] == "0" && $_POST["add_june"] == "0" ))
				add_ilp_2 ($conn, $_POST["save_2"], $_POST["add_objectives"], $_POST["add_responsible"], $_POST["add_methods"], $_POST["add_assessment"], $_POST["add_february"], $_POST["add_june"]);
		if (isset($_POST["id"])) {
			for ($i=0; $i<count ($_POST["id"]); $i++) {
				$ilp = get_ilp_2_on_id ($conn, $_POST["id"]);
				update_ilp_2 ($conn, $_POST["id"][$i], $_POST["objectives"][$i], $_POST["responsible"][$i], $_POST["methods"][$i], $_POST["assessment"][$i], $_POST["february"][$i], $_POST["june"][$i]);
			}		
		}
		update_start_date ($conn, $_POST["save_2"], $_POST["start_date"]);
	}
	
	function update_start_date ($conn, $student_id, $start_date) {
		$query = "UPDATE b2_ilp SET Start_date = '".change_date_format_db($start_date)."' WHERE Student_id = '".$student_id."'";
		mysqli_query($conn,$query);
	}
	
	function add_ilp_2 ($conn, $student_id, $objectives, $responsible, $methods, $assessment, $february, $june) {
		$query = "INSERT INTO b2_ilp_2 (Student_id, Objectives, Responsible, Methods, Assessment, February, June) VALUES ('".$student_id."', '".$objectives."', '".$responsible."', '".$methods."', '".$assessment."', '".$february."', '".$june."');";
		mysqli_query($conn,$query);
	}
	
	function update_ilp_2 ($conn, $id, $objectives, $responsible, $methods, $assessment, $february, $june) {
		$query = "UPDATE b2_ilp_2 SET Objectives = '".$objectives."', Responsible = '".$responsible."', Methods = '".$methods
			."', Assessment = '".$assessment."', February = '".$february."', June = '".$june."' WHERE ID = '".$id."'";
		mysqli_query($conn,$query);
	}
	
	function save_ilp ($conn) {
		$ilp = get_ilp ($conn, get_student ($conn, $_POST["save"]));
		if ($ilp)
			update_ilp ($conn, $_POST["save"], $_POST["start_date"], $_POST["other_info"], $_POST["description"], $_POST["strong_points"], $_POST["languages"], $_POST["extra_info"]);
		else
			add_ilp ($conn, $_POST["save"], $_POST["start_date"], $_POST["other_info"], $_POST["description"], $_POST["strong_points"], $_POST["languages"], $_POST["extra_info"]);
	}
	
	function get_ilp ($conn, $student) {
		$query = "SELECT * FROM b2_ilp WHERE Student_id = '".$student["ID"]."'";
		return mysqli_fetch_assoc(mysqli_query($conn,$query));
	}
	
	function get_ilp_2 ($conn, $student) {
		$query = "SELECT * FROM b2_ilp_2 WHERE Student_id = '".$student["ID"]."'";
		return get_values(mysqli_query($conn,$query));
	}
	
	function get_ilp_2_on_id ($conn, $id) {
		$query = "SELECT * FROM b2_ilp_2 WHERE ID = '".$id."'";
		return mysqli_fetch_assoc(mysqli_query($conn,$query));
	}
	
	function add_ilp ($conn, $student_id, $start_date, $other_info, $description, $strong_points, $language, $extra_info) {
		$date = "0000-00-00";
		if ($start_date != "") 
			$date = change_date_format_db($start_date);
		$query = "INSERT INTO b2_ilp (Student_id, Info, Description, Strengths, Start_date, Language, Extra_info) VALUES ('".$student_id."', '".$other_info."', '".$description."', '".$strong_points."', '".$date."', '".$language."', '".$extra_info."');";
		mysqli_query($conn,$query);
	}
	
	function update_ilp ($conn, $student_id, $start_date, $other_info, $description, $strong_points, $language, $extra_info) {
		$query = "UPDATE b2_ilp SET Info = '".$other_info."', Description = '".$description."', Strengths = '".$strong_points."', Start_date = '".change_date_format_db($start_date)."', Language = '".$language."', Extra_info = '".$extra_info."' WHERE Student_id = '".$student_id."'";
		mysqli_query($conn,$query);
	}
	
	function show_ilp ($conn, $student) {
		show_info_student_ilp ($conn, $student);
		$l2_course = get_l2_course($conn, $student);
		$l2 = array ("en"=>"English", "fr"=>"French", "de"=>"German");
		$ilp = get_ilp ($conn, $student);
		echo "<strong>Language history</strong></br></br>";
		echo "<table class='table_no_border'>";
		echo "<tr><td>Languages spoken at home:</td><td><input type='text' name='languages' value='".$ilp["Language"]."' size='60'/></tr>";
		echo "<tr><td>Second language (at school):</td><td>".$l2[substr($l2_course["Code"],5,2)]."</td></tr>";
		echo "<tr><td>Additional information:</td><td><input type='text' name='extra_info' value='".$ilp["Extra_info"]."' size='60' /></tr>";
		echo "</table>";
		echo "</br>";
		echo "<strong>Other information: (speech therapy, allergies, adaptations, ...)</strong></br></br>";
		echo "<textarea name='other_info' rows = '4' cols= '90'>".(isset($ilp["Info"])?$ilp["Info"]:"")."</textarea>";
		echo "</br></br>";
		echo "<strong>Description of the pupil's special needs and challenges (weakness, difficulties):</strong></br></br>";
		echo "<textarea name='description' rows = '10' cols= '90'>".(isset($ilp["Description"])?$ilp["Description"]:"")."</textarea>";
		echo "</br></br>";
		echo "<strong>Strengths and pupil's and learning styles: (academic, social/ emotional/ personality/ extra curricula):</strong></br></br>";
		echo "<textarea name='strong_points' rows = '10' cols= '90'>".(isset($ilp["Strengths"])?$ilp["Strengths"]:"")."</textarea>";		
		echo "</br></br>";
		echo "<button name='save' class='text_button' value='".$student["ID"]."'>Save</button> ";
		echo "<button name='page_2' class='text_button' value='".$student["ID"]."'>Page 2</button> ";
		
	}
	
	function show_ilp_2 ($conn, $student) {			
		$progress = array ("NMP","NA","A");
		show_info_student_ilp ($conn, $student);
		echo "<table class='table'>";
		echo "<th>Objectives of support</br></br>(Specify, what aspect(s) of the subject/learning area is been targeted?)</th>"
			."<th>Persons responsible / ILP Written by + DATE!:</th>"
			."<th>Methods</br></br>(What methods are usded to reach the objectives?)</th>"
			."<th>Assessment</br></br>(What toolas are used to assess progress?)</th>"
			."<th colspan='3'>Progress</br></br>Needs more practice (NMP)</br>Nearly achieved (NA)</br>Achieved (A)</th>";
		$ilps = get_ilp_2 ($conn, $student);
		foreach ($ilps as $ilp) {
			echo "<tr>"
				."<td class='hidden'><input type='text' name='id[]' value='".$ilp["ID"]."' /></td>"
				."<td><textarea cols = '29' rows = '3' name='objectives[]'>".$ilp["Objectives"]."</textarea></td>"
				."<td><textarea cols = '22' rows = '3' name='responsible[]'>".$ilp["Responsible"]."</textarea></td>"
				."<td><textarea cols = '23' rows = '3' name='methods[]'>".$ilp["Methods"]."</textarea></td>"
				."<td><textarea cols = '20' rows = '3' name='assessment[]'>".$ilp["Assessment"]."</textarea></td>";				
			echo "<td><select name='february[]'><option value='0'>-</option>";
			foreach ($progress as $value)
				echo "<option value='".$value."' ".($ilp["February"]==$value?"selected":"").">".$value."</option>";
			echo "</select></td><td>";
			echo "<select name='june[]'><option value='0'>-</option>";
			foreach ($progress as $value)
				echo "<option value='".$value."' ".($ilp["June"]==$value?"selected":"").">".$value."</option>";
			echo "</select></td></tr>";
		}
		
		echo "<tr>"
			."<td><textarea cols = '29' rows = '3' name='add_objectives'></textarea></td>"
			."<td><textarea cols = '22' rows = '3' name='add_responsible'></textarea></td>"
			."<td><textarea cols = '23' rows = '3' name='add_methods'></textarea></td>"
			."<td><textarea cols = '20' rows = '3' name='add_assessment'></textarea></td>"
			."<td>";		
		
		echo "<select name='add_february'><option value='0'>-</option>";
		foreach ($progress as $value)
			echo "<option value='".$value."'>".$value."</option>";
		echo "</select></td><td>";
		echo "<select name='add_june'><option value='0'>-</option>";
		foreach ($progress as $value)
			echo "<option value='".$value."'>".$value."</option>";
		echo "</select></td></tr>";
		echo "</table>";
		echo "</br></br>";
		echo "<button name='save_2' class='text_button' value='".$student["ID"]."'>Save</button> ";
		echo "<button name='page_1' class='text_button' value='".$student["ID"]."'>Page 1</button> ";
	}
	
	function show_info_student_ilp ($conn, $student) {
		echo "<table class='table_no_border'>";
		echo "<tr><td rowspan='4'><img class='Picture' id='".$student["Last_name"]." ".$student["First_name"]."' src='".get_photo($student["ID"])."' width='120' height='177'/></td>";
		$teacher = get_class_teacher ($conn, $student["Class"]);
		$lsm_teachers = get_lsm_teachers ($conn, $student);
		$ilp = get_ilp ($conn, $student);
		echo "<td class='small'><strong>Name: </strong></td>"
			."<td>".$student["Last_name"]." ".$student["First_name"]."</td>"
			."<td><strong>Date of birth: </strong></td>"
			."<td>".change_date_format($student["Date_of_birth"])."</td></tr>"
			."<tr><td><strong>Class and section:</strong></td>"
			."<td>".$student["Class"]." / ".substr($student["Class"],2,2)."</td>"
			."<td><strong>Class teacher:</strong></td>"
			."<td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td></tr>"
			."<td><strong>Start date LS support:</td>"
			."<td><input typ='text' name='start_date' size='9' value='".(($ilp["Start_date"]!="0000-00-00" && $ilp["Start_date"]!="")?change_date_format($ilp["Start_date"]):"")."' /> (dd/mm/yyyy)</td>"
			."<td><strong>LSM Teacher(s):</strong></td>"
			."<td>";
		foreach ($lsm_teachers as $lsm_teacher)
			echo $lsm_teacher["Last_name"]." ".$lsm_teacher["First_name"]."</br>";
		echo "</td></tr>";	
		echo "</table></br>";		
	}
	
	function get_lsm_teachers ($conn, $student) {
		$courses = get_all_student_teacher_courses_on_student ($conn, $student);
		$teachers = array ();
		foreach ($courses as $course) {
			if(substr($course["Course_id"],2,3) == "kma" 
				|| substr($course["Course_id"],2,3) == "kl1"
				|| substr($course["Course_id"],2,3) == "kl2") {
						$teacher = get_user ($conn, $course["Teacher_id"]);
						if(!in_array ($teacher, $teachers))
							array_push($teachers, $teacher);
			}
		}
		return $teachers;
	}
	
	function get_students_ilp ($conn) {
		$courses = get_all_ls_courses($conn,$_SESSION["user"]);
		$students = array ();
		foreach ($courses as $course) {
			$temp_course = get_course ($conn, $course["Course_id"]);
			$data = get_all_students_course ($conn, $temp_course);
			foreach ($data as $value){
				if(!in_array($value, $students))
					array_push($students, $value);
			}
		}
		
		if (is_class_teacher ($conn, $_SESSION["user"])) {
			$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "mat");
			if (empty($courses))
				$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "gen");
			$data = array ();
			foreach ($courses as $value) 
				$data = array_merge ($data, get_all_students_course($conn,$value));	
			foreach ($data as $value) {
				$courses = get_all_student_teacher_courses_on_student ($conn, $value);
				foreach ($courses as $course) {
					if(substr($course["Course_id"],2,3) == "kma" 
						|| substr($course["Course_id"],2,3) == "kl1"
						|| substr($course["Course_id"],2,3) == "kl2") {
							if(!in_array ($value, $students))
								array_push($students, $value);
					}
				}
			}
		}
		
		if (count($students) != 0)		
			return sort_array ($students, "Last_name");
	}
	
	function get_all_ls_courses ($conn, $teacher) {
		$query = "SELECT * FROM b2_teacher_course 
			WHERE Teacher_id = '".$teacher["ID"]."' AND (SUBSTRING(Course_id,3,3) = 'kma' 
			OR SUBSTRING(Course_id,3,3) = 'kl1' 
			OR SUBSTRING(Course_id,3,3) = 'kl2')";
		return get_values(mysqli_query($conn,$query));
	}
	
	function is_class_teacher ($conn, $teacher) {
		$classes = get_all_classes ($conn, "");
		foreach ($classes as $class) {
			$class_teacher = get_class_teacher ($conn, $class["Class"]);
			if ($class_teacher == $teacher)
				return true;
		}
		return false;		
	}
	
	function get_l2_course ($conn, $student) {
		$courses = get_all_student_teacher_courses_on_student ($conn, $student);
		foreach ($courses as $course) {			
			if(substr($course["Course_id"],2,3)=="l2-")
				return get_course ($conn, $course["Course_id"]);				
		}
	}
?>