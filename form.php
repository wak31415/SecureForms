<?php
    include("includes/header.php");
    include("includes/classes/User.php");
    include("includes/classes/Survey.php");
    include("includes/handlers.php");

    $surveyid = $_GET['survey_id'];
    $survey = new Survey($con, $surveyid);
    echo $survey->data['data'];
?>



<script>
var sjcl = require(['js/sjcl.js'])
const params = new URLSearchParams(window.location.search)
const key = params.get('key')

<?php
    $data = $survey->data['data'];
    echo "var data = '$data'";
?>

$(document).ready(function () {
    // decrypt form data
    // sjcl.decrypt(key, data)

    // construct form from JSON
    console.log(data)
    data = JSON.parse('{ "name":"John", "age":30, "city":"New York"}')
    console.log(data)
    
});

</script>