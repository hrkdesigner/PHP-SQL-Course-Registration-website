<?php
session_start();
$ini_array = parse_ini_file("lab5.ini");
$database =  $ini_array['db'];
$servername =  $ini_array['host'];
$dbpassword =  $ini_array['password'];
$dbusername =  $ini_array['user'];
$courses=array();
$hours=0;
$_SESSION['currentSemester']=$_POST['sem'];
$sid=$_SESSION['auth'];
try {
    $regconn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
    $regconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sem=$_POST['sem'];
    $reg_stmt = $regconn->prepare("SELECT CourseCode FROM registration where StudentId=$sid and SemesterCode='$sem'");
    $reg_stmt->execute();
    while ($reg_row = $reg_stmt->fetch()) {

        $Semester_conn2 = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
        $Semester_conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $Semester_stmt2 = $Semester_conn2->prepare("SELECT WeeklyHours FROM course where CourseCode='$reg_row[0]'");
        $Semester_stmt2->execute();
        while ($Semester_row2 = $Semester_stmt2->fetch()) {

            $hours+= $Semester_row2[0];

        }

    }
}catch(\Exception $e) {
    echo $e->getMessage();
}
echo $hours;

?>