<?php
$page_title = "Users";

require_once("../includes/db_functions.php");
require_once("../database/PdfDB.php");
require_once("../includes/header.php");


/*
 * Following code will list all the products
 */

// array for JSON response
$response = array();
$messages = array();
// include db connect class
//require_once __DIR__ . 'includes/db_functions.php';

// connecting to db
$db = get_db_connection();
$id = $_GET["id"];

// get all products from products table
//$result = AbsenceDB::getCurrentAbsences();
$SqlQuery = PdfDB::getIDAll($id);
//print_r(get_db_connection ());
$result = PdfDB::getIDAll($id);

echo"</div>";
echo"<div id='content'>";
echo "<form method='post' action='".$_SERVER["PHP_SELF"]."?id=".$id."'>";?>
    <table>
        <tbody>
        <?php
// check for empty result
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    $response["pdfs"] = array();
?>

<?php
    while ($row = mysqli_fetch_array($result)) {
        // temp user array

        ?>
        <tr>

        <td><input type="hidden" id="ID" value="<?php
        echo $messages["ID"] = $row["ID"];?>"></td>
        </tr>
        <tr>
        <td><label>Question PDF: </label></td>
        <td><input type="text" id="VragenPDF" name="VragenPDF" value="<?php
        echo $messages["VragenPDF"] = $row["VragenPDF"];?>"></td>
        </tr>
        <tr>
        <td><label>Answer PDF: </label></td>
        <td><input type="text" id="AndwoordenPDF" name="AntwoordenPDF" value="<?php

        echo $messages["AntwoordenPDF"] = $row["AntwoordenPDF"];?>"></td>
        </tr>
        <tr>
        <td><label>Offset: </label></td>
        <td><input type="text" id="Offset" name="Offset" value="<?php
        echo $messages["Offset"] = $row["Offset"];?>"></td>
        </tr>
        <tr>
            <td><input type="submit" name="submit" value="Submit"></td>
        </tr>
        <?php
        //echo $messages["message"]."<br/>";
        //echo $messages["visibility"]."<br/>";
        //array_push($response["pdfs"], $messages);
    }
    // success
    $response["success"] = 1;

    // echoing JSON response
   // echo json_encode($response);
} else {
    // no products found
    //$response["success"] = 0;
    //$response["message"] = "No products found";

    // echo no users JSON
    //echo json_encode($response);
}
?>

    </tbody>
    </table>
        <?php echo "</form>
    </div>";?>

<?php
if(isset($_POST['submit'])) {
    $message = array("ID"  => $id, "VragenPDF" => $_POST["VragenPDF"],"AntwoordenPDF"  => $_POST["AntwoordenPDF"]
    ,"Offset"  => $_POST["Offset"]);
    echo $messages["ID"] = $id;
    echo $messages["VragenPDF"] = $_POST["VragenPDF"];
    echo $messages["AntwoordenPDF"] = $_POST["AntwoordenPDF"];
    echo $messages["Offset"] = $_POST["Offset"];
    PdfDB::update($messages);
    header("Location: index.php");
}

    require_once("../includes/footer.php");

