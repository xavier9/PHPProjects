<?php
	require_once("includes/startsession.php");	
	$page_title = "My Students";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	if(isset($_POST["id"])) {
		$student = get_student($conn, $_POST["id"]);
		$teacher_courses = sort_array(get_all_student_teacher_courses_on_student_on_subject ($conn, $student, "xl1"),"Course_id");
		show_student_info($conn, $student, $teacher_courses);
	}
	else	
		show_list_intensive($conn);

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_list_intensive ($conn) {
		echo "<table width='100%' border='1' cellpadding='5' cellspacing='0' class='color'>";
		echo "<th>Surname</th><th>Name</th><th>Minutes</th><th></th>";
		$students = get_all_students ($conn);
		foreach ($students as $student) {
			if(has_ls_intensive (get_all_student_teacher_courses_on_student ($conn, $student)))
				echo "<tr><td>".$student["Last_name"]."</td>"
				."<td>".$student["First_name"]."</td>"
				."<td style='text-align:center;'>".get_minutes_intensive($conn, $student)."</td>"
				."<td style='text-align:center;width:100px;'><button type='submit' name='id' value='".$student["ID"]."'class='text_button'>Show</button></td>"
				."</tr>";
		}
		echo "</table>";
	}
	
	function has_ls_intensive ($courses) {
		foreach ($courses as $course)
			if(substr($course["Course_id"],2,3)=="xl1")
				return true;
		return false;
	}
	
	function get_minutes_intensive ($conn, $student) {
		$minutes = 0;
		$courses = get_all_student_teacher_courses_on_student ($conn, $student);
		foreach ($courses as $course) {
			if(substr($course["Course_id"],2,3)=="xl1") {
				$timetable = get_timetable_template_teacher_on_course_2 ($conn, $course["Course_id"]);
				foreach ($timetable as $value) 
					$minutes += calculate_minutes ($value["Begin"], $value["End"]);
			}				
		}
		return $minutes;
	}
	
	function get_minutes_on_course ($conn, $course) {
		$minutes = 0;
		for($i=0; $i<5; $i++) {
			$data = get_timetable_template_course_by_day ($conn, $course, $i);
			foreach ($data as $value) 
				$minutes+=calculate_minutes($value["Begin"],$value["End"]);
		}
		return $minutes;
	}
	
	function calculate_minutes ($begin, $end) {
		return ((substr($end,0,2)-substr($begin,0,2))*60)+(substr($end,3,2)-substr($begin,3,2));
	}
	
	function show_student_info ($conn, $student, $teacher_courses) {
		echo "</br></br><table border='0' cellpadding='5' cellspacing='0' width='100%' border='0'>";
		echo "<tr><td rowspan='4' style='width:120px;height:177px;'><img id='".$student["Last_name"]." ".$student["First_name"]."' src='".get_photo($student["ID"])."' width='120' height='177'/></td>";
		echo "<td style='width:100px;'><strong>Full name: </strong></td><td>".$student["Last_name"]." ".$student["First_name"]."</td>";
		echo "<td style='width:100px;'><strong>ID: </strong></td><td>".$student["ID"]."</td></tr>";
		echo "<tr><td><strong>Class: </strong></td><td>".$student["Class"]."</td>";		
		echo "<td><strong>Day of birth: </strong></td><td>". change_date_format($student["Date_of_birth"])."</td></tr>";
		$nationalities = explode(",",$student["Nationality"]);		
		echo "<tr><td><strong>Nationality: </strong></td><td colspan='3'>";
		foreach ($nationalities as $nationality)
			echo "<img id='Flag' src='".get_flag($nationality)."' width='32' height='32' />&nbsp";
		echo "</td></tr></table>";		
			
		$total = 0;
		echo "<table border='1' cellpadding='5' cellspacing='0' width='100%' border='0'>";
		echo "<th>Teacher</th><th>Course</th><th>Minutes</th>";
		foreach ($teacher_courses as $teacher_course) {
			$teacher = get_user($conn, $teacher_course["Teacher_id"]);
			$minutes = get_minutes_on_course ($conn, $teacher_course["Course_id"]);
			$total+=$minutes;
			echo "<tr><td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td><td class='center'>".$teacher_course["Course_id"]."</td>"
				."<td class='center'>".$minutes."</td></tr>";
		}
		echo "<tr><td></td><td></td><td class='center_bold'>".$total."</td></tr>";	
		echo "</table>";
	}
?>