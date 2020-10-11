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
        <div class="kalender">
            <!--<div class="kalender__header"></div>
            <div class="kalender__body">
                <div class="kalender__aside">
                    <div class="kalender__aside__enter-new">Eintragen</div>
                </div>
                <div class="kalender__main"></div>
            </div>-->
        </div>
    </main>
<?php
    include "../footer.php"
?>