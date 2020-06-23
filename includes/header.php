<?php
    require 'config/config.php';

    if (isset($_SESSION["email"])) {
        $userLoggedIn = $_SESSION["email"];
    }
    else {
        header("Location: register.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
 
    <!-- JavaScript -->
    <script type = "text/javascript"
        src = "https://code.jquery.com/jquery-2.1.1.min.js"></script>

    <script src="js/cookie_handlers.js"></script>
    <script src="js/sjcl.js"></script>
    <script src="js/form_scripts.js"></script>
    <script src="js/general_scripts.js"></script>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="style.css">
    <title>SecureForms</title>
</head>
<body>

<ul class="sidenav" id="mobile-demo">
    <li><a href="index.php">Home</a></li>
    <li><a href="logout.php">Log out</a></li>
</ul>