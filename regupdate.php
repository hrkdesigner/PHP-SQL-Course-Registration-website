<?php
session_start();
$ini_array = parse_ini_file("lab5.ini");
$database =  $ini_array['db'];
$servername =  $ini_array['host'];
$dbpassword =  $ini_array['password'];
$dbusername =  $ini_array['user'];
$courses=array();
$sid=$_SESSION['auth'];
try {
    $Semester_conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
    $Semester_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $Semester_stmt = $Semester_conn->prepare("SELECT SemesterCode,Term FROM semester");
    $Semester_stmt->execute();


    while ($Semester_row = $Semester_stmt->fetch()) {
        $hoursTotal=0;
        $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT registration.CourseCode, registration.SemesterCode,course.Title,course.WeeklyHours  FROM registration  INNER JOIN course
ON registration.CourseCode = course.CourseCode where registration.StudentId='$sid' and registration.SemesterCode= '$Semester_row[0]' ORDER BY SemesterCode ASC");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $courses[$Semester_row[0]][]=$row;
            $hoursTotal+=$row[3];
        }

    }


} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
 echo json_encode($courses);

?>