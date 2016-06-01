<?php	
	require_once("includes/startsession.php");	
	$page_title = "Welcome to Extranet Primary EEB2";
	require_once("includes/header.php");
	
	if(isset($_POST["vote"])) {
		if($_SESSION["user"]["ID"]!=0) {
			if(get_poll ($conn, $_SESSION["user"]["ID"]))
				update_poll ($conn, $_SESSION["user"]["ID"], $_POST["vote"]);
			else
				add_poll ($conn, $_SESSION["user"]["ID"], $_POST["vote"]);
		}
	}
	
	echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
	echo "<div id='content_with_title'>";
	
	// show_poll ($conn);
	
	echo "</div>";
	echo "</form>";

	require_once("includes/footer.php");
	
	function show_poll ($conn) {
		$poll = get_poll ($conn, $_SESSION["user"]["ID"]);
		echo "<h2>Question:</h2>";
		echo "<p>Do you prefer the Carnet Orale before or after the holidays? Please vote. </p>";		
		echo "<button class='".((isset($poll["Choice"]) && $poll["Choice"]==0)?"on_button":"off_button")."' name='vote' value='0'>Before the Holidays</button> ";
		echo "<button class='".((isset($poll["Choice"]) && $poll["Choice"]==1)?"on_button":"off_button")."' name='vote' value='1'>After the Holidays</button> ";
		
	}
?>
