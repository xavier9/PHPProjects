<?php

/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 17/05/2016
 * Time: 8:40
 */
class IntenciefDB
{


    public static function insert($messages){

      $SqlQuery = "INSERT INTO b2_Intensive (`Teacher_id`, `Start_uur`,
          `Eind_uur`, `Class`
          , `Day`)
          VALUES ('" . $messages["User"]["ID"] . "','"
            . $messages["timepicker_start"] . "','"
            . $messages["timepicker_end"] . "','" . $messages["Class"] . "','"
            . $messages["Day"] . "')";

        return mysqli_query(get_db_connection(), $SqlQuery);
    }
}