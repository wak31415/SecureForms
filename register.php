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
    <script type="text/javascript" src="js/sjcl.js"></script> 
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
            <input name="log_password" id="log_password" type="password" class="validate" required>
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

<script>
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;SameSite=strict";
}

$(document).ready(function () {
    $("#registerform").submit(function () {
        // Generate private/public keys
        var pair = sjcl.ecc.elGamal.generateKeys(256)
        var pub = pair.pub.get(), sec = pair.sec.get()

        // serialize them to strings
        pub = sjcl.codec.base64.fromBits(pub.x.concat(pub.y))
        sec = sjcl.codec.base64.fromBits(sec)
                
        // encrypt private key using account password
        var password = $("#reg_password").val()

        var hmacSHA256 = function (key) {
            var hasher = new sjcl.misc.hmac( key, sjcl.hash.sha256 );
            this.encrypt = function () {
                return hasher.encrypt.apply( hasher, arguments );
            };
        };
        
        var passwordSalt = sjcl.codec.hex.toBits( "cf7488cd1e48e84990f51b3f121e161318ba2098aa6c993ded1012c955d5a3e8" );
        var derivedKey = sjcl.misc.pbkdf2( password, passwordSalt, 100, 256, hmacSHA256 );
        var hexKey = sjcl.codec.hex.fromBits( derivedKey );

        sec = sjcl.encrypt(hexKey, sec)
        setCookie("privkey_password", hexKey, 1)
        
        $("#privkey").val(sec)
        $("#pubkey").val(pub)

        $("#secret_msg").val(sjcl.encrypt(pair.pub, $("#secret_msg").val()))
        
        
    });

    $("#loginform").submit(function () {                
        // encrypt private key using account password
        var password = $("#log_password").val()

        var hmacSHA256 = function (key) {
            var hasher = new sjcl.misc.hmac( key, sjcl.hash.sha256 );
            this.encrypt = function () {
                return hasher.encrypt.apply( hasher, arguments );
            };
        };
        
        var passwordSalt = sjcl.codec.hex.toBits( "cf7488cd1e48e84990f51b3f121e161318ba2098aa6c993ded1012c955d5a3e8" );
        var derivedKey = sjcl.misc.pbkdf2( password, passwordSalt, 100, 256, hmacSHA256 );
        var hexKey = sjcl.codec.hex.fromBits( derivedKey );

        setCookie("privkey_password", hexKey, 1)
    });
});
</script>

</body>
</html>