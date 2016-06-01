<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 21/04/2016
 * Time: 12:57
 */

$page_title = "Booklist";
require_once("includes/startsession.php");
require_once("includes/db_functions.php");
require_once("includes/header.php");
require_once("database/BookListDB.php");
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

$array = @file_get_contents("http://www.lookupbyisbn.com/Search/Book/".$id."/1");


// Get thumbanil
preg_match("/<div[^>]*id=\"main\">(.*?)<\\/div>/si", $array, $match);
if(isset($match[1])) {
    $thumbnail = $match[1];


    $rep = str_replace('<h3>', "", $match[1]);
    $reps = str_replace('</h3>', "</br>", $rep);
    $rep1 = str_replace('Search in Books for: ', "ISBN ", $reps);
    $rep4 = str_replace('<span style="font-weight:normal; font-style:italic;">', "", $rep1);
    $rep2 = str_replace('</span>', "", $rep4);
    $rep3 = str_replace('<p>', "", $rep2);
    $rep5 = str_replace('</p>', "</br>", $rep3);
    $rep6 = str_replace('<ul style="list-style-type:none;">', "", $rep5);
    $rep7 = str_replace('</ul>', "", $rep6);
    $rep8 = str_replace('<li style="margin-bottom:1em;">', "", $rep7);
    $rep9 = str_replace('</li>', "", $rep8);
    $rep10 = str_replace('<b>', "", $rep9);
    $rep11 = str_replace('<u>', "", $rep10);
    $rep12 = str_replace('</u>', "", $rep11);
    $rep13 = str_replace('<span>', "", $rep12);
    $rep14 = str_replace('</span>', "", $rep13);
    $rep15 = str_replace(',', "</br>", $rep14);
    $rep16 = str_replace('&nbsp;', "", $rep15);
    $rep17 = str_replace('<i>', "", $rep16);
    $rep18 = str_replace('</i>', "", $rep17);
    $rep19 = str_replace('<a href="/Lookup/Book/', "", $rep18);
    $rep20 = str_replace('" title="', "</br>", $rep19);
    $rep21 = str_replace('">', "</br>", $rep20);
    $rep22 = str_replace('</b>', "", $rep21);
    $rep23 = str_replace('</a>', "", $rep22);
    $rep24 = str_replace('Details for ', "", $rep23);
    $rep25 = str_replace('<ul id="pricesinlist', "", $rep24);
    $rep26 = str_replace('<li>', "", $rep25);
    $rep27 = str_replace('</li>', "", $rep26);
    $rep28 = str_replace('<span class=', "", $rep27);
    $rep29 = str_replace('</span>', "", $rep28);
    $split = explode('</br>', $rep29);
    $resp1 = str_replace('\r', "", json_encode($split));
    $resp2 = str_replace('\t', "", $resp1);
    $resp3 = str_replace('\n', "", $resp2);
    $resp4 = str_replace(':', "", $resp3);
    $resp5 = str_replace('<ui id="plicesinlist', "", $resp4);
    $dec = json_decode($resp5);
    $arrs = str_split($dec[7]);
    if (!is_numeric($arrs[1])) {
        if (is_numeric(str_split($dec[10]))) {
            $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                'nr1' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                'Author' => $dec[7], 'Publisher' => $dec[8], 'Publishdate' => $dec[10]);
        } else {
            if ($dec[3] != $dec[5]) {
                if (is_numeric(str_split($dec[8]))) {
                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                        'nr2' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                        'Author' => $dec[5] . "," . $dec[6] . "," . $dec[7] . "," . $dec[8], 'Publisher' => $dec[9], 'Publishdate' => $dec[10]);
                } else {
                    if (is_numeric(str_split($dec[6]))) {
                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                            'nr3' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                            'Author' => $dec[5] . "," . $dec[6], 'Publisher' => $dec[7], 'Publishdate' => $dec[8]);
                    } else {
                        if ($dec[3] != $dec[6]) {
                            if (is_numeric(str_split($dec[5]))) {

                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                    'nr4' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                                    'Author' => '', 'Publisher' => $dec[5], 'Publishdate' => $dec[6]);

                            } else {
                                //$te = str_split($dec[5]);
                                if (is_numeric(str_split($dec[10]))) {

                                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                        'nr5' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                        'Author' => $dec[5] . "," . $dec[6] . "," . $dec[7], 'Publisher' => $dec[8], 'Publishdate' => $dec[9]);
                                } else {
                                    $tes = str_split($dec[6]);
                                    if ($tes[0] != "<") {
                                        if (is_numeric(str_split($dec[9]))) {
                                            $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                'nr12' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                'Author' => $dec[5] . "," . $dec[6] . "," . $dec[7] . "," . $dec[8], 'Publisher' => $dec[9], 'Publishdate' => $dec[10]);
                                        } else {
                                            if (is_numeric(str_split($dec[8]))) {
                                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                    'nr14' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                    'Author' => $dec[5] . "," . $dec[6] . "," . $dec[7], 'Publisher' => $dec[8], 'Publishdate' => $dec[9]);
                                            } else {
                                                if (!is_numeric(str_split($dec[14]))) {
                                                    if (is_numeric(str_split($dec[9]))) {

                                                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                            'nr15' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                            'Author' => "", 'Publisher' => $dec[13], 'Publishdate' => $dec[14]);
                                                    } else {
                                                        if (is_numeric(str_split($dec[6]))) {
                                                            $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                'nr18' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                'Author' => $dec[7], 'Publisher' => $dec[8], 'Publishdate' => $dec[9]);
                                                        } else {
                                                            if (is_numeric(str_split($dec[14]))) {
                                                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                    'nr19' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                    'Author' => "", 'Publisher' => $dec[5], 'Publishdate' => $dec[6]);
                                                            } else {
                                                                if ($dec[3] == $dec[5]) {
                                                                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                        'nr20' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                        'Author' => "", 'Publisher' => $dec[13], 'Publishdate' => $dec[14]);
                                                                } else {
                                                                    if (is_numeric(str_split($dec[9]))) {
                                                                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                            'nr21' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                            'Author' => "", 'Publisher' => "", 'Publishdate' => "");
                                                                    } else {
                                                                        if (!is_numeric(str_split($dec[8]))) {
                                                                            if (is_numeric(str_split($dec[7]))) {
                                                                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                                    'nr22' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                                    'Author' => $dec[5] . ',' . $dec[6] . ',' . $dec[7] . ',' . $dec[8], 'Publisher' => $dec[9], 'Publishdate' => $dec[10]);
                                                                            } else {
                                                                                if(is_numeric(str_split($dec[6]))) {
                                                                                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                                        'nr31' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                                        'Author' => $dec[5] . ',' . $dec[6], 'Publisher' => $dec[7], 'Publishdate' => $dec[8]);
                                                                                }else{
                                                                                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                                        'nr33' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                                        'Author' => ""  , 'Publisher' => $dec[5], 'Publishdate' => $dec[6]);

                                                                                }
                                                                            }
                                                                        } else {
                                                                            $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                                                'nr32' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                                                'Author' => $dec[5] . ',' . $dec[6], 'Publisher' => $dec[7], 'Publishdate' => $dec[8]);

                                                                        }
                                                                    }
                                                                }
                                                            }

                                                        }
                                                    }
                                                } else {
                                                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                                        'nr16' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                                        'Author' => "", 'Publisher' => $dec[5], 'Publishdate' => $dec[6]);

                                                }
                                            }
                                        }
                                    } else {
                                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                            'nr13' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
                                            'Author' => "", 'Publisher' => $dec[5], 'Publishdate' => "");

                                    }
                                }
                            }
                        } else {
                            if ($dec[9] != ' ,') {
                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                    'nr6' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                                    'Author' => $dec[9] . "," . $dec[10], 'Publisher' => $dec[11], 'Publishdate' => $dec[12]);
                            } else {
                                $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                                    'nr7' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                                    'Author' => "", 'Publisher' => "", 'Publishdate' => "");

                            }
                        }
                    }
                }
            } else {
                if (is_numeric(str_split($dec[9]))) {
                    $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                        'nr8' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                        'Author' => $dec[7] . "," . $dec[8], 'Publisher' => $dec[9], 'Publishdate' => $dec[10]);
                } else {
                    $tes = str_split($dec[8]);
                    if ($tes[0] != "<") {
                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                            'nr9' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                            'Author' => $dec[7], 'Publisher' => $dec[8], 'Publishdate' => $dec[9]);
                    } else {
                        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
                            'nr9' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4], 'id' => $dec[5],
                            'Author' => "", 'Publisher' => $dec[7], 'Publishdate' => "");

                    }
                }
            }
        }
    } else {

        $arg = array('ISBN' => $dec[0], 'Display' => $dec[1],
            'nr10' => $dec[2], 'Title' => $dec[3], 'Title1' => $dec[4],
            'Author' => $dec[5], 'Publisher' => $dec[6], 'Publishdate' => $dec[7]);


    }


