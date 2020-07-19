<?php
    include('config/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
 
    <!-- JavaScript -->
    <script type = "text/javascript"
        src = "https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="node_modules/chart.js/dist/Chart.js"></script>
    <script src="js/cookie_handlers.js"></script>
    <script src="js/sjcl.js"></script>
    <script src="js/form_scripts.js"></script>
    <script src="js/general_scripts.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <title>SecureForms</title>
</head>
<body>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
    <a href="info.php" class="navbar-brand">SecureForms</a>
<?php
if(isset($_SESSION['email'])) {
    echo 
    '   <ul class="nav navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fa fa-home"></i> Dashboard</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa fa-sign-out"></i> Log out</a></li>
        </ul>
    ';
} else {
    echo 
    '   <ul class="nav navbar-nav">
            <li class="nav-item"><a class="nav-link" href="info.php">How it works</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="register.php">Sign in/Register</a></li>
        </ul>
    ';
}
?>
</nav>