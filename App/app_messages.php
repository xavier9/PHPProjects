<?php
$page_title = "Messages";

require_once("../includes/db_functions.php");
require_once("../database/MessageDB.php");

/*
 * Following code will list all the products
 */
$login = $_SESSION ["user"];
echo $login;
// array for JSON response
$response = array();

// include db connect class
//require_once __DIR__ . 'includes/db_functions.php';

// connecting to db
$db = get_db_connection();

// get all products from products table
$result = MessageDB::getMessagesAll();

// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["message"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array
        $messages = array();
        $messages["message_ID"] = $row["message_ID"];
        //echo $messages["message_ID"]."<br/>";
        $messages["title"] = $row["title"];
        //echo $messages["title"]."<br/>";
        $messages["message"] = $row["message"];
        //echo $messages["message"]."<br/>";
        $messages["screen_ID"] = $row["screen_ID"];
        //echo $messages["screen_ID"]."<br/>";
        $messages["visibility"] = $row["visibility"];
        //echo $messages["visibility"]."<br/>";
        $messages["expiredate"] = $row["expiredate"];
        //echo $messages["expiredate"]."<br/>";
        // push single product into final response array
        array_push($response["message"], $messages);
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