<?php
/**
 * Created by IntelliJ IDEA.
 * User: xavier
 * Date: 26/02/2016
 * Time: 8:18
 */
class Cources{
    public static function getAll() {
        $SqlQuery = "SELECT Student_id,
Day , Period, Room, Code, First_name, Last_name
FROM b2_student_teacher_course
JOIN b2_teacher_course ON Teacher_course_id = b2_teacher_course.ID
JOIN b2_courses ON Course_id = Code
JOIN b2_teachers ON Teacher_id = b2_teachers.ID";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function getIDAll($id) {
        $SqlQuery = "SELECT Student_id,
Day , Period, Room, Code, First_name, Last_name
FROM b2_student_teacher_course
JOIN b2_teacher_course ON Teacher_course_id = b2_teacher_course.ID
JOIN b2_courses ON Course_id = Code
JOIN b2_teachers ON Teacher_id = b2_teachers.ID
WHERE Student_id = '".$id ."';";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function getAllCourses(){
        $SqlQuery = "SELECT * FROM b2_courses";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }

    public static function getAllTeacher(){
        $SqlQuery = "SELECT * FROM b2_teacher_course";
        $result = mysqli_query(get_db_connection (),$SqlQuery);
        return $result;
    }
}