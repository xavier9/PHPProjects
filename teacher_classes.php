<?php
require_once("includes/startsession.php");
$page_title = "Welcome to Extranet Primary EEB2";
require_once("includes/db_functions.php");
require_once("includes/header.php");


?>

<body>
<div id="content">

    <?php
    $SqlQuery = "Select * from  b2_Intensive ORDER by b2_Intensive.ID ASC ";
    $res = mysqli_query(get_db_connection (),$SqlQuery);
    //echo mysqli_num_rows($res);
    if (mysqli_num_rows($res) > 0) {
        // looping through all results
        // products node
        //$response["pdfs"] = array();

            while ($ro = mysqli_fetch_array($res)) {


            $messages["User"] = mysqli_fetch_array(get_teacher(get_db_connection(),  $ro['Teacher_id']));


            //echo $ro['Teacher_id'];
  ?>

                <ul>
                    <li>
                        <?php
                        echo  $messages["User"]["First_name"]." ".$messages["User"]["Last_name"]
                        ?>

                <ul><li>
                <?php
                echo $ro['Class'];
                    echo "</br>";
                $SQL = "SELECT * FROM `b2_student_timetable`where `Section` = '".
                    $ro["Class"]."' and Teacher_id='"
                    .$messages["User"]["ID"]."';";
                $SQL;
                $rest = mysqli_query(get_db_connection (),$SQL);
                if (mysqli_num_rows($rest) > 0) {
                    // looping through all results
                    // products node
                    //$response["pdfs"] = array();

                    while ($row = mysqli_fetch_array($rest)) {

                        $messages["User"] = get_student(get_db_connection(),  $row['Student_ID']);
                        ?>
                        <ul>
                            <li>
                    <?php
                        echo $messages["User"]["First_name"]." ".
                            $messages["User"]["Last_name"];
                        ?>
                            </li>
                        </ul>
                    <?php
                    }
                }
                ?></li>
                </ul>
                    </li>
                </ul>
                    <?php

            }

        ?>

        <?php
    }
    ?>
</div>



<?php

require_once("includes/footer.php");
?>
</body>