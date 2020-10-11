<?php
    include "../header.php"
?>
    <main>
        <?php
            if (isset($_SESSION["username"])) {
                echo "<h2>Willkommen bei deinem profil, ".$_SESSION["username"]."!</h2><p>Bald wirst du hier deine persöhnlichen einstellungen verwalten können</p><hr>=)";
            } else{
                echo "<p>Du bist ausgeloggt!</p>";
            }
        ?>
    </main>
<?php
    include "../footer.php"
?>