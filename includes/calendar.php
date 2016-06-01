<?php 
	$hl = (isset($_POST["hl"])) ? $_POST["hl"] : false;
	if(!defined("L_LANG") || L_LANG == "L_LANG") {
		if($hl)
			define("L_LANG", $hl);
		else 
			define("L_LANG", "en_US"); 
	}

	date_default_timezone_set('europe/paris');

	require("includes/calendar/tc_calendar.php");
  
	$date = getdate();
	$date_format = $date["year"]."-".$date["mon"]."-".$date["mday"];
    $myCalendar = new tc_calendar("date1", true, false);
    $myCalendar->setIcon("includes/calendar/images/iconCalendar.gif");
    if($_SESSION["date"]!="0000-00-00")
		$myCalendar->setDate(substr($_SESSION["date"],8,2),substr($_SESSION["date"],5,2),substr($_SESSION["date"],0,4));
	else
		$myCalendar->setDate(date('d'), date('m'), date('Y'));	
    $myCalendar->setPath("includes/calendar/");
    $myCalendar->setYearInterval(2015, 2016);
    $myCalendar->dateAllow('2015-09-02', $date_format);
    $myCalendar->setDateFormat('j F Y');
    $myCalendar->setAlignment('left', 'bottom');
    $myCalendar->startMonday(true);
    $myCalendar->disabledDay("Sat");
    $myCalendar->disabledDay("sun");	 
	$myCalendar->writeScript();
?>
