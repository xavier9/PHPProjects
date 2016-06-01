
<?php
$page_title = "PDFReader";

require_once("../includes/db_functions.php");
require_once("../database/PdfDB.php");

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

// get all products from products table
//$result = AbsenceDB::getCurrentAbsences();
$SqlQuery = "SELECT * FROM `b2_pdfs`";
//print_r(get_db_connection ());
$result =  mysqli_query(get_db_connection (),$SqlQuery);
// check for empty result
require_once("../includes/header.php");
?>


</div>

<div id="content">
    <button type="submit" id="add" class="text_button"> Add
    </button>
    <script>
        var btn = document.getElementById('add');
        btn.addEventListener('click', function() {
            document.location.href = 'pdfadd.php';
        });
    </script>

    <div id="StudentTableContainer">

    <table id="example" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>

            <th>VragenPDF</th>
            <th>AntwoordenPDF</th>
            <th>Offset</th>
            <th>Select</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        </thead>

        <tbody>
<?php
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    //$response["pdfs"] = array();

    while ($row = mysqli_fetch_array($result)) {
        // temp user array

?>

        <tr >


<?php
        $messages["ID"] = $row["ID"];
?>
<td>
                    <?php
        //echo $messages["ID"]."<br/>";
        echo $messages["VragenPDF"] = $row["VragenPDF"];
                ?></td><td>
                    <?php
        //echo $messages["title"]."<br/>";
        echo $messages["AntwoordenPDF"] = $row["AntwoordenPDF"];
                    ?>
                    </td><td>
                    <?php
        echo $messages["Offset"] = $row["Offset"];
                    ?>
                    </td>
        <td><button onclick="select(<?php echo $messages["ID"]; ?>)" id="select" class="text_button">Select</button></td>
                <td>
                    <button onclick="edit(<?php echo $messages["ID"]; ?>)" id="edit" class="text_button">Edit</button>

                </td>
                <td>
                    <button onclick="deletet(<?php echo $messages["ID"]; ?>)" type="submit" id="delete" class="text_button">Delete</button>

                </td>
                    <?php
        //echo $messages["message"]."<br/>";
        //echo $messages["visibility"]."<br/>";
        //array_push($response["pdfs"], $messages);
    }
    // success
    //$response["success"] = 1;

    // echoing JSON response
    //echo json_encode($response);
} else {
    // no products found
    $response["success"] = 0;
    $response["message"] = "No products found";

    // echo no users JSON
   // echo json_encode($response);
                ?>

            </tr>
                <?php
}
?></tbody>

        </table>
        </div>
            </div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"
        type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">




    function select(id) {
            //alert(id);



        document.location.href = 'pdfquestions.php?id=' + id;
    }
    function edit(id){

        document.location.href = 'edit.php?id=' + id;
    }
    function deletet(id) {

        document.location.href = 'delete.php?id=' + id;
    }


</script>
<?php




require_once("../includes/footer.php");
?>

