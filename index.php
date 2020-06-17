<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/handlers.php");

    $user = new User($con, $userLoggedIn);
    setcookie("privkey", $user->data["priv_key_encrypted"], time()+86400*30,"/");
    setcookie("pubkey", $user->data["public_key"], time()+86400*30,"/");
    setcookie("secret_msg", $user->getSecretMessage());
?>

<label for="password">Your account password:</label>
<button id="encrypt" type="submit">Get secret message!</button>
<p id="decrypted_message"></p>


<script>
var sjcl = require(['js/sjcl.js'])
var pub = getCookie("pubkey")
var sec_encrypted = getCookie("privkey")
var privkey_password = getCookie("privkey_password")

$(document).ready(function () {
    
    
    $("#encrypt").click(function() {
        var sec = sjcl.decrypt(privkey_password, sec_encrypted)
        // Unserialized private key:
        sec = new sjcl.ecc.elGamal.secretKey(
            sjcl.ecc.curves.c256,
            sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
        )

        var ct = getCookie("secret_msg")
        var pt = sjcl.decrypt(sec, ct)
        
        $("#decrypted_message").text(pt)
    });
});
</script>