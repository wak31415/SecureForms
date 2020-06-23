<?php


function generate_string($strength = 16) {
    $input = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
 
    return $random_string;
}

if(isset($_POST['new_form'])) {
    $url_id = generate_string(19);
    $user_id = $user->data['id'];
    $form_name = $_POST['form_name'];
    $encryption_key = $_POST['form_key'];
    $data = $_POST['form_data'];

    // TODO: Ensure uniqueness of url_id

    $success = mysqli_query($con, "INSERT INTO surveys VALUES(NULL,'$url_id','$user_id','$form_name','$encryption_key','$data')");

    if($success) {
        header("Location: form_create.php?survey_id=$url_id");
        echo "successfully created form<br>";
    }
    else {
        echo "Error creating new form<br>";
    }
}

?>