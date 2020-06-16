<?php

    $myfile = fopen("config/root_password", "r") or die("Unable to open file!");
    $password = fgets($myfile);
    fclose($myfile);

    ob_start();
    session_start();

    $timezone = date_default_timezone_set("Europe/Paris");

    $con = mysqli_connect("127.0.0.1", "root", $password, "secureforms");
    
    if(mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_errno();
    }
?>