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
		$pdf->AddPage();
		print_header_timetable ($conn, $pdf, $_SESSION["timetable_user"]);
		$pdf->output($_SESSION["timetable_user"]["Last_name"]." ".$_SESSION["timetable_user"]["First_name"].".pdf","D");
	}
	
	if(isset($_POST["print"])) {
		require_once ("includes/db_functions.php");
		$conn = get_db_connection();
		$pdf =  new tFPDF();
		$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$pdf->AddFont('DejaVuB','','DejaVuSansCondensed-Bold.ttf',true);
		$pdf->SetFont('DejaVu','',10);
		$pdf->AliasNbPages();		
		foreach($_POST["print_all"] as $value) {
			$pdf->AddPage();
			$user = get_timetable_teacher ($conn, $value);
			print_header_timetable ($conn, $pdf, $user);
		}
		$pdf->output("Timetables.pdf","D");		
	}
	
	$page_title = "Overview Timetables";
	require_once("includes/header.php");
	
	$err ="";
	$selected = false;
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='timetable'>";	
	
	if(isset($_POST["show"]) || isset($_POST["save"]) || isset($_POST["delete"]) || isset($_POST["add"]) || isset($_POST["ready"]) 
		|| isset($_POST["not_ready"]) || isset($_GET["id"]) || isset($_POST["pdf"]) || isset($_POST["conflict"]) || isset($_POST["conflict_all"]) 
		|| isset($_POST["previous"]) || isset($_POST["next"]) || isset($_POST["search"]) || isset($_POST["free_class"])) {
		
		if(isset($_GET["id"])) {
			$_SESSION["timetable_user"] = get_timetable_teacher ($conn, $_GET["id"]);
			if($user = get_user($conn, $_GET["id"])) {
				$_SESSION["timetable_user"]["Last_name"] = $user["Last_name"];
				$_SESSION["timetable_user"]["First_name"] = $user["First_name"];
			}			
		}
		
		if(isset($_POST["show"])) {
			$_SESSION["timetable_user"] = get_timetable_teacher ($conn, $_POST["show"]);
			if($user = get_user($conn, $_POST["show"])) {
				$_SESSION["timetable_user"]["Last_name"] = $user["Last_name"];
				$_SESSION["timetable_user"]["First_name"] = $user["First_name"];
			}							
		}
		
		if(isset($_POST["ready"])) {
			$_SESSION["timetable_user"]["Ready"] = 1;
			update_timetable_user ($conn, $_SESSION["timetable_user"]);
		}
		
		if(isset($_POST["not_ready"])) {
			$_SESSION["timetable_user"]["Ready"] = 0;
			update_timetable_user ($conn, $_SESSION["timetable_user"]);
		}
		
		if(isset($_POST["previous"]))  
			$_SESSION["timetable_user"] = get_previous_user ($conn);
		
		if(isset($_POST["next"]))  
			$_SESSION["timetable_user"] = get_next_user ($conn);
		
		if(isset($_POST["free_class"])) {
			update_timetable_template_teacher_location ($conn, $_SESSION["period"]["ID"], $_POST["free_class"]);
		}
		
		if(isset($_POST["search"])) {
			$_SESSION["period"] = get_timetable_template_teacher_by_day_on_id($conn,$_POST["search"]);
			$days = array ("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");			
			echo "<h3>Free Rooms for ".$days[$_SESSION["period"]["Day"]]." From ".$_SESSION["period"]["Begin"]." till ".$_SESSION["period"]["End"]."</h3>";
			$classes = get_all_searchable_classrooms($conn);
			foreach ($classes as $class) {
				$timetable = get_timetable_template_teacher_by_location($conn, $class["Class"], $_SESSION["period"]["Day"]);
				if(is_free($timetable, $_SESSION["period"]["Day"], $_SESSION["period"]["Begin"], $_SESSION["period"]["End"]))
					echo "<button name='free_class' value='".$class["Class"]."' class='text_button'>".$class["Class"]."</button> ";
			}		
		}
		
		if(isset($_POST["delete"])) {			
			$period = get_timetable_template_teacher_by_day_on_id($conn,$_POST["delete"]);
			if($period["Type"]=="surveill") {
				update_timetable_template_teacher_location($conn, $_POST["delete"], 0);
				update_timetable_surveillance_on_location($conn,0,$period["Day"],$period["Location"]);
			}						
			update_types ($conn, $_POST["Type"]);			
			$line = get_timetable_template_teacher_by_day_on_id ($conn, $_POST["delete"]);
			$data = get_timetable_template_teacher_by_day_and_begin ($conn, $line["Teacher_id"], $line["Day"], $line["Begin"]);
			foreach ($data as $value)			
				update_timetable_template_teacher_type ($conn, $value["ID"], "free");
		}
			
		if(isset($_POST["add"])) {
			update_types ($conn, $_POST["Type"]);
			update_timetable_template_teacher_type ($conn, $_POST["add"], "-");
		}
		
		if(isset($_POST["conflict"]))  
			update_conflicts ($conn, $_POST["conflict"]);
		
		if(isset($_POST["conflict_all"]))  
			update_all_conflicts ($conn, $_SESSION["timetable_user"]["ID"]);
					
		if(isset($_POST["save"])) {			
			delete_timetable_template_teacher_on_type ($conn, $_SESSION["timetable_user"]["ID"], "free");			
			if($_POST["template"]!=0)				
				update_template ($conn, $_SESSION["timetable_user"], get_timetable_template($conn,$_POST["template"]));		
			$_SESSION["timetable_user"]["Forfait"]=$_POST["forfait"];
			$_SESSION["timetable_user"]["Class"]=$_POST["class"];
			$_SESSION["timetable_user"]["Info"]=$_POST["extra"];
			update_timetable_user($conn,$_SESSION["timetable_user"]);		
			if($_POST["day"]!=-1 && $_POST["begin_hour"]!="" && $_POST["begin_minutes"]!="" && $_POST["end_hour"]!="" && $_POST["end_minutes"]!="") 
				$err = add_block ($conn, $_SESSION["timetable_user"], $_POST["day"],$_POST["begin_hour"].":".$_POST["begin_minutes"],$_POST["end_hour"].":".$_POST["end_minutes"]);								
			if(isset($_POST["Type"])) 				
				update_types ($conn, $_POST["Type"]);
			if(isset($_POST["classes"]) && isset($_POST["classes"])) 			
				update_locations ($conn, $_POST["classes"], $_POST["ids"]);		
			if($_POST["class_add"]!="")
				add_class ($conn, $_SESSION["timetable_user"], $_POST["class_add"]);	
		}				
		
		show_teacher ($conn);
		
		if($err!="")
			echo "<table class='table_no_border err'><tr><td class='extra_small'><img src='../images/error.png'></td><td>".$err."</td></tr></table>";
		
		check_conflicts ($conn);
		check_existing_conflicts ($conn);		
		show_conflicts ($conn);
		show_header_timetable ($conn);
		show_timetable ($conn, $_SESSION["timetable_user"]);
		show_footer_timetable($conn, $_SESSION["timetable_user"]);
		
		echo "</div>";			
	}
	else {	
		if(isset($_POST["select_all"])) 
			$selected = true;
		
		if(isset($_POST["select_none"])) 
			$selected = false;
	
		if(isset($_POST["delete_teacher"])) {
			$user = get_timetable_teacher ($conn, $_POST["delete_teacher"]);
			$err = "Are you sure you want to delete ".$user["Last_name"]." ".$user["First_name"]."?</br></br><input class='text_button' type='submit' name='yes' value='Yes'/> <input class='text_button' type='submit' name='no' value='No'/> <input class='text_button' type='hidden' name='user_id' value='".$_POST["delete_teacher"]."'/></br></br>";
		}			
		
		if(isset($_POST["yes"])) {
			remove_timetable_surveillance_on_teacher_id($conn,$_POST["user_id"]);
			delete_timetable_user($conn,$_POST["user_id"]);
		}
		
		if(isset($_POST["teacher_add"])) {
			if(isset($_POST["Last_name"]) && $_POST["Last_name"]!="") {
				$data["ID"]=($_POST["ID"]!=""?$_POST["ID"]:get_id($conn));
				$data["Last_name"]= mysqli_real_escape_string ($conn,strtoupper($_POST["Last_name"]));
				$data["First_name"]=(isset($_POST["First_name"])?mysqli_real_escape_string ($conn,strtoupper(substr($_POST["First_name"],0,1)).substr($_POST["First_name"],1)):"");
				$data["Category"]=$_POST["Category"];
				$data["Ready"]=0;
				$data["Forfait"]=0;
				add_timetable_teacher ($conn, $data);
				$err = "Teacher: ".$data["Last_name"]." ".$data["First_name"]." added!";
			}				
			else				
				$err = "Please fill in at least surname";
		}		
		
		if(isset($_POST["change_timetable_teacher"])) {
			$old_user = get_timetable_teacher ($conn, $_POST["teacher_to_change"]);
			$user = get_user ($conn, $_POST["new_teacher"]);
			$user["Category"]=$_POST["Category"];
			change_timetable_teacher ($conn, $user, $old_user);
			$err = "Timetable: ".$old_user["Last_name"]." ".$old_user["First_name"]." changed to ".$user["Last_name"]." ".$user["First_name"];
		}
		
		if(isset($_POST["add_timetable_teacher"]) || isset($_POST["teacher_add"]) || isset($_POST["change_teacher"]) || isset($_POST["change_timetable_teacher"])) {
			if (isset($_POST["add_timetable_teacher"]) || isset($_POST["teacher_add"])) {
				echo "<div id='content_with_title'>";
				echo "<h3>Add Teacher</h3>";
				if($err!="")
					echo "<div class='err'>".$err."</br></br></div>";
				echo "<table class='table_no_border'>";
				echo "<tr><td>ID:</td><td><input type='text' name='ID' size='30'/></td></tr>"
						."<tr><td>Surname:</td><td><input type='text' name='Last_name' size='30'/></td></tr>"
						."<tr><td>Name:</td><td><input type='text' name='First_name' size='30'/></td></tr>"
						."<tr><td>Category:</td><td><input type='radio' name='Category' value='1'>Detaché  <input type='radio' name='Category' value='0' checked>chargé de cours</td></tr>";
				echo "<tr><td><input class='text_button' type='submit' name='teacher_add' value='Add'/> ";	
				echo "<input class='text_button' type='submit' name='back' value='Back'/></td></tr>";				
				echo "</table>";
				echo "</div>";	
				echo "</div>";					
			}			
					
			if (isset($_POST["change_teacher"]) || isset($_POST["change_timetable_teacher"])) {
				echo "<div id='content_with_title'>";
				echo "<h3>Change Teacher</h3>";
				if($err!="")
					echo "<div class='err'>".$err."</br></br></div>";
				echo "<table class='table_no_border'>";
				echo "<tr><td>Teacher:</td><td><select name='teacher_to_change'>";
				$teachers = get_timetable_teachers ($conn);
				foreach ($teachers as $teacher)
					echo "<option value='".$teacher["ID"]."' >".$teacher["Last_name"]." ".$teacher["First_name"]."</option>";
				echo "</select></tr>";
				
				echo "<tr><td>New Teacher </td><td><select name='new_teacher'>";
				$teachers = get_all_teachers ($conn);
				foreach ($teachers as $teacher)
					echo "<option value='".$teacher["ID"]."' >".$teacher["Last_name"]." ".$teacher["First_name"]."</option>";
				echo "</select></tr>";
				echo "<tr><td>Category:</td><td><input type='radio' name='Category' value='1'>Detaché  <input type='radio' name='Category' value='0' checked>chargé de cours</td></tr>";
				echo "<tr><td><input class='text_button' type='submit' name='change_timetable_teacher' value='Change'/> ";	
				echo "<input class='text_button' type='submit' name='back' value='Back'/></td></tr>";				
				echo "</table>";
				echo "</div>";	
				echo "</div>";				
			}		
		}
		else {	
			$users = get_timetable_teachers ($conn);
			echo "<div id='content'>";
			if(isset($err))
				echo "<div class='err'>".$err."</div>";
			
			echo "<input class='text_button' type='submit' name='add_timetable_teacher' value='Add Teacher'/> ";
			echo "<input class='text_button' type='submit' name='change_teacher' value='Change Teacher'/> ";		
			if($selected)
				echo "<input class='text_button' type='submit' name='select_none' value='Select None'/> ";		
			else
				echo "<input class='text_button' type='submit' name='select_all' value='Select All'/> ";		
			echo "<input class='text_button' type='submit' name='print' value='Print Selected'/> ";			
			echo "<table class='table'>";
			echo "<th>Name</th><th>Class</th><th>Forfait</th><th>Surveillance</th><th>Total</th><th>Submitted</th><th></th><th></th><th></th>";	
			foreach ($users as $user) {	
				$ready_det=$ready_cdc=false;
				if($user["Category"]==1 && calculate_minutes($conn,$user)==1530) 
					$ready_det=true;
				if($user["Ready"]==1) 
					$ready_cdc=true;

				if($teacher = get_user($conn,$user["ID"])) {
					$user["Last_name"] = $teacher["Last_name"];
					$user["First_name"] = $teacher["First_name"];
				}			

				echo "<tr><td>".$user["Last_name"]." ".$user["First_name"]."</td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".$user["Class"]."</td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".$user["Forfait"]."</td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".calculate_minutes_on_type($conn, $user, "surveill")."</td>";	echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".calculate_minutes($conn,$user)."</td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'>".($user["Submitted"]!="0000-00-00"?"Yes":"No")."</td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'><button class='text_button' name='show' value='".$user["ID"]."'>Show</button></td>";
				echo "<td class='center  ".($ready_det?'ready_det':'')."".($ready_cdc?'ready_cdc':'')."'><button class='text_button' name='delete_teacher' value='".$user["ID"]."'>Delete</button></td>";
				echo "<td><input type='checkbox' name='print_all[]' value='".$user["ID"]."' ".($selected?"checked":"")."></td></tr>";
			}
			echo "</table>";
			echo "</div>";
			echo "</div>";
		}
	}
	echo "</form>";	
	
	require_once("includes/footer.php");
	
	function check_conflicts ($conn) {
		$days = array ("Monday", "Tuesday", "Wednesday", "Thursday", "Friday");
		for($i=0; $i<count($days); $i++) {
			$timetable = get_timetable_template_teacher_by_day ($conn, $_SESSION["timetable_user"]["ID"], $i);
			foreach ($timetable as $period) {
				$type = get_template_type($conn,$period["Type"]);
				if($type["Location"]==1 && $type["Type"]!="surveill") {
					$data = get_all_timetables_template_teacher_on_location($conn, $period["Location"]);
					foreach ($data as $value) {
						if($value["Teacher_id"]!=$period["Teacher_id"]) {
							if($value["Day"]==$period["Day"]) {
								if($period["Begin"]<$value["Begin"]) {
									if($period["End"]>$value["Begin"]) {
										$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);
										if(!conflict_exists ($conn, $_SESSION["timetable_user"] ,$teacher, $period["Day"], $period["Begin"], $period["End"], $value["Begin"], $value["End"]))
											add_conflict ($conn, $_SESSION["timetable_user"] ,$teacher, $period["Day"], $period["Begin"], $period["End"], $value["Begin"], $value["End"], $value["Location"]);						}				
								}				
								else {
									if($period["Begin"]<$value["End"]) {
										$teacher = get_timetable_teacher ($conn, $value["Teacher_id"]);
										if(!conflict_exists ($conn, $_SESSION["timetable_user"] ,$teacher, $period["Day"], $period["Begin"], $period["End"], $value["Begin"], $value["End"]))
											add_conflict ($conn, $_SESSION["timetable_user"] ,$teacher, $period["Day"], $period["Begin"], $period["End"], $value["Begin"], $value["End"], $value["Location"]);
									}	
								}
							}
						}		
					}						
				}				
			}
		}
	}
	
	function check_existing_conflicts ($conn) {
		$conflicts = get_conflicts($conn, $_SESSION["timetable_user"]);
		foreach ($conflicts as $value) {
			if(!get_timetable_template_teacher_by_period_on_class ($conn, $value["Teacher_id_1"], $value["Day"], $value["Begin_1"], $value["Class"]))
				delete_conflict($conn, $value["ID"]);
			if(!get_timetable_template_teacher_by_period_on_class ($conn, $value["Teacher_id_2"], $value["Day"], $value["Begin_2"], $value["Class"]))
				delete_conflict($conn, $value["ID"]);
		}
	}
	
	function calculate_minutes ($conn, $user) {
		$min = 0;
		for ($i=0; $i<5; $i++) 
			$min += count_minutes(get_timetable_template_teacher_by_day($conn, $user["ID"], $i));
		return $min + $user["Forfait"];
	}
	
	function count_minutes ($data) {
		$min = 0;
		$begin = 0;
		foreach ($data as $value) 
			if($begin!=$value["Begin"]) {
				$begin=$value["Begin"];
				if($value["Type"]!="free" )
					$min += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+substr($value["End"],3,2)-substr($value["Begin"],3,2));
			}
		return $min;
	}
	
	function calculate_minutes_on_type ($conn, $user, $type) {
		$min = 0;
		for ($i=0; $i<5; $i++) 
			$min += count_minutes_on_type(get_timetable_template_teacher_by_day($conn, $user["ID"], $i),$type);
		return $min;
	}
	
	function count_minutes_on_type ($data, $type) {
		$min = 0;
		$begin = 0;
		foreach ($data as $value) {
			if($value["Type"]==$type) {
				if($begin!=$value["Begin"]) {
					$begin=$value["Begin"];
					$min += ((substr($value["End"],0,2)-substr($value["Begin"],0,2))*60+substr($value["End"],3,2)-substr($value["Begin"],3,2));
				
				}
			}
		}
		return $min;
	}
	
	function update_template ($conn, $user, $template) {
		$template_layout = get_template_layout($conn,$template["ID"]);
		remove_timetable_template_teacher ($conn, $user["ID"]);
		foreach ($template_layout as $value) {
			$data["Teacher_id"] = $user["ID"];
			$data["Day"] = $value["Day"];
			$data["Begin"] = $value["Begin"];
			$data["End"] = $value["End"];
			$data["Type"] = $value["Type"];
			$data["Location"] = 0;	
			add_timetable_template_teacher($conn, $data);
		}
		if($user["Category"]==1)
			$user["Forfait"]=$template["Forfait"];
		else
			$user["Forfait"]=0;
		update_timetable_user($conn,$user);
		remove_timetable_surveillance_on_teacher_id($conn,$user["ID"]);
	}
	
	function show_timetable ($conn, $user) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		for ($i=0; $i<count($days); $i++) 
			show_timetable_day($conn, $days[$i],get_timetable_template_teacher_by_day($conn, $user["ID"], $i));
	}
	
	function show_timetable_day ($conn, $day, $data) {
		$types = get_template_types($conn);
		echo "<table class='timetable'>";
		echo "<th colspan='2'>".$day."</th>";
		$begin = 0;
		foreach ($data as $value) {
			if($begin!=$value["Begin"]) {
				$begin=$value["Begin"];
				echo "<tr>";
				echo "<td class='hour'>".substr($value["Begin"],0,5)."</br>".substr($value["End"],0,5)."</td>";
				echo "<td class='".$value["Type"]."'><select name='Type[]' class='small float_left'>";
				foreach ($types as $type)
					echo "<option value='".$value["ID"].".".$type["Type"]."' ".($type["Type"]==$value["Type"]?"selected":"").">".$type["Type"]."</option>";
				echo "</select>";
				if($value["Type"]=="free")
					echo "<input type='submit' class='button add' name='add' value='".$value["ID"]."'>";
				else {
					echo "<input type='submit' class='button delete' name='delete' value='".$value["ID"]."'>";
					$type = get_template_type($conn,$value["Type"]);
					if($type["Location"]!=0 && $type["Type"]!="surveill") 
						echo "<input type='submit' class='button search' name='search' value='".$value["ID"]."'>";
				}					
				echo "</br>";				
				$type = get_template_type($conn,$value["Type"]);
				if($type["Location"]==0) 
					echo "<input type='text' value='".$value["ID"]."' name='ids[]' class='hidden'/><input style='display:none;' type='text' value='0' size='4' name='classes[]' class='center'>";	
				else
					echo "<input type='text' value='".$value["ID"]."' name='ids[]' class='hidden'/><input type='text' value='".$value["Location"]."' size='4' name='classes[]' class='center'/>";	
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "<tr><td class='hour' colspan='2'>".count_minutes($data)."</td></tr>";
		echo "</table>";
	}
	
	function update_types ($conn, $data) {
		foreach ($data as $value)
			if($value!="") {
				$line = explode (".",$value);
				$period = get_timetable_template_teacher_by_day_on_id($conn,$line[0]);
				if($period["Type"]=="surveill" && $period["Type"]!=$line[1]) {
					update_timetable_template_teacher_location($conn, $line[0], "");
					update_timetable_surveillance_on_location($conn,0,$period["Day"],$period["Location"]);
				}					
				update_timetable_template_teacher_type($conn, $line[0], $line[1]);
			}
	}
	
	function update_locations ($conn, $classes, $ids) {		
		for($i=0; $i<count($ids); $i++) {
			$period = get_timetable_template_teacher_by_day_on_id($conn, $ids[$i]);
			$type = get_template_type($conn,$period["Type"]);
			if($type["Location"]==0) {
				update_timetable_template_teacher_location($conn, $ids[$i], $period["Type"]);
				if($type["Type"]!="ep-")
					update_timetable_template_teacher_course ($conn, $ids[$i], $period["Type"]);
			}
			else {
				$data = get_timetable_template_teacher_by_day_and_begin ($conn, $period["Teacher_id"], $period["Day"], $period["Begin"]);
				foreach ($data as $value)			
					update_timetable_template_teacher_location($conn, $value["ID"], $classes[$i]);
				if($type["Type"]=="surveill")
					update_timetable_template_teacher_course ($conn, $ids[$i], $type["Type"]);
			}
		}
	}
	
	function show_conflicts ($conn) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		$conflicts = get_conflicts($conn, $_SESSION["timetable_user"]);
		echo "<table class='table_no_border err'>";		
		foreach ($conflicts as $value) {
			$teacher = get_timetable_teacher($conn, $value["Teacher_id_2"]);
			$periods = get_timetable_template_teacher_by_day_and_begin ($conn, $value["Teacher_id_2"], $value["Day"], $value["Begin_2"]);
			echo "<tr><td class='extra_small'><img src='../images/error.png'></td>"
			."<td>".$value["Class"]."</td>"
			."<td>".$days[$value["Day"]]."</td>"
			."<td>".$value["Begin_2"]."</td>"
			."<td>".$value["End_2"]."</td>"
			."<td>".$teacher["Last_name"]."</td>"
			."<td>".$periods[0]["Type"]."</td>"
			."<td class='right'><button class='text_button' name='conflict' value='".$value["ID"]."'>Approve</button></td>"
			."</tr>";
			
		}
		if ($conflicts)
			echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class='right'><input type='submit' class='text_button' name='conflict_all' value='Approve All' /></td></tr>";
		echo "</table>";
	}
	
	function show_teacher ($conn) {		
		echo "<table class='table_no_border'>";		
		echo "<tr>"
			."<td><input class='text_button' type='submit' name='previous' value='<'/></td>"
			."<td class='center'><h2>".$_SESSION["timetable_user"]["Last_name"]." ".$_SESSION["timetable_user"]["First_name"]."</h2></td>"
			."<td><input class='text_button float_right' type='submit' name='next' value='>'/></td>"			
			."</tr>"
			."</table>";	
	}
	
	function show_header_timetable ($conn) {	
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");			
		echo "<table class='create_table'>";			
		if($_SESSION["timetable_user"]["Ready"]==1)
			echo "<tr><td class='center'><input type='submit' class='text_button' name='pdf' value='PDF'/> <input type='submit' class='text_button' name='not_ready' value='Not Ready'/></td></tr>";
		else {
			echo "<th></th><th>Extra Block</th><th>Template</th><th>Class</th><th>Description</th><th></th><th></th>";
			echo "<tr>";
			echo "<td><input type='submit' class='text_button' name='save' value='Save'/></td>";	
			echo "<td class='center'>";
			echo "<select name='day'><option value='-1'>-</option>'";
			for($i=0; $i<count($days);$i++)
				echo "<option value='$i'>".$days[$i]."</option>";
			echo "</select> ";		
			$minutes = array ("00","05","10","15","20","25","30","35","40","45","50","55");
			$hours = array ("08","09","10","11","12","13","14","15","16");
			echo "<select name='begin_hour'><option value='-1'>-</option>'";
			for($i=0; $i<count($hours);$i++)
				echo "<option value='".$hours[$i]."'>".$hours[$i]."</option>";
			echo "</select> ";		
			echo "<select name='begin_minutes'><option value='-1'>-</option>'";
			for($i=0; $i<count($minutes);$i++)
				echo "<option value='".$minutes[$i]."'>".$minutes[$i]."</option>";
			echo "</select> ";		
			echo "<select name='end_hour'><option value='-1'>-</option>'";
			for($i=0; $i<count($hours);$i++)
				echo "<option value='".$hours[$i]."'>".$hours[$i]."</option>";
			echo "</select> ";		
			echo "<select name='end_minutes'><option value='-1'>-</option>'";
			for($i=0; $i<count($minutes);$i++)
				echo "<option value='".$minutes[$i]."'>".$minutes[$i]."</option>";
			echo "</select> ";		

			$templates = get_all_templates($conn);
			echo "<td class='center'><select name='template'><option value='0'>-</option>";
			foreach ($templates as $template)
				echo "<option value='".$template["ID"]."'>".$template["Description"]."</option>";
			echo "<option value='-1'>Clear</option>";
			echo "</select> ";	
			echo "<td class='center'><input type='text' name='class_add' size='1'/></td>";		
			echo "<td class='center'><input type='text' size='5' name='class' value='".$_SESSION["timetable_user"]["Class"]."' class='center'/></td>";
			echo "<td><input type='submit' class='text_button' name='pdf' value='PDF'/></td>";
			
			if($_SESSION["timetable_user"]["Category"]==0)
				echo "<td><input type='submit' class='text_button' name='ready' value='Ready'/></td>";			
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function show_footer_timetable ($conn, $user) {
		echo "<table class='create_table_footer'>";
		echo "<tr>"
			."<td rowspan='5' class='textarea'><textarea  rows='9' cols='75' name='extra'>".$user["Info"]."</textarea></td>"			
			."<td>LS:</td>"
			."<td class='minutes'>".calculate_minutes_on_type($conn, $user, "ls")."</td>"
			."<td>Ratt/Swals:</td>"
			."<td class='minutes'>".calculate_minutes_on_type($conn, $user, "jl2")."</td>"
			."</tr>"		
			."<tr><td>LSI:</td>"
			."<td class='minutes'>".calculate_minutes_on_type($conn, $user, "lsi")."</td>"
			."<td>ONL:</td>"
			."<td class='minutes'>".calculate_minutes_on_type($conn, $user, "onl")."</td>"		
			."</tr>"
			."<tr><td>Coordination:</td>"
			."<td class='minutes'>".calculate_minutes_on_type($conn, $user, "coord")."</td>"
			."<td>Forfait:</td>"
			."<td class='minutes''><input type='text' name='forfait' value='".$user["Forfait"]."' size='4' class='right'/></td>"
			."</tr>"
			."<tr><td>Surveillance:</td>"
			."<td class='minutes''>".calculate_minutes_on_type($conn, $user, "surveill")."</td>"
			."<td></td><td></td>"		
			."</tr>"		
			."<tr><td colspan='3'><strong>Total:</strong</td>"
			."<td class='minutes'><strong>".calculate_minutes($conn,$user)."<strong></td>"
			."</tr>"
			."</table>";	
	}
	
	function add_block ($conn, $user, $day, $begin, $end) {
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		if($begin<$end) {
			if(check_period($begin,$end,get_timetable_template_teacher_by_day($conn, $user["ID"], $day))) {
				$data["Teacher_id"] = $user["ID"];
				$data["Day"] = $day;
				$data["Begin"] = $begin;
				$data["End"] = $end;
				$data["Type"] = "-";
				$data["Location"] = 0;	
				add_timetable_template_teacher($conn, $data);
			}
			else
				return "Check timetable on ".$days[$day]." ".$begin." - ".$end;
		}
		else	
			return "End time must be later then start time (".$begin." - ".$end.")";
	}
	
	function add_class ($conn, $user, $class) {
		for($i=0; $i<5; $i++) {
			$timetable = get_timetable_template_teacher_by_day ($conn, $user["ID"], $i);
			foreach ($timetable as $period) {
				$type = get_template_type($conn,$period["Type"]);
				if($type["Location"]==1 && $type["Type"]!="surveill") 
					update_timetable_template_teacher_location($conn, $period["ID"], $class);
			}
		}				
	}
	
	function check_period ($begin, $end, $timetable) {
		$begin.=":00";
		$end.=":00";
		foreach ($timetable as $period) {
			if($begin<$period["Begin"]) {
				if($end>$period["Begin"]) 
					return false;
			}
			else {
				if($begin<$period["End"]) 
					return false;
			}
		}
		return true;
	}
	
	function delete_timetable_user ($conn,$user_id)  {
		remove_timetable_template_teacher ($conn, $user_id);
		delete_timetable_template_teacher_on_id($conn,$user_id);
		delete_conflicts_user($conn,$user_id);
	}
		
	function print_header_timetable ($conn, $pdf, $user) {
		$width_hour = 13;
		$width_course = 25;
		$height_hour = 8;
		$pdf->SetFillColor(220,220,220);
		$pdf->SetFont('DejaVuB', '', 14);
		$pdf->Cell(190,10,$user["Last_name"]." ".$user["First_name"],1,1,'C');
		$pdf->SetFont('DejaVu', '', 10);
		$days = array ("Monday","Tuesday","Wednesday","Thursday","Friday");
		$x = $pdf->GetX();
		$begin_y = $pdf->GetY();
		for ($i=0; $i<count($days); $i++) {
			$data = get_timetable_template_teacher_by_day($conn, $user["ID"], $i);
			print_timetable_day ($conn, $pdf, $x, $begin_y, $days[$i], $data);
			$x+=$width_hour+$width_course;			
			$pdf->SetY($begin_y);			
		}
		
		$pdf->SetAutoPageBreak(0);
		
		$pdf->Rect(10,246,115,39);		
		$pdf->SetXY(10,246);
		$pdf->Multicell(115,5,$user["Info"],0,'L',0);
		
		$height = 13;
		$width_subject = 25;
		$width_counter = 13;
		$x = 130; 
		$y = 246;
		$pdf->SetXY($x,$y);
		$pdf->Cell($width_subject,$height,"LS:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "ls"),1,0,'C');
		$pdf->Cell($width_subject,$height,"Ratt/Swals:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "jl2"),1,0,'C');		
		$y+=$height;
		
		$pdf->SetXY($x,$y);
		$pdf->Cell($width_subject,$height,"Coordination:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "coord"),1,0,'C');
		$pdf->Cell($width_subject,$height,"ONL:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes_on_type($conn, $user, "onl"),1,0,'C');		
		$y+=$height;
		
		$pdf->SetXY($x,$y);
		$pdf->SetFont('Times', 'B', 12);
		$pdf->Cell($width_subject*2+$width_counter,$height,"Total:",1,0,'L');
		$pdf->Cell($width_counter,$height,calculate_minutes($conn,$user),1,0,'C');
		
		$pdf->ln();
		$pdf->Cell(190,8,"* Gen: to decide by the teacher in extranet if it's l1-, mat, ddm, mus or art",0,0,'L');		
	}
	
	function print_timetable_day ($conn, $pdf, $x, $begin_y, $day, $data) {	
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
				
				$output_time = substr($value["Begin"],0,5)."\n".substr($value["End"],0,5);	
				if($value["Type"]=="-")
					$value["Type"]="Gen";								
				
				if($type["Location"]==1 or $value["Type"]=="Gen")
					$output.=$value["Type"]."\n".$value["Location"];
				else
					$output.=$value["Type"]."\n ";
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
	
	function get_next_user ($conn) {
		$users = get_timetable_teachers ($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["timetable_user"]) {
				if($i+1==count($users))
					return $users[0];
				else 
					return $users[$i+1];
			}
		}
	}
	
	function get_previous_user ($conn) {
		$users = get_timetable_teachers ($conn);
		for($i=0; $i<count($users); $i++) {
			if($users[$i]==$_SESSION["timetable_user"]) {
				if($i==0)
					return $users[count($users)-1];
				else 
					return $users[$i-1];
			}
		}
	}
	
	function is_free ($timetable, $day, $begin, $end) {
		foreach ($timetable as $period) {
			if($period["Day"]==$day)
				if($begin<$period["Begin"]) {
					if($end>$period["Begin"]) 
						return false;
				}
			else {
				if($begin<$period["End"]) 
					return false;
			}
		}
		return true;
	}
?>
