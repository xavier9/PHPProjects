<?php
	require_once("includes/startsession.php");	
	$page_title = "Search Students";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					
	
	echo "<label>Search a student:</label> ";
	echo "<input type='text' name='input' value='' autofocus> ";
	echo "<input type='submit' value='Search' name='search' class='text_button'/> ";
	
	if(isset($_POST["search"]))
		$_SESSION["search"] = $_POST["input"];
	
	if(isset($_POST["search"]) || isset($_POST["back"])) {			
		$students = search_students($conn,$_SESSION["search"]);
		show_list($students);
	}		
	
	if(isset($_POST["id"])) {	
		echo "<input type='submit' value='Back' name='back' class='text_button'/>";
		$student = get_student($conn, $_POST["id"]);
		$teacher_courses = sort_array(get_all_student_teacher_courses_on_student ($conn, $student),"Course_id");
		show_student_info($student, $teacher_courses);
		show_timetable_student ($conn, $student);
	}
	
	if(isset($_GET["id"])) {	
		$student = get_student($conn, $_GET["id"]);
		$teacher_courses = sort_array(get_all_student_teacher_courses_on_student ($conn, $student),"Course_id");
		show_student_info($student, $teacher_courses);
		show_timetable_student ($conn, $student);
	}	
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>