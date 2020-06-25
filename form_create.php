<?php
    include("includes/header.php");
    include("includes/handlers.php");
    include("includes/classes/Survey.php");

    $survey_id = $_GET["survey_id"];
    $survey = new Survey($con, $survey_id);
?>


<div class="container bg-light">
    <div class="row">
        <div class="col-sm-4">
            <h1>Edit Form</h1><br>
        </div>
        <div class="col-sm-8">
            <h2 id="form-title"></h2>
            <div class="alert alert-success">
                Status: <span id="save_status">saved</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 add_new">

            <div class="card">
                <div class="card-header">
                    <p id="view_results"></p>
                    <button class="btn btn-primary" id="copy_to_clipboard">Share Form</button><br>
                </div>
                <input class="btn add_new_btn" type="button" value="+ Checkbox" id="add_checkbox"><br>
                <input class="btn add_new_btn" type="button" value="+ Radio" id="add_radio"><br>
                <input class="btn add_new_btn" type="button" value="+ Text" id="add_text"><br>
                <div class="card-footer">
                    <form action="form_create.php" name="save_form_data" method="post">
                        <input type="hidden" name="form_data" id="form_data">
                        <input class="btn btn-success" type="button" value="Save Form" name="save_button" id="save">
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-sm-8">
            <div id="form_elements"></div>
        </div>
    </div>

</div>


<script>

var sec_encrypted = getCookie("privkey")
// password used to encrypt the private key sec
var privkey_password = getCookie("privkey_password")

// decrypt secret key
var sec = sjcl.decrypt(privkey_password, sec_encrypted)
// deserialize secret key
sec = new sjcl.ecc.elGamal.secretKey(
    sjcl.ecc.curves.c256,
    sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(sec))
)


var urlParams = new URLSearchParams(window.location.search)
var survey_id = urlParams.get("survey_id");
var parsedHash = new URLSearchParams(
    window.location.hash.substr(1) // skip the first char (#)
);

<?php
    $key = $survey->data['encryption_key'];
    $data = $survey->data['data'];
    echo "var data = '$data'\n";
    echo "var key = '$key'\n";
?>

key = sjcl.decrypt(sec,key);

$(document).ready(function () {    
    // construct form from JSON
    data = sjcl.decrypt(key, data);
    data = JSON.parse(data);

    // build form elements
    $("#form-title").text(data.name);
    for (question of data.elements) {
        addFormElementAdmin(question);
    }

    // create link for viewing results
    var form_link = document.createElement('A');
    form_link.href = "results.php?survey_id="+survey_id;
    form_link.innerText = "View Results";
    $("#view_results").append(form_link);

    $("input").change(function(){
        $("#save_status").text("unsaved changes");
        $("#save_status").parent().addClass("alert-danger")
        .removeClass("alert-success");
    })
    
    $(".add_new_btn").click(function() {
        $("#save_status").text("unsaved changes");
        $("#save_status").parent().addClass("alert-danger")
        .removeClass("alert-success");
        addFormElementAdmin(getElementObj(this));
    });


    $("#save").click(function() {
        var updated_data = updateFormData();
        updated_data.name = data.name;
        updated_data = sjcl.encrypt(key,JSON.stringify(updated_data));
        $("#form_data").val(updated_data);
        
        let formData = new FormData(document.forms.save_form_data);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            $("#save_status").text(this.responseText);
            if (this.responseText != "saved") {
                $("#save_status").parent().addClass("alert-danger")
                .removeClass("alert-success");
            } else {
                $("#save_status").parent().removeClass("alert-danger")
                .addClass("alert-success");
            }
            // alert(this.responseText);
        }
        xmlhttp.open("POST","includes/form_handlers/save_form.php?survey_id="+survey_id);
        xmlhttp.send(formData);
    });

    $("#copy_to_clipboard").click(function(){
        var link = "form.php?survey_id="+survey_id+"#key="+key;
        navigator.clipboard.writeText(link);
    });
    
});

</script>