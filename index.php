<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/handlers.php");
    
    $user = new User($con, $userLoggedIn);
    include("includes/form_handlers/create_new_form.php");
    setcookie("privkey", $user->data["priv_key_encrypted"], [
        'expires' => time()+86400*30,
        'path' => '/',
        'samesite' => 'Strict',
    ]);
    setcookie("pubkey", $user->data["public_key"],  [
        'expires' => time()+86400*30,
        'path' => '/',
        'samesite' => 'Strict',
    ]);
?>

<h2>Your Forms:</h2>
<ul id="form_list"></ul>

<h2>Create a new form:</h2>
<form action="index.php" id="create_new_form" method="post">
    <label for="form_name">Form Name:</label>
    <input type="text" name="form_name" id="form_name">
    <input type="hidden" name="form_key" id="form_key">
    <input type="hidden" name="form_data" id="form_data">
    <button type="submit" name="new_form">Create!</button>
</form>


<script>
var sec_encrypted = getCookie("privkey")
// password used to encrypt the private key sec
var privkey_password = getCookie("privkey_password")

var pub = getCookie("pubkey")
// deserialize public key
pub = new sjcl.ecc.elGamal.publicKey(
    sjcl.ecc.curves.c256, 
    sjcl.codec.base64.toBits(pub)
)

// decrypt secret key
var sec = sjcl.decrypt(privkey_password, sec_encrypted)
// deserialize secret key
sec = new sjcl.ecc.elGamal.secretKey(
    sjcl.ecc.curves.c256,
    sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
)

$(document).ready(function () {
    var your_forms = []
    
    <?php
        $user_id = $user->data['id'];
        $user_forms_query = mysqli_query($con, "SELECT * from surveys WHERE user_id='$user_id'");
        echo "var num_forms = " . mysqli_num_rows($user_forms_query). "\n";
        while ($row = mysqli_fetch_assoc($user_forms_query)) {
            echo "var form = {}\n";
            echo "form.urlID = '".$row['url_id']."'\n";
            echo "form.formName = '".$row['form_name']."'\n";
            echo "your_forms.push(form)\n";
        }
    ?>
    console.log(your_forms)
    
    for (i=0; i<num_forms; i++) {
        var form_item = document.createElement('LI');
        var form_link = document.createElement('A');
        form_link.href = "form_create.php?survey_id="+your_forms[i].urlID;
        form_link.innerText = your_forms[i].formName;
        form_item.appendChild(form_link);
        document.getElementById('form_list').appendChild(form_item);
    }
    
    $("#create_new_form").submit(function () {
        var key = sjcl.ecc.elGamal.generateKeys(256).sec.get()
        key = sjcl.codec.base64.fromBits(key)
        key = key.replace(/[^a-zA-Z0-9]/g,'')
        
        var data = {"name":$("#form_name").val(),"elements":[],"params":{}}
        
        $("#form_data").val(sjcl.encrypt(key, JSON.stringify(data)))
        
        key = sjcl.encrypt(pub, key)
        
        $("#form_key").val(key)
    })

});
</script>