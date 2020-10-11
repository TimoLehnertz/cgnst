<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="Cologne Speed Team" content="width=device-width, initial-scale=1">
        <title>Cologne Speet team</title>
        <link rel="icon" type="image/gif" href="/img/rolle2.gif">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/ui.css">
        <link rel="stylesheet" href="/css/normalize.css">
        <script src="/js/jquery-3.5.1.js"></script>
        <?php
            if(isset($dependency)){
                if(in_array("kalender", $dependency)){
                    echo "<link rel='stylesheet' href='/css/kalender.css'>";
                    echo "<script src='/js/kalender.js'></script>";
                }
            }
        ?>
    </head>
    <body id="body">
        <?php
            include "startAnimation.php";
        ?>
        <header>
            <nav>
                <a href="/index.php">Home</a>
                <a href="/profile">Profile</a>
                <a href="/training">Training</a>
                <a href="/timing">Timing</a>
                <a href="/index.php">Kontakt</a>
                <a href="/index.php">About us</a>
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
            <div style="color: white; line-height: 100%; font-size: 14pt; margin: 10px 20px;">!!Note this site is still undercunstruction</div>
        </header>