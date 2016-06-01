<?php

// To use the etranet DB instead, uncomment the bottom two functions,
// replace get_db_connection () with get_teacher_db_connection (),
// and replace b2_teachers with B2_teachers

/*
	function get_teacher_db_connection () {
		$conn = mysqli_connect("10.1.0.53", "root", "naibojei", "Extranet");
		mysqli_query($conn, "SET NAMES utf8");
		return $conn;
	}
	
	function get_teacher_values ($resultset) {
		$data = array();
		for ($i=0; $i<mysqli_num_rows($resultset); $i++) {
			$row = mysqli_fetch_assoc($resultset);
			array_push($data,$row);
		}
		return $data;
	}
	*/
class TeacherDB {

    public static function getById($id) {
    	$SqlQuery = "SELECT * FROM b2_teachers WHERE ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	$teacher = $result[0];
			return $teacher;
		}
    }

    public static function getAll() {
		$SqlQuery = "SELECT * FROM b2_teachers ORDER BY Last_name;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
    


}
