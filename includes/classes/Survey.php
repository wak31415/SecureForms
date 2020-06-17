<?php

class Survey {
    public $data;
    private $con;
    private $survey_details_query;

    public function __construct($con, $survey_id) {
        $this->con = $con;
        $this->survey_details_query = mysqli_query($con, "SELECT * FROM surveys WHERE id='$survey_id'");
        $this->data = mysqli_fetch_array($this->survey_details_query);
    }
}

?>