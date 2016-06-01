<?php
//require_once("includes/startsession.php");
$page_title = "Welcome to Extranet Primary EEB2";
require_once("includes/db_functions.php");
require_once("includes/header.php");
$SqlQuery = null;
if(!empty($_POST['submit'])){
    echo "Submit"."</br>";
    $messages["User"] = get_student(get_db_connection(), $_POST['user']);
    $id = $messages["User"]["ID"];


        $messages["User"]["ID"];
        $messages["teacher"] = $_POST['prima'].$_POST["year"].$_POST["mode_id"]
            .$_POST["ls_id"].$_POST["Language"];

 

    $letter = $row["substring(`Course_id`, 8,1)"];
    $messages["teacher"].=++$row["substring(`Course_id`, 8,1)"];

        $SqlQuery =  "INSERT INTO `b2_student_timetable` (`Student_ID`, `Section`)
          VALUES  ('" . $messages["User"]["ID"] . "','"
            . $messages["teacher"] . "')";
        echo $SqlQuery;
       echo mysqli_query(get_db_connection(), $SqlQuery);





}
$split = explode(",",$_GET['id']);
?>
</div>
<body>

<div id="content">
    <?php
    $SqlQuery = "Select * from  b2_students";
    $res = mysqli_query(get_db_connection (),$SqlQuery);
    //echo mysqli_num_rows($res);

        // looping through all results
        // products node
        //$response["pdfs"] = array();


            ?>
            <table class="table">
            <thead>
            <tr>
                <th>
                    Student_id
                </th>
                <th>
                    Course
                </th>

                <th>

                </th>


            </tr>
            </thead>
            <tbody>
            <?php
            while ($ro = mysqli_fetch_array($res)) {
                $splits = str_split($ro["Class"]);
                $class = str_split($split[0]);
                if($splits[0] == $class[0] && $splits[1] == $class[1]
                    && $splits[2] == $class[5] && $splits[3] == $class[6]) {
                ?>
                <tr>
                    <td>
                        <?php
                        $messages["User"] = get_student(get_db_connection(), $ro['ID']);
                        echo $messages["User"]["First_name"] . " " . $messages["User"]["Last_name"];
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $split[0];
                        ?>
                    </td>
                    <?php
    $tre   = false;
                    $SqlQuery = "select * FROM b2_student_timetable";
                    $results = mysqli_query(get_db_connection (),$SqlQuery);
                     $Sql = "Select * from  `b2_teacher_course`
WHERE Teacher_id like '%".$split[1]."%' AND `Course_id` LIKE '".$split[0]."';";
                    $result = mysqli_fetch_array(mysqli_query(get_db_connection (),$Sql));
                     $result["ID"];

                    $Sqls = "Select * from `b2_student_teacher_course` 
WHERE Teacher_course_id LIKE '".$result["ID"]."';";
                    $rest = mysqli_query(get_db_connection (),$Sqls);
                    if(mysqli_num_rows($rest) > 0) {
                        while($r = mysqli_fetch_array($rest)){
                            //echo $r["ID"]." ".$r["Teacher_course_id"]." ".$r["Student_id"];
                            if($r["Student_id"] == $messages["User"]["ID"]) {
                                $tre = true;
                            }
                        }
                    }

                    while ($student = mysqli_fetch_array($results) ){
                        if($messages["User"]["ID"] == $student["Student_ID"])
                        {
                            $tre = true;
                        }

                    }
                    if($tre){?>
                        <td>
                                <button class="text_button" onclick="del(<?php echo $ro['ID']; ?>,
                                    '<?php echo $split[0];?>', <?php echo $split[1] ?>)">Del</button>
                            </td>
                    <?php
                        }else{
                        ?>
                        <td>
                            <button class="text_button" onclick="add(<?php echo $ro['ID']; ?>,
                                '<?php echo $split[0];?>', <?php echo $split[1] ?>)">Add</button>
                        </td>
                        <?php

                    }
                    ?>


                </tr>
                <?php

            }
        }

            ?></tbody>
        </table>
    <button class="text_button" onclick="back()">back</button>

    <?php

    ?>
</div>

<script>
    function add(id, course, teacher){

        document.location.href = 'studentcourseadd.php?id='+ id +","
            +course+","+teacher;
    }
    function del(id, course, teacher) {
        document.location.href = 'studentcoursedelete.php?id=' + id +","
        +course+","+teacher;
    }
    function back(){
        document.location.href = 'Curriculum.php';
    }

</script>

<?php

require_once("includes/footer.php");
?>
</body>