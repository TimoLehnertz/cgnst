<?php
    $dependency = ["kalender"];
    include "../header.php"
?>
    <main>
        <?php
            if (isset($_SESSION["username"])) {
                echo "<p>Wilkommen ".$_SESSION["username"]."! Du bist eingeloggt!</p>";
            } else{
                echo "<p>Du bist ausgeloggt!</p>";
            }
        ?>
        <a href="createTraingsblueprint.php">Erstelle eine Trainingsvorlage</a>
        <div class="kalender"></div>
    </main>
<?php
    #include "../footer.php"
?>