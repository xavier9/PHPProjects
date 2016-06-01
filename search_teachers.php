<?php
	require_once("includes/startsession.php");	
	$page_title = "Search Teachers";
	require_once("includes/header.php");
	require_once("includes/show_info.php");
	require_once("includes/timetable.php");
		
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					
	
	echo "<label>Search a teacher: </label>";
	echo "<input type='text' name='input' value='' autofocus> ";
	echo "<input type='submit' value='Search' name='search' class='text_button'/> ";

	if(isset($_POST["search"]))
		$_SESSION["search"] = $_POST["input"];
	
	if(isset($_POST["search"]) || isset($_POST["back"])) {			
		$users = search_users ($conn, $_SESSION["search"]);
		show_list($users);
	}	
	
	if(isset($_POST["id"]) || isset($_GET["id"])) {	
		if(isset($_POST["id"])) {	
			echo "<input type='submit' value='Back' name='back' class='text_button'/>";
			$user = get_user($conn, $_POST["id"]);
		}
		
		if(isset($_GET["id"])) 
			$user = get_user($conn, $_GET["id"]);
		
		$teacher_courses = sort_array(get_all_teacher_courses ($conn, $user),"Code");
		show_teacher_info($user, $teacher_courses);
		show_timetable_teacher($conn, $user["ID"]);		
	}
		
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>