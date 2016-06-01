<?php


class UserDB {

    public static function getById($id) {
    	$SqlQuery = "SELECT * FROM b2_teachers WHERE ID = '" .$id. "' UNION SELECT * FROM b2_extra_users WHERE ID = '" .$id. "';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }

	
    public static function getAll() {
    	$SqlQuery = "SELECT * FROM b2_teachers UNION SELECT * FROM b2_extra_users ORDER BY Last_name, First_name";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }

    
}