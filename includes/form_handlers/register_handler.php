<?php
    // Declaring variables to prevent errors
    $fname = "";
    $lname = "";
    $em = "";
    $password = "";
    $password2 = "";
    $date = "";
    $error_array = array();

    if(isset($_POST['register_button'])) {
        // Registration form values

        $em = strip_tags($_POST['reg_email']);
        $em = str_replace(' ', '', $em);
        $em = strtolower($em);
        $_SESSION['reg_email'] = $em;
        
        $password = strip_tags($_POST['reg_password']);
        $password2 = strip_tags($_POST['reg_password2']);

        $privkey = $_POST['privkey']; // already encrypted
        $pubkey = $_POST['pubkey'];

        // check if email is in valid format
        if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);
            // check if email already exists
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");
            $num_rows = mysqli_num_rows($e_check);

            if($num_rows >= 1) {
                array_push($error_array,"Email already in use");
            }
        }
        else {
            array_push($error_array, "Invalid email format");
        }

        if($password != $password2) {
            array_push($error_array, "Your passwords do not match");
        }
        else {
            if(preg_match('/[^A-Za-z0-9]/',$password)) {
                array_push($error_array, "Your password can only contain english caharacters or numbers");
            }
        }

        if(strlen($password > 50 || strlen($password) < 8)) {
            array_push($error_array, "Your password must be between 8 and 50 characters");
        }

        if(empty($error_array)) {
            registerUser($con, $em, $password, $privkey, $pubkey);
        }
    }
?>