<?php
error_reporting(E_ERROR  | E_PARSE);

session_start();
if ($_SESSION['auth'])
{
    $newURL="courseSelection.php";
    header('Location: '.$newURL);
}




$errors_array =[];
$studentId = trim($_POST["id"]);
$pass2 = trim($_POST["pass2"]);



function ValidateId($id)
{


    if($id=="")
    {
        $errorObj = new stdClass();
        $errorObj->name = "ValidateId";
        $errorObj->msg = "Student ID can not be Empty!";
        return $errorObj;
    }
    else
    {
        return null;
    }

}

function ValidatePass2($pass2)
{
    $studentId = trim($_POST["id"]);
    $ini_array = parse_ini_file("lab5.ini");
    $database =  $ini_array['db'];
    $servername =  $ini_array['host'];
    $dbpassword =  $ini_array['password'];
    $dbusername =  $ini_array['user'];
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT StudentId, Password FROM student where StudentId=:id");
        $stmt->bindParam(":id", $studentId);
        $stmt->execute();


            while ($row = $stmt->fetch()) {
                if (!password_verify($_POST['pass2'],$row['Password']))
                {
                    $errorObj = new stdClass();
                    $errorObj->name = "ValidatePass2";
                    $errorObj->msg = "Incorrect Password or ID!";
                    return $errorObj;
                }
            }
        if ($stmt->rowCount() == 0) {
            $errorObj = new stdClass();
            $errorObj->name = "ValidateId";
            $errorObj->msg = "No ID found in our database";
            return $errorObj;
        }


    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    if($pass2=="") {
        $errorObj = new stdClass();
        $errorObj->name = "ValidatePass2";
        $errorObj->msg = "Password can not be empty!";
        return $errorObj;
    }
    else
    {
        return null;
    }
}

if(isset($_POST["id"]))
{
    $_SESSION["id"] = $studentId;

    if (ValidateId($studentId))
    {
        $errors_array[]=ValidateId($studentId);
    }
}


if(isset($_POST["pass2"]))
{
    $_SESSION["pass2"] = $pass2;

    if (ValidatePass2($pass2))
    {
        $errors_array[]=ValidatePass2($pass2);
    }

}
if (!$errors_array && !empty($_POST)) {


    try {


        $_SESSION['auth']=$studentId;
        $newURL="courseSelection.php";
        header('Location: '.$newURL);


    } catch(\Exception $e) {

        echo $sql . "<br>" . $e->getMessage();
    }
}
else
{
    $_SESSION["errors"] = $errors_array;

}

$studentId=0;
$postalMsg=0;
$pass2=0;
$emailMsg2=0;

$erros=$_SESSION["errors"];
foreach ($erros as $err)
{

    if ($err->name=="ValidateId")
    {
        $studentId=1;
        $postalMsg=$err->msg;

    }

    elseif ($err->name=="ValidatePass2")
    {
        $pass2=1;
        $emailMsg2=$err->msg;

    }
}
?>
<?php
if (empty($_POST) || $errors_array){
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Registration</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="courseSelection.php">Course Selection</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="current.php">Current Registration</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Log in </a>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <h3 style="text-align: center" class="mt-5">Sign In</h3>

        <div class="container mt-3 w-50">
            <form action="login.php" method="post">
                <?php
                if ($studentId==1){
                    ?>
                    <div class="mb-3 mt-3">
                        <label for="rate">Student ID:</label>
                        <input type="text" class="form-control is-invalid" id="id" placeholder="" name="id">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$postalMsg?>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="mb-3 mt-3">
                        <label for="rate">Student ID:</label>
                        <input type="text" class="form-control" value="<?=$_SESSION["id"]?>" id="id" placeholder="" name="id">
                    </div>
                <?php } ?>

                <?php if ($pass2 == 1){?>
                    <div class="mb-3 mt-3">
                        <label for="pass2">Password:</label>
                        <input type="password" class="form-control is-invalid" id="pass2" placeholder="" name="pass2">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$emailMsg2?>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="mb-3 mt-3">
                        <label for="pass2">Password:</label>
                        <input type="password" class="form-control" value="<?=$_SESSION["pass2"]?>" id="pass2" placeholder="" name="pass2">
                    </div>
                <?php } ?>





                <div class="d-grid gap-2">
                    <button class="btn btn-success" type="submit">Sign In</button>
                </div>
            </form>
        </div>
        <div class="footer">
            <p>Algonquin college 2022 Copyright C</p>
        </div>
    </div>

    </body>
    </html>
    <?php
}
?>
