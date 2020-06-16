<?php
    ob_start();
    session_start();

    $timezone = date_default_timezone_set("Europe/Paris");

    $con = mysqli_connect("127.0.0.1", "root", 'beBX2Tc57i3Y^cy$QRVMN@U2yS7#Pmqy', "secureforms");
    
    if(mysqli_connect_errno()) {
    echo "Failed to connect: " . mysqli_connect_errno();
    }
?>