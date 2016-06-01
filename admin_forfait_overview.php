<?php
	require_once("includes/startsession.php");	
	$page_title = "Overview Forfait";
	require_once("includes/header.php");
	
	if(isset($_POST["show"]))
		header("Location: admin_create_timetable.php?id=".$_POST["show"]);
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	echo "<table class='table'>";
	echo "<th>Teacher</th><th>Forfait</th><th>Extra Info</th><th></th>";	
	$data = get_info_forfait($conn);
	foreach ($data as $value) {
		echo "<tr><td>".$value["Last_name"]." ".$value["First_name"]."</td>"
			."<td class='center'>".$value["Forfait"]."</td>"
			."<td>".$value["Info"]."</td>"
			."<td class='center small'><button class='text_button' type='submit' name='show' value='".$value["ID"]."'>Show</button></td></tr>";
	}	
	echo "</table>";
	
	echo "</div>";	
	echo "</form>";
		
	require_once("includes/footer.php");
?>