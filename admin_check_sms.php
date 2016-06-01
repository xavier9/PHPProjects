<?php
	require_once("includes/startsession.php");	
	$page_title = "Check SMS";
	require_once("includes/header.php");	
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	$years = array ("p1","p2","p3","p4","p5");
	$courses = array ("l1-","l2-","mat","art","ddm","mus","ep-","eur","rel");
	
	$data = array ();
	$students = get_all_students ($conn);
	foreach ($courses as $course)
		if($course != "rel" && $course != "eur")
		$data = check_course_error ($students,$course,$years,$conn,$data);
	
	$data = check_course_error ($students,"eur",array ("p3","p4","p5"),$conn,$data);
	$data = check_errors_religion ($students, $years, $conn, $data);
	
	echo "<table class='table'>";
	echo "<th>Surname</th><th>Name</th>";
	foreach ($courses as $course)
		echo "<th>".$course."</th>";
	
	foreach ($data as $value) {
		echo "<tr><td>".$value["Last_name"]."</td><td>".$value["First_name"]."</td>";
		foreach ($courses as $course) {
			if(isset($value[$course]))
				echo "<td class='center'><img src='../images/delete2.png'  style='width:16px;height:16px'></td>";
			else
				echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	
	$data = check_errors ($students, $conn);
	if(count($data)>0) {
		echo "<h3>Students with errors</h3>";
		foreach ($data as $student)
			echo $student["Last_name"]." ".$student["First_name"]."</br>";
	}
	
	echo "</div>";
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function check_course_error ($students,$subject, $years, $conn, $data) {
		foreach ($students as $student) {
			if(in_array(substr($student["Class"],0,2), $years)) {
				$count_course = 0;
				$student_courses = get_all_student_teacher_courses_on_student ($conn, $student);
				foreach ($student_courses as $course) {
					if(substr($course["Course_id"],2,3)==$subject)
						$count_course++;
				}
				if($count_course != 1) {
					if(!check_in_array($student,$data)) {
						array_push($data, $student);
						$data[array_search($student,$data)][$subject]=1;
					}
					else 
						$data[search_in_array($student,$data)][$subject]=1;
				}
			}
		}
		return $data;
	}
	
	function check_errors_religion ($students, $years, $conn, $data) {
		foreach ($students as $student) {
			if(in_array(substr($student["Class"],0,2), $years)) {
				$count_course = 0;
				$student_courses = get_all_student_teacher_courses_on_student ($conn, $student);			
				foreach ($student_courses as $course) {
					if(substr($course["Course_id"],2,3)=="rca" || substr($course["Course_id"],2,3)=="ror" || substr($course["Course_id"],2,3)=="rju" || 
						substr($course["Course_id"],2,3)=="rpr" || substr($course["Course_id"],2,3)=="mor" || substr($course["Course_id"],2,3)=="ris")
						$count_course ++;
					}
				if($count_course != 1) {
					if(!check_in_array($student,$data)) {
						array_push($data, $student);
						$data[array_search($student,$data)]["rel"]=1;
					}
					else 
						$data[search_in_array($student,$data)]["rel"]=1;
				}
			}
		}
		return $data;
	}
	
	function check_in_array ($student, $data) {
		foreach ($data as $value)
			if ($value["ID"]==$student["ID"])
				return true;
		return false;
	}
	
	function search_in_array ($student, $data) {
		while ($value = current($data)) {
			if ($value["ID"]==$student["ID"])
				return key($data);
			next($data);
		}
	}	
	
	function check_errors ($students, $conn) {
		$data = array ();
		foreach ($students as $student) {
			$student_courses = get_all_student_teacher_courses_on_student ($conn, $student);
			foreach ($student_courses as $course) {
				if(substr($course["Course_id"],5,3)!=substr($student["Class"],2,3) 
					&& (substr($course["Course_id"],2,3)=="mat" || substr($course["Course_id"],2,3)=="art" || substr($course["Course_id"],2,3)=="ddm"
						|| substr($course["Course_id"],2,3)=="mus"|| substr($course["Course_id"],2,3)=="mat"))
					if(!in_array($student,$data))
						array_push ($data, $student);
			}
		}
		return $data;
	}
?>