<?php	
	require_once("includes/startsession.php");
	require_once("includes/pdf/tfpdf.php");
	if(isset($_POST["print"])) 	{
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		print_surveillance ($conn, $pdf, $_SESSION["period"]);
		$pdf->output($_SESSION["period"].".pdf","D");
	}	
	require_once("includes/PHPExcel.php");
	require_once("includes/PHPExcel/Writer/Excel2007.php");	
	
	if(isset($_POST["excel"])) 	{
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		export_to_excel($conn, $_SESSION["period"]);
	}
	
	$page_title = "Surveillance";
	require_once("includes/header.php");

	if(isset($_POST["save"])) 	
		$err = save_surveillance($conn, $_POST["user"]);	
		
	if(isset($_POST["time"]))
		$_SESSION["period"]=$_POST["time"];
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";	
	echo "<div id='content'>";

	$times = get_timetable_surveillance_types($conn);	
	echo "<table class='table_no_border'>";
	$i=true;
	foreach ($times as $time) {
		echo ($i?"<td>":"");
		echo "<input type='submit' class='text_button surveillance' name='time' value='".$time["Description"]."'/>";
		echo ($i?"</br>":"</td>");
		$i=!$i;		
	}
	echo "</table>";

	if(isset($err))
		echo "<div class='err'>".$err."</div>";

	
	if(isset($_POST["time"]) || isset($_POST["save"]))	
		show_surveillance ($conn, $_SESSION["period"]);		
	
	echo "</div>";	
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function show_surveillance ($conn, $description) {
		echo "<input type='submit' class='text_button float_right'  name='print' value='Print'/> ";
		echo "<input type='submit' class='text_button float_right'  name='excel' value='Excel'/> ";
		echo "<input type='submit' class='text_button float_right' name='save' value='Save'/> ";
		echo "<h3 class='center'>".$description."</h3>";		
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			show_day_surveillance($conn, $days[$i],get_timetable_surveillance($conn,$i,$description));
	}
	
	function show_day_surveillance ($conn, $day, $data) {
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		foreach ($data as $value) {
			echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
			echo "<td>".$value["Location"]."";
			$users = get_timetable_teachers ($conn);
			echo "<select name='user[]'><option value='".$value["ID"].".0'>-</option>";
			foreach ($users as $user)
				echo "<option value='".$value["ID"].".".$user["ID"]."' ".($user["ID"]==$value["Teacher_id"]?"selected":"").">".substr($user["Last_name"],0,9)."</option>";
			echo "</select>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function save_surveillance ($conn, $data) {
		$days = array ("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
		$err="";
		foreach ($data as $value) {
			$line = explode (".",$value);
			
			$surveillance = get_timetable_surveillance_on_id ($conn, $line[0]);
			if($line[1]!=0) {	
				if($surveillance["Teacher_id"]!=$line[1]) {
					if($surveillance["Teacher_id"]!=0) 
						delete_timetable_template_teacher($conn, $surveillance["Teacher_id"], $surveillance["Day"], $surveillance["Begin"], $surveillance["End"]);
					if(check_period($surveillance["Begin"],$surveillance["End"],get_timetable_template_teacher_by_day($conn, $line[1], $surveillance["Day"]))) {
						$data["Teacher_id"] = $line[1];
						$data["Day"] = $surveillance["Day"];
						$data["Begin"] = $surveillance["Begin"];
						$data["End"] = $surveillance["End"];
						$data["Type"] = "surveill";
						$data["Location"] = $surveillance["Location"];		
						$data["Course"] = "surveill";
						add_timetable_template_teacher ($conn, $data);
						update_timetable_surveillance ($conn, $line[0], $line[1]);
					}
					else {
						$user = get_timetable_teacher($conn,$line[1]);
						$err .= $user["Last_name"]." conflict with timetable. ".$days[$surveillance["Day"]]." ".$surveillance["Location"]." not saved</br>";
					}
				}
			}
			else {
				delete_timetable_template_teacher($conn, $surveillance["Teacher_id"], $surveillance["Day"], $surveillance["Begin"], $surveillance["End"]);
				update_timetable_surveillance ($conn, $line[0], $line[1]);
			}						
		}
		return $err;
	}	
	
	function check_period ($begin, $end, $timetable) {		
		foreach ($timetable as $period) 
			if(($period["Begin"]<$begin && $period["End"]>$begin) || ($period["Begin"]<$end && $period["End"]>$end) || ($period["Begin"]>$begin && $period["End"]<$end) || ($period["Begin"]==$begin && $period["End"]==$end)) 						
				return false;
		return true;
	}
	
	function get_position_first_name($string) {
		for($i=strlen($string)-1;$i>=0;$i--) {
			if(ctype_upper(substr($string,$i,1)))
				return $i;
		}
	}
	
	function print_surveillance ($conn, $pdf, $description) {
		$pdf->SetFont('DejaVuB', '', 14);
		$pdf->SetFillColor(200,200,200);
		$pdf->Cell(190,10,$description,1,1,'C',1);
			
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			print_day_surveillance($conn, $pdf, $i, $days[$i],get_timetable_surveillance($conn,$i,$description), 30);
	}
	
	function print_day_surveillance ($conn, $pdf, $day_nr, $day, $data, $start_y) {
		$pdf->SetFont('DejaVu', '', 10);
		$width_hour = 13;
		$height_hour = 8;
		$width_course = 25;	
		
		$x = $pdf->getX();
		$pdf->setXY($x+38*$day_nr,$start_y);
		$pdf->Cell($width_hour+$width_course,10,$day,1,1,'C',1);
		foreach ($data as $value) {
			$x = $pdf->getX();
			$y = $pdf->getY();
			$pdf->setXY($x+38*$day_nr,$y);
			$pdf->Multicell($width_hour,$height_hour,substr($value["Begin"],0,5)."\n".substr($value["End"],0,5),1,'C',1);
			$x = $pdf->getX();
			$y = $pdf->getY();
			$pdf->setXY($x+38*$day_nr+$width_hour,$y-2*$height_hour);
			$user = get_timetable_teacher ($conn, $value["Teacher_id"]);
			if($user)
				$pdf->Multicell($width_course,$height_hour,$value["Location"]."\n".substr($user["Last_name"],0,9),1,'C',0);
			else
				$pdf->Multicell($width_course,$height_hour,$value["Location"]."\n"."-",1,'C',0);		
		}
	}
	
	function export_to_excel ($conn, $description) {
		date_default_timezone_set('europe/paris');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) {
			$data = get_timetable_surveillance($conn,$i,$description);
			$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0, $i+1), $days[$i]); 
			for ($j=0; $j<count($data); $j++) {
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field((($j*2)+1), 0), mb_convert_encoding(substr($data[$j]["Begin"],0,5),'Windows-1252')); 
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field((($j*2)+2), 0), mb_convert_encoding(substr($data[$j]["End"],0,5),'Windows-1252')); 
			
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field((($j*2)+1), $i+1), mb_convert_encoding($data[$j]["Location"],'Windows-1252')); 
				$teacher = get_user($conn, $data[$j]["Teacher_id"]);
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field((($j*2)+2), $i+1), mb_convert_encoding(substr($teacher["Last_name"],0,8),'Windows-1252')); 
			}
		}
		
		$file_name = "export_files/".$description.".xlsx";
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save(str_replace('.php', '.xlsx', $file_name));
		header ("Location:$file_name");	
		delete($file_name);
	}
	
	
	function get_excel_field ($i, $j) {
		$letters = array ("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		return $letters[$j].($i+1);
	}
?>
