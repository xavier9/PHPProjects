<?php
	require_once("includes/startsession.php");	
	$page_title = "My Students";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	foreach ($courses as $course) 
		echo "<input type='submit' value='".$course["Code"]."' name='course' class='text_button'/> ";			
	
	if(isset($_POST["course"])) {		
		$_SESSION["course"] = get_course($conn,$_POST["course"]);
		$students = get_all_students_course($conn,$_SESSION["course"]);
		show_list($students);
	}	
	
	if(isset($_POST["id"])) {	
		$student = get_student($conn, $_POST["id"]);
		$teacher_courses = sort_array(get_all_student_teacher_courses_on_student ($conn, $student),"Course_id");
		echo "</br></br>";
		show_student_info($student, $teacher_courses);
		show_timetable_student ($conn, $student);
	}

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>