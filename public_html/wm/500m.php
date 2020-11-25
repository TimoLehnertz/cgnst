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
                        <div class="headline">
                            <h2 style="display: inline" class="margin right">Grafische übersicht</h2>
                            <button class="btn default round switch-direction"><i class="fas fa-sync-alt margin left right"></i>Richtung Umshalten</button>
                            <button class="btn default round switch-subColors"><i class="fas fa-sync-alt margin left right"></i>Subfarben benutzen</button>
                        </div>
                        <div class="content shadow basic">
                            <div class="layerDiagram">
                                <!-- Layer Diagramm -->
                            </div>
                        </div>
                        <div class="headline">
                            <h3>Statistik in Zahlen</h3>
                        </div>
                        <div class="content">
                            <?php include "winnerTable.html";?>
                        </div>
                        <div class="headline">
                            <h3>Statistik im Diagramm</h3>
                        </div>
                        <div class="content">
                            <p>
                                Statistik über die Chance von Position x in Situation x zu gewinnen.
                            </p>
                            <div class="flex row">
                                <img src="charts/winnerFromStart.PNG" class="img hover width third" alt="Chart">
                                <img src="charts/winnerFromAfterStart.PNG" class="img hover width third" alt="Chart">
                                <img src="charts/winnerFrombeforeFinish.PNG" class="img hover width third" alt="Chart">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="section">
                <h2 class="headline">Wo kommen die Daten her?</h2>
                <div class="content">
                    <p>Alle Daten wurden von hand aus youtube videos in die Datenbank eingepflegt. Die Video links sind alle in dem diagramm eingebettet</p>
                    <p>
                        Wer sich genauer mit dem projekt befassen will, kann sich die zu grunde liegende Excel Tabelle gerne <a href="Auswertung500.xlsx">hier runterladen</a>.
                    </p>
                </div>
            </section>
        </main>
        <!-- ASIDE -->
        <aside class="aside">
            <div class="content">
                <a href="index.php" class="btn slide default center">Zurück</a>
            </div>
            <?php if(doIHavePermissionFor("permission_wmdata")){?>
            <div class="content">
                <a href="insertWm.php" class="btn slide default center">Wm daten eintragen</a>
                <hr>
                <a href="insert500m.php" class="btn slide default center">500m daten eintragen</a>
            </div>
            <?php }?>
            <h3 class="headline">Infos</h3>
            <div class="content info">
               <p>
                   Klick eine Subfarbe an um infos zu bekommen
               </p>
               <p>Beispiel:</p>
               <img src="explanation.png" alt="explanation">
            </div>
        </aside>
    </div>
    <script src="/js/layerDiagram.js"></script>
<?php
    include_once "../footer.php";
?>