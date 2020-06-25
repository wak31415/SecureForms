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
        src = "https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="js/sjcl.js"></script> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>SecureForms</title>
</head>
<body>

<div class="container">
    
    <h1>SecureForms</h1>
    <p>End-to-End encrypted surveys and forms</p>
    
    <div class="login bg-light mx-auto rounded-lg">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#login">Log In</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#register">Register</a>
            </li>
        </ul>
    
        <div class="tab-content">
            <div id="login" class="tab-pane container fade"><br>
                <form action="register.php" id="loginform" method="POST">
                    <div class="form-group">
                        <label for="log_email">Email</label>
                        <input name="log_email" id="log_email" type="email" class="form-control" value="<?php
                            if(isset($_SESSION['log_email'])) { echo $_SESSION['log_email']; }
                            ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="log_password">Password</label>
                        <input name="log_password" id="log_password" type="password" class="form-control" required>
                    </div>
                    <button type="submit" name="login_button" class="btn btn-primary">Log in</button>
            
                    <?php if(in_array("Email or password incorrect", $error_array)) echo "Email or password incorrect<br>"; ?>
                </form>
            </div>
            
            <div id="register" class="tab-pane container active"><br>
                <form action="register.php" id="registerform" method="POST">
                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input name="reg_email" id="reg_email" type="email" class="form-control" value="<?php
                            if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; }
                            ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input name="reg_password" id="reg_password" type="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password2">Retype Password</label>
                        <input name="reg_password2" id="reg_password2" type="password" class="form-control" required>
                    </div>
                    <input name="privkey" id="privkey" type="text" style="display:none">
                    <input name="pubkey" id="pubkey" type="text" style="display:none">
            
                    <button type="submit" name="register_button" class="btn btn-primary">Register</button>
            
                    <?php if(in_array("Email or password incorrect", $error_array)) echo "Email or password incorrect<br>"; ?>
                </form>
            </div>
        </div>
    </div>
    
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