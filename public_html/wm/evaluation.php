<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    include_once "../includes/dbh.inc.php";
    include_once "dataAPI.php";
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h1 class="headline">Hier mehr zur auswertung der mehr als 5.000 Datensätze</h2>
                <div class="content">
                    <a href="index.php" class="btn slide default center">Zurück</a>
                    <p>Alle Daten / Ergebnisse wurden von Hand ausgewertet und wir gehen nicht davon aus, dass diese Auswertungen vollkommen Fehlerfrei sind.</p>
                    <p>
                        Alle ergebnisse sind live aus dem aktuellen stand der Datenbank
                    </p>
                </div>
                <h2 class="headline">Strecken vergleich</h2>
                <div class="content">
                    <p>
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
                    <script>
                        const countryScores = JSON.parse('<?php echo json_encode(getMedalYearCountries($mysqli, [$discipline]));?>');
                        console.log(countryScores);
                    </script>
                    <div class="country-evolving">
                        <!-- Country evolving lines -->
                    </div>
                    <div style="max-height: 50vh; overflow: auto;">
                        <p>
                            Medaillen rank in Zahlen
                        </p>
                    <?php
                        if($discipline == "100"){
                            $discipline = "100$";
                        }
                        echoTableFromArray(getWinnerTimes($mysqli, $discipline));
                    ?>
                    </div>
                </div>
            </section>
            <section class="section">
                <h3 class="headline">Entwicklung der Siegerzeiten</h3>
                <p>
                    Einige Zeiten sind leider fehlerhaft.. Rot für langsamste zeit, grün für schnellste Zeit
                </p>
                <div class="content font size medium winner-times">
                    <?php
                        echoTableFromArray(getBestTimes($mysqli));
                    ?>
                    <script>
                        $(".winner-times table tr").each(function(tr){
                            if(tr > 0){
                                if(tr % 2 == 0){
                                    $(this).css("border-bottom", "5px solid #444")
                                }
                                let min = 1000;
                                let max = 0;
                                let sum = 0;
                                let amount = 0;
                                $(this).find("td").each(function(td){
                                    if(td > 1){
                                        const val = parseFloat($(this).text());
                                        if(isNaN(val)){
                                            return;
                                        }
                                        amount++;
                                        sum += val;
                                        if(val < min){
                                            min = val;
                                        }
                                        if(val > max){
                                            max = val;
                                        }
                                    }
                                });
                                /**
                                 * avg
                                 */
                                console.log(sum);
                                $(this).append(`<td>${Math.round(sum / amount * 100) / 100}</td>`);
                                $(this).find("td").each(function(td){
                                    if(td > 1 && amount > 1){
                                        const val = parseFloat($(this).text());
                                        if(val == min && amount > 1){
                                            $(this).css("background", "Chartreuse");
                                        }
                                        if(val == max){
                                            $(this).css("background", "DarkRed");
                                            $(this).css("color", "white");
                                        }
                                    }
                                });
                            } else{
                                $(this).append("<td>Avg</td>")
                            }
                        });
                    </script>
                </div>
            </section>
        </main>
    </div>
    <script src="/js/country-evolving.js"></script>
<?php
    include_once "../footer.php";
?>