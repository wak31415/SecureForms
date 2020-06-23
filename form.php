<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Survey.php");
    include("includes/handlers.php");

    $surveyid = $_GET['survey_id'];
    $survey = new Survey($con, $surveyid);
?>



<script>
// var sjcl = require(['js/sjcl.js'])

var parsedHash = new URLSearchParams(
    window.location.hash.substr(1) // skip the first char (#)
);

var key = parsedHash.get('key');

<?php
    $data = $survey->data['data'];
    echo "var data = '$data'";
?>

$(document).ready(function () {
    // decrypt form data
    // sjcl.decrypt(key, data)
    
    // construct form from JSON
    console.log(data)
    console.log(key)
    data = sjcl.decrypt(key, data)
    data = JSON.parse(data)
    console.log(data)
    
});

</script>