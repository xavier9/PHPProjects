<?php
	require_once("includes/startsession.php");	
	$page_title = "Photobook My Students";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					
	foreach ($courses as $course) 
		echo "<input type='submit' value='".$course["Code"]."' name='course' class='text_button'/> ";			
	
	if(isset($_POST["course"])) {		
		$_SESSION["course"] = get_course($conn,$_POST["course"]);
		$students = get_all_students_course($conn,$_SESSION["course"]);
		show_photobook($students,$_SESSION["course"]);
	}	

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>