<?php
error_reporting(E_ERROR  | E_PARSE);

session_start();
if ($_SESSION['auth'])
{
    $newURL="login.php";
    header('Location: '.$newURL);
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

    <h3 class="mt-5">Welcome to Algonquin Online Registration</h3>
    <p class="w-50">
        if you have never used this before you have to <a href="newuser.php">sign up</a>
    </p>
    <p class="w-50">
        if you have already signed up, you can  <a href="login.php">Login</a>
    </p>

    <div class="footer">
        <p>Algonquin college 2022 Copyright C</p>
    </div>
</div>

</body>
</html>