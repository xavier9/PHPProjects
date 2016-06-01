
<?php
$page_title = "Booklist";

// check for empty result
require_once("../Secondary/includes/db_functions.php");
require_once("../Secondary/database/BookListDB.php");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="extranet_secondary.css?v=1.1" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php echo "<title>".(isset($page_title)?$page_title:"")."</title>";?>
</head>
<body>
<div id="page">
    <div id="header">
        <div id="logo">
            <a href="home.php"><img alt="Logo EEB2" src="../../images/Logo EEB2.jpg" height='75' width='200'/></a>
        </div>
        <div id="title">
            <h1>Extranet Secondary</h1>
        </div>
    </div>


<script>
    function languagess(langs) {

        window.location.href = "index.php?hell=" + langs['lang'];

    }
    function years(ye) {
        window.location.href = "index.php?hellos="+ ye['lang']+"," +ye['year'];
    }

</script>

<?php

?>




<?php echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>"; ?>
<div id="menus">

    <div class="tree">
        <?php

        echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "'>";
        echo "<div id='menu'>";
        $lang = array("Common", "De", "En", "Fi", "Fr", "It", "Nl", "Pt", "Sv");

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

</script>

    <div id="footer">


    </div>
    <script>
        <?php
        $spinnerdelay = 0;
        ?>
        spinner_stop();

        $(document).ready(function() {
            $(':button').click(function(){
                setTimeout(function(){show_loader();},<?php echo $spinnerdelay; ?>);
            });
            $(":input[type='button']").click(function(){
                setTimeout(function(){show_loader();},<?php echo $spinnerdelay; ?>);
            });
            $(":input[type='submit']").click(function(){
                setTimeout(function(){show_loader();},<?php echo $spinnerdelay; ?>);
            });
        });
    </script>

    <div id='loadingspinner'></div>
</div>
</body>


