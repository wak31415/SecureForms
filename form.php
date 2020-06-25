<?php
    include("config/config.php");
    include("includes/classes/Survey.php");
    include('includes/form_handlers/submit_response.php');
    
    
    $survey_id = $_GET['survey_id'];
    $survey = new Survey($con, $survey_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
 
    <!-- JavaScript -->
    <script type = "text/javascript"
        src = "https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <script src="js/sjcl.js"></script>
    <script src="js/cookie_handlers.js"></script>
    <script src="js/form_scripts.js"></script>
    <script src="js/general_scripts.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <title>SecureForms</title>
</head>
<body>

<div class="container bg-light">
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