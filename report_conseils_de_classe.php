<?php
	require_once("includes/startsession.php");	
	$page_title = "Report: Conseils de Classe";
	require_once("includes/header.php");
	require_once("includes/PHPExcel.php");
	require_once("includes/PHPExcel/Writer/Excel2007.php");	
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";
	
	if(isset($_POST["export"]) || isset($_POST["show"])) {
		$options = array ();
		$query = "SELECT * FROM b2_students WHERE ID in (SELECT Student_id FROM b2_conseils WHERE ";
		if(isset($_POST["Proposition"])) {
			foreach ($_POST["Proposition"] as $value)
				$query.="Proposition = '".$value."' OR ";
			array_push ($options, "Proposition");
		}
		if(isset($_POST["Decision"])) {
			foreach ($_POST["Decision"] as $value)
				$query.="Decision = '".$value."' OR ";
			array_push ($options, "Decision");
		}
		if(isset($_POST["Next_Intensive"])) {
			$query.="Next_Intensive = '".$_POST["Next_Intensive"]."' OR ";
			array_push ($options, "Next_Intensive");
		}
		if(isset($_POST["Next_General"])){
			$query.="Next_General = '".$_POST["Next_General"]."' OR ";
			array_push ($options, "Next_General");
		}
		if(isset($_POST["Next_Rattrapage"])){
			$query.="Next_Rattrapage = '".$_POST["Next_Rattrapage"]."' OR ";
			array_push ($options, "Next_Rattrapage");
		}
		if(isset($_POST["Next_Swals"])){
			$query.="Next_Swals = '".$_POST["Next_Swals"]."' OR ";
			array_push ($options, "Next_Swals");
		}
		if(isset($_POST["Next_Moderate"])) {
			foreach ($_POST["Next_Moderate"] as $value)
				$query.="Next_Moderate = '".$value."' OR ";
			array_push ($options, "Next_Moderate");
		}				
		$query = substr($query,0,-4).") "; 
		if(isset($_POST["Year"])) {
			$query .= "AND (";
			foreach ($_POST["Year"] as $year) 
				$query.="Class LIKE '".$year."%' OR ";
		}
		$query = substr($query,0,-4).") ORDER BY Last_name, First_name";
		$data = get_values(mysqli_query($conn,$query));
		if(isset($_POST["show"]))
			show ($conn,$data,$options);
		if(isset($_POST["export"]))
			export ($conn,$data,$options);		
	}
	
	else {	
		echo "<table class='table_no_bordder'>";
		$years = get_all_years($conn, "");
		echo "<tr><td>Select a level:</td>";
		foreach ($years as $year)
			echo "<td><input type='checkbox' name='Year[]' value='".$year["Class"]."' checked/>".$year["Class"]."</td>";
		echo "</tr>";
		
		echo "<tr><td>Proposition:</td>"
			."<td colspan='2'><input type='checkbox' name='Proposition[]' value='2' checked/>Progress</td>"
			."<td colspan='2'><input type='checkbox' name='Proposition[]' value='3' checked/>Double</td>"
			."</tr>";
			
		echo "<tr><td>Decision:</td>"
			."<td colspan='2'><input type='checkbox' name='Decision[]' value='2' checked/>Progress</td>"
			."<td colspan='2'><input type='checkbox' name='Decision[]' value='3' checked/>Double</td>"
			."</tr>";

		echo "<tr><td>LS Intensive:</td>"
			."<td><input type='checkbox' name='Next_Intensive' value='1' checked/></td>"
			."</tr>";
		
		echo "<tr><td>LS Moderate:</td>"
			."<td><input type='checkbox' name='Next_Moderate[]' value='1' checked/>LM</td>"
			."<td><input type='checkbox' name='Next_Moderate[]' value='2' checked/>Math</td>"
			."<td><input type='checkbox' name='Next_Moderate[]' value='3' checked/>LII</td>"
			."<td><input type='checkbox' name='Next_Moderate[]' value='4' checked/>DDM</td>"
			."</tr>";
			
		echo "<tr><td>LS General:</td>"
			."<td><input type='checkbox' name='Next_General' value='1' checked/></td>"
			."</tr>";
			
		echo "<tr><td>Rattrapage:</td>"
			."<td><input type='checkbox' name='Next_Rattrapage' value='1' checked/></td>"
			."</tr>";

		echo "<tr><td>Swals:</td>"
			."<td><input type='checkbox' name='Next_Swals' value='1' checked/></td>"
			."</tr>";	
		echo "</table>";		
		
		echo "<input type='submit' name='show' value='Show' class='text_button' /> ";
		echo "<input type='submit' name='export' value='Export' class='text_button' />";
		
	}
	
	echo "</div>";	
	echo "</form>";
		
	require_once("includes/footer.php");
	
	function show ($conn, $data, $options) {
		$headers = array ("Last_name"=>"Surname", "First_name"=>"Name", "Class"=>"Class", "Decision"=>"Decision", "Proposition"=>"Prop.", "Next_General"=>"LSG", "Next_Intensive"=>"LSI","Next_Rattrapage"=>"Rattrapage","Next_Swals"=>"Swals","Level"=>"Level","Section"=>"Section");
		$options2 = array ("Last_name", "First_name", "Level", "Section");
		$next_years = array ("m1"=>"m2", "m2"=>"p1", "p1"=>"p2", "p2"=>"p3", "p3"=>"p4", "p4"=>"p5", "p5"=>"s1");
		
		echo "<table class='table'>";
		foreach ($options2 as $option)
			echo "<th>".$headers[$option]."</th>";
		foreach ($options as $option) {
			if($option=="Next_Moderate")
				echo "<th>LSM LM</th><th>LSM Math</th><th>LSM LII</th><th>LSM DDM</th>";
			else
				echo "<th>".$headers[$option]."</th>";
		}
		foreach ($data as $value) {
			echo "<tr>";
			$conseils = get_conseils ($conn, $value["ID"]);
			foreach($options2 as $option) {
				if($option=="Level") {
					if($conseils["Decision"]==3)
						echo "<td class='center'>".substr($value["Class"],0,2)."</td>";
					else
						echo "<td class='center'>".$next_years[substr($value["Class"],0,2)]."</td>";
				}
				else {
					if($option=="Section")
						echo "<td class='center'>".substr($value["Class"],2,2)."</td>";
					else
						echo "<td>".$value[$option]."</td>";
				}				
			}
			foreach($options as $option) {
				switch($option){
					case "Decision":
						echo "<td class='center'>";
						if($conseils[$option]==3)
							echo "Double";
						if($conseils[$option]==2)
							echo "Progress";
						echo "</td>";
						break;
					case "Proposition":
						echo "<td class='center'>";
						if($conseils[$option]==3)
							echo "Double";
						if($conseils[$option]==2)
							echo "Progress";
						echo "</td>";
						break;
					case "Next_General":
						echo "<td class='center'>";
						if($conseils[$option]==1)
							echo "Yes";
						echo "</td>";
						break;
					case "Next_Intensive":
						echo "<td class='center'>";
						if($conseils[$option]==1)
							echo "Yes";
						echo "</td>";
						break;
					case "Next_Rattrapage":
						echo "<td class='center'>";
						if($conseils[$option]==1)
							echo "Yes";
						echo "</td>";
						break;
					case "Next_Swals":
						echo "<td class='center'>";
						if($conseils[$option]==1)
							echo "Yes";
						echo "</td>";
						break;
					case "Next_Moderate":
						$lsm = explode(",",$conseils[$option]);
						for($i=1; $i<=4; $i++) {
							echo "<td class='center'>";
							if(in_array($i,$lsm))
								echo "Yes";
							echo "</td>";
						}
						break;
						
						
				}
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function export ($conn, $data, $options) {
		date_default_timezone_set('europe/paris');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		$headers = array ("Last_name"=>"Surname", "First_name"=>"Name", "Class"=>"Class", "Decision"=>"Decision", "Proposition"=>"Proposition", "Next_General"=>"LSG", "Next_Intensive"=>"LSI","Next_Rattrapage"=>"Rattrapage","Next_Swals"=>"Swals","Level"=>"Level","Section"=>"Section");
		$next_years = array ("m1"=>"m2", "m2"=>"p1", "p1"=>"p2", "p2"=>"p3", "p3"=>"p4", "p4"=>"p5", "p5"=>"s1");
		
		$options2 = array ("Last_name", "First_name", "Level", "Section");
		
		for($i=0;$i<count($options2);$i++)
			$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$i), mb_convert_encoding($headers[$options2[$i]],'Windows-1252')); 				
		$counter=0;
		for($i=0;$i<count($options);$i++,$counter++) {
			if($options[$i]=="Next_Moderate") {
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$counter+4), mb_convert_encoding("LSM LM",'Windows-1252')); 	
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$counter+5), mb_convert_encoding("LSM Math",'Windows-1252')); 	
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$counter+6), mb_convert_encoding("LSM LII",'Windows-1252')); 	
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$counter+7), mb_convert_encoding("LSM DDM",'Windows-1252'));
				$counter+=4;
			}
			else
				$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field(0,$counter+4), mb_convert_encoding($headers[$options[$i]],'Windows-1252')); 	
				
		}		
		
		for($i=0;$i<count($data);$i++) {
			$conseils = get_conseils ($conn, $data[$i]["ID"]);
			for($j=0;$j<count($options2);$j++) {
				if($options2[$j]=="Level") {
					$level = $next_years[substr($data[$i]["Class"],0,2)];
					if($conseils["Decision"]==3)
						$level = substr($data[$i]["Class"],0,2);
					$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j), mb_convert_encoding($level,'Windows-1252'));
					$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+1), mb_convert_encoding(substr($data[$i]["Class"],2,2),'Windows-1252'));
					$j++;					
				}					
				else
					$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j), mb_convert_encoding($data[$i][$options2[$j]],'Windows-1252')); 	
			}
			for($k=0;$k<count($options);$k++)  {
				switch($options[$k]){
					case "Decision":
						$cell = "";
						if($conseils[$options[$k]]==3)
							$cell =  "Double";
						if($conseils[$options[$k]]==2)
							$cell =  "Progress";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Proposition":
						$cell = "";
						if($conseils[$options[$k]]==3)
							$cell = "Double";
						if($conseils[$options[$k]]==2)
							$cell = "Progress";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Next_General":
						$cell = "";
						if($conseils[$options[$k]]==1)
							$cell = "Yes";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Next_Intensive":
						$cell = "";
						if($conseils[$options[$k]]==1)
							$cell = "Yes";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Next_Rattrapage":
						$cell = "";
						if($conseils[$options[$k]]==1)
							$cell = "Yes";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Next_Swals":
						$cell = "";
						if($conseils[$options[$k]]==1)
							$cell = "Yes";
						$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						break;
					case "Next_Moderate":
						$lsm = explode(",",$conseils[$options[$k]]);
						for($l=1; $l<=4; $l++) {
							$cell = "";
							if(in_array($l,$lsm))
								$cell = "Yes";
							$objPHPExcel->getActiveSheet()->SetCellValue(get_excel_field($i+1,$j+$k), $cell);
						}
						break;					 		
				}
			}
		}
		
		$file_name = "export_files/report_conseils_de_classe.xlsx";
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