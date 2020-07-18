<?php
    include("includes/header.php");
    include("includes/handlers.php");
    include("includes/classes/Survey.php");
    include("includes/redirect.php");

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
            <div class="alert alert-success" id="save-alert-status">
                Status: <span id="save_status">saved</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 add_new">

            <div class="card sticky">
                <div class="card-header">
                <h3>Tools</h3>
                </div>
                <div class="btn-group">
                    <button class="btn btn-light add_new_btn" id="add_checkbox" title="Checkbox"><i class="fa fa-check-square"></i></button>
                    <button class="btn btn-light add_new_btn" id="add_radio" title="Radio"><i class="fa fa-dot-circle-o"></i></button>
                    <button class="btn btn-light add_new_btn" id="add_text" title="Text"><i class="fa fa-keyboard-o"></i></button>
                </div>
                <div class="card-footer">
                    <div class="btn-group d-flex" role="group">
                        <button class="btn btn-outline-success" name="save_button" id="save">
                            <i class="fa fa-floppy-o"></i>
                            Save
                        </button>
                        <form action="form_create.php" name="save_form_data" method="post">
                            <input type="hidden" name="form_data" id="form_data">
                        </form>
                        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#share-link-modal" id="copy_to_clipboard">
                            <i class="fa fa-paper-plane"></i>
                            Share
                        </button>
                        <a class="btn btn-outline-secondary" id="view_results">
                            <i class="fa fa-bar-chart"></i>
                            Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-8">
            <div id="form_elements"></div>
        </div>
    </div>

</div>

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

function copyLink() {
    var copyText = document.getElementById("share-link-input");
    copyText.select();
    copyText.setSelectionRange(0,99999);
    document.execCommand("copy");

}

$(document).ready(function () {  
    $('[data-toggle="tooltip"]').tooltip();

    // construct form from JSON
    data = sjcl.decrypt(key, data);
    data = JSON.parse(data);
    
    // build form elements
    $("#form-title").text(data.name);
    for (question of data.elements) {
        addFormElementAdmin(question);
    }
    
    // create link for viewing results
    $("#view_results").attr("href","results.php?survey_id="+survey_id);
    
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
        $("#form_elements").children().last().find("input").first().focus();
        $('html, body').animate({ scrollTop: $(document).height() - $(window).height() }, 'fast');
        // window.scrollTo(0,$(document).height() - $(window).height());
    });

    function saveForm() {
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
    }

    $("#save").click(saveForm);

    $(window).bind('keydown', function(event) {
        if (event.ctrlKey || event.metaKey) {
            switch (String.fromCharCode(event.which).toLowerCase()) {
            case 's':
                event.preventDefault();
                saveForm();
                break;
            }
        }
    });

    $("#copy_to_clipboard").click(function(){
        var link = "<?php echo $config['domainName'];?>/form.php?survey_id="+survey_id+"#key="+key;
        $("#share-link-input").val(link);
    });
    
});

</script>