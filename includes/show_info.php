<?php
	function show_list ($data) {
		echo "</br></br><table class='table'>";		
		echo "<th>Surname</th><th>Name</th><th></th>";
		foreach ($data as $value) 
			echo "<tr><td>".$value["Last_name"]."</td>"
				."<td>".$value["First_name"]."</td>"
				."<td class='center small'><button type='submit' name='id' value='".$value["ID"]."' class='text_button'>Show</button></td>"
				."</tr>";		
		echo "</table>";		
	}
	
	function show_list_ls ($conn, $data) {
		echo "</br></br><table class='color' border='1' cellpadding='5' cellspacing='0' width='100%' >";		
		echo "<th>Surname</th><th>Name</th><th></th>";
		foreach ($data as $value) {
			echo "<tr><td>".$value["Last_name"]."</td><td>".$value["First_name"]."</td>";
			echo "<td style='text-align:center;'>";
			$ls_student = get_ls_student($conn, $value, $_SESSION["course"]);
			if($ls_student)
				echo "<button type='submit' name='yes' value='".$value["ID"]."' class='on_button'>Yes</button>
					<button type='submit' name='no' value='".$value["ID"]."' class='off_button'>No</button>";
			else
				echo "<button type='submit' name='yes' value='".$value["ID"]."' class='off_button'>Yes</button>
					<button type='submit' name='no' value='".$value["ID"]."' class='on_button'>No</button>";
			echo "</td></tr>";
		}
		echo "</table>";		
	}

	function show_student_info ($student, $teacher_courses) {
		echo "<table class='table_no_border'>";
		echo "<tr><td rowspan='4'><img class='Picture' id='".$student["Last_name"]." ".$student["First_name"]."' src='".get_photo($student["ID"])."' width='120' height='177'/></td>";
		echo "<td class='small'><strong>Full name: </strong></td>"
			."<td>".$student["Last_name"]." ".$student["First_name"]."</td>"
			."<td class='small'><strong>ID: </strong></td>"
			."<td>".$student["ID"]."</td></tr>"
			."<tr><td><strong>Class: </strong></td>"
			."<td>".$student["Class"]."</td>"	
			."<td><strong>Day of birth: </strong></td>"
			."<td>". change_date_format($student["Date_of_birth"])."</td></tr>";
		$nationalities = explode(",",$student["Nationality"]);		
		echo "<tr><td><strong>Courses:</strong></td><td colspan='3'>";		
		$output = "";
		foreach ($teacher_courses as $teacher_course) 
			$output .= "<a href='my_lists.php?course=".$teacher_course["Course_id"]."'>".$teacher_course["Course_id"]."</a>, ";
		echo substr($output, 0, -2)."</td></tr>";		
		echo "<tr><td><strong>Nationality: </strong></td><td colspan='3'>";
		foreach ($nationalities as $nationality)
			echo "<img id='Flag' src='".get_flag($nationality)."' width='32' height='32' />&nbsp";
		echo "</td></tr>";		
		echo "</table>";
	}
	
	function show_teacher_info ($teacher, $teacher_courses) {
		echo "</br></br><table class='table_no_border'>";
		echo "<tr><td rowspan='4' class='small'><img class='Picture' id='".$teacher["Last_name"]." ".$teacher["First_name"]."' src='".get_photo($teacher["ID"])."' width='120' height='177'/></td>";
		echo "<td class='small'><strong>Full name: </strong></td><td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td>";
		echo "<td class='small'><strong>ID: </strong></td><td>".$teacher["ID"]."</td></tr>";
		echo "<tr><td><strong>E-mail: </strong></td><td colspan='3'>".$teacher["Email"]."</td>";		
		echo "</tr>";
		$nationalities = explode(",",$teacher["Nationality"]);		
		echo "<tr><td><strong>Courses:</strong></td><td colspan='3'>";		
		$output = "";
		foreach ($teacher_courses as $teacher_course) 
			$output .= "<a href='my_lists.php?course=".$teacher_course["Code"]."'>".$teacher_course["Code"]."</a>, ";
		echo substr($output, 0, -2)."</td></tr>";		
		echo "<tr><td><strong>Nationality: </strong></td><td colspan='3'>";
		foreach ($nationalities as $nationality)
			echo "<img id='Flag' src='".get_flag($nationality)."' width='32' height='32' />&nbsp";
		echo "</td></tr>";		
		echo "</table>";
	}
	
	function show_photobook ($data,$course) {
		$num_on_line = 6;	
		echo "<table class='table_no_border'>";
		echo "<caption>".$course["Code"]."</caption>";	
		for ($i=0; $i<count($data); $i++) {
			echo "<tr>";			
			for($j=0; $j<$num_on_line; $j++) {
				if(isset($data[$i+$j]))
					echo "<td class='center small'><a href='search_students.php?id=".$data[$i+$j]["ID"]."'><img class='Picture' src='".get_photo($data[$i+$j]["ID"])."' width='120' height='177'/></a></td>";
				else
					echo "<td></td>";
			}
			echo "</tr>";
			echo "<tr>";
			for($j=0; $j<$num_on_line; $j++, $i++) {
				if(isset($data[$i]))
					echo "<td class='center small'>".$data[$i]["Last_name"]." ".$data[$i]["First_name"]." </td>";
				else
					echo "<td></td>";
			}
			echo "</tr>";
			$i--;
		}	
		echo "</table>";
	}
	
	function show_photobook_teachers ($data) {
		$num_on_line = 3;				
		echo "<table class='table_no_border'>";		
		for ($i=0; $i<count($data); $i+=$num_on_line) {
			echo "<tr>";			
			for($j=0; $j<$num_on_line; $j++) {
				if(isset($data[$i+$j])) {
					echo "<td class='center small'><a href='search_teachers.php?id=".$data[$i+$j]["ID"]."'><img class='Picture' src=".get_photo($data[$i+$j]["ID"])." width='120' height='177'/></a></br>";
					echo $data[$i+$j]["Last_name"]." ".$data[$i+$j]["First_name"]."</br>".$data[$i+$j]["Email"]." </br>";
					$nationalities = explode(",",$data[$i+$j]["Nationality"]);
					foreach ($nationalities as $nationality)
						echo "<img id='flagIMG' src='".get_flag($nationality)."' width='48' height='48' />";
					echo "</td>";					
				}
				else	
					echo "<td></td>";
			}
			echo "</tr>";		
		}
		echo "</table>";
	}
?>