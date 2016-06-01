<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 18/04/2016
 * Time: 15:29
 */
require_once("../database/PdfDB.php");
require_once("../includes/db_functions.php");
$id = $_GET["id"];
PdfDB::deleteBy($id);
header("Location: index.php");