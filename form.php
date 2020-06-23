<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Survey.php");
    include("includes/handlers.php");
    include('includes/form_handlers/submit_response.php');
    
    
    $survey_id = $_GET['survey_id'];
    $survey = new Survey($con, $survey_id);
?>

<div class="form_container">
    <h1 id="form_title"></h1>
    <form id="form_frame">
        <div id="form_elements"></div>
    </form>
    <form action="form.php" id="submit_responses" name="submit_responses" method="post">
        <input type="hidden" name="id" id="survey_id">
        <input type="hidden" name="response_data" id="response_data">
        <input type="submit" value="Submit" name="submit_resp">
    </form>
</div>

<script>
// var sjcl = require(['js/sjcl.js'])

var parsedHash = new URLSearchParams(
    window.location.hash.substr(1) // skip the first char (#)
);

var key = parsedHash.get('key');

// load data from server
<?php
    $user_id = $survey->data['user_id'];
    $user_data = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM users WHERE id='$user_id'"));
    $pub_key = $user_data['public_key'];    
    $data = $survey->data['data'];
    $id = $survey->data['id'];
    echo "var data = '$data'\n";
    echo "var pub = '$pub_key'\n";
    echo "var id = '$id'\n";
?>

pub = new sjcl.ecc.elGamal.publicKey(
    sjcl.ecc.curves.c256, 
    sjcl.codec.base64.toBits(pub)
)

$(document).ready(function () {
    // decrypt form data
    data = sjcl.decrypt(key, data)
    
    // construct form from JSON
    data = JSON.parse(data)
    $("#form_title").text(data.name);
    for (question of data.elements) {
        addFormElement(question);
    }

    $("#submit_responses").submit(function() {
        // TODO: escape commas in text entry fields
        var data = $("#form_frame").serialize();
        data = sjcl.encrypt(pub,data);
        $("#response_data").val(data);
        $("#survey_id").val(id);
    });

});

</script>