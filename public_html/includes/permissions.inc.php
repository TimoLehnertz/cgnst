<?php
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}

function requirePermission($permissionName, $minState = 1, $fallbackPage = "/index.php"){
    if(!doIHavePermissionFor($permissionName, $minState)){
        header("location: $fallbackPage?error=Keine berechtigung :(");
        exit();
    }
}

function doIHavePermissionFor($permissionName, $minState = 1){
    if(!isset($_SESSION["permissions"])){
        return false;
    } else if(!isset($_SESSION["permissions"][$permissionName])){
        return false;
    } else{
        return $_SESSION["permissions"][$permissionName] >= $minState;
    }
}