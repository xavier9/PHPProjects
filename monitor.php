<?php
require_once("includes/startsession.php");
$page_title = "Monitor";
require_once("includes/header.php");
require_once ("database/MonitorSection.php");

if(!empty($_POST['submit'])){
    echo "Submit";
      $messages =    get_user (get_db_connection(), $_POST['user']);
    echo $messages['ID'];
    echo $messages['Last_name'];

    /*$SqlQuery = "INSERT INTO b2_book_list (`ISBN`, `Title`,
      `Authors`, `Publisher`
      , `Year`, `Subject`, `Language`)
      VALUES ('".$messages['ISBN']."','"
        .$messages['Title']."','".$messages['Author']."','"
        .$messages['Publisher']."','"
        .$messages['Year']."','".$messages['Subject']."','"
        .$messages['Language']."')";
    echo mysqli_query(get_db_connection (),$SqlQuery);*/
    $query = "SELECT * FROM b2_user_rights WHERE ID = '".$messages['ID']."'";
    $data = mysqli_query(get_db_connection(),$query);
    $rights = mysqli_query(get_db_connection(),$query);
if (mysqli_num_rows($rights) > 0) {
while ($ro = mysqli_fetch_array($rights)) {
    $recht = $ro["Rights"]."K";
}
}

    $query = "UPDATE b2_user_rights SET Rights = '".$recht."' WHERE ID = '".$messages['ID']."';";
    mysqli_query(get_db_connection(),$query);
    MonitorSection::insert($messages);
    //echo "test";
    header("Location: monitor.php");

}
echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";
?>
<div id="content">
    <table>
        <tr>
            <td>
        <?php

            $users = get_all_users(get_db_connection ());
            echo "<select name='user'>";
            foreach ($users as $user) {
                echo "<option value='".$user["ID"]."' ";
                if(isset($_SESSION["user_rights"]) && $_SESSION["user_rights"]["ID"]==$user["ID"])
                    echo "selected";
                echo ">".$user["Last_name"]." ".$user["First_name"]." ".$user["Email"]."</option>";


            }
            echo "</select> ";

        ?></td>

            <td></td>
            <td>
                <input type="submit" value="submit" id="submit" name="submit" class="text_button">
            </td>
        </tr>
    </table>
</div>
<?php
echo "</form>";
?>
<div id="content">
<?php
$res = MonitorSection::getAll();
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
                ID
            </th>
            <th>
                Name
            </th>
            <th>
                Sur Name
            </th>

            <th>
                Email
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
                    echo $ro['ID'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $ro['Name'];
                    ?>
                </td>
                <td>
                    <?php
                    echo $ro['LastName'];
                    ?>

                </td>
                <td>
                    <?php
                    echo $ro['Email'];
                    ?>

                </td>
                <td>
                    <button class="text_button" onclick="del(<?php echo $ro['ID']; ?>)">Del</button>
                </td>
                <td>
                    <button class="text_button" onclick="edit(<?php echo $ro['ID']; ?>)"> edit</button>
                </td>
                <td>
                    <button class="text_button" onclick="email(<?php echo $ro['ID']; ?>)">Email</button>
                </td>
            </tr>
            <?php

        }
        ?></tbody>
    </table>
    </div>
    <script>
        function edit(id){

            document.location.href = 'monedit.php?id=' + id;
        }
        function del(id) {
            document.location.href = 'mondelete.php?id=' + id;
        }
        function email(id) {
            document.location.href = 'monemail.php?id=' + id;
        }
    </script>
    <?php
}
require_once("includes/footer.php");
?>
