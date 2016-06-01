<?php
	require_once("includes/startsession.php");	
	$page_title = "My Timetable";
	require_once("includes/header.php");
	date_default_timezone_set('europe/paris');
	
	if(isset($_POST["add_course"])) {
		save_timetable_teacher ($conn, $_POST["courses"]);
		$data = get_timetable_template_teacher_by_day_on_id($conn, $_POST["add_course"]);
		add_timetable_template_teacher($conn, $data);
	}
	
	if(isset($_POST["delete_course"])) {
		save_timetable_teacher ($conn, $_POST["courses"]);
		delete_block_timetable_template_teacher($conn, $_POST["delete_course"]);
	}
	
	if(isset($_POST["save"]))
		save_timetable_teacher ($conn, $_POST["courses"]);
		
	if(isset($_POST["help"]))
		header("Location: my_timetable_help.php");
		
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	if(isset($_POST["submit"])) {
		save_timetable_teacher ($conn, $_POST["courses"]);
		if(check_timetable_teacher($conn)) {
			update_timetable_user_submitted($conn, $_SESSION["user"], date("Y-m-d"));
			echo "<div class='msg'>Thank you for submitting your timetable!</br></br></div>";
		}
		else	
			echo "<div class='err'>Timetable not submitted! Please make sure that everything is filled in!</br></br></div>";
	}		
	
	$timetable_user = get_timetable_teacher($conn, $_SESSION["user"]["ID"]);
	if($timetable_user["Submitted"]=="0000-00-00") {
		echo "<div class='bold'>Please complete your timetable and press submit!</br>If needed changes can be made afterwards.</br></br></div>";
	}
	show_timetable ($conn, $_SESSION["user"]);	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");	
	
	function show_timetable ($conn, $user) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			show_timetable_day($conn, $days[$i],get_timetable_template_teacher_by_day($conn, $user["ID"], $i));
		echo "<div id='timetable'>";
		echo "</br>";
		echo "<input type='submit' name='save' value='Save' class='text_button'/> <button type='submit' name='help' class='text_button'>Help</button> ";
		$timetable_user = get_timetable_teacher($conn, $_SESSION["user"]["ID"]);
		if($timetable_user["Submitted"]=="0000-00-00")
			echo "<input type='submit' name='submit' value='Submit' class='text_button'/> ";
		echo "</div>";
	}
	
	function show_timetable_day ($conn, $day, $data) {
		$begin=0;
		$types = get_template_types($conn);
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					echo "</br>";
					if($type["Location"]==1)
						echo "<a href='timetable_rooms.php?class=".$location."'>".$location."</a>";		
					echo "</td>";				
					echo "</tr>";
				}					
				$begin = $value["Begin"];
				$type = get_template_type($conn,$value["Type"]);
				echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
				echo "<td class='".$value["Type"]."'>";
				if(($type["Location"]!="1" || $type["Type"]=="surveill") && $type["Type"]!="ep-")
					echo $value["Type"];
				else {
					if(is_numeric($value["Course"])) {
						$student = get_student ($conn, $value["Course"]);
						echo $student["Last_name"];
					}
					else {
						show_dropdown($conn, $value);
						echo " <button type='submit' value='".$value["ID"]."' name='add_course' class='action_button'>+</button>";
					}
				}				
			}
			else {
				echo "</br>";
				show_dropdown($conn, $value);
				echo " <button type='submit' value='".$value["ID"]."' name='delete_course' class='action_button err'>X</button>";
			}		
			$location = $value["Location"];
		}		
		if(count($data)!=0) {
			echo "</br>";
			if($type["Location"]==1)
				echo "<a href='timetable_rooms.php?class=".$location."'>".$location."</a>";		
			echo "</td>";				
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function get_courses_dropdown ($conn, $type){
		$data = array ();
		$course["Code"]="-";
		array_push($data, $course);
		if($type == "class") {
			$course["Code"]="Class";
			array_push($data, $course);
		}
		
		$courses = get_all_teacher_courses ($conn, $_SESSION["user"]);
		foreach ($courses as $course) {
			if($type == "religion" && (substr($course["Code"],2,3)=="mor" || substr($course["Code"],2,3)=="rca" || substr($course["Code"],2,3)=="ris" || substr($course["Code"],2,3)=="rju" || substr($course["Code"],2,3)=="rpr" || substr($course["Code"],2,3)=="ror"))
				array_push($data, $course);
			if($type == "-" && (substr($course["Code"],2,3)=="art" || substr($course["Code"],2,3)=="ddm" || substr($course["Code"],2,3)=="l1-" || 
				substr($course["Code"],2,3)=="mus" || substr($course["Code"],2,3)=="mat" || substr($course["Code"],2,3)=="gen"))
				array_push($data, $course);	
			if($type == "ls" && (substr($course["Code"],2,3)=="jl1" || substr($course["Code"],2,3)=="kl1" || substr($course["Code"],2,3)=="jma" || 
				substr($course["Code"],2,3)=="kma" || substr($course["Code"],2,3)=="kl2")) 
				array_push($data, $course);	
			if($type == "ep-" && (substr($course["Code"],2,3)=="spo"))
				array_push($data, $course);		
			if($type == "lsi" && (substr($course["Code"],2,3)=="xl1"))
				array_push($data, $course);		
			if(substr($course["Code"],2,3)=="".$type."")
				array_push($data, $course);
		}
		return $data;
	}
	
	function show_dropdown ($conn, $value) {
		echo "<select name='courses[]'>";
		$courses = get_courses_dropdown ($conn, $value["Type"]);
		foreach ($courses as $course)
			echo "<option value='".$value["ID"].".".$course["Code"]."' ".($course["Code"]==$value["Course"]?"selected":"").">".$course["Code"]."</option>";
		echo "</select>";
	}
		
	function save_timetable_teacher ($conn, $courses){
		foreach ($courses as $course) {
			$line = explode (".", $course);
			update_timetable_template_teacher_course($conn, $line[0], $line[1]);
		}
	}
	
	function check_timetable_teacher ($conn) {
		$exceptions = array ("lunch","ls","surveill","bus","coord","class","garderie","occup");
		for ($i=0; $i<5; $i++) {
			$timetable_day = get_timetable_template_teacher_by_day($conn, $_SESSION["user"]["ID"], $i);
			foreach ($timetable_day as $value) {
				if($value["Course"]=="" || $value["Course"]=="-") {
					if(!in_array($value["Type"],$exceptions))	
						return false;
				}			
			}
		}
		return true;
	}
?>