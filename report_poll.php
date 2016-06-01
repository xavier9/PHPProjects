<?php	
	require_once("includes/startsession.php");	
	$page_title = "Report Poll";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	show_results_poll ($conn);
	echo "</br></br>";
	show_voters ($conn);
	
	echo "</div>";
	echo "</form>";

	require_once("includes/footer.php");
	
	function show_results_poll ($conn) {
		$users = get_all_users ($conn);
		$data = get_all_poll ($conn);
		$cnt_before = 0;
		$cnt_after = 0;
		
		foreach ($data as $value) {
			if($value["Choice"]==0)
				$cnt_before ++;
			else
				$cnt_after ++;
		}
		$total = $cnt_after + $cnt_before;
		
		echo "<table class='table'>";
		echo "<th>Before Holidays</th><th>After Holidays</th><th>Voted</th>";
		echo "<tr><td class='center'>".round(($cnt_before/$total*100))." %</td>"
			."<td class='center'>".round(($cnt_after/$total*100))." %</td>"
			."<td class='center'>".round(($total/count($users)*100))." %</td></tr>";
		echo "</table>";		
	}
	
	function show_voters ($conn) {
		$data = get_all_poll ($conn);
		echo "<table class='table'>";
		echo "<th>Teacher</th><th>Before Holidays</th><th>After Holidays</th>";
		foreach ($data as $value) {
			$teacher = get_user ($conn, $value["Teacher_id"]);
			echo "<tr><td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td>"
				."<td class='center small'>".($value["Choice"]==0?"X":"")."</td>"
				."<td class='center small'>".($value["Choice"]==1?"X":"")."</td></tr>";
		}
		echo "</table>";
				
	}
?>
