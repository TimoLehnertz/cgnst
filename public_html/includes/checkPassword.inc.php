<?php
if(!isset($_POST["user"]) || !isset($_POST["password"])){
    echo "error bad request";
    exit();
} else{
    include "dbh.inc.php";
    $user = $_POST["user"];
    $password = $_POST["password"];

    $sql = "SELECT password FROM user WHERE username=?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "sql error 1";
        exit();
    } else{
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)){
            $passwordCheck = password_verify($password, $row['password']);
            if($passwordCheck == false){
                echo "false";
                exit();
            } if($passwordCheck == true){
                echo "true";
                exit();
            }
        } else{
            echo "error noUser";
            exit();
        }
    }
}