<?php

$serverName = "localhost";
$dBUsername = "php";
$dBPwd = "";
$dBName = "cgnst";

$conn = mysqli_connect($serverName, $dBUsername, $dBPwd, $dBName);

if(!$conn){
    die("Connection failed: ".mysqli_connect_error());
}