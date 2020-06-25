<?php

if(isset($_POST['login_button'])) {
    $email = filter_var($_POST['log_email'], FILTER_SANITIZE_EMAIL); // sanitize email
    $_SESSION['log_email'] = $email;
    unset($_SESSION['reg_email']);
    loginUser($con, $email, $_POST['log_password']);
}
?>