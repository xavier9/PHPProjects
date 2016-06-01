<?php
	require_once("includes/startsession.php");	
	$page_title = "My European Hours";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	
	
	if(isset($_POST["next"])) {
		$student = get_next_student ($conn, $_POST["next"], $_SESSION["course"]);
		show_evaluation_student ($conn, $student["ID"]);
	}
	
	if(isset($_POST["previous"])) {
		$student = get_previous_student ($conn, $_POST["previous"], $_SESSION["course"]);
		show_evaluation_student ($conn, $student["ID"]);
	}
	
	if(isset($_POST["save"])) {
		save_evaluation ($conn);
		show_evaluation_student ($conn, $_POST["save"]);
	}
	
	if(isset($_POST["show"])) 
		show_evaluation_eur ($conn, $_POST["show"]);
	
	if(isset($_POST["back"])) 
		show_evaluation_eur ($conn, $_POST["back"]);
	
	if(isset($_POST["student"]))
		show_evaluation_student ($conn, $_POST["student"]);
	
	if(!isset($_POST["show"]) && !isset($_POST["student"]) && !isset($_POST["save"]) && !isset($_POST["back"]) && !isset($_POST["previous"]) && !isset($_POST["next"])) {
		$courses = get_eur_teacher_courses($conn,$_SESSION["user"]);
		show_eur($conn, $_SESSION["user"], $courses);
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function save_evaluation ($conn) {
		$eval = get_evaluation ($conn, $_SESSION["user"]["ID"],$_POST["save"]);
		if ($eval)
			update_evaluation ($conn, $_POST["save"], $_SESSION["user"]["ID"], $_POST["comment"], $_POST["grade"]);
		else
			add_evaluation ($conn, $_POST["save"], $_SESSION["user"]["ID"], $_POST["comment"], $_POST["grade"]);
		
	}
	
	function get_evaluation ($conn, $teacher_id, $student_id) {
		$query = "SELECT * FROM b2_eur_eval WHERE Student_id = '".$student_id."' AND Teacher_id = '".$teacher_id."'";
		$data = mysqli_query($conn, $query);
		return mysqli_fetch_assoc($data);
	}
	
	function add_evaluation ($conn, $student_id, $teacher_id, $comment, $grade) {
		$query = "INSERT INTO b2_eur_eval (Student_id, Teacher_id, Comment, Grade) VALUES ('".$student_id."', '".$teacher_id."', '".$comment."', '".$grade."')";
		mysqli_query($conn, $query);
	}
	
	function update_evaluation ($conn, $student_id, $teacher_id, $comment, $grade) {
		$query = "UPDATE b2_eur_eval SET Comment = '".$comment."', Grade ='".$grade."' WHERE Student_id = '".$student_id."' AND Teacher_id = '".$teacher_id."';";
		mysqli_query($conn, $query);
	}
	
	function show_eur ($conn, $user, $courses) {
		$data = array ();
		foreach ($courses as $course) {
			$line["Course"] = $course["Code"];
			$line["Begin"] = get_begin_eur ($conn, $user, $course["Code"]);
			$line["End"] = get_end_eur ($conn, $user, $course["Code"]);
			array_push ($data, $line);
		}
		$data = sort_array($data, "Begin");		
		echo "<div id='content'>";		
		echo "<table class='table'>";
		echo "<th>Course</th><th>Begin</th><th>End</th><th></th>";
		foreach ($data as $value) 	{
			$color = "";
			if($value["Begin"]<"2016-01-15" && $value["End"]>"2016-02-10")
				$color = 'religion';
			if($value["End"]=="2016-07-01")
				$color = 'ep-';
			echo "<tr><td class='center $color'>".$value["Course"]."</a></td>"
				."<td class='center $color'>".change_date_format($value["Begin"])."</td>"
				."<td class='center $color'>".change_date_format($value["End"])."</td>"
				."<td class='center small'><button class='text_button' name='show' value='".$value["Course"]."'>Show</button></td>"
				."</tr>";	
		}
		echo "</table>";
		echo "<p><strong>Please fill in the grades for the highlighted courses into SMS!</strong>"
			."<table class='table_no_border'>"
			."<tr><td class='religion'></td><td>February</td></tr>"
			."<tr><td class='ep-'></td><td>June</td></tr>"
			."</table>"
			."</p>";
	}	
	
	function show_evaluation_eur ($conn, $course_id) {
		$_SESSION["course"] = get_course ($conn, $course_id);
		$students = get_all_students_course ($conn, $_SESSION["course"]);
		echo "<div id='content'>";		
		echo "<table class='table'>";
		echo "<th>Surname</th><th>Name</th><th></th>";
		foreach ($students as $student)
			echo "<tr>"
				."<td>".$student["Last_name"]."</td>"
				."<td>".$student["First_name"]."</td>"
				."<td class='center small'><button class='text_button' name='student' value='".$student["ID"]."'>Show</button></td>"
				."</tr>";
		echo "</table>";
	}
	
	function show_evaluation_student ($conn, $student_id) {
		$student = get_student ($conn, $student_id);
		$courses = get_all_student_teacher_courses_on_student ($conn, $student);
		$eur = "";
		foreach ($courses as $course)
			if (substr($course["Course_id"],2,3)=="eur")
				$eur = get_course ($conn, $course["Course_id"]);	
		$eur_teachers = get_timetable_teacher_on_course ($conn, $eur["Code"]);
		$eur_teachers = explode (",",$eur_teachers["Teacher_id"]);
		echo "<div id='content_with_title'>";			
		
		echo "<table class='table_no_border'>";
		echo "<tr>"
			."<td><button class='text_button' type='submit' name='previous' value='".$student["ID"]."'><</button></td>"
			."<td class='center'><h3>Evaluation European Hours: </h3></td>"
			."<td><button class='text_button float_right' type='submit' name='next' value='".$student["ID"]."'>></button></td>"		
			."</tr>";
		echo "</table>";	
		show_info_student_eur ($conn, $student, $eur, $eur_teachers);
		show_grades ($conn, $student, $eur_teachers, $eur);
		echo "<button class='text_button' name='save' value='".$student["ID"]."'>Save</button> ";
		echo "<button class='text_button' name='back' value='".$eur["Code"]."'>Back</button> ";
	}
	
	function show_info_student_eur($conn, $student, $course, $eur_teachers) {
		echo "<table class='table_no_border'>";
		echo "<tr><td rowspan='4'><img class='Picture' id='".$student["Last_name"]." ".$student["First_name"]."' src='".get_photo($student["ID"])."' width='120' height='177'/></td>";
		$teacher = get_class_teacher ($conn, $student["Class"]);		
		echo "<td class='small'><strong>Name: </strong></td>"
			."<td>".$student["Last_name"]." ".$student["First_name"]."</td>"
			."<td><strong>Date of birth: </strong></td>"
			."<td>".change_date_format($student["Date_of_birth"])."</td></tr>"
			."<tr><td><strong>Class:</strong></td>"
			."<td>".$student["Class"]."</td>"
			."<td><strong>Class teacher:</strong></td>"
			."<td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td></tr>"
			."<tr><td><strong>Course:</strong></td>"
			."<td>".$course["Code"]."</td>"
			."<td><strong>European Hours Teachers:</strong>";
		
		echo "<td>";
		foreach ($eur_teachers as $value) {
			$eur_teacher = get_user ($conn, $value);
			echo $eur_teacher["Last_name"]." ".$eur_teacher["First_name"]."</br>";
		}
		echo "</td></tr>";		
		echo "</td></tr>";	
		echo "</table></br>";		
	}
	
	function show_grades ($conn, $student, $teachers, $course) {
		foreach ($teachers as $value) {
			$teacher = get_user ($conn, $value);
			echo "<strong>".$teacher["Last_name"]." ".$teacher["First_name"]." from "
				.change_date_format(get_begin_eur ($conn, $teacher, $course["Code"]))
				." until ".change_date_format(get_end_eur ($conn, $teacher, $course["Code"]))."</strong></br></br>";
			$eval = get_evaluation ($conn, $teacher["ID"], $student["ID"]);
			if ($_SESSION["user"]==$teacher) {			
				echo "<table class='table_no_border'>";
				echo "<tr><td>Communicating and working with others:</td>"
					."<td><input type='radio' name='grade' value='0' ".((isset($eval["Grade"]) && $eval["Grade"]==0)?"checked":"")."/></td>"
					."<td><input type='radio' name='grade' value='1' ".((isset($eval["Grade"]) && $eval["Grade"]==1)?"checked":"")."/></td>"
					."<td><input type='radio' name='grade' value='2' ".((isset($eval["Grade"]) && $eval["Grade"]==2)?"checked":"")."/></td>"
					."<td><input type='radio' name='grade' value='3' ".((isset($eval["Grade"]) && $eval["Grade"]==3)?"checked":"")."/></td></tr>"
					."<tr><td>Comment: </td>"
					."<td colspan='4'><input type='text' name='comment' size='60' value='".(isset($eval["Comment"])?$eval["Comment"]:"")."'/></td></tr>";
				echo "</table></br>";
			}
			else {
				echo "<table class='table_no_border'>";
				echo "<tr><td>Communicating and working with others:</td>"
					."<td><input type='checkbox' disabled ".((isset($eval["Grade"]) && $eval["Grade"]==0)?"checked":"")."/></td>"
					."<td><input type='checkbox' disabled ".((isset($eval["Grade"]) && $eval["Grade"]==1)?"checked":"")."/></td>"
					."<td><input type='checkbox' disabled ".((isset($eval["Grade"]) && $eval["Grade"]==2)?"checked":"")."/></td>"
					."<td><input type='checkbox' disabled ".((isset($eval["Grade"]) && $eval["Grade"]==3)?"checked":"")."/></td></tr>"
					."<tr><td>Comment: </td><td colspan='4'><input type='text' size='60' value='".(isset($eval["Comment"])?$eval["Comment"]:"")."' disabled/></td></tr>";
				echo "</table></br>";
			}
		}
	}
	
	function get_next_student ($conn, $student_id, $course) {
		$students = get_all_students_course ($conn, $_SESSION["course"]);
		for($i=0; $i<count($students); $i++) {
			if($students[$i]["ID"]==$student_id) {
				if($i+1==count($students))
					return $students[0];
				else 
					return $students[$i+1];
			}
		}		
	}
	
	function get_previous_student ($conn, $student_id, $course) {
		$students = get_all_students_course ($conn, $_SESSION["course"]);
		for($i=0; $i<count($students); $i++) {
			if($students[$i]["ID"]==$student_id) {
				if($i==0)
					return $students[count($students)-1];
				else 
					return $students[$i-1];
			}
		}
	}
?>