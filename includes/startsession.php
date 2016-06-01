<?php
	session_start();	
	$inactive = 1200;
	
	if (!isset($_SESSION['user'])) 
		header("Location: login.php");
 
	if(isset($_SESSION['timeout']) ) {
		$session_life = time() - $_SESSION['timeout'];
		if($session_life > $inactive) { 
			unset($_SESSION['user']);
			header("Location: logout.php"); 
		}
	}
	
	$_SESSION['timeout'] = time();
?>
