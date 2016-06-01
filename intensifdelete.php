<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 17/05/2016
 * Time: 8:24
 */

require_once("includes/db_functions.php");
$id = $_GET["id"];
$SqlQuery = "DELETE FROM b2_Intensive WHERE ID = '".$id."';";
mysqli_query(get_db_connection (),$SqlQuery);
header("Location: Curriculum.php");