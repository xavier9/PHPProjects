<?php
	require_once("includes/startsession.php");
  
	if (isset($_SESSION['user'])) {
		$_SESSION = array();
		session_destroy();
	}

	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
	header('Location: ' . $home_url);
?>
