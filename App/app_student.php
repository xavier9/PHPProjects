<?php
$page_title = "Students";

require_once("../includes/db_functions.php");
require_once("../database/StudentDB.php");

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
$result = StudentDB::getAllStudents();

//echo mysqli_num_rows($result);
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["student"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $messages = array();
        $messages["ID"] = $row["ID"];
        //echo $messages["message_ID"]."<br/>";
        $messages["Last_name"] = $row["Last_name"];
        //echo $messages["title"]."<br/>";
        $messages["First_name"] = $row["First_name"];
        //echo $messages["message"]."<br/>";
        $messages["Class"] = $row["Class"];
        $messages["New"] = $row["New"];
        $messages["Repeating"] = $row["Repeating"];
        $messages["Date_of_birth"] = $row["Date_of_birth"];
        $messages["Swals"] = $row["Swals"];
        $messages["Nationality"] = $row["Nationality"];
        $messages["Exit_student"] = $row["Exit_student"];
        $messages["Sex"] = $row["Sex"];

        array_push($response["student"], $messages);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
    echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["login"] = "No products found";

    // echo no users JSON
    echo json_encode($response);
}
?>