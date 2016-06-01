<?php
$page_title = "Cources";

require_once("../includes/db_functions.php");
require_once("../database/Cources.php");

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
//print_r(get_db_connection ());
$SqlQuery = "SELECT * FROM b2_courses";
$result = mysqli_query(get_db_connection (),$SqlQuery);

//$result = Cources::getAllCources();
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["courses"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $messages = array();
        $messages["Code"] = $row["Code"];
        //echo $messages["message_ID"]."<br/>";
        $messages["Description"] = $row["Description"];
        //echo $messages["title"]."<br/>";
        $messages["Exam"] = $row["Exam"];


        array_push($response["courses"], $messages);
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