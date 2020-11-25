<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    include_once "../includes/dbh.inc.php";
    include_once "dataAPI.php";
?>
    <div class="layout basic">
        <main class="main">
            <section class="section">
                <h1 class="headline">Wm DB<span class="color secondary font size medium margin left">Eine Datenbank <i class="fas fa-arrow-right"></i> Alle WM Sprint Ergebnisse <strong class="font size bigger-medium">seit 2007</strong></span></h2>
                <div class="content">
                <p>Alle Daten / Ergebnisse wurden von Hand ausgewertet und wir gehen nicht davon aus, dass diese Auswertungen vollkommen Fehlerfrei sind.</p>
                    <div>
                        <?php
                            if(isset($_POST["submit"])){
                                echoTableFromArray(query_medals_compare($mysqli, $_POST));
                            }
                        ?>
                    </div>
                </div>
            </section>
        </main>
        <aside class="aside">
            <div class="content">
            <a href="index.php" class="btn slide default center">Zurück</a>
        </aside>
    </div>
<?php
    include_once "../footer.php";
?>