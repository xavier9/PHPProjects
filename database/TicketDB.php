<?php



class TicketDB {
	// ============ DATES ================
	
	
	public static function changeCodeDatetoDBDate($date){
		 //FROM 27/04/2015
		 //TO 2015-04-27
		if (self::isDBDate($date)){return $date;}
    	$date = explode ('/',$date);
    	$date = array_reverse($date);
    	$date = implode('-', $date);
		return $date;
		
	}
	public static function changeDBDatetoCodeDate($date){
		 //FROM 2015-04-27
		 //TO 27/04/2015
		if (self::isCodeDate($date)){return $date;}
    	$date = explode ('-',$date);
    	$date = array_reverse($date);
    	$date = implode('/', $date);
		return $date;
	}
	public static function isCodeDate($date){
		return (strpos($date,"/")==2&&strpos(substr($date,3),"/")==2);
		
	}
	public static function isDBDate($date){
		return !self::isCodeDate($date);
	}
	
	
	// ============ STATUSES ==============
	public static function getAllStatuses() {
    	$SqlQuery = "SELECT * FROM b2_ticket_status;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	
	public static function getStatusById($id) {
    	$SqlQuery = "SELECT * FROM b2_ticket_status WHERE status_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
	
	// ============ DEPARTMENTS ==============
	public static function getAllDepartments() {
    	$SqlQuery = "SELECT * FROM b2_ticket_departments;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	
	public static function getDepartmentById($id) {
    	$SqlQuery = "SELECT * FROM b2_ticket_departments WHERE department_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
	// ============ USERS ==============
	
	public static function getUsersByDepartmentId($id) {
    	$SqlQuery = "SELECT * FROM b2_ticket_users WHERE department_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		return $result;
    }
	
    public static function deleteUserById($id) {
    	$SqlQuery = "DELETE FROM b2_ticket_users WHERE user_ID= '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }
	
    public static function deleteUser($user) {
		$SqlQuery = "DELETE FROM b2_ticket_users WHERE user_ID= '".$user['user_ID']."' AND department_ID= '".$user['department_ID']."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }
	
	public static function addUser($userid,$departmentid){
		$SqlQuery = "INSERT INTO b2_ticket_users (`user_ID`, `department_ID`) VALUES (\"".$userid."\",\"".$departmentid."\")";
		mysqli_query(get_db_connection (),$SqlQuery);		
	}
	
	
	public static function getUserById($id) {
    	$SqlQuery = "SELECT * FROM b2_ticket_users WHERE user_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
	public static function getFullUserById($id) {
    	$SqlQuery = "SELECT * FROM b2_teachers WHERE ID = '" .$id. "' 
				UNION SELECT * FROM b2_extra_users WHERE ID = '" .$id. "';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
	public static function getUserByIdAndDepartment($userid,$deptid) {
    	$SqlQuery = "SELECT * FROM b2_ticket_users WHERE user_ID='". $userid."' AND department_ID='".$deptid."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	public static function updateUser($user){
		self::deleteUserById($user['user_ID']);
		return self::insertUser($user);
	}
	
	public static function insertUser($user){
		$SqlQuery = "INSERT INTO b2_ticket_users (`user_ID`, `department_ID`, `leader`) VALUES (\"".$user['user_ID']."\",\"".$user['department_ID']."\",\"".$user['leader']."\")";
		mysqli_query(get_db_connection (),$SqlQuery);
		
	}
	
	// ============ TICKETS ==============
	
	
	public static function getTicketById($id) {
    	$SqlQuery = "SELECT * FROM b2_tickets WHERE ticket_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
    public static function deleteTicketById($id) {
    	$SqlQuery = "DELETE FROM b2_tickets WHERE ticket_ID= '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }
	public static function getAllClosedUserTickets($userid) {
    	$SqlQuery = "SELECT * FROM b2_tickets WHERE submitter_ID='".$userid."' AND (status_ID=4 OR status_ID = 8);";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	
	public static function getAllOpenUserTickets($userid) {
    	$SqlQuery = "SELECT * FROM b2_tickets WHERE submitter_ID='".$userid."' AND status_ID!=4 AND status_ID != 8;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
    
	public static function getAllUserTickets($userid) {
    	$SqlQuery = "SELECT * FROM b2_tickets WHERE submitter_ID='".$userid."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	public static function updateTicket($ticket){
		self::deleteTicketById($ticket['ticket_ID']);
		$SqlQuery = "INSERT INTO `b2_tickets` (`ticket_ID`, `submitter_ID`, `assignee_ID`, `status_ID`, `title`, `start_date`, `last_update`, `description`, `department_ID`) VALUES ('".$ticket['ticket_ID']."', '".$ticket['submitter_ID']."','".$ticket['assignee_ID']."','".$ticket['status_ID']."','".$ticket['title']."','".$ticket['start_date']."','".$ticket['last_update']."','".$ticket['description']."','".$ticket['department_ID']."');";
		mysqli_query(get_db_connection (),$SqlQuery);
		
	}
	public static function createTicket($ticket){
		$SqlQuery = "INSERT INTO `b2_tickets` (`submitter_ID`,`status_ID`, `title`, `start_date`, `description`, `department_ID`) VALUES ('".$ticket['submitter_ID']."','".$ticket['status_ID']."','".$ticket['title']."','".$ticket['start_date']."','".$ticket['description']."','".$ticket['department_ID']."');";
		mysqli_query(get_db_connection (),$SqlQuery);
		
	}
	public static function viewTicket($ticketid){
		
	}
	
	// ============= TICKET UPDATES =============
	
	public static function getTicketUpdatesByTicketID($ticketid){
    	$SqlQuery = "SELECT * FROM b2_ticket_updates WHERE ticket_ID='".$ticketid."' ORDER BY update_ID;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
	}
	public static function getUpdateByID($id){
    	$SqlQuery = "SELECT * FROM b2_ticket_updates WHERE update_ID='".$id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
	}
	
	public static function insertNewUpdate($update){
		$SqlQuery = "INSERT INTO `b2_ticket_updates` (`submitter_ID`, `ticket_ID`, `status_ID`, `date`, `description`) VALUES ('".$update['submitter_ID']."', '".$update['ticket_ID']."', '".$update['status_ID']."', '".$update['date']."', '".$update['description']."');";
		mysqli_query(get_db_connection (),$SqlQuery);
		$ticket=self::getTicketById($update['ticket_ID']);
		$status = self::getStatusById($update['status_ID']);
		if ($status['updatedate']){
			$ticket['last_update']=$update['date'];
		}
		if ($status['updatestatus']){
			$ticket['status_ID']=$update['status_ID'];
		}
		self::updateTicket($ticket);
	}
}