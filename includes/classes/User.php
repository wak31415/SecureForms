<?php

class User {
    public $data;
    private $con;
    private $user_details_query;
    
    public function __construct($con, $identifier) {
        $this->con = $con;
        $this->user_details_query = mysqli_query($con, "SELECT * FROM users WHERE email='$identifier' OR id='$identifier'");
        $this->fetchNewData();
    }

    public function fetchNewData() {
        $this->data = mysqli_fetch_array($this->user_details_query);
    }

    public function getID() {
        return $this->data["id"];
    }

    public function getSecretMessage() {
        return $this->data["secret_msg"];
    }

    // public function getProfilePic() {
    //     return $this->data["profile_pic"];
    // }

    // public function getName() {
    //     return $this->data["first_name"] . " " . $this->data["last_name"];
    // }

    // public function incrementPostCount() {
    //     $new_post_count = $this->data["num_posts"] + 1;
    //     $user_id = $this->data["id"];
    //     mysqli_query($this->con, "UPDATE users 
    //         SET num_posts='$new_post_count' WHERE id='$user_id'");
    // }

}

?>