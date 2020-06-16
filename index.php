<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/handlers.php");

    $user = new User($con, $userLoggedIn);
    setcookie("privkey", $user->data["priv_key_encrypted"], time()+86400*30,"/");
    setcookie("pubkey", $user->data["public_key"], time()+86400*30,"/");
    setcookie("secret_msg", $user->getSecretMessage());
?>

<label for="password">Your account password:</label>
<input type="password" id="password">
<button id="encrypt" type="submit">Get secret message!</button>
<p id="decrypted_message"></p>


<script>
var sjcl = require(['js/sjcl.js'])
var pub = getCookie("pubkey")
var sec = getCookie("privkey")
console.log(sec)

$(document).ready(function () {
    
    
    $("#encrypt").click(function() {
        var password = $("#password").val()
        sec = sjcl.decrypt(password, sec)
        console.log(sec)
        // Unserialized private key:
        sec = new sjcl.ecc.elGamal.secretKey(
            sjcl.ecc.curves.c256,
            sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
        )
        console.log(sec)

        var ct = getCookie("secret_msg")
        var pt = sjcl.decrypt(sec, ct)
        console.log(ct)
        console.log(pt)
        
        $("#decypted_message").text(pt)
    });
});
</script>