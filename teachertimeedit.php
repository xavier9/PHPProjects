<?php
//require_once("includes/startsession.php");
$page_title = "Welcome to Extranet Primary EEB2";
require_once("includes/db_functions.php");
require_once("includes/header.php");
$SqlQuery = null;

if(!empty($_POST['submit'])) {
    echo "Submit" . "</br>";




        $SqlQuery = "Update `b2_teacher_timetable` SET Teacher_id, BEGIN, END,
Day, Class) VALUES ('".

            $_POST["timepicker_start"]."','".$_POST["timepicker_end"]
            ."','".$_POST["day"]."','".$_POST["class"]
            ."')";

        echo $SqlQuery;
        //mysqli_query(get_db_connection (),$SqlQuery);
        echo mysqli_query(get_db_connection(), $SqlQuery);




    //IntenciefDB::insert($messages);
    //echo "test";
    header("Location: student_cource.php");

}

?>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
    <title></title>
    <link rel="stylesheet" href="includes/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
    <link rel="stylesheet" href="css/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />

    <script type="text/javascript" src="includes/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="includes/ui-1.10.0/jquery.ui.core.min.js"></script>
    <script type="text/javascript" src="includes/ui-1.10.0/jquery.ui.widget.min.js"></script>
    <script type="text/javascript" src="includes/ui-1.10.0/jquery.ui.tabs.min.js"></script>
    <script type="text/javascript" src="includes/ui-1.10.0/jquery.ui.position.min.js"></script>

    <script type="text/javascript" src="includes/jscript/jquery.ui.timepicker.js?v=0.3.3"></script>

    <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
    <style type="text/css">
        /* some styling for the page */
        body { font-size: 10px; /* for the widget natural size */ }
        #content { font-size: 1.2em; /* for the rest of the page to show at a normal size */
            font-family: "Lucida Sans Unicode", "Lucida Grande", Verdana, Arial, Helvetica, sans-serif;
            width: 950px; margin: auto;
        }
        .code { margin: 6px; padding: 9px; background-color: #fdf5ce; border: 1px solid #c77405; }
        fieldset { padding: 0.5em 2em }
        hr { margin: 0.5em 0; clear: both }
        a { cursor: pointer; }
        #requirements li { line-height: 1.6em; }
    </style>

    <script type="text/javascript">

        var bookIndex = 0;
        $(document).ready(function() {
            // The maximum number of options


            $('#surveyForm')

            // Add button click handler
                .on('click', '.addButton', function() {

                    bookIndex++;
                    var $template = $('#optionTemplate'),
                        $clone    = $template
                            .clone()
                            .removeClass('hide')
                            .attr('name', 'index['+bookIndex+']')
                            .insertBefore($template),
                        $option   = $clone.find('[name="option[]"');

                    // Add new field
                    //window.alert(bookIndex);
                    $('#surveyForm');

                });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#timepicker_start').timepicker({
                showLeadingZero: false,
                onSelect: tpStartSelect,
                maxTime: {
                    hour: 16, minute: 30
                }
            });
            $('#timepicker_end').timepicker({
                showLeadingZero: false,
                onSelect: tpEndSelect,
                minTime: {
                    hour: 8, minute: 00
                }
            });
        });

        // when start time change, update minimum for end timepicker
        function tpStartSelect(time, endTimePickerInst) {
            $('#timepicker_end').timepicker('option', {
                minTime: {
                    hour: endTimePickerInst.hours,
                    minute: endTimePickerInst.minutes
                }
            });
        }

        // when end time change, update maximum for start timepicker
        function tpEndSelect(time, startTimePickerInst) {
            $('#timepicker_start').timepicker('option', {
                maxTime: {
                    hour: startTimePickerInst.hours,
                    minute: startTimePickerInst.minutes
                }
            });
        }
    </script>
</head>
<body>
<?php
$SqlQuery = "SELECT * FROM `b2_teacher_timetable` where ID='".$_GET['id']."'";
$rest = mysqli_fetch_array(mysqli_query(get_db_connection (),$SqlQuery));
?>
</div>
<div id="content">
    <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."?id=".$_GET['id']."'>";?>
    <?php
    $messages["User"] = null;
    $users = get_teacher(get_db_connection(), $rest["Teacher_id"]);
    echo "<select name='user'>";
    foreach ($users as $user) {
        echo "<option value='" . $user["ID"] . "' ";
        if (isset($_SESSION["user_rights"]) && $_SESSION["user_rights"]["ID"] == $user["ID"])
            echo "selected";
        echo ">" . $user["Last_name"] . " " . $user["First_name"]. "</option>";


    }
    echo "</select> ";


        $i = 0;
        //echo mysqli_num_rows($res);

        // looping through all results
        // products node
        //$response["pdfs"] = array();
        ?>
        <table id="optionTemplate">
            <tr>
                <td>
                    <?php

                    ?>
                    Day:
                    <?php
                    echo "<select name='day'>";
                    $day = array("Monday", "Tuesday", "Wednesday"
                    , "Thursday", "Friday")
                    ?>
                    <?php

                    foreach ($day as $d) {
                        ?>
                        <option value='<?php echo $d; ?>'>
                            <?php echo $d; ?></option>


                        <?php


                    }
                    echo "</select> "; ?>

                </td>
                <td>
                    Begin & till :


                    <input type="text" style="width: 70px" name="timepicker_start" id="timepicker_start"
                           value="<?php echo $rest["Begin"];?>"/>
                    &
                    <input type="text" style="width: 70px" name="timepicker_end" id="timepicker_end"
                           value="<?php echo $rest["End"];?>"/>
                </td>

                <td><?php

                    ?>
                    Section:
                    <?php
                    echo "<select name='class'>";

                    ?>
                    <?php
                    $SqlQuery = "SELECT * FROM `b2_Intensive` WHERE  Teacher_id='" .  $rest["Teacher_id"] . "'";
                    $res = mysqli_query(get_db_connection(), $SqlQuery);
                    if (mysqli_num_rows($res) > 0) {
                        while ($ro = mysqli_fetch_array($res)) {
                            ?>
                            <option value='<?php echo $ro['Class']; ?>'>
                                <?php echo $ro['Class']; ?></option>


                            <?php

                        }
                    }
                    echo "</select> "; ?>

                </td>


            </tr>
        </table>
        <input type="submit" value="submit" id="submit" name="submit" class="text_button">
        <?php
        echo "</form>";





    ?>
</div>



<?php

require_once("includes/footer.php");
?>
</body>