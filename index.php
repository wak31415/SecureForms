<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/handlers.php");
    
    include("includes/redirect.php");
    $user = new User($con, $userLoggedIn);
    // echo $userLoggedIn;
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


<div class="container bg-light">
    <div class="row">
        <div class="col-sm-4">
            <h2>Control Center</h2>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
            New Form
            </button>
        </div>
        
        <div class="col-sm-8">
            <h2>Your Forms</h2>
            <input class="form-control" id="searchForms" type="text" placeholder="Search..">
            <br>
            <div class="list-group" id="form_list"></ul>
        </div>
    </div>

</div>

<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Enter a name for your new form</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <form action="index.php" id="create_new_form" method="post" autocomplete="off">
            <!-- Modal body -->
            <div class="modal-body">
                <div class="form-group">
                    <input class="form-control" type="text" name="form_name" id="form_name">
                </div>
                <div class="form-group">
                    <input type="hidden" name="form_key" id="form_key">
                    <input type="hidden" name="form_data" id="form_data">
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit" name="new_form">Create</button>
            </div>
        </form>

        </div>
    </div>
</div>


<script>
var sec_encrypted = getCookie("privkey")
// password used to encrypt the private key sec
var pub = getCookie("pubkey");
// deserialize public key
pub = new sjcl.ecc.elGamal.publicKey(
    sjcl.ecc.curves.c256, 
    sjcl.codec.base64.toBits(pub)
);

// decrypt secret key
var sec = sjcl.decrypt(sessionStorage.privkey_password, sec_encrypted);
// deserialize secret key
sec = new sjcl.ecc.elGamal.secretKey(
    sjcl.ecc.curves.c256,
    sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
);

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
        
    for (i=0; i<num_forms; i++) {
        var form_link = document.createElement('A');
        form_link.href = "form_create.php?survey_id="+your_forms[i].urlID;
        form_link.innerText = your_forms[i].formName;
        $(form_link).addClass("list-group-item list-group-item-action");
        $("#form_list").append(form_link);
    }

    $("#searchForms").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#form_list a").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
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