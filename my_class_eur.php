<?php
	require_once("includes/startsession.php");	
	$page_title = "My European Hours";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	$class = "";
	foreach ($courses as $course)
		if(substr($course["Code"],2,3)=="mat")
			$class = $course;
	$students = get_all_students_course($conn,$class);
	show_list ($conn, $students);
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_list ($conn, $data) {
		$output = array ();
		foreach ($data as $value) {
			$course = get_eur_course(get_all_student_teacher_courses_on_student ($conn, $value));
			$timetable = get_timetable_template_teacher_on_course($conn, $course);
			$teacher = get_user($conn, $timetable["Teacher_id"]);			
			$line["Student"] = $value["Last_name"]." ".$value["First_name"];
			$line["Course"] = $course;
			$line["Location"] = $timetable["Location"];
 			$line["Teacher"] = $teacher["Last_name"]." ".$teacher["First_name"];
			array_push ($output, $line);
		}
		$output = sort_array ($output, "Location");
		echo "<table class='table'>";		
		echo "<th>Student</th><th>Course</th><th>Class</th><th>Teacher</th>";		
		foreach ($output as $line) {
			echo "<tr><td>".$line["Student"]."</td>"
				."<td class='center'>".$line["Course"]."</td>"
				."<td class='center'>".$line["Location"]."</td>"
				."<td>".$line["Teacher"]."</td>"
				."</tr>";	
		}				
		echo "</table>";		
	}
	
	function get_eur_course ($courses) {
		foreach ($courses as $course)
			if(substr($course["Course_id"],2,3)=="eur")
				return $course["Course_id"];	
	}
?>