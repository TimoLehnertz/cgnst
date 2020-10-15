<?php

if (isset($_POST["login-submit"])) {
    include "dbh.inc.php";

    $mailUsername = $_POST["mailUsername"];
    $password = $_POST["password"];
    $rememberMe = $_POST["rememberMe"];

    if(empty($mailUsername) || empty($password)){
        header("location: ../index.php?error=emptyFields");
        exit();
    } else{
        $sql = "SELECT * FROM user WHERE username=? OR email=?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)){
            header("location: ../index.php?error=sqlError1");
            exit();
        } else{
            mysqli_stmt_bind_param($stmt, "ss", $mailUsername, $mailUsername);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($result)){
                $passwordCheck = password_verify($password, $row['password']);
                if($passwordCheck == false){
                    header("location: ../index.php?error=wrongPassword");
                    exit();
                } else if($passwordCheck == true){
                    $message = "";
                    if(isset($rememberMe)){
                        #ini_set('session.cookie_lifetime', time() + 60 * 60 * 24 * 30);
                        #$message = "&rememberMe=1";
                        #setcookie('user', $row['username'], time() + 60 * 60 * 30);
                        #setcookie('userpw', $row['password'], time() + 60 * 60 * 30);
                    }
                    session_start();
                    $_SESSION['userId'] = $row['iduser'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['permission_administration'] = true;
                    header("location: ../index.php?login=success$message");
                    exit();
                } else{
                    header("location: ../index.php?error=wrongPassword");
                    exit();
                }
            } else{
                header("location: ../index.php?error=sqlError2");
                exit();
            }
        }
    }

} else{
    header("location: ../index.php");
    exit();
}