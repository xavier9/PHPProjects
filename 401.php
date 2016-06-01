<?php
	require_once("includes/startsession.php");	
	$page_title = "Not Authorized!";
	require_once("includes/header.php");
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content'>";	
	
	echo "<div class='err'>" 
		."<strong>You are not authorized to view this page!"
		."</br></br>"
		."Please contact your system administrator!</strong>"
		."</div>";
		
	echo "</div>";
	echo "</form>";
	
	require_once("includes/footer.php");
?>