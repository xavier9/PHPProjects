<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 21/04/2016
 * Time: 12:57
 */



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


/*
Plugin Name: WP ISBN
Plugin URI: http://wordpress.org/extend/plugins/wp-isbn/
Description: Allows you use ISBN shortcode and maintain a personal library in your WordPress.
Author: bi0xid, pixelatedheart
Author URI: http://bi0xid.es/
Version: 0.1
Text Domain: wp-isbn
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/***** First version - shortcode ******/
// [isbn 1234567890123]


        $isbn = $id;
        // Looking for the ISBN - we use isbnsearch.org
        $url = 'http://www.isbnsearch.org/isbn/' . $isbn;
         $data = file_get_contents($url);

        // Get thumbanil
        preg_match("/<div[^>]*class=\"thumbnail\">(.*?)<\\/div>/si", $data, $match);
        $thumbnail = $match[1];

        // Get name of the book
        preg_match('#<h2>(.*?)</h2>#is', $data, $match);
        //echo $title = $match[1];
        //echo json_encode($title);
        preg_match('/<div[^>]*class="bookinfo">(.*?)<\\/div>/si', $data, $match);
        //echo $match[1];
         $rep = str_replace('<p>', "", $match[1]);
        $reps = str_replace( '</p>',"</br>",$rep);
        $rep1= str_replace('<h2>', "", $reps);
        $rep4= str_replace('</h2>', "</br>", $rep1);
        $rep2= str_replace('<strong>', "", $rep4);
        $rep3 =str_replace('</strong>', "</br>", $rep2);
        $rep5 =str_replace('<a href="/isbn/', "", $rep3);
$rep6 =str_replace('">', "", $rep5);
        $rep7 =str_replace('</a>', "", $rep6);
        $split = explode('</br>',$rep7);
$resp1 =str_replace('\r', "", json_encode($split));
$resp2 = str_replace('\t', "", $resp1);
$resp3 = str_replace('\n', "", $resp2);
$resp4 = str_replace(':', "", $resp3);
$dec = json_decode($resp4);
$arg = array('Title' => $dec[0],$dec[1] => $dec[2], $dec[3] => $dec[4],
    $dec[5] => $dec[6], $dec[7] => $dec[8], $dec[9] => $dec[10], $dec[11] => $dec[12],
    $dec[13] => $dec[14]);
//echo json_encode($split);
echo json_encode($arg);


?>
    <div id="content">
        <?php echo $arg[0];?>
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
                    echo $arg['Title'];?>
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

                    echo $arg['Author'];echo $arg['Authors'];
                    ?> " width="50" maxlength="50" size="50"></td>
            </tr>

            <tr>
                <td>
                    <label>
                        Publisher
                    </label>
                </td>
                <td>
                    <input type="text" id="Publisher" name="Publisher" value="<?php

                        echo $arg['Publisher'];
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
?>