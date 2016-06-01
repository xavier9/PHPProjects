<?php 
	require_once("includes/startsession.php");		
	require_once("includes/pdf/tfpdf.php");
	if(isset($_POST["print"])) {
		require_once ("includes/db_functions.php");
		require_once ("includes/functions.php");
		$conn = get_db_connection();
		if($conseils = get_conseils ($conn, $_SESSION["student"]["ID"])) {
			$pdf =  new tFPDF();
			$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
			$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			print_conseils ($_SESSION["student"], $conseils, $pdf);
			$pdf->output($_SESSION["student"]["Last_name"]." ".$_SESSION["student"]["First_name"].".pdf","D");
		}
	}
	
	if(isset($_POST["print_overview"])) {
		require_once ("includes/db_functions.php");
		require_once ("includes/functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->AliasNbPages();
		$class = substr($_SESSION["Course"][0]["Code"],0,2).substr($_SESSION["Course"][0]["Code"],5,3);
		
		print_conseils_overview ($conn, $pdf, $class);
		$pdf->output("overview.pdf","D");
	}
	
	$page_title = "Conseils de classe";
	require_once("includes/header.php");
	date_default_timezone_set('Europe/Brussels');
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."' id='conseils_form'>";
	echo "<div id='content'>";		
		
	if (!isset($_POST["id"]) && !isset($_GET["id"]) && !isset($_POST["previous"]) && !isset($_POST["next"]) && !isset($_POST["save"]) && !isset($_POST["print"]) )
		unset($_SESSION["student"]);
	
	if(isset($_POST["id"])) 
		$_SESSION["student"] = get_student($conn, $_POST["id"]);	
	
	if(isset($_GET["id"])) 
		$_SESSION["student"] = get_student($conn, $_GET["id"]);	
	
	if(isset($_POST["previous"]))  
		$_SESSION["student"] = get_previous_student ($conn);
		
	if(isset($_POST["next"]))  
		$_SESSION["student"] = get_next_student ($conn);	
	
	if(isset($_POST["overview"])) 
		header("location:admin_conseils_de_classe.php?class=".$_SESSION["class"]."");	
	
	if(!isset($_SESSION["student"])) {	
		$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "mat");
		if(empty($courses))
			$courses = get_course_on_teacher_subject ($conn, $_SESSION["user"], "gen");
		$_SESSION["Course"] = $courses;		
		
		$students = array ();
		foreach ($courses as $value) 
			$students = array_merge ($students, get_all_students_course($conn,$value));
		show_list($conn, $students);	
	}	
	
	if (isset ($_POST["save"])) {
		$conseils ["Student_id"] = $_SESSION["student"]["ID"];
		if(isset($_POST["doubled"])) 
			$conseils["Doubled"] = implode (",",$_POST["doubled"]);
		else 
			$conseils["Doubled"] = 0;
		if(isset($_POST["Moderate"])) 
			$conseils["Moderate"] = implode (",",$_POST["Moderate"]);
		else 
			$conseils["Moderate"] = 0;
		if(isset($_POST["Next_Moderate"])) 
			$conseils["Next_Moderate"] = implode (",",$_POST["Next_Moderate"]);	
		else 
			$conseils["Next_Moderate"] = 0;		
		$conseils ["LM"] = (isset($_POST["LM"])?$_POST["LM"]:0);
		$conseils ["Math"] = (isset($_POST["Math"])?$_POST["Math"]:0);
		$conseils ["LII"] = (isset($_POST["LII"])?$_POST["LII"]:0);
		$conseils ["DDM"] = (isset($_POST["DDM"])?$_POST["DDM"]:0);
		$conseils ["Proposition"] = (isset($_POST["Proposition"])?$_POST["Proposition"]:0);
		$conseils ["Decision"] = (isset($_POST["Decision"])?$_POST["Decision"]:0);
		$conseils ["Intensive"] = (isset($_POST["Intensive"])?$_POST["Intensive"]:0);		
		$conseils ["General"] = (isset($_POST["General"])?$_POST["General"]:0);
		$conseils ["Rattrapage"] = (isset($_POST["Rattrapage"])?$_POST["Rattrapage"]:0);
		$conseils ["Swals"] = (isset($_POST["Swals"])?$_POST["Swals"]:0);
		$conseils ["Next_Intensive"] = (isset($_POST["Next_Intensive"])?$_POST["Next_Intensive"]:0);
		$conseils ["Next_General"] = (isset($_POST["Next_General"])?$_POST["Next_General"]:0);
		$conseils ["Next_Rattrapage"] = (isset($_POST["Next_Rattrapage"])?$_POST["Next_Rattrapage"]:0);
		$conseils ["Next_Swals"] = (isset($_POST["Next_Swals"])?$_POST["Next_Swals"]:0);
		$conseils ["Absences"] = (isset($_POST["Absences"])?$_POST["Absences"]:0);
		$conseils ["Comments"] = (isset($_POST["Comments"])?mysqli_real_escape_string($conn,$_POST["Comments"]):"");
		if(!get_conseils($conn,$conseils["Student_id"]))
			add_conseils ($conn, $conseils["Student_id"]);
		save_conseils ($conn, $conseils);		
	}		
	
	if(isset($_SESSION["student"])) {
		$conseils = get_conseils ($conn, $_SESSION["student"]["ID"]);
		echo "<input class='text_button' type='submit' name='previous' value='<'/>"
			."<input class='text_button float_right' type='submit' name='next' value='>' />";		
		show_student_info($_SESSION["student"], $conseils);	
		show_conseils ($conn, $conseils);	
	}		
		
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_list ($conn, $data) {
		$dates = get_conseils_dates ($conn, substr($_SESSION["Course"][0]["Code"],0,2));
		$today = date("Y-m-d");
		echo "<table class='table' >";		
		echo "<th>Surname</th><th>Name</th><th>Completed</th><th></th>";
		foreach ($data as $value) { 
			echo "<tr><td>".$value["Last_name"]."</td><td>".$value["First_name"]."</td>"
			."<td class='center small'>".get_conseils_completed ($conn, $value)." %</td>";
			echo "<td class='center small'>";
			if($dates["Turn_in"]>=$today)				
				echo "<button type='submit' name='id' value='".$value["ID"]."' class='text_button'>Show</button>";
			echo "</td>";
			echo "</tr>";		
		}
		echo "</table>";	
		echo "</br><input type='submit' name='print_overview' value='Print Class Overview' class='text_button' /> ";		
	}
	
	function show_student_info ($student, $conseils) {
		echo "<table class='table_no_border'>";
		echo "<tr><td rowspan='4'><img id='".$student["Last_name"]." ".$student["First_name"]."' src='".get_photo($student["ID"])."' width='120' height='177'/></td>";
		echo "<td class='small'><strong>Full name: </strong></td><td>".$student["Last_name"]." ".$student["First_name"]."</td>";
		echo "<td class='small'><strong>ID: </strong></td><td>".$student["ID"]."</td></tr>";
		echo "<tr><td><strong>Class: </strong></td><td>".$student["Class"]."</td>";		
		echo "<td><strong>Date of birth: </strong></td><td>". change_date_format($student["Date_of_birth"])."</td></tr>";
		$nationalities = explode(",",$student["Nationality"]);		
		echo "<tr><td><strong>Nationality: </strong></td><td>";
		foreach ($nationalities as $nationality)
			echo "<img id='Flag' src='".get_flag($nationality)."' width='20' height='20' />&nbsp";
		echo "<td><strong>Absences:</strong></td>";
		echo "<td><input type='text' name='Absences' value='".$conseils["Absences"]."' size='2' class='center' /></td></tr>";	
		echo "</table>";
	}

	function show_conseils ($conn, $conseils) {
		echo "<table class='table_no_border'>";
		$doubled = explode (",",$conseils["Doubled"]);
		echo "<tr><td>Doubled:</td>"
			."<td><input type='checkbox' name='doubled[]' value='-1' ".(in_array(-1,$doubled)?"checked":"")."/>No</td>"
			."<td><input type='checkbox' name='doubled[]' value='1' ".(in_array(1,$doubled)?"checked":"")."/>Year 1</td>"
			."<td><input type='checkbox' name='doubled[]' value='2' ".(in_array(2,$doubled)?"checked":"")."/>Year 2</td>"
			."<td><input type='checkbox' name='doubled[]' value='3' ".(in_array(3,$doubled)?"checked":"")."/>Year 3</td>"
			."<td><input type='checkbox' name='doubled[]' value='4' ".(in_array(4,$doubled)?"checked":"")."/>Year 4</td>"
			."<td><input type='checkbox' name='doubled[]' value='5' ".(in_array(5,$doubled)?"checked":"")."/>Year 5</td>"
			."</tr>";
		
		$data1 = array("LM","Math","LII","DDM");
		$data2 = array ("Proposition","Decision");
		
		foreach ($data1 as $value) {
			if(substr($_SESSION["student"]["Class"],0,1)!='m' || $value!='LII') {
				echo "<tr><td>".$value.":</td>"
					."<td><input type='radio' name='".$value."' value='1' ".($conseils[$value]==1?"checked":"")." />i.a.</td>"
					."<td><input type='radio' name='".$value."' value='2' ".($conseils[$value]==2?"checked":"")."/>p.a.</td>"
					."<td><input type='radio' name='".$value."' value='3' ".($conseils[$value]==3?"checked":"")."/>s.a.</td>"
					."<td><input type='radio' name='".$value."' value='4' ".($conseils[$value]==4?"checked":"")."/>pf.a.</td>";
				if($value=='LII')
					echo "<td><input type='radio' name='".$value."' value='5' ".($conseils[$value]==5?"checked":"")."/>Beg.1</td>"
						. "<td><input type='radio' name='".$value."' value='6' ".($conseils[$value]==6?"checked":"")."/>Beg.2</td>";
				echo "</tr>";
			}
		}	
				
		foreach ($data2 as $value) 
			echo "<tr><td>".$value.":</td>"
				."<td><input type='radio' name='".$value."' value='1' ".($conseils[$value]==1?"checked":"")."/>Pass</td>"
				."<td><input type='radio' name='".$value."' value='2' ".($conseils[$value]==2?"checked":"")."/>Progress</td>"
				."<td><input type='radio' name='".$value."' value='3' ".($conseils[$value]==3?"checked":"")." />Double</td>"
				."</tr>";
		echo "</table>";
		
		echo "<table class='table_no_border'>"
			."<tr><td><h4>Learning Support</h4></td>"
			."<td><h4>Comments</h4></td></tr>"
			."</table>"; 	
			
		$ls = array ("Intensive","Moderate","General","Rattrapage","Swals");
		echo "<table class='table_conseills float_left'>";
		echo "<th></th><th>This Year</th><th>Next Year</th>";
		
		$moderate = explode (",",$conseils["Moderate"]);
		$next_moderate = explode (",",$conseils["Next_Moderate"]);
		foreach($ls as $value) {
			if($value == "Moderate")
				echo "<tr><td>".$value."</td>"
				."<td><input type='checkbox' name='".$value."[]' value='1' ".(in_array("1",$moderate)?"checked":"")."/>LM "
				."<input type='checkbox' name='".$value."[]' value='2' ".(in_array("2",$moderate)?"checked":"")." />Math "
				."<input type='checkbox' name='".$value."[]' value='3' ".(in_array("3",$moderate)?"checked":"")."/>LII "
				."<input type='checkbox' name='".$value."[]' value='4' ".(in_array("4",$moderate)?"checked":"")."/>DDM "
				."<input type='checkbox' name='".$value."[]' value='5' ".(in_array("5",$moderate)?"checked":"")."/>No</td>"
				."<td><input type='checkbox' name='Next_".$value."[]' value='1' ".(in_array("1",$next_moderate)?"checked":"")."/>LM "
				."<input type='checkbox' name='Next_".$value."[]' value='2' ".(in_array("2",$next_moderate)?"checked":"")." />Math "
				."<input type='checkbox' name='Next_".$value."[]' value='3' ".(in_array("3",$next_moderate)?"checked":"")."/>LII "
				."<input type='checkbox' name='Next_".$value."[]' value='4' ".(in_array("4",$next_moderate)?"checked":"")."/>DDM "
				."<input type='checkbox' name='Next_".$value."[]' value='5' ".(in_array("5",$next_moderate)?"checked":"")."/>No</td>"
				."</tr>";
			else
				echo "<tr><td>".$value."</td>"
				."<td><input type='radio' name='".$value."' value='1' ".($conseils[$value]==1?"checked":"")."/>Yes "
				."<input type='radio' name='".$value."' value='2' ".($conseils[$value]==2?"checked":"")."/>No</td>"
				."<td><input type='radio' name='Next_".$value."' value='1' ".($conseils["Next_".$value]==1?"checked":"")." />Yes "
				."<input type='radio' name='Next_".$value."' value='2' ".($conseils["Next_".$value]==2?"checked":"")."/>No</td>"
				."</tr>";
			
		}
		echo "</table>";
		
		echo "<textarea name='Comments' form='conseils_form' rows='13' cols='35' class='conseils_comments'>".$conseils["Comments"]."</textarea>";
		
		echo "<p>N´oubliez pas que pendant le Conseil de Classe il faut noter le soutien LSG LSM Ratt, Swals prévu pour l´année prochaine. Avec les décisions du Conseil de Classe nous ferons les horaires qui ne changeront pas jusqu´après les vacances de Toussaint.</p>";
		
		echo "<input type='submit' name='save' value='Save' class='text_button' /> ";
		echo "<input type='submit' name='print' value='Print' class='text_button' /> ";
		if(isset($_SESSION["admin_class"]))
			echo "<input type='submit' name='overview' value='Overview' class='text_button' /> ";
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
	
	function get_next_student ($conn) {
		$students = array ();
		foreach ($_SESSION["Course"] as $value) 
			$students = array_merge ($students, get_all_students_course($conn,$value));
		for($i=0; $i<count($students); $i++) {
			if($students[$i]==$_SESSION["student"]) {
				if($i+1==count($students))
					return $students[0];
				else 
					return $students[$i+1];
			}
		}
	}
	
	function get_previous_student ($conn) {
		$students = array ();
		foreach ($_SESSION["Course"] as $value) 
			$students = array_merge ($students, get_all_students_course($conn,$value));
		for($i=0; $i<count($students); $i++) {
			if($students[$i]==$_SESSION["student"]) {
				if($i==0)
					return $students[count($students)-1];
				else 
					return $students[$i-1];
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
		$pdf->Image("../images/arnedo.jpg",150,250,$img_width, $img_height);
		
	}
	
	function print_conseils_overview ($conn, $pdf, $class) {
		$dates = array (1=>"18-06-2015", 2=>"23-06-2015", 3=>"22-06-2015", 4=>"25-06-2015", 5=>"24-06-2015");
		$pdf->AddPage("L");
		$pdf->SetFont('DejaVu', '', 10);
		$teacher = get_class_teacher ($conn, $class);
		$pdf->setFillColor (180,180,180);
		$pdf->Cell(30,10,$dates[substr($class,1,1)],0,0,'L');
		$pdf->Cell(70,10,$teacher["Last_name"]." ".$teacher["First_name"],0,0,'C');
		$pdf->Cell(21,10,$class,0,0,'C');
		$pdf->Cell(21,10,"2014-2015",0,0,'C');
		$pdf->Cell(70,10,"This Year",1,0,'C',1);
		$pdf->Cell(70,10,"Next Year",1,1,'C',1);
		
		$name_width = 55;
		$headers = array ("Doubled","LM","Math","LII","DDM","Prop.","Decision","Int.","Moderate","Gen.","Rattr.","Swals","Int.","Moderate","Gen.","Rattr.","Swals");
		$width = array (15,10,10,10,10,16,16,10,30,10,10,10,10,30,10,10,10);
		$students = get_students_on_class ($conn, $class);
				
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
	}
?>