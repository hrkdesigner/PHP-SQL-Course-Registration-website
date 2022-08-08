<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
if (!$_SESSION['auth'])
{
    $newURL="index.php";
    header('Location: '.$newURL);
}
else
{
    $sid=$_SESSION['auth'];
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
if (isset($_POST['checkboxCourse']))
{
foreach ($_POST['checkboxCourse'] as $value)
{
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("Delete FROM registration where StudentId='$sid' and CourseCode='$value'");
        $stmt->execute();


        while ($row = $stmt->fetch()) {
            $_SESSION['row']=$row;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

}
else if ($_POST['submitted'])
{
    $_SESSION['delError']="Please select at least one course to delete!";
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
<?php if($_SESSION['delError']!=''){ ?>
    <script>
        Swal.fire(
            'OPPS!',
            '<?=$_SESSION['delError']?>',
            'error'
        )
    </script>
<?php } unset($_SESSION['delError']);?>
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
                        <a class="nav-link " href="courseSelection.php">Course Selection</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="current.php">Current Registration</a>
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

    <h3 class="mt-5">Welcome <?=$_SESSION['row'][1]?> to Algonquin Current Registration</h3>

    <p id="error"></p>
    <form id="delform" method="post" action="current.php">
<input type="hidden" name="submitted" value="1">
        <table id="t1" class="table">
            <thead>
            <tr>
                <th scope="col">Year</th>
                <th scope="col">Term</th>
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
            <button class="btn btn-danger " onclick="del()" type="button">Delete</button>

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
        var url = 'regupdate.php';
        var params = 'sem=1';
        var totHours=0;
        http.open('POST', url, true);
        //Send the proper header information along with the request
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.onreadystatechange = function() {//Call a function when the state changes.
            if(http.readyState == 4 && http.status == 200) {
                let jsonText = http.responseText;
                const obj = JSON.parse(jsonText);
                for(var k in obj) {
                    for(var k2 in obj[k]) {
                        console.log(obj[k][k2]);
                        let tr = document.createElement("tr");
                        let td1 = document.createElement("td");
                        let td2 = document.createElement("td");
                        let td3 = document.createElement("td");
                        let td4 = document.createElement("td");
                        let td5 = document.createElement("td");
                        let td6 = document.createElement("td");
                        var x = document.createElement("INPUT");
                        x.setAttribute("type", "checkbox");
                        x.setAttribute("name", "checkboxCourse[]");
                        x.setAttribute("value", obj[k][k2][0]);
                        td6.appendChild(x);
                        td1.innerText="20"+obj[k][k2]['SemesterCode'].substr(1, 2);
                        td2.innerText=obj[k][k2]['SemesterCode'].substr(0, 1);
                        td3.innerText=obj[k][k2][0];
                        td4.innerText=obj[k][k2][2];
                        td5.innerText=obj[k][k2][3];
                        tr.appendChild(td1)
                        tr.appendChild(td2)
                        tr.appendChild(td3)
                        tr.appendChild(td4)
                        tr.appendChild(td5)
                        tr.appendChild(td6);
                        document.getElementById("CoursesTable").appendChild(tr);
                        totHours+=parseInt(obj[k][k2]['WeeklyHours']);
                    }
                    let tr = document.createElement("tr");
                    let tdcolspan = document.createElement("td");
                    tdcolspan.setAttribute("colspan", "6");
                    tdcolspan.style.fontWeight = 'bold';
                    tdcolspan.style.paddingLeft = '80%';
                    tdcolspan.style.paddingTop = '2%';
                    tdcolspan.style.paddingBottom = '2%';
                    tdcolspan.innerText="Total Weekly Hous = "+totHours;
                    tr.appendChild(tdcolspan);
                    document.getElementById("CoursesTable").appendChild(tr);
                    console.log(totHours);
                    totHours=0;
                }
            }
        }
        http.send(params);

    }
    function  del()
    {
        Swal.fire({
            title: 'Do you want to delete these courses?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Delete',
            denyButtonText: `Don't Delete`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                document.getElementById('delform').submit();
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        })
    }
</script>
</body>
</html>