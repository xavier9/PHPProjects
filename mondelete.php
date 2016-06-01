<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 18/04/2016
 * Time: 15:29
 */
require_once("database/MonitorSection.php");
require_once("includes/db_functions.php");
$id = $_GET["id"];
$query = "SELECT * FROM b2_user_rights WHERE ID = '".$id."'";

$rights = mysqli_query(get_db_connection(),$query);
if (mysqli_num_rows($rights) > 0) {
    while ($ro = mysqli_fetch_array($rights)) {

               $recht =  str_replace ("K", "", $ro["Rights"]);


    }
}
echo $recht;
$query = "UPDATE b2_user_rights SET Rights = '".$recht."' WHERE ID = '".$id."';";
mysqli_query(get_db_connection(),$query);
MonitorSection::deleteBy($id);
header("Location: monitor.php");