//echo json_encode($split);

    echo json_encode($arg);

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

                echo $arg['Author'];
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
        $lang = array("Common", "En","Fr","Sw","De","Fi","It", "Nl", "Pt");
        ?>
        <tr>
            <td>
                Section:
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
}else{
    $sort = str_replace('-', "", $isbn);
    $urls = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $sort;
    $datas = file_get_contents($urls);

    $argss = json_decode($datas, true);
   $datass = file_get_contents($argss['items'][0]['selfLink']);
    $argsss =  json_decode($datass, true);

    if($argss['totalItems'] != 0) {

        ?>
        <div id="content">

            <?php echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "?ID=" . $isbn . "'>"; ?>
            <table>
                <tbody>
                <tr>
                    <td>
                        <label>ISBN</label>
                    </td>
                    <td>
                        <input type="text" id="ID" name="ID" value="<?php echo $isbn; ?>" width="50" maxlength="50"
                               size="50">
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
                        echo $argss['items'][0]['volumeInfo']['title'] . ":" . $argss['items'][0]['volumeInfo']['subtitle']; ?>
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
                        echo @implode(",", $argss['items'][0]['volumeInfo']['authors']); ?>
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

                        echo $argsss['volumeInfo']['publisher'];
                        //echo $dec[10];

                        ?>
            " width="50" maxlength="50" size="50"></td>
                </tr>


                <?php
                $year = array(1, 2, 3, 4, 5, 6, 7);
                ?>
                <tr>
                    <td>
                        Year:
                    </td>
                    <td>
                        <select name="year_id">
                            <option value=""></option>
                            <?php
                            foreach ($year as $y) {
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
                $lang = array("Commen", "En", "Fr", "Sv", "De", "Fi", "It", "Nl", "Pt");
                ?>
                <tr>
                    <td>
                        Language:
                    </td>
                    <td>
                        <select name="Language_id">
                            <option value=""></option>
                            <?php
                            foreach ($lang as $l) {
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
        <?php echo "</form>" ?><?php
    }else{
        ?>
<div id="content">

    <?php echo "<form method='post' action='" . $_SERVER["PHP_SELF"] . "?ID=" . $isbn . "'>"; ?>
    <table>
        <tbody>
        <tr>
            <td>
                <label>ISBN</label>
            </td>
            <td>
                <input type="text" id="ID" name="ID" value="<?php echo $isbn; ?>" width="50" maxlength="50"
                       size="50">
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
                echo ""; ?>
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
                echo ""; ?>
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

                echo "";
                //echo $dec[10];

                ?>
            " width="50" maxlength="50" size="50"></td>
        </tr>


        <?php
        $year = array(1, 2, 3, 4, 5, 6, 7);
        ?>
        <tr>
            <td>
                Year:
            </td>
            <td>
                <select name="year_id">
                    <option value=""></option>
                    <?php
                    foreach ($year as $y) {
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
        $lang = array("Commen", "En", "Fr", "Sv", "De", "Fi", "It", "Nl", "Pt");
        ?>
        <tr>
            <td>
                Language:
            </td>
            <td>
                <select name="Language_id">
                    <option value=""></option>
                    <?php
                    foreach ($lang as $l) {
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
<?php echo "</form>";
    }
}






require_once("includes/footer.php");

?>