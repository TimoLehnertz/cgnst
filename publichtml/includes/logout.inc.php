<?php
    if(!isset($_POST["login-submit"])){
        header("location: ../index.php");
        exit();
    } else{
    session_start();
    session_unset();
    session_destroy();
    header("location: ../index.php");
}