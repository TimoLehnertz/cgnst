<?php

$serverName = "cgnst.ddns.net";
$dBUsername = "php";
$dBPwd = "!YouDontKnow!";
$dBName = "cgnst";

$conn = mysqli_connect($serverName, $dBUsername, $dBPwd, $dBName);

if(!$conn){
    die("Connection failed: ".mysqli_connect_error()."!!!!!!!!!!!!!!!!!!!!!!!!!!");
}