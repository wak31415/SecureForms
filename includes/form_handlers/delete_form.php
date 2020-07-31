<?php
    include("../classes/Survey.php");
    $config = json_decode(file_get_contents("../../config/server.json"),true);
    $myfile = fopen("../../".$config["dbPasswordPath"], "r") or die("Unable to open file!");
    $password = rtrim(fgets($myfile),"\r\n");
    fclose($myfile);

    $con = mysqli_connect("127.0.0.1", "root", $password, "secureforms");
    if (!$con) {
        die("Could not connect");
    }
    
    $survey_id = $_GET['survey_id'];
    $survey = new Survey($con, $survey_id);
    $id = $survey->data['id'];

    if(mysqli_query($con, "DELETE FROM surveys WHERE url_id = '$survey_id'")){
        if(mysqli_query($con, "DELETE FROM submissions WHERE survey_id = '$id'")) {
            echo "1";
        } else {
            echo "0";
        }
    } else {
        echo "0";
    }
?>