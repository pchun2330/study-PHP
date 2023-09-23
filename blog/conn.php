<?php
    $host = "localhost";
    $username = "tutorial";
    $password = "123456789";
    $dbname = "blog";

    $conn = new mysqli($host,$username,$password,$dbname);

    if(!empty($conn -> connect_error)){
        die("Cannot connect to mySQL database" . $conn -> connect_error);
    }
?>