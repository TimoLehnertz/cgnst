<?php
    include "header.php";
?>
    <main>
        <?php
            if (isset($_SESSION["username"])) {
                echo "<p>Welcome ".$_SESSION["username"]."! You are logged in!</p>";
            } else{
                echo "<p>You are logged out!</p>";
            }
        ?>
    </main>
<?php
    include "footer.php";
?>