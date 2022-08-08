<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
if (!$_SESSION['auth'])
{
    $newURL="index.php";
    header('Location: '.$newURL);
}
$ini_array = parse_ini_file("lab5.ini");
$database =  $ini_array['db'];
$servername =  $ini_array['host'];
$dbpassword =  $ini_array['password'];
$dbusername =  $ini_array['user'];
try {
$conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT StudentId,StudentName FROM student where StudentId=:id");
$stmt->bindParam(":id", $_SESSION['auth']);
$stmt->execute();


while ($row = $stmt->fetch()) {
   $_SESSION['row']=$row;
}
} catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
if (isset($_POST["SemesterCode"]) && !$_POST['checkboxCourse'])
{
    $_SESSION['currentSemester']=$_POST['SemesterCode'];
$_SESSION['errors_course']="Please select at least one course";
}
 if(isset($_POST['checkboxCourse']))
 {
     $_SESSION['currentSemester']=$_POST['SemesterCode'];
     $hours=0;
     $sid=$_SESSION['auth'];
     try {
         $regconn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
         $regconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $sem=$_POST['SemesterCode'];
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
     $newhours=0;
     foreach ($_POST['checkboxCourse'] as $value)
     {

         $Semester_conn2 = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
         $Semester_conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $Semester_stmt2 = $Semester_conn2->prepare("SELECT WeeklyHours FROM course where CourseCode='$value'");
         $Semester_stmt2->execute();
         while ($Semester_row2 = $Semester_stmt2->fetch()) {

             $newhours+= $Semester_row2[0];

         }
     }
if (($hours+$newhours)<=16)
{
     foreach ($_POST['checkboxCourse'] as $value)
     {

             try {
                 $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
                 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                 $data = array($_SESSION['auth'],$value,$_POST['SemesterCode'],($_SESSION['auth'].$value.$_POST['SemesterCode']));
                 $stmta = $conn->prepare("INSERT INTO registration (StudentId, CourseCode, SemesterCode,regId) VALUES (?, ?, ?,?)");
                 $stmta->execute($data);
                 $_SESSION['success2']="You have registered!";

             } catch(\Exception $e) {
                 $_SESSION['errors_course']="You have already registered this course!";
             }

     }
}
else
{
    $_SESSION['errors_course']="Exceeded avilable hours for this semester";
}
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lab5</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .footer {
            position: relative;
            margin-top: 42%;
            left: 0;
            bottom: 0;
            width: 100%;
            padding-top: 40px;
            height: 100px;
            background-color: darkgreen;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
<?php if($_SESSION['errors_course']!=''){ ?>
<script>
    Swal.fire(
        'OPPS!',
        '<?=$_SESSION['errors_course']?>',
        'error'
    )
</script>
<?php } unset($_SESSION['errors_course']);?>
<?php if($_SESSION['success2']!=''){ ?>
<script>
    Swal.fire(
        'Good Job!',
        '<?=$_SESSION['success2']?>',
        'success'
    )
</script>
<?php } unset($_SESSION['success2']); ?>
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-dark " style="background: darkgreen">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">AC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="courseSelection.php">Course Selection</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="current.php">Current Registration</a>
                    </li>
                    <?php
                    if ($_SESSION['auth']){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out </a>
                    </li>
                    <?php }?>

                </ul>
            </div>
        </div>
    </nav>

    <h3 class="mt-5">Welcome <?=$_SESSION['row'][1]?> to Algonquin Course Selection</h3>
    <p class="w-50">You have registered <span id="regHours"></span> hours for the selected Semester</p>
    <p class="w-50">You can register <span id="remHours"></span> more hours of Course(s) for the selected Semester</p>
    <p class="w-50">Please note that the Selected courses will not be displayed in this list</p>

<p id="error"></p>
    <form method="post" action="courseSelection.php">
        <select onchange="update()" class="form-select w-25" id="ddlViewBy" name="SemesterCode" aria-label="Default select example">
            <?php
            try {
                $Semester_conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
                $Semester_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $Semester_stmt = $Semester_conn->prepare("SELECT SemesterCode,Term FROM semester");
                $Semester_stmt->execute();


                while ($Semester_row = $Semester_stmt->fetch()) {
                    ?>

                    <option <?php if ($_SESSION['currentSemester']==$Semester_row[0]){echo 'selected';} ?> value="<?=$Semester_row[0]?>"><?=$Semester_row[1]?></option>

                    <?php
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }?>
        </select>
        <table id="t1" class="table">
            <thead>
            <tr>
                <th scope="col">Code</th>
                <th scope="col">Title</th>
                <th scope="col">Hours</th>
                <th scope="col">Select</th>
            </tr>
            </thead>
            <tbody id="CoursesTable">
            </tbody>
        </table>
        <div class="div float-right">
            <button class="btn btn-success " type="submit">Register</button>

        </div>
    </form>

    <div class="footer">
        <p>Algonquin college 2022 Copyright C</p>
    </div>
</div>
<script>
    // Swal.fire(
    //     'Good job!',
    //     'You clicked the button!',
    //     'success'
    // )
    update();
  function update()
  {
      document.getElementById("CoursesTable").innerHTML="";
      var http = new XMLHttpRequest();
      var url = 'semesterupdate.php';
      var params = 'sem='+document.getElementById("ddlViewBy").value;
      http.open('POST', url, true);

      //Send the proper header information along with the request
      http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      http.onreadystatechange = function() {//Call a function when the state changes.
          if(http.readyState == 4 && http.status == 200) {
              let jsonText = http.responseText;
              console.log(jsonText);
              const obj = JSON.parse(jsonText);
              for(var k in obj) {
                  let tr = document.createElement("tr");
                  let td1 = document.createElement("td");
                  let td2 = document.createElement("td");
                  let td3 = document.createElement("td");
                  let td4 = document.createElement("td");
                  var x = document.createElement("INPUT");
                  x.setAttribute("type", "checkbox");
                  x.setAttribute("name", "checkboxCourse[]");
                  x.setAttribute("value", obj[k][0]);
                  td4.appendChild(x);
                  td1.innerText=obj[k][0];
                  td2.innerText=obj[k][1];
                  td3.innerText=obj[k][2];
                  tr.appendChild(td1)
                  tr.appendChild(td2)
                  tr.appendChild(td3)
                  tr.appendChild(td4)
                  document.getElementById("CoursesTable").appendChild(tr);
                  console.log( obj[k]);
              }
          }
      }
      http.send(params);


      document.getElementById("CoursesTable").innerHTML="";
      var http2 = new XMLHttpRequest();
      var url2 = 'hours.php';
      var params2 = 'sem='+document.getElementById("ddlViewBy").value;
      http2.open('POST', url2, true);

      //Send the proper header information along with the request
      http2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      http2.onreadystatechange = function() {//Call a function when the state changes.
          if(http2.readyState == 4 && http2.status == 200)
          {
              document.getElementById("regHours").innerText=http2.responseText;
              document.getElementById("remHours").innerText=16-http2.responseText;
          }
      }
      http2.send(params2);
  }
</script>
</body>
</html>