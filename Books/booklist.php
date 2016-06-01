
<?php
$page_title = "Booklist";

// check for empty result
//require_once("includes/startsession.php");
require_once("../includes/db_functions.php");
require_once("../includes/header.php");
require_once("../database/BookListDB.php");
require_once("../database/MonitorSection.php");
if(!empty($_POST["ISBN"]) ){
    header("Location: isbn.php?ID=".$_POST["ISBN"]);

}



?>
<script>
    function languagess(langs) {

                window.location.href = "booklist.php?hell=" + langs['lang'];

    }
    function years(ye) {
        window.location.href = "booklist.php?hellos="+ ye['lang']+"," +ye['year'];
    }

</script>

<?php

?>
<div>
    <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";?>
    <table>
        <tr>
            <td>
                <label>ISBN: </label>
            </td>
            <td>
                <input type="text" name="ISBN" id="ISBN" size="20">
            </td>
            
            <td>
                <p>
                    Typical location of an ISBN on the back of a book.
                </p>
            </td>

        </tr>
        <tr>
            <td>
                <input type="submit" id="submit" class="text_button" value="submit">
            </td>
        </tr>

    </table>
    <img id="isbn" src="Books/sample-isbn.png" alt="ISBN" style="width:150px;height:100px;">
</div>
<?php echo "</form>";

    ?>

    <?php echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>"; ?>
    <div id="menus">

        <div class="tree">
            <?php

            echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>";
            echo "<div id='menu'>";
            $lang = array("Common", "En", "Fr", "Sw", "De", "Fi", "It", "Nl", "Pt");

            // looping through all results
            // products node
            //$response["pdfs"] = array();

            foreach ($lang as $row) {
                $me['lang'] = $row;

                echo "<input type='button' onclick='languagess(" . json_encode($me) . ")'
                            class='menu_button'
                        value='" . $row . "'></br>" ?>
                <div id="submenu<?php echo $row; ?>" <?php $split = explode(',', $_GET['hellos']);
                if ($_GET['hell'] == $row || $split[0] == $row) {
                    ?> style="display: block" <?php } else { ?>style="display: none" <?php } ?>>

                    <?php

                    $year = array(1, 2, 3, 4, 5, 6, 7);

                    // looping through all results
                    // products node
                    //$response["pdfs"] = array();

                    foreach ($year as $rowy) {
                        $mens['lang'] = $row;
                        $mens['year'] = $rowy;
                        echo "<input type='button'  onclick='years(" . json_encode($mens) . ")'
                            class='menu_item_button'
                           value='" . $rowy . "'></br>";


                    }


                    ?>
                </div>
            <?php }

            ?>


            <?php


            echo "</div>";
            echo "</form>";

            ?>

        </div>
    </div>


<div id="content_3">
    <input type="button" onclick="printDiv('printableArea')" value="Print" class="text_button"/>


    <?php
    if (isset($_GET['hell'])) {
        ?>

    <div id="printableArea">



            <?php

            ?>

            <?php

            //echo $l;
            $dec = $_GET['hell'];
            $res = BookListDB::getAllLanguages($dec);
            //echo mysqli_num_rows($res);
            if (mysqli_num_rows($res) > 0) {
                // looping through all results
                // products node
                //$response["pdfs"] = array();
?>  <table class="table">
                    <thead>
            <tr>
                <th>
                    Year
                </th>
                <th>
                    Section
                </th>
                <th>
                    Subject
                </th>
                <th>
                    Title
                </th>
                <th>
                    Authors
                </th>
                <th>
                    Publisher
                </th>
                <th>
                    ISBN
                </th>
                <th>

                </th>
                <th>

                </th>

            </tr>
            </thead>
            <tbody>
                <?php
                while ($ro = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo $ro['Year'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Language'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Subject'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Title'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Authors'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Publisher'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['ISBN'];
                            ?>
                        </td>


                        <td>
                            <button  class="text_button" onclick="del(<?php echo $ro['ID']; ?>)">Del</button>
                        </td>
                        <td>
                            <button class="text_button" onclick="edit(<?php echo $ro['ID']; ?>)"> edit </button>
                        </td>
                    </tr>
                    <?php

                }
                ?></tbody>
        </table>
                <?php
            }else{
                    echo "Still notting";
                }


            ?>


        </div>
        <?php
    }
    ?>
 

    <?php
    if (isset($_GET['hellos'])) {
        ?>



            <?php

            ?>
        <div id="printableArea">
            <?php

            //echo $l;
            $dec = $_GET['hellos'];
            $split = explode(',', $dec);
            $res = BookListDB::getAllLanguageYear($split[0], $split[1]);
            echo "YEAR: " . $split[1];
            //echo mysqli_num_rows($res);
            if (mysqli_num_rows($res) > 0) {
                // looping through all results
                // products node
                //$response["pdfs"] = array();
            ?>
                <table class="table">

                <thead>
            <tr>
                <th>
                    Year
                </th>
                <th>
                    Section
                </th>
                <th>
                    Subject
                </th>
                <th>
                    Title
                </th>
                <th>
                    Authors
                </th>
                <th>
                    Publisher
                </th>
                <th>
                    ISBN
                </th>
                <th>

                </th>
                <th>

                </th>

            </tr>
            </thead>
            <tbody>
            <?php
                while ($ro = mysqli_fetch_array($res)) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo $ro['Year'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Language'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Subject'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Title'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Authors'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['Publisher'];
                            ?>

                        </td>
                        <td>
                            <?php
                            echo $ro['ISBN'];
                            ?>
                        </td>


                        <td>
                            <button  class="text_button" onclick="del(<?php echo $ro['ID']; ?>)">Del</button>
                        </td>
                        <td>
                            <button class="text_button" onclick="edit(<?php echo $ro['ID']; ?>)"> edit </button>
                        </td>
                    </tr>
                    <?php

                }
            ?>
            </tbody>
        </table>
            <?php

            }else{
                echo "Stil notting";
            }


            ?>


        </div>
        <?php
    }
    ?>
</div>
<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
    function edit(id){

        document.location.href = 'edit.php?id=' + id;
    }
    function del(id) {
        document.location.href = 'delete.php?id=' + id;
    }

</script>


<?php




require_once("includes/footer.php");
?>

