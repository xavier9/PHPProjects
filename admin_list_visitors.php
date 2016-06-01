<?php
	require_once("includes/startsession.php");	
	$page_title = "List Visitors";
	require_once("includes/header.php");
	
	$_SESSION["date"]=date("Y-m-d");
	
	if(isset($_POST["In"]))
		check_visitor_in ($conn, $_POST["In"], date("H:i:s"));
		
	if(isset($_POST["Out"]))
		check_visitor_out ($conn, $_POST["Out"], date("H:i:s"));
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";					

	echo "<table class='table_no_border'>";
	echo "<tr><td class='small'><input type='submit' class='text_button' name='previous' value='<'/></td>"
		."<td class='center'>".change_date_format($_SESSION["date"])."</td>"
		."<td class='small right'><input type='submit' class='text_button' name='next' value='>'/></td>"
		."</tr>";
	echo "</table>";
	show_list_vistors($conn);

	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
	
	function show_list_vistors ($conn){
		$visitors = get_visitors ($conn, $_SESSION["date"]);
		echo "<table class='table'>";
		echo "<th>Contact</th><th>Time</th><th></th><th></th>";
		foreach ($visitors as $visitor){
			$contact = get_contact ($conn, $visitor["Contact_id"]);
			echo "<tr><td>".$contact["Last_name"]." ".$contact["First_name"]."</td>"
				."<td class='small center'>".substr($visitor["Begin"],0,5)." - ".substr($visitor["End"],0,5)."</td>";
			if($visitor["Arrive"]!="00:00:00")
				echo "<td class='small center'>".substr($visitor["Arrive"],0,5)."</td>";
			else
				echo "<td class='small center'><button class='text_button' name='In' value='".$visitor["ID"]."'>In</button></td>";
			if($visitor["Exits"]!="00:00:00")
				echo "<td class='small center'>".substr($visitor["Exits"],0,5)."</td>";
			else
				echo "<td class='small center'><button class='text_button' name='Out' value='".$visitor["ID"]."'>Out</button></td>";		
			echo "</tr>";
		}
		echo "</table>";
	}
?>