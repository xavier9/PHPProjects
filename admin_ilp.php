<?php
	require_once("includes/startsession.php");	
	$page_title = "My ILP";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	if(isset ($_POST["show"]))
		header ("location: my_ilp.php?show=".$_POST["show"]."");
	
	if(isset ($_POST["show_2"])) 		
		header ("location: my_ilp.php?show_2=".$_POST["show_2"]."");
	
	if(!isset($_POST["show"]) && !isset($_POST["show_2"])) 
		show_students ($conn);
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_students ($conn) {				
		$students = get_students_ilp ($conn);
		if (count ($students) != 0) {
			echo "<table class='table'>";
			echo "<th>Surname</th><th>Name</th><th>Page 1</th><th>Page 2</th>";
			foreach ($students as $student) {
				echo "<tr>"
					."<td>".$student["Last_name"]."</td>"
					."<td>".$student["First_name"]."</td>"
					."<td class='center small'><button type='submit' class='text_button' value='".$student["ID"]."' name='show'>Show</button></td>"
					."<td class='center small'><button type='submit' class='text_button' value='".$student["ID"]."' name='show_2'>Show</button></td>"
					."</tr>";
			}
			echo "</table>";
		}
		
	}
	
	function get_students_ilp ($conn) {
		$courses = get_all_ls_courses($conn);
		$students = array ();
		foreach ($courses as $course) {
			$temp_course = get_course ($conn, $course["Course_id"]);
			$data = get_all_students_course ($conn, $temp_course);
			foreach ($data as $value){
				if(!in_array($value, $students))
					array_push($students, $value);
			}
		}
		return sort_array ($students, "Last_name");
	}
	
	function get_all_ls_courses ($conn) {
		$query = "SELECT * FROM b2_teacher_course 
			WHERE (SUBSTRING(Course_id,3,3) = 'kma' 
			OR SUBSTRING(Course_id,3,3) = 'kl1' 
			OR SUBSTRING(Course_id,3,3) = 'kl2')";
		return get_values(mysqli_query($conn,$query));
	}
	
?>