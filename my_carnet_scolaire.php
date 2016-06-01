<?php
	require_once("includes/startsession.php");	
	$page_title = "My Carnet Scolaire";
	require_once("includes/header.php");
	
	$year = "2015 - 2016";
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";		
	
	$courses = get_all_teacher_courses($conn,$_SESSION["user"]);
	$classes = array();
	foreach ($courses as $course) {
		if(substr($course["Code"],2,3)=="mat")
			array_push ($classes, substr($course["Code"],0,2).substr($course["Code"],5,3));
	}
	
	echo "<table class='table'>";
	echo "<th>Year</th><th>February</th><th>July</th>";
	echo "<tr><td class='center small'>".$year."</td>";
	
	echo "<td class='center medium'>";
	foreach ($classes as $class) {		
		$file = "carnet scolaire/".$year."/feb/B2_".strtoupper(substr($class,0,2))."_".strtoupper(substr($class,2,3))."_Semester_1.pdf";
		if(file_exists($file))
			echo "<a href='".$file."'>".$class.".pdf</a></br>";
		else
			echo "Not yet available</br>";
	}
	echo"</td>";	
	
	echo "<td class='center medium'>";
	foreach ($classes as $class) {	
		$file = "carnet scolaire/".$year."/july/B2_".strtoupper(substr($class,0,2))."_".strtoupper(substr($class,2,3))."_Semester_2.pdf";
		if(file_exists($file))
			echo "<a href='".$file."'>".$class."</a></br>";
		else
			echo "Not yet available</br>";
	}
	echo"</td>";
	
	echo "</tr>";
	echo "</table>";
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>