<?php
error_reporting(E_ERROR  | E_PARSE);

session_start();
if ($_SESSION['auth'])
{
    $newURL="courseSelection.php";
    header('Location: '.$newURL);
}

$ini_array = parse_ini_file("lab5.ini");
$database =  $ini_array['db'];
$servername =  $ini_array['host'];
$dbpassword =  $ini_array['password'];
$dbusername =  $ini_array['user'];


$errors_array =[];
$name = trim($_POST["name"]);
$studentId = trim($_POST["id"]);
$phone = trim($_POST["phone"]);
$pass = trim($_POST["pass"]);
$pass2 = trim($_POST["pass2"]);


function ValidateName($name)
{


    if ($name == "")
    {
        $errorObj = new stdClass();
        $errorObj->name = "ValidateName";
        $errorObj->msg = "Name is required";
        return $errorObj;

    }

    else
    {
        return null;
    }
}
function ValidateId($id)
{
    $ini_array = parse_ini_file("lab5.ini");
    $conn = mysqli_connect($ini_array['host'], $ini_array['user'], $ini_array['password'], $ini_array['db']);
// Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT StudentId FROM student where StudentId='$id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $errorObj = new stdClass();
        $errorObj->name = "ValidateId";
        $errorObj->msg = "Student ID is Already registered";
        return $errorObj;

    }

    mysqli_close($conn);

    if($id=="")
    {
        $errorObj = new stdClass();
        $errorObj->name = "ValidateId";
        $errorObj->msg = "Student ID is Empty";
        return $errorObj;
    }
    else
    {
        return null;
    }

}
function ValidatePhone($phone)
{
    $expression = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/';
    if(!preg_match($expression, $phone))
    {
        $errorObj = new stdClass();
        $errorObj->name = "ValidatePhone";
        $errorObj->msg = "Phone is Invalid";
        return  $errorObj;
    }

    else
    {
        return null;
    }
}
function ValidatePass($pass)
{
    $expression ='/^(?=.*[A-Z]).(?=.*[a-z]).{6,}$/';
    if(!preg_match($expression, $pass)) {
        $errorObj = new stdClass();
        $errorObj->name = "ValidatePass";
        $errorObj->msg = "Password is at least 6 characters long, contains at least one upper case, one 
lowercase and one digit";
        return $errorObj;
    }
    else
    {
        return null;
    }
}
function ValidatePass2($pass2)
{
    if($pass2!=$_POST["pass"]) {
        $errorObj = new stdClass();
        $errorObj->name = "ValidatePass2";
        $errorObj->msg = "Password doesn't match!";
        return $errorObj;
    }
    else
    {
        return null;
    }
}


if(isset($_POST["name"]))
{
    $_SESSION["name"] = $name;

    if (ValidateName($name))
    {
        $errors_array[]=ValidateName($name);

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

if(isset($_POST["phone"]))
{
    $_SESSION["phone"] = $phone;

    if (ValidatePhone($phone))
    {
        $errors_array[]=ValidatePhone($phone);

    }
}

if(isset($_POST["pass"]))
{
    $_SESSION["pass"] = $pass;

    if (ValidatePass($pass))
    {
        $errors_array[]=ValidatePass($pass);

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
        $hash=password_hash($pass, PASSWORD_DEFAULT);

        $conn = new PDO("mysql:host=$servername;dbname=$database", $dbusername, $dbpassword);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $data = array($studentId, $name, $phone, $hash);
        $stmta = $conn->prepare("INSERT INTO student (StudentId, StudentName, Phone, Password) VALUES (?, ?, ?, ?)");

        $stmta->execute($data);

        $_SESSION['auth']=$studentId;
        $newURL="courseSelection.php";
        header('Location: '.$newURL);


    } catch(PDOException $e) {

        echo $sql . "<br>" . $e->getMessage();
    }
}
else
{
    $_SESSION["errors"] = $errors_array;

}

$name=0;
$nameMsg=0;
$studentId=0;
$postalMsg=0;
$phone=0;
$phoneMsg=0;
$phone2=0;
$phone2Msg=0;
$pass=0;
$pass2=0;
$emailMsg=0;
$emailMsg2=0;

$erros=$_SESSION["errors"];
foreach ($erros as $err)
{
    if ($err->name=="ValidatePrincipal")
    {
        $principal=1;
        $principalMsg=$err->msg;

    }
    elseif ($err->name=="ValidateRate")
    {
        $rate=1;
        $rateMsg=$err->msg;

    }

    elseif ($err->name=="ValidateName")
    {
        $name=1;
        $nameMsg=$err->msg;

    }

    elseif ($err->name=="ValidateId")
    {
        $studentId=1;
        $postalMsg=$err->msg;

    }

    elseif ($err->name=="ValidatePhone")
    {
        $phone=1;
        $phoneMsg=$err->msg;

    }
    elseif ($err->name=="ValidatePhone2")
    {
        $phone2=1;
        $phone2Msg=$err->msg;

    }
    elseif ($err->name=="ValidatePass")
    {
        $pass=1;
        $emailMsg=$err->msg;

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

        <h3 style="text-align: center" class="mt-5">Sign Up</h3>

        <div class="container mt-3 w-50">
            <form action="newuser.php" method="post">
                <?php
                if ($name==1){
                    ?>
                    <div class="mb-3 mt-3">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control is-invalid" id="name" placeholder="" name="name">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$nameMsg?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="mb-3 mt-3">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" value="<?=$_SESSION["name"]?>" id="name" placeholder="" name="name">
                    </div><?php }?>

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
                <?php if ($phone == 1){?>
                    <div class="mb-3 mt-3">
                        <label for="phone">Phone Number:(nnn-nnn-nnnn)</label>
                        <input type="text" class="form-control is-invalid" id="phone" placeholder="" name="phone">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$phoneMsg?>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="mb-3 mt-3">
                        <label for="phone">Phone Number:(nnn-nnn-nnnn)</label>
                        <input type="text" value="<?=$_SESSION["phone"]?>" class="form-control" id="phone" placeholder="" name="phone">
                    </div>
                <?php } ?>



                <?php if ($pass == 1){?>
                    <div class="mb-3 mt-3">
                        <label for="pass">Password:</label>
                        <input type="password" class="form-control is-invalid" id="pass" placeholder="" name="pass">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$emailMsg?>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="mb-3 mt-3">
                        <label for="pass">Password:</label>
                        <input type="password" class="form-control" value="<?=$_SESSION["pass"]?>" id="pass" placeholder="" name="pass">
                    </div>
                <?php } ?>

                <?php if ($pass2 == 1){?>
                    <div class="mb-3 mt-3">
                        <label for="pass2">Password Agian:</label>
                        <input type="password" class="form-control is-invalid" id="pass2" placeholder="" name="pass2">
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            <?=$emailMsg2?>
                        </div>
                    </div>
                <?php } else {?>
                    <div class="mb-3 mt-3">
                        <label for="pass2">Password Agian:</label>
                        <input type="password" class="form-control" value="<?=$_SESSION["pass2"]?>" id="pass2" placeholder="" name="pass2">
                    </div>
                <?php } ?>





                <div class="d-grid gap-2">
                    <button class="btn btn-success" type="submit">Submit</button>
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
