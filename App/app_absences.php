<?php
$page_title = "Users";

require_once("../includes/db_functions.php");
require_once("../database/AbsenceDB.php");

/*
 * Following code will list all the products
 */

// array for JSON response
$response = array();

// include db connect class
//require_once __DIR__ . 'includes/db_functions.php';

// connecting to db
$db = get_db_connection();

// get all products from products table
//$result = AbsenceDB::getCurrentAbsences();
$SqlQuery = "SELECT * FROM `b2_teacher_absences` WHERE `date` = CURDATE() ORDER BY name";
//print_r(get_db_connection ());
$result = AbsenceDB::getCurrentAbsences();
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["absence"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $messages = array();
        $messages["absence_ID"] = $row["absence_ID"];
        //echo $messages["message_ID"]."<br/>";
        $messages["title"] = $row["title"];
        //echo $messages["title"]."<br/>";
        $messages["name"] = $row["name"];
        $messages["hours"] = $row["hours"];
        //echo $messages["message"]."<br/>";
        $messages["date"] = $row["date"];
        //echo $messages["screen_ID"]."<br/>";
        $messages["note"] = $row["note"];
        //echo $messages["visibility"]."<br/>";
        $messages["reason_ID"] = $row["reason_ID"];
        //echo $messages["expiredate"]."<br/>";
        // push single product into final response array
        array_push($response["absence"], $messages);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No products found";

    // echo no users JSON
    echo json_encode($response);
}
?>