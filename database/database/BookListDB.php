<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 22/04/2016
 * Time: 10:24
 */
class BookListDB
{
    public static function insert($absence) {

        $SqlQuery = "INSERT INTO b2_book_list (`ISBN`, `Title`,
          `Authors`, `Publisher`
          , `Year`, `Subject`, `Language`) 
          VALUES ('".$absence['ISBN']."','"
            .$absence['Title']."','".$absence['Author']."','"
            .$absence['Publisher']."','"
            .$absence['Year']."','".$absence['Subject']."','"
            .$absence['Language']."')";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function getAll() {
        $SqlQuery = "SELECT * FROM b2_book_list;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAllLanguage() {
        $SqlQuery = "SELECT DISTINCT Language FROM b2_book_list;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAllYear() {
        $SqlQuery = "SELECT DISTINCT Year FROM b2_book_list;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function getAllVak() {
        $SqlQuery = "SELECT DISTINCT Vak FROM b2_book_list;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAllLanguages($lang) {
        $SqlQuery = "SELECT * FROM b2_book_list
 WHERE Language='".$lang."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAllLanguageYear($lang, $year) {
        $SqlQuery = "SELECT * FROM b2_book_list 
WHERE Language='".$lang."'
        && Year=".$year.";";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getAllLanguageVak($lang, $vak) {
        $SqlQuery = "SELECT * FROM b2_book_list WHERE
Language='".$lang."' && Vak='".$vak."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
    public static function getID($id) {
        $SqlQuery = "SELECT * FROM b2_book_list WHERE
ID='".$id."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function deleteBy($id) {
        $SqlQuery = "DELETE FROM b2_book_list WHERE ID = '".$id."';";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function update($message){
        self::deleteBy($message['ID']);




            $SqlQuery = "INSERT INTO b2_book_list (`ID`, `ISBN`, `Title`,
          `Authors`, `Publisher`
          , `Year`, `Subject`, `Language`) 
          VALUES ('".$message['ID']."','".$message['ISBN']."','"
                .$message['Title']."','".$message['Authors']."','"
                .$message['Publisher']."','"
                .$message['Year']."','".$message['Subject']."','"
                .$message['Language']."')";
            mysqli_query(get_db_connection (),$SqlQuery);
        }
}