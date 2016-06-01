<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="css/extranet_primary.css?v=1.1" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php echo "<title>".(isset($page_title)?$page_title:"")."</title>";?>
	<script src="includes/jscript/jquery-1.11.2.min.js"></script>
	<script src="includes/jscript/loading.js"></script>
	<script>
		$(function(){
			$('.Picture').imgPreload()
		})
	</script>
</head>
<body>
<?php
require_once ("db_functions.php");
$conn = get_db_connection();
require_once ("functions.php");
date_default_timezone_set("Europe/Brussels");

if(isset($_POST["reset"]))	{
	$_SESSION["user"]=$_SESSION["admin_user"];
	unset($_SESSION["admin_user"]);
	header("Location: user_rights.php");
}

if(isset($_POST["logout"]))
	header("Location: logout.php");

if(isset($page_title)) {
?>
<div id="page">
	<div id="header">
		<div id="logo">
			<a href="home.php"><img alt="Logo EEB2" src="../images/Logo EEB2.jpg" height='75' width='200'/></a>
		</div>
		<div id="title">
			<h1>Extranet Primary</h1>
		</div>
		<?php
		if (isset($_SESSION["user"])) {
		?>
		<div id="user">
			<?php
			echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
			echo "<a href='change_password.php'><img id='user_img' src='".get_photo($_SESSION["user"]["ID"])."' alt='".$_SESSION["user"]["Last_name"]." ".$_SESSION["user"]["First_name"]."' width='50' height='75'></a>";
			echo "<strong>".$_SESSION["user"]["Last_name"]." ".$_SESSION["user"]["First_name"]."</strong></br></br><input type='submit' value='Logout' name='logout' class='text_button'/> ";
			if(isset($_SESSION["admin_user"]))
				echo "<input type='submit' value='Reset' name='reset' class='text_button'/>";
			echo "</form>";
			?>
		</div>
	</div>
	<?php
	require_once ("navmenu.php");
	$file_name = explode ("/",$_SERVER["PHP_SELF"]);
	$menu_item = get_menu_item_on_page ($conn, $file_name[1]);
	$user_rights = get_user_rights($conn, $_SESSION["user"]["ID"]);

	if($file_name[1]!="401.php")
		if(!has_access($menu_item["Rights"], $user_rights))
			header("Location:401.php");
	}
	}
	else {
	?>
	<div id="page_login">
		<div id="logo">
			<img alt="Logo EEB2" src="../images/Logo EEB2.jpg" height='75' width='200'/>
		</div>
		<div id="title_login">
			<h1>Extranet Primary</h1>
		</div>
<?php
}
?>