<?php

class MessageDB {

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
		 //FROM 27/04/2015
		 //TO 2015-04-27
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

    public static function getById($id) {
    	$SqlQuery = "SELECT * FROM b2_screen_messages WHERE message_ID = '". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }
	
    public static function getAll() {
		$SqlQuery = "SELECT * FROM b2_screen_messages;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	public static function getMessagesAll(){
		$result = mysqli_query(get_db_connection (),"SELECT *FROM b2_screen_messages") or die(mysql_error());
		//$SqlQuery = "SELECT * FROM b2_screen_messages;";
		//$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		return $result;
	}
    public static function getMessagesForScreen($screenid) {
    	$SqlQuery = "SELECT * FROM b2_screen_messages WHERE screen_ID = ".$screenid.";";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result;
		}
    }
	public static function getVisibleMessagesForScreen($screenid){
		self::removeExpiredMessages();
    	$SqlQuery = "SELECT * FROM b2_screen_messages WHERE screen_ID = ".$screenid." AND visibility=true;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result;
		}
		
	}
	
    public static function getOrphanedMessages() {
    	$SqlQuery = "SELECT * FROM b2_screen_messages WHERE screen_ID = 0;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result;
		}
    }
	
    public static function deleteById($id) {
    	$SqlQuery = "DELETE FROM b2_screen_messages WHERE message_ID = '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }
    
    public static function delete($message) {
        self::deleteById($message['message_ID']);
    }
	
	public static function insert($message){
		$date=$message['expiredate'];
    	$date = self::changeCodeDatetoDBDate($date);
		$SqlQuery = "INSERT INTO b2_screen_messages (title, message,screen_ID,visibility,expiredate) VALUES ('".$message['title']."','".$message['message']."','".$message['screen_ID']."','".$message['visibility']."','".$date."')";
		mysqli_query(get_db_connection (),$SqlQuery);
	}

	public static function update($message){
		$SqlQuery = "UPDATE b2_screen_messages SET title = '".$message['title']."' ,message = '".$message['message']."', screen_ID = '".$message['screen_ID']."', visibility = '".$message['visibility']."'";
		if (isset($message['expiredate'])){
	    	$message['expiredate'] = self::changeCodeDatetoDBDate($message['expiredate']);
			$SqlQuery= $SqlQuery.", expiredate = '".$message['expiredate']."'";
		}
		$SqlQuery = $SqlQuery." WHERE message_ID = '".$message['message_ID']."';";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
	public static function makeMessageVisible($message){
		$message['visibility']=true;
		self::update($message);
	}
	public static function makeMessageInvisible($message){
		$message['visibility']=false;
		self::update($message);
	}

	public static function removeMessageFromScreen($message){
		$message['visibility']=false;
		self::update($message);
	}
	
	public static function removeMessageFromScreenByID($id){
		$SqlQuery = "UPDATE b2_screen_messages SET screen_ID = 0  WHERE message_ID = '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
	public static function removeExpiredMessages(){
		$SqlQuery = "select * from b2_screen_messages where  expiredate < CURDATE()";
		$expired = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		foreach ($expired as $message) {
			$SqlQuery = "update `b2_screen_messages` SET `expiredate` = NULL WHERE message_ID='".$message['message_ID']."';";
			mysqli_query(get_db_connection (),$SqlQuery);
			$SqlQuery = "update `b2_screen_messages` SET visibility=0 WHERE message_ID='".$message['message_ID']."';";
			mysqli_query(get_db_connection (),$SqlQuery);
		}
	}

	public static function removeExpireDate($id){
		$SqlQuery = "update `b2_screen_messages` SET `expiredate` = NULL WHERE message_ID='".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
	}


}



