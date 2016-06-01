<?php 
	require_once("includes/startsession.php");	
	$page_title = "Add absences";
	require_once("includes/header.php");

	if(!$_POST)
		$_SESSION["date"]="0000-00-00";
	
	if(isset($_POST["half_day"])) {
		$absence = get_absence($conn,$_POST["half_day"],$_SESSION["date"]);		
		if($absence)
			update_absence_day($conn,$absence["ID"],1);
		else
			add_absence($conn,$_SESSION["user"],$_POST["half_day"],$_SESSION["date"],1,1);
	}

	if(isset($_POST["day"])) {
		$absence = get_absence($conn,$_POST["day"],$_SESSION["date"]);		
		if($absence)
			update_absence_day($conn,$absence["ID"],2);
		else
			add_absence($conn,$_SESSION["user"],$_POST["day"],$_SESSION["date"],2,1);
	}

	if(isset($_POST["no"])) {
		$absence = get_absence($conn,$_POST["no"],$_SESSION["date"]);		
		if($absence)
			update_absence_justification($conn,$absence["ID"],1);
		else
			add_absence($conn,$_SESSION["user"],$_POST["no"],$_SESSION["date"],0,1);
	}	

	if(isset($_POST["medical"])) {
		$absence = get_absence($conn,$_POST["medical"],$_SESSION["date"]);		
		if($absence)
			update_absence_justification($conn,$absence["ID"],2);
		else
			add_absence($conn,$_SESSION["user"],$_POST["medical"],$_SESSION["date"],0,2);
	}	

	if(isset($_POST["parents"])) {
		$absence = get_absence($conn,$_POST["parents"],$_SESSION["date"]);		
		if($absence)
			update_absence_justification($conn,$absence["ID"],3);
		else
			add_absence($conn,$_SESSION["user"],$_POST["parents"],$_SESSION["date"],0,3);
	}	

	if(isset($_POST["delete"])) 
		delete_absence($conn,$_POST["delete"]);		
	
	$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "mat");
	if (empty($courses))
		$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "gen");
	$students = array ();
	foreach ($courses as $value) 
		$students = array_merge ($students, get_all_students_course($conn,$value));
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content_with_title'>";	
	
	if(isset($_POST["date"])) 
		$_SESSION["date"] = $_POST["date1"];
				
	echo "<table class='table_no_border'>";
	echo "<tr>";
	echo "<td >";
	if(isset($_SESSION["date"])) {
		if($_SESSION["date"]=="0000-00-00")
			echo "<h3>Absences: Please select a date</h3>";
		else 
			echo "<h3>Absences: ".change_date_format($_SESSION["date"])."</h3>";
	}	
	echo "</td>";
	echo "<td class='medium'>";
	require_once("includes/calendar.php");
	echo "</td><td class='small'><input type='submit' name='date' value='Show' class='text_button'>";	
	echo "</td></tr>";
	echo "</table>";
	
	if($_SESSION["date"]!="0000-00-00")
		show_info($conn,$students,$_SESSION["date"]);
	else
		echo "</br></br></br></br></br></br></br></br></br></br></br>";
		
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_info ($conn, $students, $date) {
		echo "<table class='table'>";		
		echo "<th>Surname</th><th>Name</th><th>Day</th><th>Justification</th><th></th>";
		
		foreach ($students as $student) {	
		    echo "<tr>";
			echo "<td>".$student["Last_name"]."</td>";
			echo "<td>".$student["First_name"]."</td>";
			
			$absence = get_absence ($conn, $student["ID"], $date);
			echo "<td class='center'>";					
			if($absence["Day"]==1)
				echo "<button name='half_day' value='".$student["ID"]."' class='on_button'>1/2</button> ";
			else
				echo "<button name='half_day' value='".$student["ID"]."' class='off_button'>1/2</button> ";				
			if($absence["Day"]==2)
				echo "<button name='day' value='".$student["ID"]."' class='on_button'>Full</button> ";
			else
				echo "<button name='day' value='".$student["ID"]."' class='off_button'>Full</button> ";
	
			echo "</td>";
			
			echo "<td class='center'>";			
			if($absence["Justification"]==1)
				echo "<button name='no' value='".$student["ID"]."' class='on_button'>No</button> ";
			else
				echo "<button name='no' value='".$student["ID"]."' class='off_button'>No</button> ";			
			if($absence["Justification"]==2)
				echo "<button name='medical' value='".$student["ID"]."' class='on_button'>Medical</button> ";
			else
				echo "<button name='medical' value='".$student["ID"]."' class='off_button'>Medical</button> ";			
			if($absence["Justification"]==3)
				echo "<button name='parents' value='".$student["ID"]."' class='on_button'>Parents</button> ";
			else
				echo "<button name='parents' value='".$student["ID"]."' class='off_button'>Parents</button> ";				
			echo "</td>";
			echo "<td class='center'><button name='delete' value='".$absence["ID"]."' class='on_button'>X</button></td> ";
			echo "</tr>";
		}		
		echo "</table>";
	}
?>