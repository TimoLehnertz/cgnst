<?php
    if(!isset($_POST["logout-submit"])){
        header("location: ../index.php");
        exit();
    } else{
    session_start();
    session_unset();
    session_destroy();
    header("location: ../index.php");
}