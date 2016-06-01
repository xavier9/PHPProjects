<?php
	require_once("includes/startsession.php");	
	$page_title = "Scan Cards";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					
	
	echo "<label>Scan a barcode:</label> ";
	echo "<input type='text' name='input' value='' autofocus> ";
	echo "<input type='submit' value='Search' name='search' class='text_button'/> ";
	
	if(isset($_POST["search"])) {
		if($student = get_student($conn, $_POST["input"])) {
			$teacher_courses = sort_array(get_all_student_teacher_courses_on_student ($conn, $student),"Course_id");
			show_student_info($student, $teacher_courses);
			show_timetable_student ($conn, $student);
		}
		else {
			if($user = get_user($conn, $_POST["input"])) {
				$teacher_courses = sort_array(get_all_teacher_courses ($conn, $user),"Code");
				show_teacher_info($user, $teacher_courses);
				show_timetable_teacher($conn, $user["ID"]);		
			}
		}	
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>