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
                </div>
                <h2 class="headline">Hier mehr zur auswertung der mehr als 5.000 Datensätze</h2>
                <div class="content">
                    <h3>Strecken vergleich</h3>
                    <form action="medals_compare.php" method="POST">
                        <?php echoSelectorForName($mysqli, "discipline", "Initialstrecke", "init"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke1", "vgl1"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke2", "vgl2"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke3", "vgl3"); ?>
                        <button name="submit" class="btn slide font size big margin top width max padding top bottom">Vergleichen</button>
                    </form>
                </div>
            </section>
        </main>
        <aside class="aside">
            <div class="content">
                <a href="index.php" class="btn slide default center">Zurück</a>
            <a href="500m.php" class="btn slide default center">Zur 500m Auswertung</a>
        </aside>
    </div>
<?php
    include_once "../footer.php";
?>