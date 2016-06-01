<?php
	require_once ("includes/startsession.php");	
	require_once ("includes/pdf/tfpdf.php");
	require_once ("includes/db_functions.php");
	require_once ("includes/functions.php");
	
	$conn = get_db_connection();	
	if(isset($_POST["print"]))
		print_all_teachers ($conn);
	
	$page_title = "Print Photobook";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";			
	echo "<input type='submit' value='Print Photobook' name='print' class='text_button' />";
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function print_all_teachers ($conn) {
		$pdf = new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$pdf->AddPage();		
		$pic_width = 35;
		$pic_height = 52.5;
		$text_width = 40;
		$text_height = 7;
		$teachers = get_all_teachers ($conn);
		$y = 10;	
		$x = 10;
		$lines = 0;
		$num_photo = 4;
		for($i=0;$i<count($teachers);$i+=$num_photo) {	
			for ($j=0; $j<$num_photo; $j++) {		
				if(isset($teachers[$i+$j])) {
					print_teacher ($pdf, $teachers[$i+$j], $x, $y, $pic_width, $pic_height, $text_width, $text_height);	
					$x += $pic_width+15;				
				}
			}
			$y += $pic_height+$text_height*2;	
			$x = 10;
			$lines ++;
			if($lines==4) {
				$pdf->AddPage();
				$y = 10;
				$lines = 0;
			}
		}	
		$pdf->output("photos_teachers_primary.pdf","D");	
	}
	
	function print_teacher ($pdf, $teacher, $x, $y, $pic_width, $pic_height, $text_width, $text_height) {		
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Image(get_photo($teacher["ID"]),$x,$y,$pic_width,$pic_height);
		$pdf->setXY ($x, $y+$pic_height);
		if(strlen($teacher["Last_name"])>25)
			$pdf->SetFont('DejaVu', '', 7);
		$pdf->Cell($text_width,$text_height,$teacher["Last_name"],0,1,'L');
		$pdf->setXY ($x, $y+$pic_height+$text_height);
		$pdf->Cell($text_width,$text_height,$teacher["First_name"],0,1,'L');
	}
?>