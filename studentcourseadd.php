<?php
echo "Submit"."</br>";
require_once("includes/db_functions.php");

$id = explode(",", $_GET['id'] );
$messages["teacher"] = $id[1];


$SqlQuery =  "INSERT INTO `b2_student_timetable` (`Student_ID`, `Section`, Teacher_id)
          VALUES  ('" . $id[0] . "','"
    . $messages["teacher"] . "','"
    . $id[2]. "');";
echo $SqlQuery;
echo mysqli_query(get_db_connection(), $SqlQuery);
header("Location: student_course.php?id=".$id[1].",".$id[2]);