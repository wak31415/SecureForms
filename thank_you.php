<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
 
    <!-- JavaScript -->
    <script type = "text/javascript"
        src = "https://code.jquery.com/jquery-2.1.1.min.js"></script>

    <script src="js/cookie_handlers.js"></script>
    <script src="js/sjcl.js"></script>
    <script src="js/form_scripts.js"></script>
    <script src="js/general_scripts.js"></script>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="style.css">
    <title>SecureForms</title>
</head>
<body>

<h1>Thank you for your submission</h1>
<p>Your response has been successfully recorded!</p>

<?php
    echo $_GET['msg'];
?>