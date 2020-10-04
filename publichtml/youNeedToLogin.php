<?php
    include "header.php";
    if(!isset($_SESSION["username"])){
?>
    <main>
        <p>Du musst dich einloggen um diesen Bereich einzusehen :(</p>
        <hr>
        <form id='signIn-sign-Out-form' action='/login.php' method='POST'>
            <button class='rectShadow' type='submit' name='login-submit'>Login</button>
        </form>
    </main>
<?php
    } else{
?>
    <main>
        <p>Du Bis angemeldet als <?php echo $_SESSION["username"]; ?>!</p>
    </main>
<?php
    }
    include "footer.php";
?>