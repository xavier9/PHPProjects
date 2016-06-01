<?php	
	require_once("includes/startsession.php");

	require_once("includes/pdf/tfpdf.php");
	if(isset($_POST["pdf"])) {
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		print_header_timetable ($conn, $pdf, $_SESSION["timetable_user"]);
		$pdf->output($_SESSION["timetable_user"]["Last_name"]." ".$_SESSION["timetable_user"]["First_name"].".pdf","D");
	}
	
	if(isset($_POST["print"])) {
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->SetFont('DejaVu','',10);
		$pdf->AliasNbPages();		
		foreach($_POST["print_all"] as $value) {
			$pdf->AddPage();
			$user = get_timetable_teacher ($conn, $value);
			print_header_timetable ($conn, $pdf, $user);
		}
		$pdf->output("Timetables.pdf","D");		
	}	
	
	$page_title = "Overview Timetables";
	require_once("includes/header.php");
	
	if(isset($_POST["previous"]))  
			$_SESSION["timetable_user"] = get_previous_user ($conn);
		
	if(isset($_POST["next"]))  
			$_SESSION["timetable_user"] = get_next_user ($conn);
		
	if(isset($_POST["show"])) {
		$_SESSION["timetable_user"] = get_timetable_teacher ($conn, $_POST["show"]);
		if($user = get_user($conn, $_POST["show"])) {
			$_SESSION["timetable_user"]["Last_name"] = $user["Last_name"];
			$_SESSION["timetable_user"]["First_name"] = $user["First_name"];
		}
	}

	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	
	if(isset($_POST["show"]) || isset($_POST["next"]) || isset($_POST["previous"])) {
		echo "<div id='timetable'>";						
		show_header_timetable ($conn);
		show_timetable ($conn, $_SESSION["timetable_user"]);
		show_footer_timetable($conn, $_SESSION["timetable_user"]);		
		echo "</div>";			
	}
	else {			
		$users = get_timetable_teachers ($conn);
		echo "<div id='content'>";
		if(isset($err))
			echo "<div id='msg'>".$err."</div>";
		
		echo "<input class='text_button' type='submit' name='print' value='Print Selected'/> ";			
		echo "<table class='table'>";
		echo "<th>Name</th><th>Class</th><th>Forfait</th><th>Surveillance</th><th>Total</th><th>Submitted</th><th></th><th></th>";	
		foreach ($users as $user) {	
			$ready_det=$ready_cdc=false;
			if($user["Category"]==1 && calculate_minutes($conn,$user)==1530) 
				$ready_det=true;
			if($user["Ready"]==1) 
				$ready_cdc=true;

			if($teacher = get_user($conn,$user["ID"])) {
				$user["Last_name"] = $teacher["Last_name"];
				$user["First_name"] = $teacher["First_name"];
			}			

			echo "<tr><td>".$user["Last_name"]." ".$user["First_name"]."</td>";
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".$user["Class"]."</td>";
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".$user["Forfait"]."</td>";
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".calculate_minutes_on_type($conn, $user, "surveill")."</td>";		
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".calculate_minutes($conn,$user)."</td>";
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".($user["Submitted"]!="0000-00-00"?"Yes":"No")."</td>";
			echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'><button class='text_button' name='show' value='".$user["ID"]."'>Show</button></td>";
			echo "<td><input type='checkbox' name='print_all[]' value='".$user["ID"]."'></td></tr>";
		}
		echo "</table>";
		echo "</div>";		
	}
	
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function calculate_minutes ($conn, $user) {
		$min = 0;
		for ($i=0; $i<5; $i++) 
			$min += count_minutes(get_timetable_template_teacher_by_day($conn, $user["ID"], $i));
		return $min + $user["Forfait"];
	}
	
	function count_minutes ($data) {
		$min = 0;
		$begin = 0;
		foreach ($data as $value) 
			if($begin!=$value["Begin"]) {
				$begin=$value["Begin"];
				if($value["Type"]!="free")
					$min += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+substr($value["End"],3,2)-substr($value["Begin"],3,2));
			}
		return $min;
	}
	
	function calculate_minutes_on_type ($conn, $user, $type) {
		$min = 0;
		for ($i=0; $i<5; $i++) 
			$min += count_minutes_on_type(get_timetable_template_teacher_by_day($conn, $user["ID"], $i),$type);
		return $min;
	}
	
	function count_minutes_on_type ($data, $type) {
		$min = 0;
		$begin = 0;
		foreach ($data as $value) {
			if($value["Type"]==$type) {
				if($begin!=$value["Begin"]) {
					$begin=$value["Begin"];
					$min += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+substr($value["End"],3,2)-substr($value["Begin"],3,2));
				
				}
			}
		}
		return $min;
	}
	
	function show_timetable ($conn, $user) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			show_timetable_day($conn, $days[$i],get_timetable_template_teacher_by_day($conn, $user["ID"], $i));
	}
	
	function show_timetable_day ($conn, $day, $data) {
		$no_link = array ("surveill","class","lunch","coord","occup","periscolai","Special","triparti","Woluwe","bus");
		$types = get_template_types($conn);
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		$begin = 0;
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					echo "</td>";
					echo "</tr>";
				}										
				$begin = $value["Begin"];
				echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
				echo "<td class='".$value["Type"]."'>";
				$type = get_template_type($conn,$value["Type"]);
				if($type["Location"]==1) 
					echo "<a href='timetable_rooms.php?class=".$value["Location"]."'>".$value["Location"]."</a></br>";		
				if(in_array($value["Course"],$no_link))
					echo $value["Course"];
				else
					echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a>";
				echo "</br>";				
			}
			else {
				if(in_array($value["Course"],$no_link))
					echo $value["Course"];
				else
					echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a>";
				echo "</br>";
			}
		}
		echo "<tr><td class='hour' colspan='2'>".count_minutes($data)."</td></tr>";
		echo "</table>";
	}
	
	function show_header_timetable ($conn) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		echo "<table class='table_no_border'>";		
		echo "<tr>"
			."<td><input class='text_button' type='submit' name='previous' value='<'/></td>"
			."<td class='center'><h2>".$_SESSION["timetable_user"]["Last_name"]." ".$_SESSION["timetable_user"]["First_name"]."</h2></td>"
			."<td><input class='text_button float_right' type='submit' name='next' value='>'/></td>"		
			."<tr><td class='center' colspan='3'><input type='submit' class='text_button' name='pdf' value='PDF'/></td></tr>"
			."</tr>"
			."</table>";	
		
	}
	
	function show_footer_timetable ($conn, $user) {
		echo "<table border='2' cellpadding='5' cellspacing='0' width='100%'>";
		echo "<tr><td rowspan='5' style='width:60%;'><textarea style='width:98%;height:150px;' disabled name='extra'>".$user["Info"]."</textarea></td>";			
		echo "<td>LS:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "ls")."</td>"
			."<td>Ratt/Swals:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "jl2")."</td>"
			."</tr>";			
		echo "<tr><td>LSI:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "lsi")."</td>"
			."<td>ONL:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "onl")."</td>"		
			."</tr>";
		echo "<tr><td>Coordination:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "coord")."</td>";
		echo "<td>Forfait:</td><td style='width:40px;text-align:right;'>".$_SESSION["timetable_user"]["Forfait"]."</td></tr>";
		echo "<tr><td>Surveillance:</td><td style='width:40px;text-align:right;'>".calculate_minutes_on_type($conn, $_SESSION["timetable_user"], "surveill")."</td>"
			."<td></td><td></td>"		
			."</tr>";		
		echo "<tr><td colspan='3'><strong>Total:</strong</td><td style='width:40px;text-align:right;'><strong>".calculate_minutes($conn,$_SESSION["timetable_user"])."<strong></td></tr>";
		echo "</table>";	
	}
		
	function print_header_timetable ($conn, $pdf, $user) {
		$width_hour = 13;
		$width_course = 25;
		$height_hour = 8;
		$pdf->SetFillColor(160,160,160);
		$pdf->SetFont('DejaVuB', '', 14);
		$pdf->Cell(190,10,$user["Last_name"]." ".$user["First_name"],1,1,'C');
		$pdf->SetFont('DejaVu', '', 10);
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		$x = $pdf->GetX();
		$begin_y = $pdf->GetY();
		for ($i=0; $i<count($days); $i++) {
			$data = get_timetable_template_teacher_by_day($conn, $user["ID"], $i);
			print_timetable_day ($conn, $pdf, $x, $begin_y, $days[$i], $data);
			$x+=$width_hour+$width_course;			
			$pdf->SetY($begin_y);	
		}
		
		$pdf->SetAutoPageBreak(0);
		
		$pdf->Rect(10,246,115,39);		
		$pdf->SetXY(10,246);
		$pdf->Multicell(115,5,$user["Info"],0,'L',0);
		
		$height = 13;
		$width_subject = 25;
		$width_counter = 13;
		$x = 130; 
		$y = 246;
		$pdf->SetXY($x,$y);
		$pdf->Cell($width_subject,$height,"LS:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "ls"),1,0,'C');
		$pdf->Cell($width_subject,$height,"Ratt/Swals:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "jl2"),1,0,'C');		
		$y+=$height;
		
		$pdf->SetXY($x,$y);
		$pdf->Cell($width_subject,$height,"Coordination:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "coord"),1,0,'C');
		$pdf->Cell($width_subject,$height,"ONL:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "onl"),1,0,'C');		
		$y+=$height;
		
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Times', 'B', 12);
		$pdf->Cell($width_subject*2+$width_counter,$height,"Total:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes($conn,$user),1,0,'C');
	}
	
	function print_timetable_day ($conn, $pdf, $x, $begin_y, $day, $data) {
		$width_hour = 13;
		$width_course = 25;
		$height_hour = 8;
		$begin=0;
		$output = "";
		$output_time = "";
		$lines = 2;
		$pdf->SetX($x);	
		$y = $pdf->GetY();
		$pdf->Multicell($width_hour+$width_course,$height_hour,$day,1,'C',1);
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					$pdf->SetX($x);	
					$y = $pdf->GetY();
					$pdf->Multicell($width_hour,$height_hour*$lines/2,$output_time,1,'C',1);
					$pdf->SetXY($x+$width_hour,$y);
					$pdf->Multicell($width_course,$height_hour,$output,1,'C');
					$pdf->SetY($y+$height_hour*$lines);
					$output = "";
					$output_time = "";
					$lines = 2;
				}					
				$begin = $value["Begin"];
				$type = get_template_type($conn,$value["Type"]);
				
				$output_time = substr($value["Begin"],0,5)."\n".substr($value["End"],0,5);
				if($type["Location"]==1) 	
					$output = $value["Location"]."\n";
				else
					$output = "\n";				
				if(is_numeric($value["Course"])) {
					$student = get_student ($conn, $value["Course"]);
					$output.=substr($student["Last_name"]." ".$student["First_name"],0,14);
				}
				else {
					if(substr($value["Course"],0,5)=='m1gen' || substr($value["Course"],0,5)=='m2gen')
						$output.="m2".substr($value["Course"],5,3);
					else
						$output.=$value["Course"];
					}
			}
			else {
				$type = get_template_type($conn,$value["Type"]);
				$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
				if(substr($value["Course"],0,5)!='m1gen' && substr($value["Course"],0,5)!='m2gen') {
					$output.="\n".$value["Course"];
					$lines++;
				}		
			}
		}
		if($output!="") {
			$pdf->SetX($x);	
			$y = $pdf->GetY();
			$pdf->Multicell($width_hour,$height_hour*$lines/2,$output_time,1,'C',1);
			$pdf->SetXY($x+$width_hour,$y);
			$pdf->Multicell($width_course,$height_hour,$output,1,'C');	
		}
	}
	
	function get_next_user ($conn) {
		$users = get_timetable_teachers ($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["timetable_user"]) {
				if($i+1==count($users))
					return $users[0];
				else 
					return $users[$i+1];
			}
		}
	}
	
	function get_previous_user ($conn) {
		$users = get_timetable_teachers ($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["timetable_user"]) {
				if($i==0)
					return $users[count($users)-1];
				else 
					return $users[$i-1];
			}
		}
	}
?>
