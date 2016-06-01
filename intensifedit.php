<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 17/05/2016
 * Time: 9:37
 */
//require_once("includes/startsession.php");
$page_title = "Welcome to Extranet Primary EEB2";
require_once("includes/db_functions.php");
require_once("includes/header.php");
$SqlQuery = null;
if(!empty($_POST['submit'])){
    echo "Submit"."</br>";
    $messages["User"] = get_user (get_db_connection(), $_POST['user']);
    $ids = $messages["User"]["ID"];
        $messages["ID"] = $_POST["ID"];
        $messages["User"]["ID"];


        $messages["Class"] = $_POST['prima'].$_POST["year"].$_POST["mode_id"]
            .$_POST["ls_id"].$_POST["Language"].$_POST["letter"];

    $SqlQuery = "UPDATE b2_Intensive SET Teacher_id 
= '".$messages["User"]["ID"]."'
, Class = '".$messages['Class']."'"
        ." WHERE ID = '".$messages['ID']."';";
    //mysqli_query(get_db_connection (),$SqlQuery);

        echo $SqlQuery;
       echo mysqli_query(get_db_connection(), $SqlQuery);





    //IntenciefDB::insert($messages);
    //echo "test";
    header("Location: Curriculum.php");

}
?>

</div>
    <body>

<?php
$id = $_GET['id'];
$SqlQuery = "SELECT * FROM b2_Intensive WHERE
ID='".$id."';";
$result = mysqli_query(get_db_connection (),$SqlQuery);
while ($ro = mysqli_fetch_array($result)) {
    $ro['ID'];
    $ro['Teacher_id'];
    $ro['Class'];
    $arra = array();
    $arra = str_split($ro['Class']);

    ?>
    <div id="content">
        <?php echo "<form method='post' action='" . $_SERVER["PHP_SELF"] ."?id=".$id. "'>"; ?>
        <input type="hidden" name="ID" value="<?php echo $ro['ID']; ?>">
        <table>
            <tr>
                <td>
                    <?php

                    $users = get_teacher(get_db_connection(), $ro['Teacher_id']);
                    echo "<select name='user'>";
                    foreach ($users as $user) {
                        echo "<option value='" . $user["ID"] . "' ";
                        if (isset($_SESSION["user_rights"]) && $_SESSION["user_rights"]["ID"] == $user["ID"])
                            echo "selected";
                        echo ">" . $user["Last_name"] . " " . $user["First_name"] . "</option>";


                    }
                    echo "</select> ";

                    ?>
                </td>
            </tr>
            <?php

            ?>
            <tr>
                <td>

                    <?php
                    $split = str_split($ro['Class']);
                    $mode = array("x", "j", "k");
                    $ls = array("l1", "ma", "l2");
                    $year = array(1, 2, 3, 4, 5);
                    ?>
                    Section:

                    <input type="text" name="prima" value="p" readonly width="1" maxlength="1" size="1">
                    <select name="year"<?php echo $i; ?>">
                    <?php
                    foreach ($year as $y) {
                        ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php
                    }
                    ?>
                    </select>

                    <select name="mode_id">
                        <?php
                        foreach ($mode as $m) {
                            ?>
                            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <select name="ls_id">
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

                    <select name="Language">
                        <?php
                        while ($ro = mysqli_fetch_array($lang)) {

                            ?>
                            <option
                                value="<?php echo $ro["substring(Class, 3,2)"]; ?>"><?php echo $ro["substring(Class, 3,2)"]; ?></option>
                            <?php

                        }
                        ?>
                    </select>
                    <input type="text" id="letter" name="letter"
                           value="<?php echo $split[7];?>">
                </td>
            </tr>


        </table>

        <input type="submit" value="submit" id="submit" name="submit" class="text_button">
        <?php echo "</form>" ?>
    </div>
    <?php
}
?>
