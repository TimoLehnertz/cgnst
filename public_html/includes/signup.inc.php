<?php
if (isset($_POST["signup-submit"])) {
    include "dbh.inc.php";

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["passwordRepeat"];

    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        header("Location: ../signup.php?error=emptyFields&username=".$username."&email=".$email);
        exit();
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Z0-9]*$/", $username)){
        header("Location: ../signup.php?error=invalidEmailUsername");
        exit();
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../signup.php?error=invalidEmail&username=".$username."&email=".$email);
        exit();
    }else if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
        header("Location: ../signup.php?error=invalidUsername&email=".$email);
        exit();
    } else if($password != $passwordRepeat){
        header("Location: ../signup.php?error=passwordCheck&username=".$username."&email=".$email);
        exit();
    } else{
        $sql = "SELECT username FROM user WHERE username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=sqlError1");
            exit();
        } else{
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../signup.php?error=userTaken&email=".$email);
                exit();
            } else{
                $sql = "INSERT INTO user(username, email, password) VALUES (?, ?, ?);";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../signup.php?error=sqlError2");
                    exit();
                } else{
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../signup.php?signup=success");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else{
    header("Location: ../signup.php");
    exit();
}