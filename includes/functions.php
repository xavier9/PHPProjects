<?php	
	function get_photo ($id) {
		$files = array ("../images/teachers2015/".$id.".jpg",
						"../images/teachers2014/".$id.".jpg",
						"../images/teachers2013/".$id.".jpg",
						"../images/teachers2012/".$id.".jpg",
		                "../images/teachers2011/".$id.".jpg",
						"../images/students2015/".$id.".jpg",
						"../images/students2014/".$id.".jpg",
						"../images/students2013/".$id.".jpg",
						"../images/students2012/".$id.".jpg",
						"../images/students2011/".$id.".jpg");
		foreach ($files as $file)
			if(file_exists($file))
				return $file;
		return "../images/unknown.gif";	
	}
	
	function get_flag ($nationality) {
		return "../images/flags/".$nationality."2.png";
	}
	
	function change_date_format ($date) {
		return substr($date,8)."/".substr($date,5,2)."/".substr($date,0,4);
	}

	function change_date_format_db ($date) {
		return substr($date,6)."-".substr($date,3,2)."-".substr($date,0,2);
	}
	
	function get_ldap_user ($ldapconn, $id) {
		$filter1 = "(|(employeenumber=$id))";
		$filter2 = array("*");
		$ldapconn = get_ldap_connection ();
		$result = ldap_search($ldapconn,"ou=Users,dc=eeb2,dc=be",$filter1,$filter2) or die ("Error in Query");
		$data = ldap_get_entries($ldapconn, $result);
		ldap_close($ldapconn);
		if(isset($data[0]))
			return $data[0];
	}
	
	function get_email_student ($ldap, $student) {
		$result = ldap_search($ldap,"ou=Users,dc=eeb2,dc=be","uid=$student",array("uid","mail")) or die ("Error in Query");
		$data = ldap_get_entries($ldap, $result);
		return (isset($data[0]["mail"][0]))?$data[0]["mail"][0]:"";
	}
	
	function get_email_teacher ($ldap, $teacher) {
		$result = ldap_search($ldap,"ou=Users,dc=eeb2,dc=be","employeenumber=$teacher",array("uid","employeenumber","mail")) or die ("Error in Query");
		$data = ldap_get_entries($ldap, $result);
		return (isset($data[0]["mail"][0]))?$data[0]["mail"][0]:"";
	}
	
	function read_csv ($file_name, $table_names, $skip_first_line) {
		if (($handle = fopen($file_name, "r")) !== FALSE) {
			$counter=0;
			$print_data =array();
			if($skip_first_line) {
				$data = fgetcsv($handle, 1000, ";");
			}
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) { 
				for ($i=0; $i<count($table_names);$i++) {
					$print_data[$counter][$table_names[$i]]=$data[$i];	
				}
				$counter++;				
			}
			fclose($handle);
			return $print_data;
		}
	}	
	
	function get_file_headers ($file_name) {
		if (($handle = fopen($file_name, "r")) !== FALSE) {
			return fgetcsv($handle, 1000, ";");
		}
	}
	
	function decrypt_password ($password) {
		$password = substr($password,4);
		$new_password = "";
		for($i=0;$i<strlen($password);$i++) {
			$new_password .= chr(substr($password,$i,3));
			$i+=2;
		}
		return $new_password;
	}
	
	function encrypt_password ($password) {
		$new_password = "EEB2";
		for($i=0;$i<strlen($password);$i++) {
			$new_password .= substr(number_format(ord(substr($password,$i,1))/1000,3),-3);
		}
		return $new_password;
	}
	
	function check_password_rules($pwd) {
		$error = array("Please fill in at least 8 characters!", 
					"Please fill in at least one digit!", 
					"Please fill in at least one lower case!", 
					"Please fill in at least one uppercase!");
		if (strlen($pwd) < 8) 
			return $error[0];
		if( !preg_match("#[0-9]+#", $pwd) )
			return $error[1];		
		if( !preg_match("#[a-z]+#", $pwd) ) 
			return $error[2];
		if( !preg_match("#[A-Z]+#", $pwd) ) 
			return $error[3];
		return "ok";
	}
	
	function normalize ($string) {
		$table = array(
			"'"=>'`'
		);		
		return strtr($string, $table);
	}
	
	function normalize_special_char ($string) {
		$table = array(
			"'"=>'`', 
			"Ä–"=>"&#278", 
			"Å«"=>"&#363",
			"Åª"=>"&#362",
			"ÄŒ"=>"&#268",
			"Ä“"=>"&#275",
			"Äž"=>"&#486",
			"Ä’"=>"&#274", 
			"Ä€"=>"&#256", 
			"Ä—"=>"&#279", 
			"Ä«"=>"&#299",
			"Äª"=>"&#298",
			// "Ä"=>"&#257", 
		);		
		return strtr($string, $table);
	}
	
	function sort_array ($array, $sort) {
		$sortArray = array(); 
		foreach($array as $person){ 
			foreach($person as $key=>$value){ 
				if(!isset($sortArray[$key])){ 
					$sortArray[$key] = array(); 
				} 
				$sortArray[$key][] = $value; 
			} 
		} 
		array_multisort($sortArray[$sort],SORT_ASC,$array); 
		return $array;
	}		
?>