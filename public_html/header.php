<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="Example of a meta description this will often show up in search results">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cologne Speet team</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body id="body">
        <?php
            include "startAnimation.php";
        ?>
        <header>
            <nav>
                <a href="/index.php">Home</a>
                <a href="#">Profile</a>
                <a href="/training">Training</a>
                <a href="#">Kontakt</a>
                <a href="#">About us</a>
            </nav>
            <div>
            <?php if (isset($_SESSION["username"])) {//Signed in?>
                <?php echo $_SESSION["username"];?>
                <form id='signIn-sign-Out-form' action='/includes/logout.inc.php' method='POST'>
                    <button class='rectShadow' type='submit' name='login-submit'>Logout</button>
                </form>
            <?php } else{//not signed in?>
                <form id='signIn-sign-Out-form' action='/login.php' method='POST'>
                    <button class='rectShadow' type='submit' name='login-submit'>Login</button>
                </form>
            <?php }?>
            </div>
        </header>