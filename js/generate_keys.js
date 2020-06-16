var sjcl = require(['js/sjcl.js'])

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
        sec = sjcl.encrypt(password, sec)
        
        $("#privkey").val(sec)
        $("#pubkey").val(pub)

        $("#secret_msg").val(sjcl.encrypt(pair.pub, $("#secret_msg").val()))
        
        
    });
});