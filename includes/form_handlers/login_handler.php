<?php

if(isset($_POST['login_button'])) {
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // sanitize email

    $_SESSION['log_email'] = $email;
    $password = password_hash($_POST['log_password'], PASSWORD_DEFAULT);

    loginUser($con, $email, $password);
}
?>