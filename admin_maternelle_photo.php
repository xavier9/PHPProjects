<?php
	require_once("includes/startsession.php");	
	require_once("includes/pdf/tfpdf.php");
	require_once("includes/db_functions.php");	
	require_once("includes/functions.php");	
	
	if(isset($_POST["pdf"])) {
		$conn = get_db_connection();
		mysqli_set_charset($conn,"utf8");
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$pdf->AddPage();		
		$pdf->SetFont('DejaVuB', '', 10);
		print_selected_students ($conn, $pdf, get_students ($conn,$_POST["students"]));
		$pdf->output("card_maternelle.pdf","D");	
	}
	
	$page_title = "Card Maternelle";
	require_once("includes/header.php");
	require_once("includes/show_info.php");	
		
	$checked = "";
	if(isset($_POST["all"]))	
		$checked = "checked";
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	echo "<input type='submit' name='pdf' class='text_button' value='PDF'/> ";	
	echo "<input type='submit' name='all' class='text_button' value='Select All'/>";	
	
	$students = get_students_on_class($conn, "m");	
	echo "<table class='table'>";		
	for ($i=0; $i<count($students); $i+=4){
		echo "<tr>";
		if(isset($students[$i]))
			echo "<td><input type='checkbox' name='students[]' value='".$students[$i]["ID"]."' $checked/>"
				.$students[$i]["Last_name"]." ".$students[$i]["First_name"]."</td>";
		
		if(isset($students[$i+1]))
			echo "<td><input type='checkbox' name='students[]' value='".$students[$i+1]["ID"]."' $checked/>"
				.$students[$i+1]["Last_name"]." ".$students[$i+1]["First_name"]."</td>";
		
		if(isset($students[$i+2]))
			echo "<td><input type='checkbox' name='students[]' value='".$students[$i+2]["ID"]."' $checked/>"
				.$students[$i+2]["Last_name"]." ".$students[$i+2]["First_name"]."</td>";
		
		if(isset($students[$i+3]))
			echo "<td><input type='checkbox' name='students[]' value='".$students[$i+3]["ID"]."' $checked/>"
				.$students[$i+3]["Last_name"]." ".$students[$i+3]["First_name"]."</td>";
				
		echo "</tr>";
	}		
	echo "</table>";			
	
	echo "</div>";
	echo "</form>";
	
	function get_students ($conn, $ids) {
		$students  = array();
		foreach ($ids as $id) 
			array_push ($students, get_student($conn, $id));
		return $students;		
	}
	
	function print_selected_students ($conn, $pdf, $students) {
		$pic_width = 25;
		$pic_height = 37.5;
		$text_width = 70;
		$text_height = 10;
		$students = sort_array($students,"Class");
		$y = 10;	
		$lines = 0;
		for($i=0;$i<count($students);$i+=2) {			
			$pdf->setXY($pdf->getX(),$y);		
			print_student ($pdf, $students[$i], $pic_width, $pic_height, $text_width, $text_height);					
			if(isset($students[$i+1])) {
				$pdf->setXY($pdf->getX()+$pic_width+$text_width+1,$y);		
				print_student ($pdf, $students[$i+1], $pic_width, $pic_height, $text_width, $text_height);		
				$y += $pic_height+7;		
				$lines ++;
				if($lines==6) {
					$pdf->AddPage();
					$y = 10;
					$lines = 0;
				}
			}									
		}
	}	
	
	function print_student ($pdf, $student, $pic_width, $pic_height, $text_width, $text_height) {
		$x = $pdf->getX();
		$y = $pdf->getY();	
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Image(get_photo($student["ID"]),$pdf->getX(),$pdf->getY(),$pic_width,$pic_height);
		$pdf->setXY($x+$pic_width,$y);
		$pdf->Cell($text_width,$text_height,"Elève Maternelle",0,1,'C');
		$pdf->setXY($x+$pic_width,$pdf->getY());
		$pdf->Cell($text_width,$text_height,$student["Last_name"],0,1,'C');
		$pdf->setXY($x+$pic_width,$pdf->getY());
		$pdf->Cell($text_width,$text_height,$student["First_name"],0,1,'C');
		$pdf->setXY($x+$pic_width,$pdf->getY());
		$pdf->SetFont('DejaVuB', '', 14);
		$pdf->Cell($text_width,$text_height,$student["Class"],0,1,'C');
	}
?>