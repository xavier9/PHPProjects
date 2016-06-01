<?php
	require_once('includes/pdf/tfpdf.php');	
	require_once('includes/db_functions.php');	
	
	$conn = get_db_connection();
	mysqli_set_charset($conn,"utf8");	
	$pdf = new tFPDF("L", "mm", "A4");
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->SetFont('DejaVu','',10);
	$pdf->AliasNbPages();
	$print = array ("l2-","mat","jl2","onl","rpr","ror","rca","mor","rju","gen","ris","eur");
			
	print_classes ($conn, $pdf, $print);
			
	function print_classes ($conn, $pdf, $print) {
		$classes = get_all_classrooms ($conn);
		foreach ($classes as $class) {
			$courses = get_all_courses_on_class ($conn, $class); 
			print_courses ($conn,$pdf,$class,$courses,$print);		
			print_ls_intensive_courses ($conn,$pdf,$class,$courses);				
		}	
		$pdf->output("List_students_by_class.pdf","D");
	}	
	
	function print_courses ($conn,$pdf,$class,$courses, $print) {
		foreach ($courses as $course) {
			if(in_array(substr($course["Course"],2,3),$print)) 
				print_page($conn,$pdf,$course,$class);
		}
	}
	
	function print_ls_intensive_courses ($conn,$pdf,$class,$courses) {
		$new_page = false;
		foreach ($courses as $course) {
			if($student = get_student($conn, $course["Course"])) {	
				if(!$new_page) {
					$pdf->AddPage();	
					$new_page = true;
				}
				$temp = get_teacher_on_ls ($conn, $student["ID"],$class);
				$teacher = get_teacher($conn,$temp["Teacher_id"]);	
				$pdf->SetFont('DejaVuB','',12);
				$pdf->Cell(100,5,"Class: ".$class["Class"],0,0,'L');				
				$pdf->Cell(80,5,"Course: LS Intensive",0,0,'L');
				$pdf->Cell(95,5,"Teacher: ".normalize($teacher["Last_name"]." ".$teacher["First_name"]),0,1,'L');
				$pdf->Ln(5);
				$pdf->SetFont('DejaVu','',10);
				$pdf->Cell(100,5, normalize($student["Last_name"]." ".$student["First_name"]),0,0,"L");
				$phones = explode (",",$student["Phone"]);
				foreach($phones as $phone)
					$pdf->Cell(40,5,$phone,0,0,"L");
				$pdf->Ln(5);		
				$pdf->Ln(5);				
			}				
		}
	}

	function get_all_courses_on_class ($conn, $class) {
		$query = "SELECT DISTINCT Course FROM `b2_timetable_template_teacher` WHERE Location = '".$class["Class"]."' ORDER BY Course";
		return get_values(mysqli_query ($conn,$query));
	}
	
	function get_teacher_course_on_course_id ($conn, $course_id) {
		$query = "SELECT * FROM b2_teacher_course WHERE Course_id = '".$course_id."'";
		$data = mysqli_query($conn,$query);
		return mysqli_fetch_assoc($data);
	}
	
	function get_teacher ($conn, $id) {
		$query = "SELECT * FROM b2_teachers WHERE ID = '$id'";
		$data = mysqli_query($conn,$query);
		return mysqli_fetch_assoc($data);
	}
	
	function get_students_on_teacher_course ($conn, $teacher_course) {
		$query = "SELECT * FROM b2_students WHERE ID in (SELECT Student_id FROM b2_student_teacher_course WHERE Teacher_course_id = '".$teacher_course["ID"]."')";
		return get_values(mysqli_query($conn,$query));
	}
	
	function print_page ($conn, $pdf, $course, $class) {
		$pdf->AddPage();	
		$teacher_course = get_teacher_course_on_course_id($conn,$course["Course"]);
		$teacher = get_teacher ($conn, $teacher_course["Teacher_id"]);
		$students = get_students_on_teacher_course($conn, $teacher_course);
		$pdf->SetFont('DejaVuB','',12);
		if(substr($course["Course"],2,3)=="eur" ) 
			$pdf->Cell(80,5,"Course: ".$course["Course"],0,1,'L');
		else {
			$pdf->Cell(100,5,"Class: ".$class["Class"],0,0,'L');	
			if(substr($course["Course"],2,3)=="mat")
				$pdf->Cell(80,5,"Course: ".substr($course["Course"],0,2).substr($course["Course"],5,3),0,0,'L');
			else
				$pdf->Cell(80,5,"Course: ".$course["Course"],0,0,'L');
			$pdf->Cell(95,5,"Teacher: ".normalize($teacher["Last_name"]." ".$teacher["First_name"]),0,1,'L');
		}
		$pdf->Ln(5);
		$pdf->SetFont('DejaVu','',10);
		foreach ($students as $student) {
			$pdf->Cell(100,5, normalize($student["Last_name"]." ".$student["First_name"]),0,0,"L");
			$phones = explode (",",$student["Phone"]);
			foreach($phones as $phone)
				$pdf->Cell(40,5,$phone,0,0,"L");
			$pdf->Ln(5);
		}
	}
	
	function get_teacher_on_ls ($conn, $course_id, $class) {
		$query = "SELECT DISTINCT Teacher_id FROM b2_timetable_template_teacher WHERE Course = '".$course_id."' AND Location='".$class["Class"]."'";
		$data = mysqli_query($conn,$query);
		return mysqli_fetch_assoc($data);
	}
	
	function normalize ($string) {
		$table = array(
			"'"=>'`', 
			"&#278"=>"E", 
			"&#363"=>"u",
			"&#362"=>"U",
			"&#268"=>"C",
			"&#275"=>"e",
			"&#486"=>"G",
			"&#274"=>"E",
			"&#256"=>"A",
			"&#279"=>"e",
			"&#299"=>"i",
			"&#298"=>"I",
			"&#257"=>"A",
			
		);			
		return strtr($string, $table);
	}
?>