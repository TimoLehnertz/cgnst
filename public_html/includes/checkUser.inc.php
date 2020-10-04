<?php
if(!isset($_GET["user"])){
    echo "error bad request".implode($_POST);
    exit();
} else{
    include "dbh.inc.php";
    $user = $_GET["user"];

    $sql = "SELECT username, email FROM user WHERE username=? OR email=?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        echo "sql error 1";
        exit();
    } else{
        mysqli_stmt_bind_param($stmt, "ss", $user, $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($result)){
            echo $row["username"].",".$row["email"];
            exit();
        } else{
            echo "noUser";
            exit();
        }
    }
}