<?php
/* General */
function get_db_connection () {
	static $connection;
	if(!isset($connection)) {
		$config = parse_ini_file('config.ini');
		$conn = mysqli_connect($config["server"], $config["username"], $config["password"], $config["dbname"]);
		mysqli_query($conn, "SET NAMES utf8");
	}
	if($connection === false) {
		return mysqli_connect_error();
	}
	return $conn;
}

function get_ldap_connection () {
	$config = parse_ini_file('config.ini');
	$ldap_connection = ldap_connect($config["ldap_host"], $config["ldap_port"]) or die("Cannot connect to ldap !");
	return $ldap_connection;
}

function get_values ($resultset) {
	$data = array();
	//2echo mysqli_num_rows($resultset);
	for ($i=0; $i<mysqli_num_rows($resultset); $i++) {
		$row = mysqli_fetch_assoc($resultset);
		array_push($data,$row);
	}
	return $data;
}
/* EOF General */
/*----------------------------------------------------------------------------------------------------*/

/* import_sms */
function count_records ($conn, $table) {
	$query = "SELECT count(*) as rows FROM $table";
	$row = mysqli_fetch_assoc(mysqli_query($conn,$query));
	return $row ["rows"];
}

function delete_table ($conn, $table_name) {
	$query = "DELETE FROM $table_name";
	return mysqli_query($conn,$query);
}

function import_to_db ($conn, $data, $table_name, $table_headers) {
	$counter = 0;
	foreach ($data as $value) {
		$query = "INSERT INTO $table_name (" ;
		for ($j=0;$j<count($table_headers);$j++) {
			$query.=$table_headers[$j].",";
		}
		$query=substr($query,0,-1);
		$query.=") VALUES (";
		for ($j=0;$j<count($table_headers);$j++) {
			$query.="'".$value[$table_headers[$j]]."',";
		}
		$query=substr($query,0,-1).")";

		if(mysqli_query($conn,$query))
			$counter++;
	}
	return $counter;
}

function count_courses ($conn) {
	$query = "SELECT count(Code) as count FROM b2_courses";
	$row = mysqli_fetch_assoc(mysqli_query($conn,$query));
	return $row["count"];
}
/* EOF import_sms */
/*----------------------------------------------------------------------------------------------------*/

