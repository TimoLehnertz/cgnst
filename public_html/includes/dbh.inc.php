<?php

// $serverName = "cgnst.ddns.net";
$serverName = "localhost";
$dBUsername = "root";
$dBPwd = "12345678";
// $dBUsername = "php";
// $dBPwd = "!YouDontKnow!";
$dBName = "cgnst";

$conn = mysqli_connect($serverName, $dBUsername, $dBPwd, $dBName);
$mysqli = new mysqli($serverName, $dBUsername, $dBPwd, $dBName);

if(!$conn || $mysqli->connect_errno){
    die("Connection failed: ".mysqli_connect_error());
}