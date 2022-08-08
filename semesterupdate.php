<?php
session_start();
$ini_array = parse_ini_file("lab5.ini");
$database =  $ini_array['db'];
$servername =  $ini_array['host'];
$dbpassword =  $ini_array['password'];
$dbusername =  $ini_array['user'];
$sid=$_SESSION['auth'];
$courses=array();
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM courseOffer where SemesterCode=:id");
    $stmt->bindParam(":id", $_POST['sem']);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $stmt1 = $conn->prepare("SELECT * FROM course where CourseCode=:id");
        $stmt1->bindParam(":id", $row[0]);
        $stmt1->execute();
        while ($row1 = $stmt1->fetch()) {
            $stmt12 = $conn->prepare("SELECT * FROM registration where StudentId='$sid' and CourseCode='$row1[0]'");
            $stmt12->execute();
            if ($stmt12->rowCount() == 0) {
                array_push($courses,$row1);
            }
            else
            {
                continue;
            }
        }

    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
echo json_encode($courses);

?>