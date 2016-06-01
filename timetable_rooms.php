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
		print_timetable ($conn, $pdf, $_POST["classroom"]);
		$pdf->output("Timetable ".$_POST["classroom"].".pdf","D");
	}
	
	if(isset($_POST["all_pdf"])) {
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$classes = get_all_searchable_classrooms ($conn);
		foreach ($classes as $class) {
			print_timetable ($conn, $pdf, $class["Class"]);
		}
		$pdf->output("Timetable ".$_POST["classroom"].".pdf","D");
	}
	
	$page_title = "Timetable: Rooms";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";	
	$classrooms = get_all_searchable_classrooms ($conn);
	echo "<select name='classroom'>";
	foreach ($classrooms as $classroom) {
		echo "<option value='".$classroom["Class"]."' ";
		if((isset($_POST["select_class"]) && $_POST["classroom"]==$classroom["Class"]) || (isset($_GET["class"]) && $_GET["class"]==$classroom["Class"]))
			echo "selected";
		echo " >".$classroom["Class"]."</option>";
	}
	echo "</select> ";
	echo "<input type='submit' name='select_class' value='Show' class='text_button'/> ";	
	if(isset($_POST["select_class"]) || isset($_GET["class"]))
		echo "<input type='submit' name='pdf' value='PDF' class='text_button'/> ";		
		
	if($_SESSION["user"]["ID"]=='1')
		echo "<input type='submit' name='all_pdf' value='Print All' class='text_button'/> ";		
	
	if(isset($_POST["select_class"])) {
		$types = array("-","onl","ep-","eur","religion","bus","class","lunch","l2-","surveill","jl2","occup");
		if(check_timetable ($conn, $types, $_POST["classroom"]))
			show_timetable ($conn, "Timetable: ",$_POST["classroom"], $types);			
		$types = array("ls");
		if(check_timetable ($conn, $types, $_POST["classroom"]))
			show_timetable ($conn, "Timetable LS: ",$_POST["classroom"], $types);	
		$types = array("lsi");
		if(check_timetable ($conn, $types, $_POST["classroom"]))
			show_timetable ($conn, "Timetable LS Intensive: ",$_POST["classroom"], $types);	
	}	
	
	if(isset($_GET["class"])) {
		$types = array("-","onl","ep-","eur","religion","bus","class","lunch","l2-","surveill","jl2","occup");
		if(check_timetable ($conn, $types, $_GET["class"]))
			show_timetable ($conn, "Timetable: ",$_GET["class"], $types);			
		$types = array("ls");
		if(check_timetable ($conn, $types, $_GET["class"]))
			show_timetable ($conn, "Timetable LS: ",$_GET["class"], $types);	
		$types = array("lsi");
		if(check_timetable ($conn, $types, $_GET["class"]))
			show_timetable ($conn, "Timetable LS Intensive: ",$_GET["class"], $types);	
	}	
	
	echo "</div>";
	echo "</form>";
	
	require_once ("includes/footer.php");
	
	function show_timetable ($conn, $title, $location, $types) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		echo"<div id='timetable'>";
		echo "<h3>".$title." ".$location."</h3>";
		for ($i=0; $i<count($days); $i++) 
			show_timetable_day($conn, $days[$i],get_timetable_template_teacher_by_location($conn, $location, $i), $types);
		echo "</div>";
	}
	
	function show_timetable_day ($conn, $day, $data, $types) {
		$begin=0;
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				if($begin!=0) {
					echo "</td>";
					echo "</tr>";
				}										
				$begin = $value["Begin"];
				$type = get_template_type($conn,$value["Type"]);
				if(in_array($value["Type"],$types)) {
					echo "<tr>";
					echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
					echo "<td class='".$type["Type"]."'>";
					$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
					echo "<a href='timetable_teachers.php?teacher=".$teacher["ID"]."'>".$teacher["Last_name"]."</a></br>";
					echo "<a href='my_lists.php?course=".$value["Course"]."'>".$value["Course"]."</a></br>";
				}
				else
					$begin='-1';
			}
			else {
				$type = get_template_type($conn,$value["Type"]);
				if(in_array($value["Type"],$types)) {
					$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
					echo $value["Course"]."</br>";
				}
			}
		}		
		echo "</table>";
	}	

	function check_timetable ($conn, $types, $location) {
		$data = get_timetable_template_teacher_on_class($conn, $location);
		foreach ($data as $value) {
			if(in_array($value["Type"],$types))
				return true;
		}
		return false;
	}	
		
	function print_timetable ($conn, $pdf, $location) {
		$pdf->SetFillColor(210,210,210);		
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
				
		$types = array ("-","onl","ep-","eur","religion","bus","class","lunch","l2-","surveill","jl2","occup");
		if(check_timetable($conn,$types,$location)) {
			$pdf->AddPage();			
			$pdf->SetFont('DejaVuB', '', 14);
			$pdf->Cell(190,10,"Timetable: ".$location,1,1,'C');
			$x = $pdf->GetX();
			$begin_y = $pdf->GetY();
			$pdf->SetFont('DejaVu', '', 8);
			print_timetable_on_type($conn, $pdf, $x, $begin_y, $days, $types, $location);
		}
		
		$types = array ("ls");
		if(check_timetable($conn,$types,$location)) {
			$pdf->AddPage();
			$pdf->SetFont('DejaVuB', '', 14);
			$pdf->Cell(190,10,"Timetable LS: ".$location,1,1,'C');
			$pdf->SetFont('DejaVu', '', 8);
			$x = $pdf->GetX();
			$begin_y = $pdf->GetY();
			print_timetable_on_type($conn, $pdf, $x, $begin_y, $days, $types, $location);
		}
		$types = array ("lsi");
		if(check_timetable($conn,$types,$location)) {
			$pdf->AddPage();
			$pdf->SetFont('DejaVuB', '', 14);
			$pdf->Cell(190,10,"Timetable LS Intensive: ".$location,1,1,'C');
			$pdf->SetFont('DejaVu', '', 8);
			$x = $pdf->GetX();
			$begin_y = $pdf->GetY();
			$types = array ("lsi");
			print_timetable_on_type($conn, $pdf, $x, $begin_y, $days, $types, $location);
		}
	}
	
	function print_timetable_on_type ($conn, $pdf, $x, $begin_y, $days, $types, $location) {
		$width_hour = 13;
		$width_course = 25;
		$height_hour = 8;
		for ($i=0; $i<count($days); $i++) {
			$data = get_timetable_template_teacher_by_location($conn, $location, $i);
			print_timetable_day ($conn, $pdf, $x, $begin_y, $days[$i], $data, $types);
			$x+=$width_hour+$width_course;			
			$pdf->SetY($begin_y);	
		}
	}
	
	function print_timetable_day ($conn, $pdf, $x, $begin_y, $day, $data, $types) {
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
				if(in_array($value["Type"],$types)) {
					$output_time = substr($value["Begin"],0,5)."\n".substr($value["End"],0,5);
					$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);
					$output = substr($teacher["Last_name"],0,12)."\n";
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
				else
					$begin='0';
			}
			else {
				$type = get_template_type($conn,$value["Type"]);
				if(in_array($value["Type"],$types)) {
					$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);				
					if(substr($value["Course"],0,5)!='m1gen' && substr($value["Course"],0,5)!='m2gen') {
						$output.="\n".$value["Course"];
						$lines++;
					}
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
?>