<?php
    if (isset($_SESSION["email"])) {
        $userLoggedIn = $_SESSION["email"];
    }
    else {
        header("Location: info.php");
    }
?>