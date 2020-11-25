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
                            if(isset($_GET["search"])){
                                stringFromFilter($_GET);
                                echoTableFromArray(getResultFromFilter($mysqli, $_GET));
                            } else{
                                $presetFilter = array("year" => "2019", "sex" => "m", "discipline" => "500d", "category" => "SEN");
                                stringFromFilter($presetFilter);
                                echoTableFromArray(getResultFromFilter($mysqli, $presetFilter));
                            }
                        ?>
                    </div>
                </div>
            </section>
        </main>
        <aside class="aside">
            <div class="content">
            <a href="500m.php" class="btn slide default center">Zur 500m Auswertung</a>
            <a href="evaluation.php" class="btn slide default center">Zu weiteren Auswertung</a>
                <h2>Suchen</h2>
                <form action="#">
                    <?php echoSelectorFor($mysqli, "year", "Jahr");?>
                    <?php echoSelectorFor($mysqli, "location", "Stadt");?>
                    <?php echoSelectorFor($mysqli, "category", "Altersklasse");?>
                    <?php echoSelectorFor($mysqli, "sex", "Geschlecht");?>
                    <?php echoSelectorFor($mysqli, "discipline", "Strecke");?>
                    <?php echoSelectorFor($mysqli, "place", "Platzierung");?>
                    <?php echoSelectorFor($mysqli, "country", "Land(läufer)");?>
                    <?php echoSelectorFor($mysqli, "name", "Name");?>
                    <?php echoSelectorFor($mysqli, "surename", "Nachnahme");?>
                    <button name="search" class="btn slide font size big margin top width max padding top bottom" type="submit">Finden</button>
                </form>
            </div>
            <?php if(doIHavePermissionFor("permission_wmdata")){?>
            <div class="content">
                <a href="insertWm.php" class="btn slide default center">Wm daten eintragen</a>
                <hr>
                <a href="insert500m.php" class="btn slide default center">500m daten eintragen</a>
            </div>
            <?php }?>
        </aside>
    </div>
<?php
    function stringFromFilter($filter){
        $delimiter = "";
        echo "<p class='font size bigger-medium color secondary'><span class='color primary margin right'>Filter: </span>";
        foreach ($filter as $key => $value) {
            if($key == "search" || strlen($value) == 0){
                continue;
            }
            echo $delimiter.$key.": ".$value;
            $delimiter = ", ";
        }
        echo "</p>";
    }
    include_once "../footer.php";
?>