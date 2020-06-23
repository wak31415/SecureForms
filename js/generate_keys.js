var sjcl = require(['js/sjcl.js'])

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
        console.log(hexKey)

        sec = sjcl.encrypt(hexKey, sec)
        setCookie("privkey_password", hexKey, 1)
        
        $("#privkey").val(sec)
        $("#pubkey").val(pub)

        $("#secret_msg").val(sjcl.encrypt(pair.pub, $("#secret_msg").val()))
        
        
    });

    $("#loginform").submit(function () {                
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
        console.log(hexKey)

        setCookie("privkey_password", hexKey, 1)
    });
});