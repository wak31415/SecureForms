<?php
    if (isset($_SESSION["email"])) {
        header("Location: index.php");
    }
    include("includes/header.php");
    include('includes/form_handlers/user_authorization.php');
    include('includes/form_handlers/login_handler.php');
    include('includes/form_handlers/register_handler.php');
?>

<div class="container">
    
    <div class="login bg-light mx-auto rounded-lg">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link <?php if(isset($_SESSION['log_email'])) echo 'active';?>" data-toggle="tab" href="#login">Log In</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(!isset($_SESSION['log_email'])) echo 'active';?>" data-toggle="tab" href="#register">Register</a>
            </li>
        </ul>
    
        <div class="tab-content">
            <div id="login" class="tab-pane container <?php if(isset($_SESSION['log_email'])) {echo 'active';} else echo 'fade';?>"><br>
                <?php 
                    
                    if(in_array("Email or password incorrect", $error_array)) {
                        echo "<div class='alert alert-danger'>Email or password incorrect</div>";
                    }
                ?>
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
                </form>
            </div>
            
            <div id="register" class="tab-pane container <?php if(!isset($_SESSION['log_email'])) {echo 'active';} else echo 'fade';?>"><br>
                <?php
                    if(in_array("Email already in use", $error_array)) {
                        echo "<div class='alert alert-danger'>This email is already in use</div>";
                    }
                ?>
                <form action="register.php" id="registerform" class="needs-validation" novalidate="novalidate" method="POST">
                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input name="reg_email" id="reg_email" type="email" class="form-control" value="<?php
                            if(isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; }
                            ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password</label>
                        <input pattern=".{12,}"  name="reg_password" id="reg_password" type="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password2">Retype Password</label>
                        <input name="reg_password2" id="reg_password2" type="password" class="form-control" required>
                    </div>
                    <input name="privkey" id="privkey" type="text" style="display:none">
                    <input name="pubkey" id="pubkey" type="text" style="display:none">
            
                    <button type="submit" name="register_button" class="btn btn-primary">Register</button>
            
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
    $("#registerform").validate({
        rules: {
            reg_email: {
                required: true,
                email: true
            },
            reg_password: {
                required: true,
                minlength: 12
            },
            reg_password2: {
                required: true,
                equalTo: "#reg_password"
            }
        }, 
        messages: {
            reg_email: "Please enter a valid email",
            reg_password: {
                required: "Please enter a password",
                minlength: "Your password must have at least 12 characters"
            },
            reg_password2: {
                required: "Please confirm your password",
                equalTo: "Passwords don't match"
            }
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            // Add the `help-block` class to the error element
            error.addClass( "invalid-feedback" );

            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.parent( "label" ) );
            } else {
                error.insertAfter( element );
            }
        }
    });
    

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

        sec = sjcl.encrypt(hexKey, sec);
        sessionStorage.privkey_password = hexKey;
        
        $("#privkey").val(sec);
        $("#pubkey").val(pub);       
        
    });

    $("#loginform").submit(function () {                
        // encrypt private key using account password
        var password = $("#log_password").val();

        var hmacSHA256 = function (key) {
            var hasher = new sjcl.misc.hmac( key, sjcl.hash.sha256 );
            this.encrypt = function () {
                return hasher.encrypt.apply( hasher, arguments );
            };
        };
        
        var passwordSalt = sjcl.codec.hex.toBits( "cf7488cd1e48e84990f51b3f121e161318ba2098aa6c993ded1012c955d5a3e8" );
        var derivedKey = sjcl.misc.pbkdf2( password, passwordSalt, 100, 256, hmacSHA256 );
        var hexKey = sjcl.codec.hex.fromBits( derivedKey );
        
        sessionStorage.privkey_password = hexKey;
    });
});
</script>

</body>
</html>