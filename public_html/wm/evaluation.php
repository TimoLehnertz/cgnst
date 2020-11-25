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
                    <p>
                        Alle ergebnisse sind live aus dem aktuellen stand der Datenbank<br>
                        Wähle eine Strecke aus die mit einer oder 3 anderen verglichen werden soll<br>
                        Es werden alle Athleten verglichen, die jemans eine Medaille in der initialstrecke gewonnen haben.
                        Außerdem wird ihr durchschnittliches ergebniss auf den referenz strecken ausgegeben<br>
                        Final wird dann der Durchschnittswert aller Durchschnittsergebnisse genommen. Dieser wert gib in gewisser weise Aufschluss
                        darüber, wie wichtig die referenzstrecken in bezug auf die initialstrecke ist<br>
                        500m wählt auch die 500d und 500m road aus
                    </p>
                    <form action="medals_compare.php" method="POST">
                        <?php echoSelectorForName($mysqli, "discipline", "Initialstrecke", "init"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke1", "vgl1"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke2", "vgl2"); ?>
                        <?php echoSelectorForName($mysqli, "discipline", "Vgl. strecke3", "vgl3"); ?>
                        <button name="submit" class="btn slide font size big margin top width max padding top bottom">Vergleichen</button>
                    </form>
                </div>
            </section>
            <section class="section">
                <h2 class="headline">Entwicklung der Länder</h2>
                <div class="content">
                    <h4>Berechnung:</h4>
                    <p>
                        <ul>
                            <li>Gold: 3 Punkte</li>
                            <li>Silber: 2 Punkte</li>
                            <li>Bronze: 1 Punkte</li>
                        </ul>
                    </p>
                   
                    <form action="#" method="GET">
                        <p>
                            Zeige ergebnisse für 
                            <?php
                                $discipline = "500";
                                if(isset($_GET["discipline"])){
                                    if(strlen($_GET["discipline"]) > 0){
                                        $discipline = $_GET["discipline"];
                                    }
                                }
                                echo $discipline;
                            ?>
                        </p>
                        <?php echoSelectorForName($mysqli, "discipline", "Strecke (500: alle 500er)", "discipline");?>
                        <button class="btn slide default margin top" type="submit" name="search" value="Los!">Los!</button>
                    </form>
                    <?php echoSelectorForName($mysqli, "country", "Land suchen", "search");?>
                    <button class="reset-scale btn slide vertical topleft default">Größe zurücksetzen</button>
                    <button class="view-all btn slide vertical topleft default">Alle anzeigen</button>
                    <div class="country-evolving">
                        <!-- Country evolving lines -->
                    </div>
                </div>
            </section>
            <section class="section">
                <h3 class="headline">Entwicklung der Siegerzeiten</h3>
                <div class="content">
                   
            </section>
        </main>
        <aside class="aside">
            <div class="content">
                <a href="index.php" class="btn slide default center">Zurück</a>
            </div>
        </aside>
    </div>
    <script>
        const countryScores = JSON.parse('<?php echo json_encode(getMedalYearCountries($mysqli, [$discipline]));?>');
        console.log(countryScores);
    </script>
    <script src="/js/country-evolving.js"></script>
<?php
    include_once "../footer.php";
?>