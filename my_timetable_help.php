<?php
	require_once("includes/startsession.php");	
	$page_title = "My Timetable Help";
	require_once("includes/header.php");
	
	if(isset($_POST["back"]))
		header("Location: my_timetable.php");
				
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content_with_title'>";		
	
	
	echo "<h3>How to fill in your timetable:</h3>";
	echo "1) Please fill in all periods with a course. (See below a list of your courses with the description)</br></br>";	
	echo "2) <button type='submit' name='add_course' class='action_button'>+</button>: To add an extra course to a period (ex. when you have 2 or 3 groups of religion or when you have swals students in your class)</br></br>";
	echo "3) <button style='color:red;font-weight:bold;' type='submit' name='add_course' class='action_button'>X</button>: To delete a course from a period</br>";
	
	echo "</br><table class='table'>";
	echo "<caption>List of courses</caption>";	
	$courses = get_all_teacher_courses ($conn, $_SESSION["user"]);
	$religion = array ("ror","rpr","ris","rju","mor","rca");
	foreach ($courses as $course) {
		if(in_array(substr($course["Code"],2,3),$religion))
			echo "<tr><td class='religion center'>";
		else
			echo "<tr><td class='center ".substr($course["Code"],2,3)."'>";
		echo $course["Code"]."</td><td>".$course["Description"]." year ".substr($course["Code"],1,1)."</td></tr>";
	}
	echo "</table></br>";
	
	echo "If you still have questions or need help: please contact Kjell (office 322) or e-mail:<a href='mailto:vrkl@eeb2.be' class='link'>kjell.vanlaer@eeb2.be</a>";
	echo "<div class='right'><button type='submit' name='back' class='text_button'>Back</button></div>";
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");	
?>