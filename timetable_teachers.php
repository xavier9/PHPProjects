<?php
	require_once("includes/startsession.php");	
	$page_title = "Timetable: Teachers";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";	
	$teachers = get_timetable_teachers ($conn);
	echo "<select name='teacher'>";
	foreach ($teachers as $teacher) {
		echo "<option value='".$teacher["ID"]."' ";
		if((isset($_POST["teacher"]) && $_POST["teacher"]==$teacher["ID"]) 
			|| (isset($_GET["teacher"]) && $_GET["teacher"]==$teacher["ID"]))
			echo "selected";
		echo ">".$teacher["Last_name"]." ".$teacher["First_name"]."</option>";
	}
	echo "</select> ";
	echo "<input type='submit' name='select_teacher' value='Show' class='text_button'/> ";		
	
	if(isset($_POST["select_teacher"]) || isset($_GET["teacher"])) {
		if(isset($_POST["select_teacher"]))
			$user = get_user($conn, $_POST["teacher"]);
		if(isset($_GET["teacher"]))
			$user = get_user($conn, $_GET["teacher"]);
			
		$teacher_courses = sort_array(get_all_teacher_courses ($conn, $user),"Code");
		show_teacher_info($user, $teacher_courses);
		show_timetable_teacher($conn, $user["ID"]);	
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once ("includes/footer.php");
?>