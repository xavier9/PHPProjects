
<html>
<head>
    <?php
    /**
     * Created by IntelliJ IDEA.
     * User: xavier
     * Date: 18/04/2016
     * Time: 8:48
     */
    require_once("../includes/db_functions.php");
    require_once("../database/PdfDB.php");
    require_once("../includes/header.php");
    $messages = array();

    ?>
    <?php
if(!empty($_POST["question"])&& !empty($_POST["answer"]) && !empty($_POST["offset"])){
     $messages["VragenPDF"] = $_POST["question"];
     $messages["AntwoordenPDF"] = $_POST["answer"];
     $messages["Offset"] = $_POST["offset"];
    if($messages["VragenPDF"] != null) {
        PdfDB::insert($messages);
        header("Location: index.php");
    }else{
        echo "Test";
    }
    }
    ?>
</head>
<body>
<div class="page">

    <?php echo "<form method='post' action='".$_SERVER["PHP_SELF"]."'>";?>
        <div id="content">
        <table class="form">
            <tr>
                <td>
                    <label>question pdf </label>
                </td>
                <td>
                    <input type="text" name="question" id="question">
                </td>
            </tr>
            <tr>
                <td>
                    <label>answer pdf </label>
                </td>
                <td>
                    <input type="text"  name="answer" id="answer"/>
                </td>
            </tr>
            <tr>
                <td>
                    <label>offset </label>
                </td>
                <td>
                    <input type="text" name="offset" id="offset"/>


                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit"  value="Add"/>



                </td>

                <td>
                    <input type="submit" value="Back"/>

                </td>
            </tr>
        </table>
        </div>
<?php echo "</form>"?>


    </div>
</body>
</html>
