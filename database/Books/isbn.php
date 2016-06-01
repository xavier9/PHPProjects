<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 21/04/2016
 * Time: 12:57
 */

$page_title = "Booklist";
//require_once("../includes/startsession.php");
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
$id = $_GET['ID'];
if(!empty($_POST['submit'])){
    echo "Submit";
    if(!empty($_POST['Title'])
        && !empty($_POST['year_id'])
        && !empty($_POST['Language_id'])){
        echo $messages["ISBN"] = $id;
        echo $messages["Title"] = $_POST["Title"];
        echo $messages["Author"] = $_POST["Author"];
        echo $messages["Publisher"] = $_POST["Publisher"];
        echo $messages["Year"] = $_POST["year_id"];
        echo $messages["Subject"] = $_POST["Subject"];
        echo $messages["Language"] = $_POST["Language_id"];
        /*$SqlQuery = "INSERT INTO b2_book_list (`ISBN`, `Title`,
          `Authors`, `Publisher`
          , `Year`, `Subject`, `Language`)
          VALUES ('".$messages['ISBN']."','"
            .$messages['Title']."','".$messages['Author']."','"
            .$messages['Publisher']."','"
            .$messages['Year']."','".$messages['Subject']."','"
            .$messages['Language']."')";
        echo mysqli_query(get_db_connection (),$SqlQuery);*/
        BookListDB::insert($messages);
        //echo "test";
        header("Location: booklist.php");
    }
}



$result =  mysqli_query(get_db_connection (),$SqlQuery);



$isbn = $id;
// Looking for the ISBN - we use isbnsearch.org
$url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;

echo $data = file_get_contents($url);
$args = json_decode($data, true);
echo $datas = file_get_contents($args['items'][0]['selfLink']);
$argss =  json_decode($datas, true);
echo $argss['volumeInfo']['publisher'];
?>
    <div id="content">

        <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."?ID=".$isbn."'>";?>
        <table>
            <tbody>
            <tr>
                <td>
                    <label>ISBN</label>
                </td>
                <td>
                    <input type="text" id="ID" name="ID" value="<?php echo $isbn; ?>" width="50" maxlength="50" size="50">
                </td>
            </tr>
            <tr>
                <td>
                    <label>
                        Title
                    </label>
                </td>
                <td>
                    <input type="text" id="Title" name="Title" value="<?php
                    echo $args['items'][0]['volumeInfo']['title'];?>
    " width="50" maxlength="100" size="50"></td>
            </tr>

            <tr>
                <td>
                    <label>
                        Authors
                    </label>
                </td>
                <td>
                    <input type="text" id="Author" name="Author" value="<?php
                    echo @implode(",", $args['items'][0]['volumeInfo']['authors']); ?>
                    " width="50" maxlength="50" size="50"></td>
            </tr>

            <tr>
                <td>
                    <label>
                        Publisher
                    </label>
                </td>
                <td>
                    <input type="text" id="Publisher" name="Publisher" value="<?php

                    echo $args['items'][0]['volumeInfo']["publisher"];
                    //echo $dec[10];

                    ?>
            " width="50" maxlength="50" size="50"></td>
            </tr>


            <?php
            $year = array(1,2,3,4,5,6,7);
            ?>
            <tr>
                <td>
                    Year:
                </td>
                <td>
                    <select name="year_id">
                        <option value=""></option>
                        <?php
                        foreach($year as $y){
                            ?>
                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    Subject:
                </td>
                <td>

                    <input name="Subject" type="text" id="Subject" value="" width="50" maxlength="50" size="50">


                </td>
            </tr>
            <?php
            $lang = array("Commen", "En","Fr","Sv","De","Fi","It", "Nl", "Pt");
            ?>
            <tr>
                <td>
                    Language:
                </td>
                <td>
                    <select name="Language_id">
                        <option value=""></option>
                        <?php
                        foreach($lang as $l){
                            ?>
                            <option value="<?php echo $l; ?>"><?php echo $l; ?></option>
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
            </tbody>
        </table>
    </div>
<?php echo "</form>"?>
<?php




require_once("../includes/footer.php");

echo phpinfo(true);
?>