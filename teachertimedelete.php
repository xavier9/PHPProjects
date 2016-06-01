<?php
require_once("includes/db_functions.php");
$id = $_GET["id"];

echo $SqlQuery = "DELETE FROM b2_teacher_timetable WHERE ID = '".$id."';";
mysqli_query(get_db_connection (),$SqlQuery);
header("Location: teacher_timetable.php");
