<?php

class AbsenceDB {

	public static function printAbsencesToPDF($date){
			$reasons = self::getAllReasons();
			$absences= self::getAbsencesByDate($date);
			$header = array('Name', 'Reason', 'Hours','Note');
			
			require_once("includes/pdf/tfpdf.php");
			$pdf =  new tFPDF();
			$pdf->SetFont('Arial','',12);
			$pdf->AddPage();
			$pdf->SetFont('','B');
    		$w = array(75 , 36, 20, 60);
    		$pdf->SetFillColor(240,240,240);
		    $added = 0;
			foreach ($w as $val) {
				$added = $added + $val;
			}
		    $pdf->Cell($added,7,"Absent teachers on ".$date,1,0,'C',true);
		    $pdf->Ln();
		    for($i=0;$i<count($header);$i++){
		        $pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
			}
		    $pdf->Ln();
			$pdf->SetFont('');
    		$pdf->SetFillColor(245,245,245);
		    $fill = false;
			$pagecounter = 0;
		    foreach($absences as $absence)
		    {
		    	$pagecounter ++;
				if ($pagecounter == 42){
					
    				$pdf->Cell(array_sum($w),0,'','T');
					
					$pagecounter = 0;
					$pdf->AddPage();
					
					
    				$pdf->SetFillColor(240,240,240);		
					$pdf->SetFont('','B');
				    $pdf->Cell($added,7,"Absent teachers on ".$date,1,0,'C',true);
				    $pdf->Ln();
				    for($i=0;$i<count($header);$i++){
				        $pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
					}
				    $pdf->Ln();
					$pdf->SetFont('');
		    		$pdf->SetFillColor(245,245,245);
				}
		    	$absencereason = '';
				$reasons = AbsenceDB::getAllReasons();
				foreach ($reasons as $reason){
					if ($reason['reason_ID']==$absence['reason_ID']){$absencereason=$reason;}
				}
		    	$hours = $absence['hours'];
				$note =$absence['note'];
				if ($hours=="123456789"){$hours=" ";}
		        $pdf->Cell($w[0],6,$absence['title']." ".utf8_decode($absence['name']),'LR',0,'L',$fill);
		        $pdf->Cell($w[1],6,$absencereason['abbreviation']." ".$absencereason['reason'],'LR',0,'L',$fill);
		        $pdf->Cell($w[2],6,$hours,'LR',0,'C',$fill);
				if (strlen($note)<24){
					$pdf->Cell($w[3],6,$absence['note'],'LR',0,'L',$fill);
				}else{
					$words = explode(" ",$note);
					$counter =0;
					$firsthalf= "";
					$secondhalf= "";
					while ($counter<count($words)&&((strlen($words[$counter])+strlen($firsthalf))<24)){
						$firsthalf = $firsthalf." ".$words[$counter];
						$counter++;
					}
					for ($i=$counter; $i < count($words); $i++) { 
						$secondhalf = $secondhalf." ".$words[$i];
					}					
					
					$pdf->Cell($w[3],6,$firsthalf,'LR',0,'L',$fill);
		        	$pdf->Ln(); 
		        	$pdf->Cell($w[0],6," ",'LR',0,'L',$fill);
			        $pdf->Cell($w[1],6,"",'LR',0,'L',$fill);
			        $pdf->Cell($w[2],6," ",'LR',0,'C',$fill);
					$pdf->Cell($w[3],6,$secondhalf,'LR',0,'L',$fill);
				}
		        
		        $pdf->Ln();
		        $fill = !$fill;
		    }
    		$pdf->Cell(array_sum($w),0,'','T');
			$pdf->output("Absent Teachers ".$date.".pdf","D");
			
	}

    public static function getById($id) {
    	$SqlQuery = "SELECT * FROM b2_teacher_absences WHERE absence_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
	public static function changeCodeDatetoDBDate($date){
		 //FROM 27/04/2015
		 //TO 2015-04-27
		if (self::isDBDate($date)){return $date;}
    	$date = explode ('/',$date);
    	$date = array_reverse($date);
    	$date = implode('-', $date);
		return $date;
		
	}
	public static function changeDBDatetoCodeDate($date){
		 //FROM 27/04/2015
		 //TO 2015-04-27
		if (self::isCodeDate($date)){return $date;}
    	$date = explode ('-',$date);
    	$date = array_reverse($date);
    	$date = implode('/', $date);
		return $date;
	}
	public static function isCodeDate($date){
		return (strpos($date,"/")==2&&strpos(substr($date,3),"/")==2);
		
	}
	public static function isDBDate($date){
		return !self::isCodeDate($date);
	}

    public static function insert($absence) {
    	$date=$absence['date'];
    	$date = self::changeCodeDatetoDBDate($date);
		$SqlQuery = "INSERT INTO b2_teacher_absences (`title`, `name`, `hours`, `date`, `note`, `reason_ID`) VALUES ('".$absence['title']."','".$absence['name']."','".$absence['hours']."','".$date."','".$absence['note']."','".$absence['reason_ID']."')";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
    public static function update($absence) {
    	self::deleteById($absence['absence_ID']);
		
    	$date=$absence['date'];
    	$date = self::changeCodeDatetoDBDate($date);
		$SqlQuery = "INSERT INTO b2_teacher_absences (`absence_ID`, `title`, `name`, `hours`, `date`, `note`, `reason_ID`) VALUES ('".$absence['absence_ID']."','".$absence['title']."','".$absence['name']."','".$absence['hours']."','".$date."','".$absence['note']."','".$absence['reason_ID']."')";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
    public static function getCurrentAbsences(){
		$SqlQuery = "SELECT * FROM `b2_teacher_absences` WHERE `date` = CURDATE() ORDER BY name";
		//print_r(get_db_connection ());
		$result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAbsencesByDate($date){
    	// turns date from d/m/Y to Y-m-d
    	// the program uses d/m/Y, the db uses Y-m-d
    	$date = self::changeCodeDatetoDBDate($date);
		$SqlQuery = "SELECT * FROM `b2_teacher_absences` WHERE `date` = '".$date."' ORDER BY name";
		$result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
	
    public static function deleteById($id) {
    	$SqlQuery = "DELETE FROM b2_teacher_absences WHERE absence_ID= '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function delete($absence) {
        self::deleteById($absence['absence_ID']);
    }
	
	
    public static function getReasonById($id) {
    	$SqlQuery = "SELECT * FROM b2_teacher_absence_reasons WHERE reason_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
    public static function getAllReasons() {
    	$SqlQuery = "SELECT * FROM b2_teacher_absence_reasons;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }

	
    public static function getReasonNameById($id) {
    	$SqlQuery = "SELECT * FROM b2_teacher_absence_reasons WHERE reason_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
			$temp = $result[0];
        	return $temp['reason'];
		}
    }


}



