<?php
	require_once("includes/startsession.php");	
	$page_title = "Reservations: Rooms";
	require_once("includes/header.php");
	date_default_timezone_set('Europe/Brussels');	

	$msg = "";	
		
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
		
	if(!isset($_POST["add"]) && !isset($_POST["delete"]) && !isset($_POST["next_week"]) && !isset($_POST["this_week"])) 
		unset($_SESSION["class"]);
	
	if(isset($_POST["class"])) 
		$_SESSION["class"] = get_classroom($conn, $_POST["class"]);

	if(isset($_POST["add"])) {
		if($_POST["day"]==-1 || $_POST["begin_hour"]==-1 || $_POST["begin_min"]==-1	
			&& $_POST["end_hour"]==-1 && $_POST["end_min"]==-1)
			$msg = "Please fill in all fields!";		
		else {
			if(isset($_SESSION["Next"]))
				$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime("Next week", time())). " + ".$_POST["day"]." days"));
			else
				$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime("This week", time())). " + ".$_POST["day"]." days"));
			$begin = date("H:i:s", strtotime($_POST["begin_hour"].$_POST["begin_min"]));  
			$end = date("H:i:s", strtotime($_POST["end_hour"].$_POST["end_min"]));  
			
			if($end <= $begin)
				$msg = "End time must be greater then begin time!";	
			else {
				$today = getdate();
				if((sprintf("%04d-%02d-%02d", $today["year"], $today["mon"], $today["mday"]))>$date 
					|| (((sprintf("%04d-%02d-%02d", $today["year"], $today["mon"], $today["mday"]))==$date) 
						&& (sprintf("%02d:%02d:%02d",$today["hours"],$today["minutes"],"00")>$begin)))
					$msg = "Reservation can't be in the past!";	
				else {
					$reservations = get_reservations_on_class_by_day($conn, $_SESSION["class"]["Class"], $date);							
					$fixed_hours = get_timetable_template_teacher_by_location($conn, $_SESSION["class"]["Class"], (date('N',strtotime($date))-1));
					$data = merge_data ($reservations, $fixed_hours);					
					if(check_reservation($data,$begin,$end))	{
						if(check_credits($begin,$end,get_credits($conn)))
							add_reservation ($conn, $_SESSION["class"], $date, $begin, $end, $_SESSION["user"]);
						else 
							$msg = "Not enough credits for your reservation!";	
					}
					else
						$msg = "Time already reserved. Please select another time!";	
				}
			}
		}
	}	
	
	if(isset($_POST["delete"])) 
		delete_reservation($conn, $_POST["delete"]);
	
	if(isset($_POST["next_week"]))
		$_SESSION["Next"]=1;
	
	if(isset($_POST["this_week"]))
		unset($_SESSION["Next"]);				
		
	$classes = get_all_reservable_classrooms($conn);
	foreach ($classes as $class) 
		echo "<input type='submit' value='".$class["Class"]."' name='class' class='text_button'> ";
		
	if($msg!="")
			echo "<div class='err'></br>".$msg."</div>";

	if(isset($_SESSION["class"])) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		$hours = array ('08','09','10','11','12','13','14','15','16');
		$minutes = array ('00','05','10','15','20','25','30','35','40','45','50','55');
		echo "<table class='table_no_border'>";
		echo "<tr><td><h3>Reservations: ".$_SESSION["class"]["Class"]." ".$_SESSION["class"]["Description"];
		if($_SESSION["class"]["Location"])
				echo "</td><td><h3>Room: ".$_SESSION["class"]["Location"]."</h3>";
		echo "</h3></td><td class='right'><h3>Credits: ".get_credits($conn)." min.</h3></td></tr>";
		echo "<tr><td colspan='2'>Make a reservation: ";
		echo "<select name='day'><option value='-1'>-</option>'";
		for($i=0; $i<count($days);$i++)
			echo "<option value='$i'>".$days[$i]."</option>";
		echo "</select>";				
		echo " From <select name='begin_hour'><option value='-1'>-</option>'";
		for($i=0; $i<count($hours);$i++)
			echo "<option value='".$hours[$i]."'>".$hours[$i]."</option>";
		echo "</select> : ";
		echo "<select name='begin_min'><option value='-1'>-</option>'";
		for($i=0; $i<count($minutes);$i++)
			echo "<option value='".$minutes[$i]."'>".$minutes[$i]."</option>";
		echo "</select>";
		echo " Till <select name='end_hour'><option value='-1'>-</option>'";
		for($i=0; $i<count($hours);$i++)
			echo "<option value='".$hours[$i]."'>".$hours[$i]."</option>";
		echo "</select> : ";
		echo "<select name='end_min'><option value='-1'>-</option>'";
		for($i=0; $i<count($minutes);$i++)
			echo "<option value='".$minutes[$i]."'>".$minutes[$i]."</option>";
		echo "</select> ";	
		echo "<input type='submit' name='add' value='Add' class='text_button'/> ";
		if(isset($_SESSION["Next"]))
			echo "<input type='submit' value='This week' name='this_week' class='text_button'> ";
		else
			echo "<input type='submit' value='Next week' name='next_week' class='text_button'> ";
			
		echo "</td>";		
		
		echo "</table>";
		echo "<div id='timetbale'>";
			show_reservations($conn, $_SESSION["class"]["Class"]);
		echo "</div>";
	}
	
	echo "</div>";
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function show_reservations ($conn, $class) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) {
			if(isset($_SESSION["Next"]))
				$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime("Next week", time())). " + ".$i." days"));
			else
				$date = date("Y-m-d", strtotime(date("Y-m-d", strtotime("This week", time())). " + ".$i." days"));
			$reservations = get_reservations_on_class_by_day($conn, $class, $date);
			$fixed_hours = get_timetable_template_teacher_by_location($conn, $class, $i);
			$data = merge_data ($reservations, $fixed_hours);			
			show_reservations_day($conn, $days[$i], $date, $data);
		}
	}
	
	function show_reservations_day ($conn, $day, $date, $data) {
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</br>".change_date_format($date)."</th>";
		foreach ($data as $value) {
			echo "<tr>";
			echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
			if($teacher = get_user($conn,$value["Teacher_id"])) {
				if ($teacher["ID"]==$_SESSION["user"]["ID"] && $value["Category"]==1) {
					echo "<td class='religion'>".$teacher["Last_name"];
					$today = getdate();
					if((sprintf("%04d-%02d-%02d", $today["year"], $today["mon"], $today["mday"]))<$date 
						|| ((sprintf("%04d-%02d-%02d", $today["year"], $today["mon"], $today["mday"]))==$date 
							&& sprintf("%02d:%02d:%02d",$today["hours"],$today["minutes"],"00")<$value["Begin"]))
						echo"<input type='submit' class='button' style=\"width:20px;float:right;background: url('../images/delete2.png') no-repeat;\" name='delete' value='".$value["ID"]."'>";
					echo "</td>";
				}
				else {
					if($value["Category"]==0)
						echo "<td class='eur'>".$teacher["Last_name"]."</td>";
					else
						echo "<td>".$teacher["Last_name"]."</td>";
				}
			}
			else 
				echo "<td>".$value["Teacher_id"]."</td>";
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
	
	function get_credits($conn) {
		$exceptions = array ("86628");
		$start_date = date("Y-m-d", strtotime("This week", time()));
		$end_date =  date('Y-m-d', strtotime($start_date. ' + 14 days'));
		$data = get_reservations_credits ($conn, $_SESSION["class"]["Class"], $start_date, $end_date, $_SESSION["user"]);
		$minutes = 0;
		foreach ($data as $value) {
			$minutes += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+(substr($value["End"],3,2)-substr($value["Begin"],3,2)));
		}
		if(in_array ($_SESSION["user"]["ID"], $exceptions))
			return 1000;
		return 180 - $minutes;
	}
	
	function check_reservation ($data, $begin, $end) {
		foreach ($data as $period) {
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
	
	function check_credits($begin,$end,$credits) {
		return $credits >= ((substr($end,0,2)-substr($begin,0,2))*60+(substr($end,3,2)-substr($begin,3,2)));
	}
?>