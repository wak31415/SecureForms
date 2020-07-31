<?php
    include("includes/header.php");
    include("includes/navbar.php");
?>

<div class="container">
    <div class="jumbotron alert-success">
        <h2><i class="fa fa-check-square-o"></i> Thank you for your submission!</h2>
        <p>Your response has been successfully encrypted and recorded.</p>
    </div>

    <?php
        if (!isset($_SESSION['email'])) {
            echo
            '
            <div class="jumbotron text-center">
            <h1>Start creating your own free and secure forms today!</h1>
            <p>Easy to use, end-to-end encrypted, open source and free... What more could you want?</p>
            <a href="register.php" class="btn btn-success btn-lg" role="button">Register</a>
            </div>
            ';
        }
    ?>
</div>
