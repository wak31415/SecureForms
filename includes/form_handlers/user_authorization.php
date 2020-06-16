<?php

function loginUser($con, $email, $password) {
    $check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    $check_login_query = mysqli_num_rows($check_database_query);

    if($check_login_query == 1) {
        $row = mysqli_fetch_array($check_database_query);
        $_SESSION['email'] = $email;
        header("Location: index.php");
    }
    else {
        array_push($error_array, "Email or password incorrect");
    }
}

function registerUser($con, $email, $password, $privkey, $pubkey, $secret_msg) {
    $date = date("Y-m-d H:i:s");
    
    mysqli_query($con, "INSERT INTO users VALUES(NULL,'$email','$password','$date','$privkey','$pubkey','$secret_msg')");
    array_push($error_array, "Successfully registered!");

    $_SESSION['reg_fname'] = "";
    $_SESSION['reg_lname'] = "";
    $_SESSION['reg_email'] = "";

    loginUser($con, $email, $password);
}

?>