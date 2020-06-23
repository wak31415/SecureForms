<?php
    include("includes/header.php");
    include("includes/handlers.php");
    include("includes/classes/Survey.php");

    $survey_id = $_GET["survey_id"];
    $survey = new Survey($con, $survey_id);
?>

<div class="form_container">
    <p id="save_status">Status: saved</p>
    <div id="form_elements"></div>

    <div class="add_new">
        <input class="add_new_btn" type="button" value="+ Checkbox" id="add_checkbox">
        <input class="add_new_btn" type="button" value="+ Radio" id="add_radio">
        <input class="add_new_btn" type="button" value="+ Text" id="add_text">
        <form action="form_create.php" name="save_form_data" method="post">
            <input type="hidden" name="form_data" id="form_data">
            <input type="button" value="Save Form" name="save_button" id="save">
        </form>
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

    for (question of data.elements) {
        addFormElement(question);
    }

    $("input").change(function(){
        $("#save_status").text("Status: unsaved changes")
        .addClass("unsaved");
    })
    
    $(".add_new_btn").click(function() {
        $("#save_status").text("Status: unsaved changes")
        .addClass("unsaved");
        addFormElement(getElementObj(this));
    });


    $("#save").click(function() {
        var data = JSON.stringify(updateFormData());
        data = sjcl.encrypt(key,data);
        $("#form_data").val(data);
        
        let formData = new FormData(document.forms.save_form_data);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            $("#save_status").text("Status: " + this.responseText)
            .removeClass("unsaved");
            // alert(this.responseText);
        }
        xmlhttp.open("POST","includes/form_handlers/save_form.php?survey_id="+survey_id);
        xmlhttp.send(formData);
    });
    
});

</script>