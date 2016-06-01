<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 24/02/2016
 * Time: 13:30
 */
class StudentDB
{
    public static function getAllStudents() {
        $SqlQuery = "SELECT * FROM b2_students;";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
}