<?php
	require_once("includes/startsession.php");	
	
	require_once("includes/pdf/tfpdf.php");
	if(isset($_POST["print_students"])) {
		require_once ("includes/db_functions.php");
		require_once ("includes/functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		print_conseils_class ($conn, $pdf);
		$pdf->output($_SESSION["admin_class"]."-students.pdf","D");
	}
	
	if(isset($_POST["print_overview"])) {
		require_once ("includes/db_functions.php");
		require_once ("includes/functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		print_conseils_overview ($conn, $pdf);
		$pdf->output($_SESSION["admin_class"]."-overview.pdf","D");
	}
	
	$page_title = "Conseils de Classe";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	if(!isset ($_POST["class"]) && !isset ($_POST["id"]) && !isset($_GET["class"]) 
		&& !isset($_POST["print_students"]) && !isset($_POST["print_overview"])) 
		unset($_SESSION["admin_class"]);
	
	if(isset($_POST["class"]))
		$_SESSION["admin_class"] = $_POST["class"];
	
	if(isset ($_POST["id"])) 		
		header ("location: my_conseils_de_classe.php?id=".$_POST["id"]."");
	
	if(isset($_SESSION["admin_class"])) {		
		if(substr($_SESSION["admin_class"],0,1)=='m') 
			$_SESSION["Course"][0] = get_course ($conn, substr($_SESSION["admin_class"],0,2)."gen".substr($_SESSION["admin_class"],2,3));
		else
			$_SESSION["Course"][0] = get_course ($conn, substr($_SESSION["admin_class"],0,2)."mat".substr($_SESSION["admin_class"],2,3));
		$students = get_students_on_class ($conn, $_SESSION["admin_class"]);
		show_list($conn, $students);	
		echo "</br>";
		echo "<input type='submit' name='print_overview' value='Print Class Overview' class='text_button' /> ";
		echo "<input type='submit' name='print_students' value='Print Students' class='text_button' /> ";
	}
	
	if(!isset ($_SESSION["admin_class"])) {		
		$classes = get_all_classes ($conn, "");		
		echo "<table class='table'>";
		echo "<th>Class</th><th>Teacher</th><th>Completed</th><th></th>";
		foreach ($classes as $class) {
			$teacher = get_class_teacher ($conn, $class["Class"]);
			echo "<tr>"			
				."<td>".$class["Class"]."</td>"
				."<td>".$teacher["Last_name"]." ".$teacher["First_name"]."</td>"
				."<td class='center small'>".get_class_completed_conseils($conn, $class)." %</td>"
				."<td class='center small''><button type='submit' name='class' value='".$class["Class"]."' class='text_button'>Show</button></td>"
				."</tr>";
		}	
		echo "</table>";	
	}
	
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function get_class_completed_conseils ($conn, $class) {
		$students = get_students_on_class ($conn, $class["Class"]);
		$counter = 0; 
		foreach ($students as $student)
			$counter += get_conseils_completed($conn, $student);
		return round($counter/count($students),2);
		
	}
	
	function get_conseils_completed ($conn, $student) {
		$options = array ("Doubled","LM","Math","LII","Proposition","Decision","Intensive","Moderate","General","Rattrapage","Swals","Next_Intensive","Next_Moderate","Next_General","Next_Rattrapage","Next_Swals");
		$counter = 0;
		if($conseils = get_conseils ($conn, $student["ID"])) {
			foreach ($options as $value)
				if($conseils[$value]!=0)
					$counter++;
		}
		$divider = count($options);		
		if(substr($student["Class"],0,1)=='m')
			$divider--;
		return round($counter/$divider*100);
	}
	
	function show_list ($conn, $data) {
		echo "<table class='table' >";		
		echo "<th>Surname</th><th>Name</th><th>Completed</th><th></th>";
		foreach ($data as $value) 
			echo "<tr><td>".$value["Last_name"]."</td><td>".$value["First_name"]."</td>"
			."<td class='center small'>".get_conseils_completed ($conn, $value)." %</td>"
			."<td class='center small'><button type='submit' name='id' value='".$value["ID"]
			."'class='text_button'>Show</button></td>"
			."</tr>";		
		echo "</table>";		
	}
	
	function print_conseils_class ($conn, $pdf) {
		$students = get_students_on_class ($conn, $_SESSION["admin_class"]);
		foreach ($students as $student) {
			if($conseils = get_conseils ($conn, $student["ID"])) {
				$pdf->AddPage();
				print_conseils($student, $conseils, $pdf);
			}
		}
	}
	
	function print_conseils ($student, $conseils, $pdf) {
		$img_width = 25;
		$img_height = 37.5;
		$pdf->SetFont('DejaVuB', '', 14);
		$pdf->Cell(190,10,"Conseils de Classe 2014 - 2015",0,1,'C');
		$pdf->ln();
		$pdf->SetFont('DejaVu', '', 10);
		$x = $pdf->getX();
		$pdf->Image(get_photo($student["ID"]),$x,$pdf->getY(),$img_width, $img_height);
		$pdf->setXY($x+$img_width+5,$pdf->getY());
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Cell(30,10,"Full name:",0,0,'L');
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Cell(50,10,$student["Last_name"]." ".$student["First_name"],0,1,'L');
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->setXY($x+$img_width+5,$pdf->getY());
		$pdf->Cell(30,10,"Class:",0,0,'L');
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Cell(50,10,$student["Class"],0,1,'L');
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->setXY($x+$img_width+5,$pdf->getY());
		$pdf->Cell(30,10,"Date of birth:",0,0,'L');
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Cell(50,10,change_date_format($student["Date_of_birth"]),0,1,'L');
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->setXY($x+$img_width+5,$pdf->getY());
		$pdf->Cell(30,10,"Absences:",0,0,'L');
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Cell(50,10,$conseils["Absences"],0,1,'L');
		$pdf->ln();
		
		$y = $pdf->getY();
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Cell(30,10,"Doubled:",0,0,'L');
		$pdf->SetFont('DejaVu', '', 10);
		if ($conseils["Doubled"]==-1)
			$pdf->Cell(50,10,"No",0,1,'L');
		else
			$pdf->Cell(50,10,$conseils["Doubled"],0,1,'L');
		
		$grades = array (0 => "", 1 => "i.a.", 2 => "p.a.", 3 => "s.a.", 4 => "pf.a.", 5 => "Beg.1", 6 => "Beg.2");
		$data = array ("LM","Math","LII","DDM");
		foreach ($data as $value) {
			$pdf->SetFont('DejaVuB', '', 10);
			$pdf->Cell(30,10,$value.":",0,0,'L');
			$pdf->SetFont('DejaVu', '', 10);
			$pdf->Cell(50,10,$grades[$conseils[$value]],0,1,'L');
		}
		
		$grades = array (0 => "", 1 => "Pass", 2 => "Progress", 3 => "Double");
		$data = array ("Proposition","Decision");
		foreach ($data as $value) {
			$pdf->SetFont('DejaVuB', '', 10);
			$pdf->Cell(30,10,$value.":",0,0,'L');
			$pdf->SetFont('DejaVu', '', 10);
			$pdf->Cell(50,10,$grades[$conseils[$value]],0,1,'L');
		}
		
		$ls_title_width = 25;
		$ls_width = 45;
		$x = $pdf->getX()+80;
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->setXY($x, $y);
		$pdf->Cell(($ls_title_width+2*$ls_width),10,"Learning Support",0,1,'C');
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->setXY($x, $pdf->getY());
		$pdf->Cell($ls_title_width,10,"",0,0,'L');
		$pdf->Cell($ls_width,10,"This Year",0,0,'C');
		$pdf->Cell($ls_width,10,"Next Year",0,1,'C');
		$grades = array (0 => "", 1 => "Yes", 2 => "No");
		$grades2 = array (0 => "", 1 => "LM", 2 => "Math", 3 => "LII", 4 => "DDM", 5 => "No");
		$data = array ("Intensive","Moderate","General","Rattrapage","Swals");
		foreach ($data as $value) {
			if($value!="Moderate") {
				$pdf->setXY($x, $pdf->getY());
				$pdf->SetFont('DejaVuB', '', 10);
				$pdf->Cell($ls_title_width,10,$value.":",0,0,'L');
				$pdf->SetFont('DejaVu', '', 10);
				$pdf->Cell($ls_width,10,$grades[$conseils[$value]],0,0,'C');
				$pdf->SetFont('DejaVu', '', 10);
				$pdf->Cell($ls_width,10,$grades[$conseils["Next_".$value]],0,1,'C');
			}
			else {
				$moderate = explode(",", $conseils[$value]);
				$next_moderate = explode(",", $conseils["Next_".$value]);				
				$pdf->setXY($x, $pdf->getY());
				$pdf->SetFont('DejaVuB', '', 10);
				$pdf->Cell($ls_title_width,10,$value.":",0,0,'L');
				$pdf->SetFont('DejaVu', '', 10);
				$output ="";
				foreach ($moderate as $item)
					$output.=$grades2[$item].", ";
				$pdf->Cell($ls_width,10,substr($output, 0, -2) ,0,0,'C');
				$pdf->SetFont('DejaVu', '', 10);				
				$output ="";
				foreach ($next_moderate as $item)
					$output.=$grades2[$item].", ";
				$pdf->Cell($ls_width,10,substr ($output, 0, -2) ,0,1,'C');
			}
		}
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Cell(30,10,"Comments",0,1,'L');
		$pdf->SetFont('DejaVu', '', 10);
		$pdf->Multicell(190,5,$conseils["Comments"],0,'L',0);
		$pdf->setXY(150, 240);
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Cell(50,5,"Signature Director",0,1,'L');
		$pdf->Image("../images/arnedo.jpg",155,250,25,30);
		
	}
	
	function print_conseils_overview ($conn, $pdf) {
		$dates = array (1=>"18-06-2015", 2=>"23-06-2015", 3=>"22-06-2015", 4=>"25-06-2015", 5=>"24-06-2015");
		$pdf->AddPage("L");
		$pdf->SetFont('DejaVu', '', 10);
		$teacher = get_class_teacher ($conn, $_SESSION["admin_class"]);
		$pdf->setFillColor (180,180,180);
		$pdf->Cell(30,10,$dates[substr($_SESSION["admin_class"],1,1)],0,0,'L');
		$pdf->Cell(70,10,$teacher["Last_name"]." ".$teacher["First_name"],0,0,'C');
		$pdf->Cell(21,10,$_SESSION["admin_class"],0,0,'C');
		$pdf->Cell(21,10,"2014-2015",0,0,'C');
		$pdf->Cell(70,10,"This Year",1,0,'C',1);
		$pdf->Cell(70,10,"Next Year",1,1,'C',1);
		
		$name_width = 55;
		$headers = array ("Doubled","LM","Math","LII","DDM","Prop.","Decision","Int.","Moderate","Gen.","Rattr.","Swals","Int.","Moderate","Gen.","Rattr.","Swals");
		$width = array (15,10,10,10,10,16,16,10,30,10,10,10,10,30,10,10,10);
		$students = get_students_on_class ($conn, $_SESSION["admin_class"]);
				
		$pdf->Cell($name_width,10,"Name",1,0,'C',1);
		for($i=0; $i<count($headers); $i++) 
			$pdf->Cell($width[$i],10,$headers[$i],1,0,'C',1);
		$pdf->ln();
		
		foreach ($students as $student) {
			$counter = 0;
			if($conseils = get_conseils ($conn, $student["ID"])) {
				$pdf->Cell($name_width,10,$student["Last_name"]." ".$student["First_name"],1,0,'L');
						
				if ($conseils["Doubled"]==-1)
					$pdf->Cell($width[$counter],10,"No",1,0,'C');
				else
					$pdf->Cell($width[$counter],10,$conseils["Doubled"],1,0,'C');
				$counter++;
				
				$grades = array (0 => "", 1 => "i.a.", 2 => "p.a.", 3 => "s.a.", 4 => "pf.a.", 5 => "Beg.1", 6 => "Beg.2");
				$data = array ("LM","Math","LII","DDM");
				for($i=0; $i<count($data); $i++,$counter++) {
					$pdf->setTextColor (0,0,0);
					if($conseils[$data[$i]]==1 ||$conseils[$data[$i]]==2)
						$pdf->setTextColor (255,0,0);
					if($conseils[$data[$i]]==4)
						$pdf->setTextColor (0,0,255);
					$pdf->Cell($width[$counter],10,$grades[$conseils[$data[$i]]],1,0,'C');
				}
				
				$pdf->setTextColor (0,0,0);
				$grades = array (0 => "", 1 => "Pass", 2 => "Progress", 3 => "Double");
				$data = array ("Proposition","Decision");
				for($i=0; $i<count($data); $i++,$counter++) {
					if($conseils[$data[$i]]==2 || $conseils[$data[$i]]==3)	
						$pdf->setTextColor (255,0,0);
					$pdf->Cell($width[$counter],10,$grades[$conseils[$data[$i]]],1,0,'C');
				}
				
				$grades = array (0 => "", 1 => "Yes", 2 => "No");
				$grades2 = array (0 => "", 1 => "LM", 2 => "Math", 3 => "LII", 4 => "DDM", 5 => "No");
				$data = array ("Intensive","Moderate","General","Rattrapage","Swals","Next_Intensive","Next_Moderate","Next_General","Next_Rattrapage","Next_Swals");
				for($i=0; $i<count($data); $i++,$counter++) 
					if($data[$i]=="Moderate" || $data[$i]=="Next_Moderate") {
						$pdf->setTextColor (0,0,0);
						if($conseils[$data[$i]]!=5)
							$pdf->setTextColor (255,0,0);
						$output = "";
						$moderate = explode(",", $conseils[$data[$i]]);
						foreach ($moderate as $value)
							$output .= $grades2[$value].",";
						$pdf->Cell($width[$counter],10,substr($output,0,-1),1,0,'C');
					}
					else {
						$pdf->setTextColor (0,0,0);
						if($conseils[$data[$i]]==1)
							$pdf->setTextColor (255,0,0);
						$pdf->Cell($width[$counter],10,$grades[$conseils[$data[$i]]],1,0,'C');
					}
				$pdf->setTextColor (0,0,0);
				$pdf->ln();
			}
		}		
		$pdf->ln(8);
		$pdf->SetFont('DejaVuB', '', 12);
		$pdf->Cell(30,10,"Comments",0,1,'L');
		$pdf->SetFont('DejaVu', '', 10);
		foreach ($students as $student) {
			if($conseils = get_conseils ($conn, $student["ID"])) {
				if($conseils["Comments"]!="") {
					$pdf->MultiCell(282,5,$student["Last_name"]." ".$student["First_name"].": ".$conseils["Comments"],0,'L',0);
					$pdf->ln();
				}		
			}
		}
		
		if($pdf->getY()+10>160)
			$pdf->AddPage("L");
		$pdf->setXY(50, $pdf->getY()+10);
		$pdf->SetFont('DejaVuB', '', 10);
		$pdf->Cell(100,10,"Signature Secrétaire de la réunion du Conseil",0,0,'');
		$pdf->Cell(70,10,"Signature Teacher",0,0,'');
		$pdf->Cell(70,10,"Signature Director",0,1,'L');
		$pdf->Image("../images/arnedo.jpg",225,$pdf->getY()+5,25,30);		
	}
?>