<?php
	require_once("includes/startsession.php");	
	$page_title = "My Lists";
	require_once("includes/header.php");
	require_once("includes/PHPExcel.php");
	require_once("includes/PHPExcel/Writer/Excel2007.php");	
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");

	if(isset($_POST["excel"])) {
		$students = get_all_students_course($conn,$_SESSION["course"]);
		export($conn, $students,$_POST["options"]);
	}
	
	if(isset($_POST["type"])) 
		$_SESSION ["type"]=$_POST["type"];
	
	if (!isset($_POST["year"]) && !isset($_POST["type"]) && !isset($_POST["course"]) && !isset($_POST["excel"]) && !isset($_POST["id"]) 
			&& !isset($_POST["update"])) {
		unset($_SESSION ["year"]);
		unset($_SESSION ["type"]);
		unset($_SESSION ["course"]);
	}	
	
	if (isset($_POST["year"])) { 
		$_SESSION["year"]=$_POST["year"];
		unset($_SESSION ["type"]);
		unset($_SESSION ["course"]);
	}
	
	if(isset($_GET["course"])) {
		$_SESSION["year"]=substr($_GET["course"],0,2);
		$_SESSION["type"]=substr($_GET["course"],2,3);
		$_SESSION["course"]=get_course($conn,$_GET["course"]);
		
	}
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					
	
	$years = get_all_years($conn, "");
	foreach($years as $year) {
		if(isset($_SESSION["year"]) && $year["Class"]==$_SESSION["year"])
			echo "<input type='submit' value='".$year["Class"]."' name='year' class='list_button_selected'/> ";
		else
			echo "<input type='submit' value='".$year["Class"]."' name='year' class='list_button'/> ";
	}
	echo "</br></br>";
	
	if (isset($_SESSION["year"])) { 
		$courses = get_all_types_of_courses ($conn,$_SESSION["year"]);
		foreach($courses as $course) 
			if(isset($_SESSION["type"]) && $course["Code"]==$_SESSION["type"])
				echo "<input type='submit' value='".$course["Code"]."' name='type' class='list_button_selected'/> ";
			else
				echo "<input type='submit' value='".$course["Code"]."' name='type' class='list_button'/> ";
	}
	echo "</br></br>";
	
	if(isset($_POST["course"]))
		$_SESSION["course"] = get_course($conn,$_POST["course"]);
	
	if (isset($_SESSION["type"])) { 
		$courses = get_all_courses_on_value ($conn, $_SESSION["year"].$_SESSION["type"]);
		foreach($courses as $course) {
			if(isset($_SESSION["course"]) && $course["Code"]==$_SESSION["course"]["Code"])
				echo "<button type='submit' value='".$course["Code"]."' name='course' class='list_button_selected'>".substr($course["Code"],-3)."</button> ";
			else
				echo "<button type='submit' value='".$course["Code"]."' name='course' class='list_button'>".substr($course["Code"],-3)."</button> ";
		}
	}	
	
	if(isset($_POST["course"]) || isset($_POST["excel"]) || isset($_GET["course"])|| isset($_POST["update"])) {		
		$students = get_all_students_course($conn,$_SESSION["course"]);
		if(isset($_POST["options"]))
			$options = $_POST["options"];
		else
			$options = array ("Last_name","First_name");
		show_options($options);
		$data = get_timetable_teacher_on_course($conn, $_SESSION["course"]["Code"]);
		$ids = explode (",", $data["Teacher_id"]);
		echo "<table class='table_no_border'>";
		$output = "<tr><td><h3>Course: ".$_SESSION["course"]["Code"]."</h3></td><td><h3>Teacher(s): ";
		foreach ($ids as $value) {
			$teacher = get_user($conn,$value);
			$output .= $teacher["Last_name"]." ".$teacher["First_name"]." / ";
		}
		echo substr($output,0,-2)."</h3></td></tr></table>";
		show_list_options($conn, $students, $options);
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
	
	function show_list_options ($conn, $data, $options) {
		$headers = array ("Last_name"=>"Surname", "First_name"=>"Name", "Class"=>"Class", "Date_of_birth"=>"Date Of Birth", "Nationality"=>"Nationality", "Sex"=>"Gender", "Phone"=>"Contact", "Courses"=>"Courses");
		echo "<table class='table'>";			
		foreach ($options as $option)
			echo "<th>".$headers["$option"]."</th>";
		echo "<th></th>";
		
		foreach ($data as $value)  {
			echo "<tr>";
			foreach ($options as $option) {
				if($option=="Courses") {
					$courses = get_all_student_teacher_courses_on_student ($conn, $value);
					$output ="";
					foreach ($courses as $course)
						$output .= $course["Course_id"].", ";
					echo "<td>".substr($output,0,-2)."</td>";	
				}
				else 
					echo "<td>".$value[$option]."</td>";
			}
			echo "<td class='small center'><button type='submit' name='id' value='".$value["ID"]."' class='text_button'>Show</button></td>";
			echo "</tr>";
		}				
		echo "</table>";		
	}
	
	function show_options($options) {
		echo "</br></br>";
		$choices = array ("Last_name","First_name","Class","Date_of_birth","Nationality","Sex","Phone","Courses");
		$headers = array ("Surname","Name","Class","Date of birth","Nationality","Gender","Phone","Courses");
		for($i=0; $i<count($choices); $i++)	{
			echo "<input type='checkbox' name='options[]' value='".$choices[$i]."'";
			if(is_checked($choices[$i],$options))
				echo "checked";
			echo ">".$headers[$i]." ";
		}
		echo "<button type='submit' value='".$_SESSION["course"]["Code"]."' name='excel' class='text_button float_right'>Export</button> ";
		echo "<button type='submit' value='".$_SESSION["course"]["Code"]."' name='update' class='text_button float_right'>Update</button> ";

	}
	
	function is_checked ($option, $data) {
		foreach ($data as $value)
			if($value==$option)
				return true;
		return false;
	}
	
	function export ($conn, $data, $options) {
		date_default_timezone_set('europe/paris');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		for($i=0;$i<count($data);$i++) {
			for($j=0;$j<count($options);$j++) 
				if($options[$j]=="Courses") {
					$courses = get_all_student_teacher_courses_on_student ($conn, $data[$i]);
					for($k=0;$k<count($courses);$k++) 
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i,$j+$k), mb_convert_encoding($courses[$k]["Course_id"],'Windows-1252')); 
				}
				else {	
					if ($options[$j]=="Date_of_birth") 
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i,$j), utf8_encode(change_date_format($data[$i][$options[$j]])));
					else	
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i,$j), mb_convert_encoding($data[$i][$options[$j]],'Windows-1252')); 
				}					
		}
		
		$file_name = "export_files/".$_SESSION["course"]["Code"].".xlsx";
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save(str_replace('.php', '.xlsx', $file_name));
		header ("Location:$file_name");	
		delete($file_name);
	}
	
	function get_excel_field ($i, $j) {
		$letters = array ("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		return $letters[$j].($i+1);
	}
?>