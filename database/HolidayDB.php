<?php



class HolidayDB {

    public static function getById($id) {
    	$SqlQuery = "SELECT * FROM b2_screen_holidays WHERE holiday_ID='". $id."';";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($result == array()){
			return null;
		}else{
        	return $result[0];
		}
    }

    public static function insert($holiday) {
		$SqlQuery = "INSERT INTO b2_screen_holidays (`name`, `day`, `month`, `country`, `yearly`) VALUES (\"".$holiday['name']."\",\"".$holiday['day']."\",\"".$holiday['month']."\",\"".$holiday['country']."\",\"".$holiday['yearly']."\")";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
    public static function update($holiday) {
    	self::deleteById($holiday['holiday_ID']);
		$SqlQuery = "INSERT INTO b2_screen_holidays (`holiday_ID`, `name`, `day`, `month`, `country`, `yearly`) VALUES (\"".$holiday['holiday_ID']."\",\"".$holiday['name']."\",\"".$holiday['day']."\",\"".$holiday['month']."\",\"".$holiday['country']."\",\"".$holiday['yearly']."\")";
		mysqli_query(get_db_connection (),$SqlQuery);
	}
	
    public static function getHolidayByDayMonth($day, $month){
		$SqlQuery = "SELECT * FROM `b2_screen_holidays` WHERE `day` = ".$day." AND `month` = ".$month.";";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	
    public static function getHolidayByDate($date){
    	$day = date('d', $date);
		$month = date('m', $date);
		$SqlQuery = "SELECT * FROM `b2_screen_holidays` WHERE `day` = ".$day." AND `month` = ".$month.";";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
	
    public static function deleteById($id) {
    	$SqlQuery = "DELETE FROM b2_screen_holidays WHERE holiday_ID= '".$id."';";
		mysqli_query(get_db_connection (),$SqlQuery);
    }

    public static function delete($holiday) {
        self::deleteById($holiday['holiday_ID']);
    }
	
    public static function getAll() {
    	$SqlQuery = "SELECT * FROM b2_screen_holidays;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }

	
    public static function deleteOldHolidays() {
    	$month= date('m');
		$day = date('d');
    	$SqlQuery = "SELECT * FROM b2_screen_holidays h1 WHERE h1.month < ".$month." AND yearly='FALSE' UNION 
    	SELECT * FROM b2_screen_holidays h2 WHERE month=".$month." AND h2.day < ".$day." AND yearly='FALSE';";
		$oldholidays = get_values(mysqli_query(get_db_connection (),$SqlQuery));
		if ($oldholidays != array()){
			foreach ($oldholidays as $holiday) {
				self::delete($holiday);
			}	
		}
    }
    public static function getAllSortedByDate() {
    	$SqlQuery = "SELECT * FROM b2_screen_holidays ORDER BY month,day;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
    public static function getAllSortedByCountry() {
    	$SqlQuery = "SELECT * FROM b2_screen_holidays ORDER BY country;";
		$result = get_values(mysqli_query(get_db_connection (),$SqlQuery));
        return $result;
    }
    
}