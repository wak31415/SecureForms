<?php
    include('config/config.php');
?>

<nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
    <a href="info.php" class="navbar-brand">SecureForms</a>
    <ul class="nav navbar-nav">
<?php
if(isset($_SESSION['email'])) {
    echo 
    '   
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fa fa-home"></i> Dashboard</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fa fa-sign-out"></i> Log out</a></li>
        </ul>
    ';
} else {
    echo 
    '   
            <li class="nav-item"><a class="nav-link" href="info.php">How it works</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="register.php">Sign in/Register</a></li>
        </ul>
    ';
}
?>
</nav>