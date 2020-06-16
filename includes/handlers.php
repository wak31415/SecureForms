<?php
function lookupEntry($con, $table, $id, $return_item) {
    $query = mysqli_query($con, "SELECT * FROM '$table' WHERE id='$id'");
    $row = mysqli_fetch_array($query);
    return $row[$return_item];
} 
?>