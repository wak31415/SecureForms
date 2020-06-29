<?php
    // include("../../config/config.php");
    $config = json_decode(file_get_contents("../../config/server.json"),true);
    $myfile = fopen("../../".$config["dbPasswordPath"], "r") or die("Unable to open file!");
    $password = rtrim(fgets($myfile),"\r\n");
    fclose($myfile);

    $con = mysqli_connect("127.0.0.1", "root", $password, "secureforms");
    
    $encrypted_data = $_POST['form_data'];

    $survey_id = $_GET['survey_id'];
    if(mysqli_query($con,"UPDATE surveys SET data='$encrypted_data' WHERE url_id='$survey_id';")){
        echo "saved";
        // header("Location:index.php");
    } else {
        echo "Failed to update entry";
    }

?>
