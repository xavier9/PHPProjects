<?php
	require_once("includes/startsession.php");	
	$page_title = "Photobook Students";
	require_once("includes/header.php");
	require_once("includes/show_info.php");

	if(isset($_POST["type"])) 
		$_SESSION ["type"]=$_POST["type"];
	
	if (!isset($_POST["year"]) && !isset($_POST["type"]) && !isset($_POST["course"])) {
		unset($_SESSION ["year"]);
		unset($_SESSION ["type"]);
		unset($_SESSION ["course"]);
	}	
	
	if (isset($_POST["year"])) { 
		$_SESSION["year"]=$_POST["year"];
		unset($_SESSION ["type"]);
		unset($_SESSION ["course"]);
	}
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	$ls = array ("jl1","jma","kl1","kma");
		
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'> ";
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
	
	if(isset($_POST["course"])) {		
		$_SESSION["course"] = get_course($conn,$_POST["course"]);
		$students = get_all_students_course($conn,$_SESSION["course"]);
		show_photobook($students,$_SESSION["course"]);
	}	

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>