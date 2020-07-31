<?php
    include("includes/header.php");
    include("includes/navbar.php");
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
            <br><br>
        </div>
        
        <div class="col-sm-8">
            <h2>Your Forms</h2>
            <input class="form-control" id="searchForms" type="text" placeholder="Search..">
            <br>
            <div class="grid-container" id="form_list"></div>
        </div>
    </div>

</div>

<!-- Create Form Modal -->
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

<!-- Share Form Modal -->
<div class="modal fade" id="share-link-modal">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header"><h4>Share your form with others!</h4></div>
    <div class="modal-body">
        <p>This link contains the key to decrypt the form content. Only share with people you trust!</p>
        <div class="input-group mb-3">
            <input id="share-link-input" type="text" class="form-control">
            <div class="input-group-append">
                <button onclick="copyLink()" class="btn btn-success" type="submit">Copy</button>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>
</div>
</div>

<!-- Delete Form Modal -->
<div class="modal fade" id="delete-form-modal">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header"><h4>Confirm deletion</h4></div>
    <div class="modal-body">
        <div class="alert alert-danger">You are about to <strong>permanently delete</strong> this form and its submissions. 
        Are you sure you want to proceed? You will not be able to recover any associated data.</div>
        <input type="hidden" id="survey_id">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="delete-form-btn">Delete</button>
    </div>
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

function copyLink() {
    var copyText = document.getElementById("share-link-input");
    copyText.select();
    copyText.setSelectionRange(0,99999);
    document.execCommand("copy");

}

$(document).ready(function () {
    // Get individual forms from server
    var your_forms = [];

    function deleteForm() {
        var survey_id = $("#survey_id").val();
        $.ajax({
            url: "includes/form_handlers/delete_form.php?survey_id="+survey_id,
            success: function(result) {
                if (result != "1") {
                    $("<div>").addClass("alert alert-danger alert-delete-status fade show fixed-bottom")
                    .appendTo($(".container"))
                    .html("Error deleting form...");
                    setTimeout(function() {
                        $(".alert-delete-status").alert('close');
                    }, 2000);
                } else {
                    location.reload();
                }
                
            }
        });
    }

    function drawFormList() {
        your_forms = [];
        // TODO: The problem is the following: the php code is evaluated once
        // when the page is loaded, so when ajax calls this function, it simply
        // uses the old php code. The code section below needs to be done in ajax
        <?php
            $user_id = $user->data['id'];

            $user_forms_query = mysqli_query($con, "SELECT * from surveys WHERE user_id='$user_id'");
            echo "var num_forms = " . mysqli_num_rows($user_forms_query). ";\n";
            while ($row = mysqli_fetch_assoc($user_forms_query)) {
                echo "var form = {};\n";
                echo "form.urlID = '".$row['url_id']."';\n";
                echo "form.formName = '".$row['form_name']."';\n";
                echo "form.encryptionKey = '".$row['encryption_key']."';\n";
                echo "your_forms.push(form);\n";
            }
        ?>
            
        // Construct form_list
        for (i=0; i<num_forms; i++) {
            $("<div>").addClass("card survey-card m-2 shadow-sm")
            .append(
                $("<div>").addClass("card-body")
                .append(
                    $("<a>").addClass("card-link")
                    .attr("href","form_create.php?survey_id="+your_forms[i].urlID)
                    .append(
                        $("<h5>").addClass("card-link")
                        .text(your_forms[i].formName)
                    )
                )
                    
            )
            .append(
                $("<div>").addClass("card-body text-right")
                .append(
                    $("<div>").addClass("btn-group")
                    .append(
                        $("<a>").addClass("btn btn-primary")
                        .text("View Results")
                        .attr("href","results.php?survey_id="+your_forms[i].urlID)
                    )
                    .append(
                        $("<button>").addClass("btn btn-primary dropdown-toggle dropdown-toggle-split")
                        .attr("data-toggle","dropdown")
                        .html("<span class='caret'></span>")
                    )
                    .append(
                        $("<div>").addClass("dropdown-menu")
                        .append(
                            $("<a>").addClass("dropdown-item")
                            .html("<i class='fa fa-fw fa-pencil'></i> Edit")
                            .attr("href","form_create.php?survey_id="+your_forms[i].urlID)
                        )
                        .append(
                            $("<button>").addClass("dropdown-item")
                            .html("<i class='fa fa-fw fa-paper-plane'></i> Share")
                            .attr({
                                "data-form-id":i,
                                "data-toggle":"modal",
                                "data-target":"#share-link-modal"
                            })
                            .click(function(){
                                var survey_id = your_forms[Number($(this).attr("data-form-id"))].urlID;
                                var key = sjcl.decrypt(sec,your_forms[Number($(this).attr("data-form-id"))].encryptionKey);
                                var link = "<?php echo $config['domainName'];?>/form.php?survey_id="+survey_id+"#key="+key;
                                $("#share-link-input").val(link);
                            })
                        )
                        .append(
                            $("<button>").addClass("dropdown-item")
                            .html("<i class='fa fa-fw fa-trash'></i> Delete")
                            .attr({
                                "data-form-id":i,
                                "data-toggle":"modal",
                                "data-target":"#delete-form-modal"
                            })
                            .click(function() {
                                var survey_id = your_forms[Number($(this).attr("data-form-id"))].urlID;
                                $("#survey_id").val(survey_id);
                            })
                        )
                    )
                )
            )
            .appendTo("#form_list");
        }
    }

    drawFormList();

    $("#delete-form-btn").click(deleteForm);

    $("#searchForms").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#form_list h5").filter(function() {
            $(this).parent().parent().parent().toggle($(this).text().toLowerCase().indexOf(value) > -1)
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
    });
});

</script>