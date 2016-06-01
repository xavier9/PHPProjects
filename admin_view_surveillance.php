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
	echo "</tr></table>";
	
	if(isset($_POST["time"]) || isset($_POST["save"]))	
		show_surveillance ($conn, $_SESSION["period"]);		
	
	echo "</div>";	
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function show_surveillance ($conn, $description) {
		echo "<input type='submit' class='text_button float_right'  name='print' value='Print'/> ";		
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
			echo "<td>".$value["Location"]."</br>";
			$teacher = get_user ($conn, $value["Teacher_id"]);
			echo substr($teacher["Last_name"],0,9)."</td></tr>";
		}
		echo "</table>";
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
?>
