<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 19/05/2016
 * Time: 14:01
 */

require_once("includes/db_functions.php");
$id = explode(",",$_GET["id"]);

echo $SqlQuery = "DELETE FROM b2_student_timetable WHERE Student_ID = '".$id[0]."'
and Section = '".$id[1]."' and  Teacher_id ='".$id[2]."';";
mysqli_query(get_db_connection (),$SqlQuery);
header("Location: student_course.php?id=".$id[1].",".$id[2]);
