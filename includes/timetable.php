<?php	
	function show_timetable_teacher ($conn, $teacher_id) {
		$teacher = get_timetable_teacher($conn, $teacher_id);
		echo "<h3>Timetable: ".$teacher["Last_name"]." ".$teacher["First_name"]."</h3>";
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			show_timetable_day_teacher($conn, $days[$i],get_timetable_template_teacher_by_day($conn, $teacher["ID"], $i));
	}
	
	function show_timetable_student ($conn, $student) {
		$courses = array ("eur","l1-","l2-","mat","rju","ror","ris","rca","rpr","ddm","art","mus","mor","ep-","gen");
		$title = "Timetable";	
		show_timetable_student_on_courses($conn, $title, $student, $courses);
		$courses = array ("jl1","kl1","jma","kma","jl2","onl");
		$title = "Timetable LS, Rattrapage & ONL";
		show_timetable_student_on_courses($conn, $title, $student, $courses);
		$courses = array ("xl1");
		$title = "Timetable Intensive";
		show_timetable_student_on_courses($conn, $title, $student, $courses);
	}
	
	function show_timetable_student_on_courses ($conn, $title, $student, $timetable_courses) {
		if(check_courses($conn, $student, $timetable_courses)) {
			echo"<div id='timetable'>";
			echo "<h3>".$title.": ".$student["Last_name"]." ".$student["First_name"]."</h3>";
			$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
			for ($i=0; $i<count($days); $i++) {
				$courses = get_all_student_teacher_courses_on_student ($conn, $student);
				$data = array ();
				foreach ($courses as $course) {
					if(in_array(substr($course["Course_id"],2,3),$timetable_courses))
						$data = array_merge ($data, get_timetable_template_course_by_day ($conn, $course["Course_id"], $i));
				 }
				if(count($data)!=0)
					$data = sort_array($data, "Begin");
				show_timetable_day_student($conn, $days[$i],$data);
			}
			echo "</div>";
		}
	}
	
	function check_courses ($conn, $student, $timetable_courses) {
		$courses = get_all_student_teacher_courses_on_student ($conn, $student);
		foreach ($courses as $course) {
			if(in_array(substr($course["Course_id"],2,3),$timetable_courses))
				return true;
		 }
		 return false;
	}
	
	function show_timetable_day_teacher ($conn, $day, $data) {
		$no_link = array ("surveill","class","lunch","coord","occup","periscolai","Special","triparti","Woluwe","bus");
		$begin=0;
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					echo "</td>";
					echo "</tr>";
				}										
				$begin = $value["Begin"];
				$type = get_template_type($conn,$value["Type"]);
				echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
				echo "<td class='".$type["Type"]."'>";
				if($type["Location"]==1) 			
					echo "<a href='timetable_rooms.php?class=".$value["Location"]."'>".$value["Location"]."</a></br>";
				if(in_array($value["Course"],$no_link))
					echo $value["Course"];
				else
					echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a></br>";
			}
			else {
				$type = get_template_type($conn,$value["Type"]);
				$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
				if(in_array($value["Course"],$no_link))
					echo $value["Course"];
				else
					echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a></br>";
			}
		}		
		echo "</table>";
	}		
	
	function show_timetable_day_student ($conn, $day, $data) {
		$begin=0;
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					echo "</td>";
					echo "</tr>";
				}										
				$begin = $value["Begin"];
				$type = get_template_type($conn,$value["Type"]);
				echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
				echo "<td class='".$type["Type"]."'>";
				if($type["Location"]==1) 			
					echo "<a href='timetable_rooms.php?class=".$value["Location"]."'>".$value["Location"]."</a></br>";
				echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a></br>";
				$teacher = get_user($conn, $value["Teacher_id"]);
				echo "<a href='timetable_teachers.php?teacher=".$teacher["ID"]."'>".substr($teacher["Last_name"],0,9)."</a></br>";
			}
			else {
				$type = get_template_type($conn,$value["Type"]);
				$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
				echo $value["Course"]."</br>";
			}
		}		
		echo "</table>";
	}		
?>