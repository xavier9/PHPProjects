<?php   
	require_once("includes/startsession.php");	
	$page_title = "Overiew Absences";
	require_once("includes/header.php");	
	require_once("includes/show_info.php");	
	
	$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "mat");
	if (empty($courses))
		$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "gen");
	$students = array ();
	foreach ($courses as $value) 
		$students = array_merge ($students, get_all_students_course($conn,$value));

	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	if(isset($_POST["detail"]))
		show_details ($conn, $_POST["detail"]);
	else	
		show_info($conn, $students);	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_info ($conn, $students) {
		echo "<table class='table'>";	
		echo "<th rowspan='2'>Lastname</th><th rowspan='2'>Firstname</th><th colspan='3'>1/2 Day</th><th colspan='3'>Day</th><th></th>";
		echo "<tr><td class='center'>No</td>"
			."<td class='center'>Medical</td>"
			."<td class='center'>Parents</td>"
			."<td class='center'>No</td>"
			."<td class='center'>Medical</td>"
			."<td class='center'>Parents</td>"
			."<td></td></tr>";
		foreach ($students as $student) {				
			echo "<tr><td>".$student["Last_name"]."</td><td>".$student["First_name"]."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 1, 1)."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 1, 2)."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 1, 3)."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 2, 1)."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 2, 2)."</td>"
				."<td class='center extra_small'>".count_absences ($conn, $student["ID"], 2, 3)."</td>"
				."<td class='small center'><button class='text_button' value='".$student["ID"]."' name='detail'>Show</button></td>"
				."</tr>";
		}		
		echo "</table>";	
	}
	
	function show_details ($conn, $id) {
		$days = array ("","1/2 day", "Full day");
		$justifications = array ("","No", "Medical", "Parents");
	
		$student = get_student ($conn, $id); 		
		$teacher_courses = sort_array(get_all_student_teacher_courses_on_student ($conn, $student),"Course_id");
		show_student_info ($student, $teacher_courses);
	
		echo "<table class='table'>";
		echo "<th>Date</th><th>Day</th><th>Justification</th>";
		$absences = get_absences ($conn, $student["ID"]);
		foreach ($absences as $absence) 
			echo "<tr>"
				."<td class='center'>".change_date_format($absence["Date_of_absence"])."</td>"
				."<td class='center'>".$days[$absence["Day"]]."</td>"
				."<td class='center'>".$justifications[$absence["Justification"]]."</td>"
				."</tr>";
		echo "</table>";
	
	}
?>