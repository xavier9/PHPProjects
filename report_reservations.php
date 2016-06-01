<?php
	require_once("includes/startsession.php");	
	$page_title = "Report: Reservations";
	require_once("includes/header.php");
	date_default_timezone_set('Europe/Brussels');	
	
	$sort = "total";
	$dir = SORT_DESC;
	if(isset($_POST["sort"])) 
		$sort = $_POST["sort"];
	
	if(isset($_POST["sort_asc"])) {
		$sort = $_POST["sort_asc"];
		$dir = SORT_ASC;
	}
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	overview_week($conn);
	overview_month ($conn);
	overview_teachers ($conn, $sort, $dir);	
	
	echo "</div>";	
	echo "</form>";
		
	require_once("includes/footer.php");
	
	function overview_teachers ($conn, $sort, $dir) {
		echo "<h3>Teachers</h3>";
		$teachers = get_all_teachers ($conn);
		$classes = get_all_reservable_classrooms($conn);
		$output = array();
		foreach ($teachers as $teacher) {
			$total = 0;
			$line ["teacher"]= $teacher["Last_name"]." ".$teacher["First_name"];
			foreach ($classes as $class) {
				$data =  get_reservations_teacher ($conn, $teacher, $class["Class"]);
				$minutes = count_minutes($data);
				$total += $minutes;
				$line [$class["Class"]] = $minutes;				
			}
			if ($total > 0) {
				$line ["total"] = $total;
				array_push($output, $line);
			}
		}	
		array_sort_by_column($output, $sort, $dir);
		echo "<table class='table'>";
		echo "<th>Teacher <input type='submit' class='button arrow' name='sort_asc' value='teacher'></th>";
		foreach ($classes as $class) 
			echo "<th>".$class["Class"]." <input type='submit' class='button arrow' name='sort' value='".$class["Class"]."'></th>";
		echo "<th>Total <input type='submit' class='button arrow' name='sort' value='total'></th>";
		foreach ($output as $line) {
			echo "<tr><td>".$line["teacher"]."</td>";
			foreach ($classes as $class) 
				echo "<td class='center small'>".$line[$class["Class"]]."</td>";
			echo "<td class='center small'>".$line["total"]."</td></tr>";
		}	
		echo "</table>";		
	}
	
	function overview_week ($conn) {
		echo "<h3>This Week</h3>";
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		$minutes = array (350,350,230,350,350);
		$classes = get_all_reservable_classrooms($conn);
		echo "<table class='table'>";
		echo "<th>Day</th>";
		foreach ($classes as $class) 
			echo "<th>".$class["Class"]."</th>";		
		for ($i=0; $i<count($days); $i++) { 
			echo "<tr>";
			echo"<td>".$days[$i]."</td>";
			foreach ($classes as $class) {
				$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime("This week", time())). " + ".$i." days"));
				$reservations = get_reservations_on_class_by_day($conn, $class["Class"], $date);
				$fixed_hours = get_timetable_template_teacher_by_location($conn, $class["Class"], $i);
				$data = merge_data ($reservations, $fixed_hours);
				echo "<td class='center'>".round(count_minutes($data)/$minutes[$i]*100)." %</td>";
			}
			echo "</tr>";		
		}		
		echo "</table>";
	}
	
	function overview_month	($conn) {
		echo "<h3>Last Month</h3>";
		$minutes = 1630;
		$classes = get_all_reservable_classrooms($conn);		
		echo "<table class='table'>";
		echo "<th>Week</th>";
		foreach ($classes as $class) 
			echo "<th>".$class["Class"]."</th>";		
		for ($i=0; $i<4; $i++) {
			echo "<tr>";
			echo"<td>".date("d/m", strtotime("-".(4-$i)." weeks monday"))
				." - ".date("d/m", strtotime("-".(4-$i)." weeks monday + 4 days"))."</td>";
			foreach ($classes as $class) {
				$count = 0;
				for($j=0; $j<5; $j++) {
					$date = date("Y-m-d", strtotime("-".(4-$i)." weeks monday + ".$j." days"));
					$reservations = get_reservations_on_class_by_day($conn, $class["Class"], $date);
					$fixed_hours = get_timetable_template_teacher_by_location($conn, $class["Class"], $j);
					$data = merge_data ($reservations, $fixed_hours);
					$count += count_minutes($data);
				}
				echo "<td class='center'>".round($count/$minutes*100)." %</td>";
			}
			echo "</tr>";		
		}		
		echo "</table>";
	}
	
	function merge_data ($data, $data2) {
		$rtn_array = array (); 
		foreach ($data as $value) {
			$line["ID"]=$value["ID"];
			$line["Begin"]=$value["Begin"];
			$line["End"]=$value["End"];
			$line["Teacher_id"]=$value["Teacher_id"];
			$line["Category"]="1";
			array_push($rtn_array, $line);
		}
		foreach ($data2 as $value) {
			$line["ID"]=$value["ID"];
			$line["Begin"]=$value["Begin"];
			$line["End"]=$value["End"];
			$line["Teacher_id"]=$value["Teacher_id"];
			$line["Category"]="0";
			array_push($rtn_array, $line);
		}
		array_sort_by_column($rtn_array, "Begin");
		return $rtn_array;
	}
	
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}	
	
	function count_minutes ($data) {		
		$total = 0;
		foreach ($data as $value) 
			$total += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+substr($value["End"],3,2)-substr($value["Begin"],3,2));
		return $total;
	}
?>