<?php
require_once("includes/startsession.php");
$page_title = "Monitor";
require_once("includes/header.php");
require_once ("database/MonitorSection.php");
if(!empty($_POST['submit'])){
echo "Submit";
echo $messages["ID"] = $_POST["id"];
echo $messages["Name"] = $_POST["name"];
echo $messages["LastName"] = $_POST["lname"];
echo  $messages['Email'] = $_POST["email"];
/*$SqlQuery = "INSERT INTO b2_book_list (`ISBN`, `Title`,
`Authors`, `Publisher`
, `Year`, `Subject`, `Language`)
VALUES ('".$messages['ISBN']."','"
.$messages['Title']."','".$messages['Author']."','"
.$messages['Publisher']."','"
.$messages['Year']."','".$messages['Subject']."','"
.$messages['Language']."')";
echo mysqli_query(get_db_connection (),$SqlQuery);*/
MonitorSection::update($messages);
//echo "test";
header("Location: monitor.php");

}
//echo $_GET['id'];
$res = MonitorSection::getID($_GET['id']);
//echo mysqli_num_rows($res);



echo "<form method='post' action='".$_SERVER["PHP_SELF"]."?id=".$_GET['id']."'>";
if (mysqli_num_rows($res) > 0) {
    while ($ro = mysqli_fetch_array($res)) {
    ?>
    <div id="content">
        <table>
            <tr>
                <td>
                    <label>
                        ID:
                    </label>
                </td>
                <td>
                    <input style="text" id="id" name="id" value="<?php echo $ro['ID']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        Name:
                    </label>
                </td>
                <td>
                    <input style="text" id="name" name="name" value="<?php echo $ro['Name']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        Sur Name:
                    </label>
                </td>
                <td>
                    <input style="text" id="lname" name="lname" value="<?php echo $ro['LastName']; ?>">
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        Email:
                    </label>
                </td>
                <td>
                    <input style="text" id="email" name="email" value="<?php echo $ro['Email']; ?>">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" value="submit" id="submit" name="submit" class="text_button">
                </td>
            </tr>
        </table>
    </div>
<?php
    }
}
echo "</form>";

require_once("includes/footer.php");
?>
