<?php

if(isset($_POST['login_button'])) {
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // sanitize email

    $_SESSION['log_email'] = $email;
    $password = md5($_POST['log_password']);

    loginUser($con, $email, $password);
}
?>