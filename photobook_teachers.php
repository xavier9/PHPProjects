<?php
	require_once("includes/startsession.php");
	$page_title = "Photobook Teachers";
	require_once("includes/header.php");
	require_once("includes/show_info.php");

	echo "<div id='content'>";
	$users = get_all_teachers ($conn);
	show_photobook_teachers ($users);
	echo "</div>";
	
	require_once("includes/footer.php");
?>



