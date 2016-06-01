<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 21/04/2016
 * Time: 12:57
 */



require_once("../includes/db_functions.php");
require_once("../includes/header.php");
require_once("../database/BookListDB.php");
$SqlQuery = "SELECT DISTINCT Description
from  b2_courses
Where Description Not Like '%1%'
And Description Not Like '%2%'
And Description Not Like '%3%'
And Description Not Like '%4%'
And Description Not Like '%5%'
And Description Not Like '%6%'
And Description Not Like '%7%'
And Description Not Like '%8%'
And Description Not Like '%9%'
And Description Not Like '%SE%'";
//print_r(get_db_connection ());
$id = $_GET['id'];




$results =  mysqli_query(get_db_connection (),$SqlQuery);

$result = BookListDB::getID($id);


    ?>

    <?php

    ?>
    <div id="content">
       <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."?id=".$id."'>";?>
    <table>
    <tbody>
    <?php
if (mysqli_num_rows($result) > 0) {
    // looping through all results
    // products node
    //$response["pdfs"] = array();
    ?>

    <?php
    while ($row = mysqli_fetch_array($result)) {
        //echo json_encode($row);
        ?>
        <tr>
        <td>
            <label>
                ISBN
            </label>
        </td>
        <td>
            <input type="text" id="ISBN" name="ISBN"
                   value="<?php echo $row['ISBN'];?>" width="50"
                   maxlength="50" size="50"></td>
        </tr>
        <tr>
            <td>
                <label>
                    Title
                </label>
            </td>
            <td>
                <input type="text" id="Title" name="Title"
                       value="<?php echo $row['Title'];?>" width="50"
                       maxlength="50" size="50"></td>
        </tr>
        <tr>

            <td>
                <label>
                    Authors
                </label>
            </td>
            <td>
                <input type="text" id="Author" name="Author"
                       value="<?php echo $row['Authors'];?>" width="50"
                       maxlength="50" size="50"></td>
        </tr>

        <tr>
            <td>
                <label>
                    Publisher
                </label>
            </td>
            <td>
                <input type="text" id="Publisher" name="Publisher"
                       value="<?php echo $row['Publisher'];?>" width="50"
                       maxlength="50" size="50"></td>
        </tr>

        <tr>
            <td>
                <label>
                    PageCount
                </label>
            </td>
            <td>
                <input type="text" id="PageCount" name="PageCount"
                       value="<?php echo $row['PageCount'];?>" width="50"
                       maxlength="50" size="50">
            </td>
        </tr>

        <?php
        $year = array(1, 2, 3, 4, 5, 6, 7);
        ?>
        <tr>
            <td>
                Year:
            </td>
            <td>
                <select name="year_id">

                    <?php
                    foreach ($year as $y) {
                        ?>
                        <option <?php if($row['Year'] == $y) echo"selected" ?>>
                            <?php echo $y; ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Vak:
            </td>
            <td>

                <select name="descript_id">
                    <?php
                    if (mysqli_num_rows($results) > 0) {
                        while ($rows = mysqli_fetch_array($results)) {
                            ?>
                            <option <?php if($row["Vak"] == $rows["Description"]) echo"selected" ?>>
                                <?php echo $messages["Description"] = $rows["Description"]; ?></option>
                        <?php }} ?>
                </select>

            </td>
        </tr>
        <?php
        $lang = array("Commen", "En", "Fr", "Sv", "De", "Fi", "It", "Nl", "Pt");
        ?>
        <tr>
            <td>
                Language:
            </td>
            <td>
                <select name="Language_id">

                    <?php
                    foreach ($lang as $l) {
                        ?>
                        <option <?php if($row['Language'] == $l) echo"selected"?>><?php echo $l; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <input type="submit" id="submit" value="submit" name="submit" class="text_button">
            </td>
        </tr>
        <?php
    }
}
        ?>
    </tbody>
    </table>
        <?php echo "</form>"?>
    </div>

<?php
if(isset($_POST['submit'])) {
    $message = array("ID"  => $id, "ISBN" => $_POST["ISBN"]
    ,"Title"  => $_POST["Title"]
    ,"Authors"  => $_POST["Author"],
    "Publisher" => $_POST["Publisher"],
    "PageCount" => $_POST["PageCount"],
    "Year" => $_POST["year_id"],
    "Subject" => $_POST["descript_id"],
    "Language" => $_POST["Language_id"]);
    //echo $message["Title"];
    BookListDB::update($message);
    //header("Location: http://localhost:63342/Booklist/");
}



require_once("../includes/footer.php");
?>