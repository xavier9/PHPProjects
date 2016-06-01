<?php
	require_once("includes/startsession.php");	
	$page_title = "Find free room";
	require_once("includes/header.php");
	$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
	
	if(isset($_POST["search"])) {
		$_SESSION["begin"]=$_POST["begin"].":00";
		$_SESSION["end"]=$_POST["end"].":00";
		$_SESSION["day"]=$_POST["day"];
	}
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";				
	
	echo "<table class='table'>";
	echo "<th>Day</th><th>Begin</th><th>End</th><th></th>";
	echo "<tr>";
	echo "<td class='center'><select name='day'><option value='-1'>-</option>'";
	for($i=0;$i<5;$i++) {
		echo "<option value='$i' ";
		if(isset($_SESSION["day"]) && $_SESSION["day"]==$i)
			echo "selected";
		echo ">".$days[$i]."</option>";
	}
	echo "</select></td>";	
	echo "<td class='center'><input class='center small' type='text' name='begin' value='".(isset($_SESSION["begin"])?substr($_SESSION["begin"],0,5):"")."'/></td>";
	echo "<td class='center'><input class='center small' type='text' name='end' value='".(isset($_SESSION["end"])?substr($_SESSION["end"],0,5):"")."'/></td>";
	echo "<td class='center'><input type='submit' value='Search' name='search' class='text_button'/></td>";
	echo "</tr>";
	echo "</table>";		
	
	if(isset($_POST["search"])) {
		echo "<h3>Free rooms for ".$days[$_SESSION["day"]]." ".$_SESSION["begin"]." - ".$_SESSION["end"]."</h3>";		
		$classes = get_all_searchable_classrooms($conn);
		foreach ($classes as $class) {
			$timetable = get_timetable_template_teacher_by_location($conn, $class["Class"], $_SESSION["day"]);
			if(is_free($timetable, $_SESSION["day"], $_SESSION["begin"], $_SESSION["end"]))
				echo "<button type='submit' value='".$class["Class"]."' name='class' class='text_button'>".$class["Class"]."</button> ";
		}		
	}
	
	echo "</div>";
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function is_free ($timetable, $day, $begin, $end) {
		foreach ($timetable as $period) {
			if($period["Day"]==$day)
				if($begin<$period["Begin"]) {
					if($end>$period["Begin"]) 
						return false;
				}
			else {
				if($begin<$period["End"]) 
					return false;
			}
		}
		return true;
	}
?>