/* b2_menu */
function get_menu ($conn) {
	$query = "SELECT * FROM b2_menu ORDER BY ID";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_menu */
/*----------------------------------------------------------------------------------------------------*/

/* b2_menu_items */
function get_menu_items ($conn, $id) {
	$query = "SELECT * FROM b2_menu_items WHERE Menu_id = '$id' ORDER BY ID";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_menu_items */
/*----------------------------------------------------------------------------------------------------*/

/* b2_user_rights */
function get_user_rights ($conn, $id) {
	$query = "SELECT * FROM b2_user_rights WHERE ID = '$id'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function insert_user_rights ($conn, $id, $rights) {
	$query = "INSERT INTO b2_user_rights (ID,Rights) VALUES ('".$id."','".$rights."')";
	mysqli_query($conn,$query);
}
/* EOF b2_user_rights */
/*----------------------------------------------------------------------------------------------------*/

/* Login */
function is_teacher ($conn, $id) {
	$query = "SELECT * FROM b2_teachers WHERE ID='".$id."'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)==1)
		return true;
	return false;
}

function get_teacher($conn, $id){

		$query = "SELECT * FROM b2_teachers WHERE ID='".$id."'";
		$resultset = mysqli_query($conn,$query);
		return $resultset;
	
}
/* EOF Login */
/*----------------------------------------------------------------------------------------------------*/


/* b2_students */
function get_all_students ($conn) {
	$query = "SELECT * FROM b2_students ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}

function get_student ($conn, $id) {
	$query = "SELECT * FROM b2_students WHERE ID='$id'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)==1)
		return mysqli_fetch_assoc($resultset);
	return false;
}

function get_all_students_course ($conn,$course) {
	$query = "SELECT DISTINCT s.* FROM b2_students s, b2_student_teacher_course s2
					  WHERE s.ID = s2.Student_id
					  AND s2.Teacher_course_id in (SELECT ID FROM b2_teacher_course WHERE Course_id='".$course["Code"]."') ORDER BY Last_name;";
	return get_values(mysqli_query($conn,$query));
}

function search_students ($conn, $search) {
	$query = "SELECT * FROM b2_students WHERE (Last_name like '%$search%' OR First_name like '%$search%' OR ID like '%$search%' OR Class LIKE '$search%') ORDER BY Last_name";
	return get_values(mysqli_query($conn,$query));
}

function get_students_on_class ($conn, $class) {
	$query = "SELECT * FROM b2_students WHERE Class LIKE '$class%'";
	return get_values(mysqli_query($conn,$query));
}

function get_all_classes ($conn, $cat) {
	$query = "SELECT DISTINCT Class FROM b2_students WHERE Class like '".$cat."%' ORDER BY Class";
	return get_values(mysqli_query($conn,$query));
}

function get_all_sections ($conn) {
	$query = "SELECT DISTINCT SUBSTRING(Class,3,3) as Section FROM b2_students ORDER BY Section";
	return get_values(mysqli_query($conn,$query));
}

function is_class ($conn, $class) {
	$query = "SELECT * FROM b2_students WHERE Class='".$class."'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)>=1)
		return true;
	return false;
}
/* EOF b2_students */
/*----------------------------------------------------------------------------------------------------*/

/* Users */
function get_all_users ($conn) {
	$query = "SELECT * FROM b2_teachers UNION SELECT * FROM b2_extra_users ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}

function get_all_teachers ($conn) {
	$query = "SELECT * FROM b2_teachers ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}

function get_user ($conn, $id) {
	$query = "SELECT * FROM b2_teachers WHERE ID = '$id' UNION SELECT * FROM b2_extra_users WHERE ID = '$id'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function add_user ($conn, $user) {
	$query = "INSERT INTO b2_extra_users (ID,Last_name,First_name,Nationality,Date_of_birth,Sex,Category,Email) VALUES
			('".$user["ID"]."','".$user["Last_name"]."','".$user["First_name"]."','".$user["Nationality"]."','".$user["Date_of_birth"]."','".$user["Sex"]."','".$user["Category"]."','".$user["Email"]."');";
	mysqli_query($conn,$query);
	insert_password ($conn, $user["ID"], $user["Password"]);
	insert_user_rights ($conn, $user["ID"], '');
}

function delete_user ($conn, $id) {
	$query = "DELETE FROM b2_extra_users WHERE ID='".$id."'";
	mysqli_query($conn,$query);
	$query = "DELETE FROM b2_passwords WHERE ID='".$id."'";
	mysqli_query($conn,$query);
	$query = "DELETE FROM b2_user_rights WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function update_user ($conn, $user) {
	$query = "UPDATE b2_teachers SET Last_name='".$user["Last_name"]."', First_name='".$user["First_name"]."', Nationality='".$user["Nationality"]."', Date_of_birth='".$user["Date_of_birth"]."', Sex='".$user["Sex"]."', Category='".$user["Category"]."', Rights='".$user["Rights"]."', Email='".$user["Email"]."' WHERE ID='".$user["ID"]."'";
	mysqli_query($conn,$query);
	$query = "UPDATE b2_extra_users SET Last_name='".$user["Last_name"]."', First_name='".$user["First_name"]."', Nationality='".$user["Nationality"]."', Date_of_birth='".$user["Date_of_birth"]."', Sex='".$user["Sex"]."', Category='".$user["Category"]."', Rights='".$user["Rights"]."', Email='".$user["Email"]."' WHERE ID='".$user["ID"]."'";
	mysqli_query($conn,$query);
}

function is_extra_user ($conn, $username) {
	$query = "SELECT * FROM b2_extra_users WHERE CONCAT(LOWER(First_name),'.',REPLACE(LOWER(Last_name),' ','_'))='".$username."'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)==1)
		return mysqli_fetch_assoc($resultset);
	return false;
}

function search_users ($conn, $search) {
	$query = "SELECT * FROM b2_teachers WHERE ID LIKE '%".$search."%'
			OR Last_name LIKE  '%".$search."%' 
			OR First_name LIKE  '%".$search."%'
			OR Date_of_birth LIKE  '%".$search."%' 
			OR Nationality LIKE  '%".$search."%' 
			OR Sex LIKE  '%".$search."%' 
			OR Category LIKE  '%".$search."%' 
			OR Email LIKE  '%".$search."%' 
			OR ID = '".$search."'
		UNION SELECT * FROM b2_extra_users WHERE ID LIKE '%".$search."%' 
			OR Last_name LIKE  '%".$search."%' 
			OR First_name LIKE  '%".$search."%'
			OR Date_of_birth LIKE  '%".$search."%' 
			OR Nationality LIKE  '%".$search."%' 
			OR Sex LIKE  '%".$search."%' 
			OR Category LIKE  '%".$search."%'
			OR ID = '".$search."'			
			OR Email LIKE  '%".$search."%'  ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}

function get_class_teacher($conn, $class) {
	$query = "SELECT Teacher_id FROM b2_teacher_course WHERE Course_id ='".substr($class,0,2)."mat".substr($class,2,3)."'";
	$row = mysqli_fetch_assoc(mysqli_query($conn, $query));
	$teacher_ids = explode (",",$row["Teacher_id"]);
	if(count($teacher_ids) == 1) {
		if (empty($row["Teacher_id"])) {
			$query = "SELECT Teacher_id FROM b2_teacher_course WHERE Course_id ='".substr($class,0,2)."l1-".substr($class,2,3)."'";
			$row = mysqli_fetch_assoc(mysqli_query($conn, $query));
			$query = "SELECT * FROM b2_teachers WHERE ID ='".$row["Teacher_id"]."'";
			return mysqli_fetch_assoc(mysqli_query($conn,$query));
		}
		else {
			$query = "SELECT * FROM b2_teachers WHERE ID ='".$row["Teacher_id"]."'";
			return mysqli_fetch_assoc(mysqli_query($conn,$query));
		}
	}
	else {
		foreach ($teacher_ids as $teacher_id) {
			$query = "SELECT Teacher_id FROM b2_teacher_course WHERE Course_id ='".substr($class,0,2)."l1-".substr($class,2,3)."'";
			$row = mysqli_fetch_assoc(mysqli_query($conn, $query));
			if($row["Teacher_id"]==$teacher_id) {
				$query = "SELECT * FROM b2_teachers WHERE ID ='".$teacher_id."'";
				return mysqli_fetch_assoc(mysqli_query($conn,$query));
			}
		}
	}
}

function get_extra_users ($conn) {
	$query = "SELECT * FROM b2_extra_users ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}
/* EOF Users */
/*----------------------------------------------------------------------------------------------------*/

/* b2_login */
function get_last_login ($conn, $user) {
	$query = "SELECT * FROM b2_login WHERE ID = '".$user["ID"]."'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)==1)
		return mysqli_fetch_assoc($resultset);
	return false;
}

function insert_last_login ($conn, $user, $datetime) {
	$query = "INSERT INTO b2_login (ID, Last_login) VALUES ('".$user["ID"]."','".$datetime."')";
	mysqli_query($conn,$query);
}

function update_last_login ($conn, $user, $datetime) {
	$query = "UPDATE b2_login SET Last_login = '".$datetime."' WHERE ID = '".$user["ID"]."'";
	mysqli_query($conn,$query);
}
/* EOF b2_login */
/*----------------------------------------------------------------------------------------------------*/

/* b2_passwords */
function update_password($conn, $id, $password) {
	$query = "UPDATE b2_passwords SET Password='".$password."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function get_password ($conn, $id) {
	$query = "SELECT Password FROM b2_passwords WHERE ID = '".$id."'";
	$row = mysqli_fetch_assoc(mysqli_query($conn, $query));
	return $row["Password"];
}

function insert_password ($conn, $id, $password) {
	$query = "INSERT INTO b2_passwords (ID,Password) VALUES ('".$id."','".$password."')";
	mysqli_query($conn,$query);
}
/* EOF b2_passwords */
/*----------------------------------------------------------------------------------------------------*/

/* b2_timetable_templates */
function get_all_templates ($conn) {
	$query = "SELECT * FROM b2_timetable_templates";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template ($conn, $id) {
	$query = "SELECT * FROM b2_timetable_templates WHERE ID ='".$id."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}
/* EOF b2_timetable_templates*/
/*----------------------------------------------------------------------------------------------------*/

/* b2_timetable_template_layout */
function get_template_layout ($conn, $template_id) {
	$query = "SELECT * FROM b2_timetable_template_layout WHERE Template_id ='".$template_id."' ORDER BY Day, Begin";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_timetable_template_layout */
/*----------------------------------------------------------------------------------------------------*/

/* b2_timetable_teachers */
function get_timetable_teachers ($conn) {
	$query = "SELECT * FROM b2_timetable_teachers ORDER BY Last_name, First_name";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_teacher ($conn, $id) {
	$query = "SELECT * FROM b2_timetable_teachers WHERE ID = '$id'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function get_timetable_teacher_on_course ($conn, $course) {
	$query = "SELECT Teacher_id FROM b2_teacher_course WHERE Course_id = '".$course."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function add_timetable_teacher ($conn, $data) {
	$query = "INSERT INTO b2_timetable_teachers (ID,Category,Ready,Forfait,Last_name,First_name,Class) VALUES ('".$data["ID"]."','".$data["Category"]."','".$data["Ready"]."','".$data["Forfait"]."','".$data["Last_name"]."','".$data["First_name"]."','')";
	mysqli_query($conn,$query);
}

function update_timetable_user ($conn, $user) {
	$query = "UPDATE b2_timetable_teachers SET Last_name='".$user["Last_name"]."', First_name='".$user["First_name"]."', Category='".$user["Category"]."', Ready='".$user["Ready"]."', Forfait='".$user["Forfait"]."', Class='".$user["Class"]."', Info ='".$user["Info"]."' WHERE ID='".$user["ID"]."'";
	mysqli_query($conn,$query);
}

function update_timetable_user_submitted ($conn, $user, $date) {
	$query = "UPDATE b2_timetable_teachers SET Submitted='".$date."' WHERE ID='".$user["ID"]."'";
	mysqli_query($conn,$query);
}

function get_id($conn) {
	$query = "SELECT MAX(ID) as id FROM b2_timetable_teachers";
	$data = mysqli_query($conn,$query);
	$row = mysqli_fetch_assoc($data);
	if($row["id"]<3000000)
		return 3000000;
	else
		return $row["id"]+1;
}

function get_info_forfait ($conn) {
	$query = "SELECT * FROM b2_timetable_teachers WHERE Category = 1 ORDER BY Forfait, Last_name";
	return get_values(mysqli_query($conn,$query));
}

function change_timetable_teacher ($conn, $user, $old_user) {
	$query = "UPDATE `b2_timetable_surveillance` SET Teacher_id = '".$user["ID"]."' WHERE Teacher_id = '".$old_user["ID"]."';";
	mysqli_query($conn,$query);
	// echo $query;
	$query = "UPDATE `b2_timetable_template_teacher` SET Teacher_id = '".$user["ID"]."' WHERE Teacher_id = '".$old_user["ID"]."';";
	mysqli_query($conn,$query);
	// echo $query;
	$query = "UPDATE `b2_timetable_teachers` SET ID = '".$user["ID"]."', Last_name = '".$user["Last_name"]."',
			First_name = '".$user["First_name"]."', 
			Category = '".$user["Category"]."'
			WHERE ID = '".$old_user["ID"]."';";
	mysqli_query($conn,$query);
	// echo $query;
}
/* EOF b2_timetable_teachers*/
/*----------------------------------------------------------------------------------------------------*/

/* b2_timetable_template_type */
function get_template_types ($conn) {
	$query = "SELECT Type FROM b2_timetable_template_type ORDER BY Type";
	return get_values(mysqli_query($conn,$query));
}

function get_template_type ($conn,$type) {
	$query = "SELECT * FROM b2_timetable_template_type WHERE Type='".$type."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}
/* EOF b2_timetable_template_type*/
/*----------------------------------------------------------------------------------------------------*/

/*----------------------------------------------------------------------------------------------------*/
/* b2_timetable_surveillance */
function get_timetable_surveillance ($conn, $day, $description) {
	$query = "SELECT * FROM b2_timetable_surveillance WHERE Day='".$day."' AND Description ='".$description."' ORDER BY  Location";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_surveillance_types ($conn) {
	$query = "SELECT DISTINCT Description FROM b2_timetable_surveillance ORDER BY Description";
	return get_values(mysqli_query($conn,$query));
}

function update_timetable_surveillance ($conn, $id, $teacher_id) {
	$query = "UPDATE b2_timetable_surveillance SET Teacher_id='".$teacher_id."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function update_timetable_surveillance_on_location ($conn, $teacher_id, $day, $location) {
	$query = "UPDATE b2_timetable_surveillance SET Teacher_id='".$teacher_id."' WHERE Day='".$day."' AND Location='".$location."'";
	mysqli_query($conn,$query);
}

function get_timetable_surveillance_on_id ($conn, $id) {
	$query = "SELECT * FROM b2_timetable_surveillance WHERE ID='".$id."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function remove_timetable_surveillance_on_teacher_id($conn, $teacher_id) {
	$query = "UPDATE b2_timetable_surveillance SET Teacher_id='0' WHERE Teacher_id='".$teacher_id."'";
	mysqli_query($conn,$query);
}

function get_surveillances_locations($conn, $begin){
	$query = "SELECT DISTINCT Location FROM b2_timetable_surveillance WHERE Begin = '".$begin."' ORDER BY Location";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_timetable_surveillance */
/*----------------------------------------------------------------------------------------------------*/

/* b2_timetable_bus_maternelle */
function get_timetable_bus_mat ($conn, $day, $description) {
	$query = "SELECT * FROM b2_timetable_bus_maternelle WHERE Day='".$day."' AND Description ='".$description."' ORDER BY  Location";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_bus_mat_types ($conn) {
	$query = "SELECT DISTINCT Description FROM b2_timetable_bus_maternelle ORDER BY Description";
	return get_values(mysqli_query($conn,$query));
}

function update_timetable_bus_mat ($conn, $id, $teacher_id) {
	$query = "UPDATE b2_timetable_bus_maternelle SET Teacher_id='".$teacher_id."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function update_timetable_bus_mat_on_location ($conn, $teacher_id, $day, $location) {
	$query = "UPDATE b2_timetable_bus_maternelle SET Teacher_id='".$teacher_id."' WHERE Day='".$day."' AND Location='".$location."'";
	mysqli_query($conn,$query);
}

function get_timetable_bus_mat_on_id ($conn, $id) {
	$query = "SELECT * FROM b2_timetable_bus_maternelle WHERE ID='".$id."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function remove_timetable_bus_mat_on_teacher_id($conn, $teacher_id) {
	$query = "UPDATE b2_timetable_bus_maternelle SET Teacher_id='0' WHERE Teacher_id='".$teacher_id."'";
	mysqli_query($conn,$query);
}

function get_bus_mat_locations($conn, $begin){
	$query = "SELECT DISTINCT Location FROM b2_timetable_bus_maternelle WHERE Begin = '".$begin."' ORDER BY Location";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_timetable_bus_maternelle */
/*----------------------------------------------------------------------------------------------------*/





/* b2_timetable_template_teacher*/
function get_timetable_template_teacher_by_day_and_begin ($conn, $teacher_id, $day, $begin) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Teacher_id ='".$teacher_id."' AND Day ='".$day."' AND Begin ='".$begin."' ORDER BY Begin, Course";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_teacher_by_period_on_class ($conn, $teacher_id, $day, $begin, $class) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Teacher_id ='".$teacher_id."' AND Day ='".$day."' AND Begin ='".$begin."' AND Location = '".$class."' ORDER BY Begin, Course";
	return get_values(mysqli_query($conn,$query));
}

function get_eur_timetable_template ($conn) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Type = 'eur' ORDER BY Teacher_id, Begin";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_teacher_by_day ($conn, $teacher_id, $day) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Teacher_id ='".$teacher_id."' AND Day ='".$day."' ORDER BY Begin, Course";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_course_by_day ($conn, $course, $day) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Course = '".$course."' AND Day ='".$day."' ORDER BY Begin";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_teacher_by_location ($conn, $location, $day) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Location ='".$location."' AND Day ='".$day."' ORDER BY Begin, Course";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_teacher_by_day_on_id ($conn, $id) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE ID ='".$id."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function get_timetable_template_teacher_on_course ($conn, $course) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Course ='".$course."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function get_timetable_template_teacher_on_course_2 ($conn, $course) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Course ='".$course."'";
	return get_values(mysqli_query($conn,$query));
}

function add_timetable_template_teacher ($conn, $data) {
	$query = "INSERT INTO b2_timetable_template_teacher (Teacher_id,Day,Begin,End,Type,Location,Course) VALUES ('".$data["Teacher_id"]."','".$data["Day"]."','".$data["Begin"]."','".$data["End"]."','".$data["Type"]."','".$data["Location"]."','";
	if(isset($data["Course"]))
		$query.= $data["Course"];
	$query.= "');";

	mysqli_query($conn,$query);
}

function update_timetable_template_teacher_type ($conn, $id, $type) {
	$query = "UPDATE b2_timetable_template_teacher SET Type='".$type."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function update_timetable_template_teacher_location ($conn, $id, $location) {
	$query = "UPDATE b2_timetable_template_teacher SET Location='".$location."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function update_timetable_template_teacher_course ($conn, $id, $course) {
	$query = "UPDATE b2_timetable_template_teacher SET Course='".$course."' WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function delete_timetable_template_teacher_on_id ($conn, $teacher_id) {
	$query = "DELETE FROM b2_timetable_teachers WHERE ID='".$teacher_id."'";
	mysqli_query($conn,$query);
}

function delete_block_timetable_template_teacher ($conn, $id) {
	$query = "DELETE FROM b2_timetable_template_teacher WHERE ID='".$id."'";
	mysqli_query($conn,$query);
}

function delete_timetable_template_teacher ($conn, $teacher_id, $day, $begin, $end) {
	$query = "DELETE FROM b2_timetable_template_teacher WHERE Teacher_id='".$teacher_id."' AND Day='".$day."' AND Begin='".$begin."' AND End='".$end."'";
	mysqli_query($conn,$query);
}

function delete_timetable_template_teacher_on_type ($conn, $teacher_id, $type) {
	$query = "DELETE FROM b2_timetable_template_teacher WHERE Teacher_id='".$teacher_id."' AND Type='".$type."'";
	mysqli_query($conn,$query);
}

function remove_timetable_template_teacher ($conn, $teacher_id) {
	$query = "DELETE FROM b2_timetable_template_teacher WHERE Teacher_id ='".$teacher_id."' ";
	mysqli_query($conn,$query);
}

function block_exists ($conn, $teacher_id, $day, $begin, $end) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Teacher_id='".$teacher_id."' AND Begin='".$begin."' AND Day='".$day."' AND End ='".$end."'";
	$resultset = mysqli_query($conn,$query);
	if(mysqli_num_rows($resultset)==1)
		return mysqli_fetch_assoc($resultset);
	return -1;
}

function get_all_timetables_template_teacher_on_location ($conn, $location) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Location = '$location' ORDER BY Day, Begin";
	return get_values(mysqli_query($conn,$query));
}

function get_timetable_template_teacher_on_class ($conn, $location) {
	$query = "SELECT * FROM b2_timetable_template_teacher WHERE Location ='".$location."' ORDER BY Begin";
	return get_values(mysqli_query($conn,$query));
}
/*EOF b2_timetable_template_teacher*/
/*----------------------------------------------------------------------------------------------------*/

/* b2_courses */
function get_course ($conn,$id) {
	$query = "SELECT * FROM b2_courses WHERE Code = '$id'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function get_all_teacher_courses ($conn, $user) {
	$query = "SELECT DISTINCT Code, Description FROM b2_courses WHERE Code in (SELECT Course_id FROM b2_teacher_course WHERE Teacher_id = '".$user["ID"]."' OR Teacher_id LIKE '".$user["ID"].",%' OR Teacher_id LIKE '%,".$user["ID"]."' OR Teacher_id LIKE '%,".$user["ID"].",%') ORDER BY Code";
	return get_values(mysqli_query ($conn,$query));
}

function get_eur_teacher_courses ($conn, $user) {
	$query = "SELECT DISTINCT Code, Description FROM b2_courses WHERE Code in (SELECT Course_id FROM b2_teacher_course WHERE (Teacher_id = '".$user["ID"]."' OR Teacher_id LIKE '".$user["ID"].",%' OR Teacher_id LIKE '%,".$user["ID"]."' OR Teacher_id LIKE '%,".$user["ID"].",%') AND SUBSTRING(Course_id,3,3)='eur') ORDER BY Code";
	return get_values(mysqli_query ($conn,$query));
}

function get_eur_courses ($conn) {
	$query = "SELECT * FROM b2_teacher_course WHERE Course_id LIKE '%eur%'";
	return get_values(mysqli_query ($conn,$query));
}

function get_all_student_teacher_courses_on_student ($conn, $student) {
	$query = "SELECT * FROM b2_teacher_course WHERE ID in (SELECT Teacher_course_id FROM b2_student_teacher_course WHERE Student_id = '".$student["ID"]."') ORDER BY Course_id;";
	return get_values(mysqli_query ($conn,$query));
}

function get_all_student_teacher_courses_on_student_on_subject ($conn, $student, $subject) {
	$query = "SELECT * FROM b2_teacher_course WHERE ID in (SELECT Teacher_course_id FROM b2_student_teacher_course WHERE Student_id = '".$student["ID"]."') AND SUBSTRING(Course_id,3,3)='".$subject."' ORDER BY Course_id;";
	return get_values(mysqli_query ($conn,$query));
}

function get_course_on_teacher_subject ($conn, $teacher, $course) {
	$query = "SELECT * FROM b2_courses WHERE Code in (SELECT Course_id FROM b2_teacher_course
		WHERE (Teacher_id = '".$teacher["ID"]."' 
		OR Teacher_id LIKE '%,".$teacher["ID"]."' 
		OR Teacher_id LIKE '".$teacher["ID"].",%' 
		OR Teacher_id LIKE '%,".$teacher["ID"].",%' )
		AND Course_id LIKE '%$course%') ORDER BY Code;";
	return get_values(mysqli_query ($conn,$query));
}
/*EOF b2_courses*/
/*----------------------------------------------------------------------------------------------------*/

/* photobook */
function get_all_years ($conn, $value="") {
	$query = "SELECT DISTINCT SUBSTR(Class,1,2) as Class FROM b2_students WHERE Class like '".$value."%' ORDER BY Class";
	return get_values(mysqli_query($conn,$query));
}

function get_all_types_of_courses ($conn, $year) {
	$query = "SELECT DISTINCT(SUBSTRING(Code,3,3)) AS Code FROM b2_courses WHERE Code like '$year%'";
	return get_values(mysqli_query($conn,$query));
}

function get_all_courses_on_value ($conn, $value) {
	$query = "SELECT Code FROM b2_courses WHERE Code like '$value%' or Code like '".substr($value,0,2)."%".substr($value,3)."'";
	return get_values(mysqli_query($conn,$query));
}
/*EOF photobook*/
/*----------------------------------------------------------------------------------------------------*/

/* b2_absences */
function add_absence ($conn, $teacher, $student_id, $date, $day, $justification) {
	$query = "INSERT INTO b2_absences (Teacher_id, Student_id, Date_of_absence, Day, Justification) VALUES ('".$teacher["ID"]."','".$student_id."','".$date."','".$day."','".$justification."');";
	mysqli_query($conn,$query);
}

function update_absence_day ($conn, $id, $day) {
	$query = "UPDATE b2_absences SET Day = '".$day."' WHERE ID = '".$id."';";
	mysqli_query($conn,$query);
}

function update_absence_justification ($conn, $id, $justification) {
	$query = "UPDATE b2_absences SET Justification = '".$justification."' WHERE ID = '".$id."';";
	mysqli_query($conn,$query);
}

function delete_absence ($conn, $id) {
	$query = "DELETE FROM b2_absences WHERE ID = '".$id."';";
	mysqli_query($conn,$query);
}

function get_absence ($conn, $student_id, $date) {
	$query = "SELECT * FROM b2_absences WHERE Student_id = '".$student_id."' AND Date_of_absence ='".$date."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}

function get_absences ($conn, $student_id) {
	$query = "SELECT * FROM b2_absences WHERE Student_id = '".$student_id."' ORDER BY Date_of_absence DESC";
	return get_values(mysqli_query($conn,$query));
}

function count_absences ($conn, $id, $day, $justification) {
	$query = "SELECT count(ID) as number FROM b2_absences WHERE Student_id = '$id' AND Day = '$day' AND Justification = '$justification'";
	$data = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($data);
	return $row["number"];
}
/*EOF b2_absences*/
/*----------------------------------------------------------------------------------------------------*/

/* b2_classrooms */
function get_all_classrooms ($conn) {
	$query = "SELECT * FROM b2_classrooms ORDER BY Class";
	return get_values(mysqli_query($conn,$query));
}

function get_all_reservable_classrooms ($conn) {
	$query = "SELECT * FROM b2_classrooms WHERE Reservable = '1' ORDER BY Class";
	return get_values(mysqli_query($conn,$query));
}

function get_all_searchable_classrooms ($conn) {
	$query = "SELECT * FROM b2_classrooms WHERE Searchable = '0' ORDER BY Class";
	return get_values(mysqli_query($conn,$query));
}

function get_classroom ($conn, $class) {
	$query = "SELECT * FROM b2_classrooms WHERE Class = '".$class."' ORDER BY Class";
	$data = mysqli_query($conn, $query);
	return mysqli_fetch_assoc($data);

}

/* EOF b2_classrooms */
/*----------------------------------------------------------------------------------------------------*/

/* b2_reservations */
function get_reservations_on_class_by_day ($conn, $class, $date) {
	$query = "SELECT * FROM b2_reservations WHERE Classroom ='".$class."' AND Date = '".$date."' ORDER BY Begin";
	return get_values(mysqli_query($conn,$query));
}

function get_reservations_credits ($conn, $class, $start_date, $end_date, $teacher) {
	$query = "SELECT * FROM b2_reservations WHERE Classroom = '$class' AND Date >= '$start_date' AND Date <= '$end_date' AND Teacher_id ='".$teacher["ID"]."'";
	return get_values(mysqli_query($conn,$query));
}

function add_reservation ($conn, $class, $date, $begin, $end, $teacher) {
	$query = "INSERT INTO b2_reservations (Classroom,Date,Begin,End,Teacher_id) VALUES ('".$class["Class"]."','".$date."','".$begin."','".$end."','".$teacher["ID"]."');";
	mysqli_query($conn,$query);
}

function delete_reservation ($conn, $id) {
	$query = "DELETE FROM b2_reservations WHERE ID = '".$id."';";
	mysqli_query($conn,$query);
}

function get_reservations_teacher ($conn, $teacher, $class) {
	$query = "SELECT * FROM b2_reservations WHERE Classroom ='".$class."' AND Teacher_id = '".$teacher["ID"]."' ORDER BY Date, Begin";
	// echo $query;
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_reservations */
/*----------------------------------------------------------------------------------------------------*/

/* b2_european_hours */
function eur_exists ($conn, $user, $course) {
	$query = "SELECT * FROM b2_european_hours WHERE Teacher_id ='".$user["ID"]."' AND Course_id = '".$course."'";
	$data = get_values(mysqli_query($conn,$query));
	if($data)
		return true;
	else
		return false;
}

function update_eur ($conn, $user, $course, $begin, $end) {
	$query = "UPDATE b2_european_hours SET Begin ='".$begin."', End='".$end."' WHERE Teacher_id ='".$user["ID"]."' AND Course_id='".$course."'";
	mysqli_query($conn,$query);
}

function insert_eur ($conn, $user, $course, $begin, $end) {
	$query = "INSERT INTO b2_european_hours (Teacher_id,Course_id,Begin,End) VALUES ('".$user["ID"]."','".$course."','".$begin."','".$end."');";
	mysqli_query($conn,$query);
}

function get_begin_eur ($conn, $user, $course) {
	$query = "SELECT Begin FROM b2_european_hours WHERE Teacher_id ='".$user["ID"]."' AND Course_id = '".$course."'";
	$data = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($data);
	return $row["Begin"];
}

function get_end_eur ($conn, $user, $course) {
	$query = "SELECT End FROM b2_european_hours WHERE Teacher_id ='".$user["ID"]."' AND Course_id = '".$course."'";
	$data = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($data);
	return $row["End"];
}

function get_all_eur ($conn) {
	$query = "SELECT * FROM b2_european_hours ORDER BY Teacher_id, Begin";
	return get_values(mysqli_query($conn,$query));
}

function get_current_eur ($conn, $user) {
	date_default_timezone_set("Europe/Brussels");
	$today = date("Y-m-d");
	$query = "SELECT Course_id FROM `b2_european_hours` WHERE Teacher_id ='".$user["ID"]."' AND End >= '".$today."' AND Begin <= '".$today."' ORDER BY Course_id";
	return get_values(mysqli_query($conn,$query));
}
/* EOF b2_european_hours */
/*----------------------------------------------------------------------------------------------------*/

/* b2_conflicts */
function add_conflict ($conn, $teacher_1, $teacher_2, $day, $begin_1, $end_1, $begin_2, $end_2, $class) {
	$query = "INSERT INTO b2_conflicts (Teacher_id_1, Teacher_id_2, Day, Begin_1, End_1, Begin_2, End_2, Approved, Class) VALUES
			('".$teacher_1["ID"]."','".$teacher_2["ID"]."','".$day."','".$begin_1."','".$end_1."','".$begin_2."','".$end_2."','0','".$class."');";
	mysqli_query($conn,$query);
}

function conflict_exists ($conn, $teacher_1, $teacher_2, $day, $begin_1, $end_1, $begin_2, $end_2) {
	$query = "SELECT * FROM b2_conflicts
			WHERE Teacher_id_1 ='".$teacher_1["ID"]."' 
			AND Teacher_id_2 = '".$teacher_2["ID"]."'
			AND Day = '".$day."'
			AND Begin_1 = '".$begin_1."'
			AND Begin_2 = '".$begin_2."'
			AND End_1 = '".$end_1."'
			AND End_2 = '".$end_2."'";
	$data = get_values(mysqli_query($conn,$query));
	if($data)
		return true;
	else
		return false;
}

function get_conflicts ($conn, $teacher) {
	$query = "SELECT * FROM b2_conflicts WHERE Teacher_id_1 ='".$teacher["ID"]."' AND Approved = '0'";
	return get_values(mysqli_query($conn,$query));
}

function update_conflicts ($conn, $id) {
	$query = "UPDATE b2_conflicts SET Approved = '1' WHERE ID = '".$id."'";
	mysqli_query($conn,$query);
}

function update_all_conflicts ($conn, $id) {
	$query = "UPDATE b2_conflicts SET Approved = '1' WHERE Teacher_id_1 = '".$id."'";
	mysqli_query($conn,$query);
}


function delete_conflict ($conn, $id) {
	$query = "DELETE FROM b2_conflicts WHERE ID = '".$id."'";
	mysqli_query($conn,$query);
}

function delete_conflicts_user ($conn, $user) {
	$query = "DELETE FROM b2_conflicts WHERE Teacher_id_1 = '".$user."' OR Teacher_id_2 = '".$user."'";
	mysqli_query($conn,$query);
}
/* EOF b2_conflicts */
/*----------------------------------------------------------------------------------------------------*/


/* b2_conseils */
function get_conseils ($conn, $id) {
	$query = "SELECT * FROM b2_conseils WHERE Student_id ='".$id."'";
	$data = mysqli_query($conn, $query);
	return mysqli_fetch_assoc($data);
}

function add_conseils ($conn, $id) {
	$query = "INSERT INTO b2_conseils (Student_id) VALUES ('".$id."');";
	mysqli_query($conn,$query);
}

function save_conseils ($conn, $conseils) {
	$query = "UPDATE b2_conseils
			SET Doubled = '".$conseils["Doubled"]."', 
				LM = '".$conseils["LM"]."', 
				Math = '".$conseils["Math"]."',
				LII = '".$conseils["LII"]."',
				DDM = '".$conseils["DDM"]."',
				Proposition = '".$conseils["Proposition"]."',
				Decision = '".$conseils["Decision"]."',
				Intensive = '".$conseils["Intensive"]."',
				General = '".$conseils["General"]."',
				Rattrapage = '".$conseils["Rattrapage"]."',
				Swals = '".$conseils["Swals"]."',
				Next_Intensive = '".$conseils["Next_Intensive"]."',
				Next_General = '".$conseils["Next_General"]."',
				Next_Rattrapage = '".$conseils["Next_Rattrapage"]."',
				Moderate = '".$conseils["Moderate"]."',
				Next_Moderate = '".$conseils["Next_Moderate"]."',
				Next_Swals = '".$conseils["Next_Swals"]."',
				Comments = '".$conseils["Comments"]."',
				Absences = '".$conseils["Absences"]."'
				WHERE Student_id = '".$conseils["Student_id"]."'";
	mysqli_query($conn,$query);
}
/* EOF b2_conseils */
/*----------------------------------------------------------------------------------------------------*/

/* b2_conseils_dates */
function get_conseils_dates ($conn, $level) {
	$query = "SELECT * FROM b2_conseils_dates WHERE Level ='".$level."'";
	$data = mysqli_query($conn, $query);
	return mysqli_fetch_assoc($data);
}

function update_conseils_dates ($conn, $level, $turn_in, $date) {
	$query = "UPDATE b2_conseils_dates
			SET Turn_in = '".$turn_in."', 
				Date = '".$date."'
				WHERE Level = '".$level."'";
	mysqli_query($conn,$query);
}
/* EOF b2_conseils_dates */
/*----------------------------------------------------------------------------------------------------*/

function get_menu_item_on_page ($conn, $page) {
	$query = "SELECT * FROM b2_menu_items WHERE Link = '$page'";
	$data = mysqli_query($conn, $query);
	return mysqli_fetch_assoc($data);
}

function add_poll ($conn, $teacher_id, $choice) {
	$query = "INSERT INTO b2_poll (Teacher_id, Choice) VALUES ('".$teacher_id."','".$choice."')";
	mysqli_query($conn, $query);
}

function update_poll ($conn, $teacher_id, $choice) {
	$query = "UPDATE b2_poll SET Choice = '".$choice."' WHERE Teacher_id = '".$teacher_id."'";
	mysqli_query($conn, $query);
}

function get_poll ($conn, $teacher_id) {
	$query = "SELECT * FROM b2_poll WHERE Teacher_id ='".$teacher_id."'";
	$data = mysqli_query($conn, $query);
	return mysqli_fetch_assoc($data);
}

function get_all_poll ($conn) {
	$query = "SELECT * FROM b2_poll";
	return get_values(mysqli_query($conn,$query));
}

function get_contacts ($conn, $student_id) {
	$query = "SELECT * FROM b2_contacts WHERE ID in (SELECT Parent_id FROM b2_student_contacts WHERE Student_id = '".$student_id."')";
	return get_values(mysqli_query($conn,$query));
}

function add_visitor ($conn, $teacher_id, $contact_id, $date) {
	$query = "INSERT INTO b2_visitors (Teacher_id,Contact_id,Date) VALUES ('".$teacher_id."','".$contact_id."','".$date."')";
	mysqli_query($conn,$query);
}

function get_visitors ($conn, $date) {
	$query = "SELECT * FROM b2_visitors WHERE Date = '".$date."'";
	return get_values(mysqli_query($conn,$query));
}

function get_contact ($conn, $id) {
	$query = "SELECT * FROM b2_contacts WHERE ID = '".$id."'";
	$data = mysqli_query($conn,$query);
	return mysqli_fetch_assoc($data);
}


?>