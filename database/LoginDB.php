<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 23/02/2016
 * Time: 9:01
 */
class LoginDB
{

    public static function getAllUsers() {
        $result = mysqli_query(get_db_connection (),"SELECT *FROM b2_users") or die(mysql_error());
        //$SqlQuery = "SELECT * FROM b2_screen_messages;";
        //$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }


}