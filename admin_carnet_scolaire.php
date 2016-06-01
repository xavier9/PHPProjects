<?php
	require_once("includes/startsession.php");	
	$page_title = "Canret Scolaire";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content_with_title'>";
	
	show_carnet ($conn, "Carnet Scolaire February 2015 - 2016", "carnet scolaire/2015 - 2016/feb", "_Semester_1");
	show_carnet ($conn, "Carnet Scolaire July 2014 - 2015", "carnet scolaire/2014 - 2015/july", "_-_Semester_2");
	show_carnet ($conn, "Carnet Scolaire February 2014 - 2015", "carnet scolaire/2014 - 2015/feb", "_-_Semester_1");
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_carnet ($conn, $title, $folder, $file_name) {
		echo "<h3>".$title."</h3>";	
		$sections = get_all_sections ($conn);
		$years = get_all_years($conn,"p");
		echo "<table class='table'>";
		echo "<th>Year</th>";
		foreach ($sections as $section)
			echo "<th>".$section["Section"]."</th>";	
		foreach ($years as $year) {
			echo "<tr><td class='center'>".$year["Class"]."</td>";
			foreach ($sections as $section) {
				$file = $folder."/B2_".strtoupper($year["Class"])."_".strtoupper($section["Section"]).$file_name.".pdf";
				if(file_exists($file))
					echo "<td class='center'><a href='".$file."'><img src='../images/check.png'  style='width:32px;height:32px'></a></td>";
				else
					echo "<td class='free'></td>";	
			}
			echo "</tr>";	
		}	
		echo "</table>";			
	}
?>