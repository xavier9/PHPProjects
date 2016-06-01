<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 22/04/2016
 * Time: 10:24
 */
class MonitorSection
{
    public static function insert($absence) {

        $SqlQuery = "INSERT INTO b2_monitor_section (`ID`,`Name`, `LastName`,
          `Email`) 
          VALUES ('".$absence['ID']."','".$absence['First_name']."','"
            .$absence['Last_name']."','".$absence["Email"]."')";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function getAll() {
        $SqlQuery = "SELECT * FROM b2_monitor_section;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }


    public static function getID($id) {
        $SqlQuery = "SELECT * FROM b2_monitor_section WHERE
ID='".$id."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function deleteBy($id) {

        $SqlQuery = "DELETE FROM b2_monitor_section WHERE ID = '".$id."';";
        mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function update($message){
        self::deleteBy($message['ID']);



        $SqlQuery = "INSERT INTO b2_monitor_section (`ID`,`Name`, `LastName`,
          `Email`) 
          VALUES ('".$message['ID']."','".$message['Name']."','"
            .$message['LastName']."','".$message["Email"]."')";

        mysqli_query(get_db_connection (),$SqlQuery);
    }
}