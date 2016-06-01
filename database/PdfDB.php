<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 15/04/2016
 * Time: 12:40
 */
class PdfDB
{
    public static function getById($id) {
        $SqlQuery = "SELECT * FROM b2_pdfs WHERE ID='". $id."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        if ($result == array()){
            return null;
        }else{
            return $result[0];
        }
    }

    public static function getAll() {
        $SqlQuery = "SELECT * FROM b2_pdfs ";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        if ($result == array()){
            return null;
        }else{
            return $result[0];
        }
    }

    public static function insert($message){
        $SqlQuery = "INSERT INTO b2_pdfs (VragenPDF, AntwoordenPDF, Offset) VALUES ('".$message['VragenPDF']."','".$message['AntwoordenPDF']."','".$message['Offset']."')";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function getIDAll($id) {
        $SqlQuery = "SELECT *
FROM b2_pdfs
WHERE ID = '".$id ."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function deleteBy($id) {
        $SqlQuery = "DELETE FROM b2_pdfs WHERE ID = '".$id."';";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function update($message){
        self::deleteBy($message['ID']);


        $SqlQuery = "INSERT INTO b2_pdfs (ID, VragenPDF, AntwoordenPDF, Offset) VALUES ('".$message['ID']."','".$message['VragenPDF']."','".$message['AntwoordenPDF']."','".$message['Offset']."')";
        mysqli_query(get_db_connection (),$SqlQuery);
    }
}