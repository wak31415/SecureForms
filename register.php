<?php
    require 'config/config.php';
    require 'includes/form_handlers/user_authorization.php';
    require 'includes/form_handlers/login_handler.php';
    require 'includes/form_handlers/register_handler.php';
    if (isset($_SESSION["email"])) {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script type = "text/javascript"
        src = "https://code.jquery.com/jquery-2.1.1.min.js"></script> 
    <script type="text/javascript" src="js/require.js"></script>
    <script type="text/javascript" src="js/generate_keys.js"></script> 
    <title>SecureForms</title>
</head>
<body>

<h1>SecureForms</h1>
<p>End-to-End encrypted surveys and forms</p>


<h2>Log In</h2>

<div id="login">
    <form action="register.php" id="loginform" method="POST">
        <div class="row">
            <div class="input-field col s12">
            <input name="log_email" id="email" type="email" class="validate" value="<?php
                if(isset($_SESSION['log_email'])) { echo $_SESSION['log_email']; }
                ?>"
                required>
            <label for="email">Email</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
            <input name="log_password" id="password" type="password" class="validate" required>
            <label for="password">Password</label>
            </div>
        </div>
        <button type="submit" name="login_button">Log in</button>

        <?php if(in_array("Email or password incorrect", $error_array)) echo "Email or password incorrect<br>"; ?>
    </form>
</div>

<h2>Register</h2>

<div id="register">
    <form action="register.php" id="registerform" method="POST">
        <div class="row">
            <div class="input-field col s12">
            <input name="reg_email" id="reg_email" type="email" class="validate" value="<?php
                if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; }
                ?>"
                required>
            <label for="email">Email</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
            <input name="reg_password" id="reg_password" type="password" class="validate" required>
            <label for="password">Password</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
            <input name="reg_password2" id="reg_password2" type="password" class="validate" required>
            <label for="password">Retype Password</label>
            </div>
        </div>
        <input name="secret_msg" id="secret_msg" type="text" required>
        <input name="privkey" id="privkey" type="text" style="display:none">
        <input name="pubkey" id="pubkey" type="text" style="display:none">

        <button type="submit" name="register_button">Log in</button>

        <?php if(in_array("Email or password incorrect", $error_array)) echo "Email or password incorrect<br>"; ?>
    </form>
</div>


</body>
</html>