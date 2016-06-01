<?php
require_once("includes/startsession.php");
$page_title = "Welcome to Extranet Primary EEB2";
require_once("includes/db_functions.php");
require_once("includes/header.php");
$SqlQuery = null;
$count = 0;
$GLOBALS['Count'] = 0;


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
    <script src="jquery.js"></script>
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

    <?php


    if(!empty($_POST['submit'])){
        //echo "Submit"."</br>";
        //echo $salarieid = $_POST["index[0]"];
        //$lengt = strlen($_POST["year_id"]);
//echo $_POST["teacher"];
            $messages["User"] = get_user(get_db_connection(), $_POST["teacher"]);
            $id = $messages["User"]["ID"];

            $messages["User"]["ID"];




        for($i =0; $i < count($_POST["prima"]); $i++){
            $messages[$i]["Class"] =$_POST['prima'][$i].$_POST["year"][$i]
                .$_POST["mode_id"][$i]
                .$_POST["ls_id"][$i].$_POST["Language"][$i];

            $sqlq = "Select substring(`Course_id`, 8,1) 
from b2_teacher_course where `Course_id` like '".$messages[$i]["Class"]."%'  
order by `Course_id` DESC";
            $reqult = mysqli_query(get_db_connection(), $sqlq);

            $row = mysqli_fetch_assoc($reqult);

            $sqlr = "Select substring(`Class`, 8,1) 
from `b2_Intensive`  where `Class` like '".$messages[$i]["Class"]."%'  
order by `Class` DESC";
            $re = mysqli_query(get_db_connection(), $sqlr);

            $rows = mysqli_fetch_assoc($re);
            if(empty($rows["substring(`Class`, 8,1)"])) {
                $letter = $row["substring(`Course_id`, 8,1)"];
                $messages[$i]["Class"] .= ++$row["substring(`Course_id`, 8,1)"];
            }else{
                $letter = $rows["substring(`Class`, 8,1)"];
                $messages[$i]["Class"] .= ++$rows["substring(`Class`, 8,1)"];

            }

              $SqlQuery = "INSERT INTO `b2_Intensive` 
(`Teacher_id`, 
           `Class`
          )
          VALUES  ('" . $messages["User"]["ID"] . "','"
                 . $messages[$i]["Class"] . "')";
             mysqli_query(get_db_connection(), $SqlQuery);
        }





        //IntenciefDB::insert($messages);
        //echo "test";
        //header("Location: Curriculum.php");

    }
    ?>
</head>
<body>

<div id="content">
    <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";?>
    <?php
    $messages["User"] = null;
    $users = get_all_teachers(get_db_connection());
    echo "<select name='user'>";
    foreach ($users as $user) {
        echo "<option value='" . $user["ID"] . "' ";
        if (isset($_SESSION["user_rights"]) && $_SESSION["user_rights"]["ID"] == $user["ID"])
            echo "selected";
        echo ">" . $user["Last_name"] . " " . $user["First_name"]. "</option>";


    }
    echo "</select> ";
    ?>
    <input type="submit" value="Show" class="text_button" id="show" name="show"/>
    <?php
    echo "</form>";
    
    if(isset($_POST["show"])) {
        $teacher = mysqli_fetch_array(get_teacher(get_db_connection(), $_POST['user']));
        ?>
        <h2>
        <?php
        echo $teacher['First_name']." ".$teacher['Last_name'];
        ?>
        </h2>
        <?php echo "<form method='post' id='surveyForm' action='" . $_SERVER["PHP_SELF"] . "'>"; ?>
        <table>
            <thead>
            <tr>
                <td>
                    Section
                </td>
                <td>

                </td>
                <td>

                </td>

            </tr>

            </thead>

            <tbody id="optionTemplate" name="index[0]">

            <tr>
                <td>
                    <input type="hidden" name="teacher" id="teacher" value="<?php echo $teacher["ID"];?>">


                    <?php
                    $mode = array("x", "j", "k");
                    $ls = array("l1", "ma", "l2");
                    $year = array(1, 2, 3, 4, 5);
                    ?>
                    Section:

                    <input type="text" name="prima[]" value="p" readonly width="1" maxlength="1" size="1">
                    <select name="year[]">
                    <?php
                    foreach ($year as $y) {
                        ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php
                    }
                    ?>
                    </select>

                    <select name="mode_id[]">
                        <?php
                        foreach ($mode as $m) {
                            ?>
                            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <select name="ls_id[]">
                        <?php
                        foreach ($ls as $s) {
                            ?>
                            <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                    $SqlQuery = "SELECT distinct substring(Class, 3,2) FROM `b2_students` ORDER BY substring(Class, 3,2) ASC";
                    $lang = mysqli_query(get_db_connection(), $SqlQuery);

                    ?>

                    <select name="Language[]">
                        <?php
                        while ($ro = mysqli_fetch_array($lang)) {

                            ?>
                            <option
                                value="<?php echo $ro["substring(Class, 3,2)"]; ?>"><?php echo $ro["substring(Class, 3,2)"]; ?></option>
                            <?php

                        }
                        ?>
                    </select>
                </td>

                <td>

                    <button type="button" class="btn btn-default addButton">Add</button>
                </td>
                <td>

                </td>

            </tr>

            </tbody>


        </table>

        <input type="submit" value="submit" id="submit" name="submit" class="text_button">
        <?php echo "</form>" ?>
        <?php
        $SqlQuery = "Select * from  b2_Intensive WHERE Teacher_id = '".$teacher["ID"]."' ORDER by b2_Intensive.ID ASC ";
        $res = mysqli_query(get_db_connection(), $SqlQuery);
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
                        Teacher_id
                    </th>
                    <th>
                        Section
                    </th>

                    <th>

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

                            $messages["User"] = mysqli_fetch_array(get_teacher(get_db_connection(), $ro['Teacher_id']));
                            echo $messages["User"]["First_name"] . " " . $messages["User"]["Last_name"];

                            //echo $ro['Teacher_id'];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo $ro['Class'];
                            ?>

                        </td>
                        <td>
                            <button class="text_button" onclick="addStudent('<?php echo $ro['Class']; ?>',
                            <?php echo $teacher['ID'];?>)">Add students</button>

                        </td>
                        <td>
                            <button class="text_button" onclick="del(<?php echo $ro['ID']; ?>)">Del</button>
                        </td>
                        <td>
                            <button class="text_button" onclick="edit(<?php echo $ro['ID']; ?>)"> edit</button>

                        </td>
                    </tr>
                    <?php

                }

            }
                ?></tbody>
                <?php
                $Sql = "Select * from  `b2_teacher_course` 
WHERE  `Course_id` LIKE 'p_k%'
      OR `Course_id` LIKE 'p_x%'
      OR `Course_id` LIKE 'p_j%'";
                $resul = mysqli_query(get_db_connection(), $Sql);
            while ($row = mysqli_fetch_array($resul)) {
    if ($row["Teacher_id"] == $teacher["ID"]){

        ?>
        <tr>
            <td>
                <?php

                echo $messages["User"]["First_name"] . " " . $messages["User"]["Last_name"];

                //echo $ro['Teacher_id'];
                ?>
            </td>

            <td>
                <?php
                echo $row['Course_id'];
                ?>

            </td>
            <td>
                <button class="text_button" onclick="addStudent('<?php echo $row['Course_id']; ?>',
                <?php echo $teacher['ID']; ?>)">Add students
                </button>

            </td>

        </tr>
        <?php
    }
    }
                ?></tbody>
            </table>
            <?php

    }

    ?>
</div>

<script>
    function edit(id){

        document.location.href = 'intensifedit.php?id=' + id;
    }
    function del(id) {
        document.location.href = 'intensifdelete.php?id=' + id;
    }
    function addStudent(teacher, clas){
        document.location.href = 'student_course.php?id=' + teacher+ ","+clas;
    }
</script>

<?php

require_once("includes/footer.php");
?>
</body>