<?php

    if(isset($_POST['submit_resp'])) {
        $id = $_POST['id'];
        $data = $_POST['response_data'];
        
        $success = mysqli_query($con, "INSERT INTO submissions VALUES(NULL,'$id','$data')");
        if($success) {
            header("Location: thank_you.php");
        } else {
            echo "Error submitting your response<br>";
        }
    }

?>