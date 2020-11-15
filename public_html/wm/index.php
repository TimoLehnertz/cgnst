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
                            function stringFromFilter($filter){
                                $delimiter = "";
                                echo "<p class='font size bigger-medium color secondary'><span class='color primary margin right'>Filter: </span>";
                                foreach ($filter as $key => $value) {
                                    if($key == "search" || strlen($value == 0)){
                                        continue;
                                    }
                                    echo $delimiter.$key.": ".$value;
                                    $delimiter = ", ";
                                }
                                echo "</p>";
                            }
                        ?>
                    </div>
                </div>
            </section>
        </main>
        <aside class="aside">
            <div class="content">
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
            <div class="content">
                <a href="insert.php">Neue daten einfügen</a>
            </div>
        </aside>
    </div>
<?php
    function echoSelectorFor($mysqli, $column, $label){
        $values = getAvailableWmColumnValues($mysqli, $column, $label);
        $ranId = random_int(0, 100000);
        $selected = isset($_GET[$column]);
        echo "<p><label style='width: 100px' for='$ranId'>$label:</label></p><p>";
        echo "<select name='$column' id='$ranId'>";
        if(!$selected){
            echo "<option selected value=''>Auswählen</option>";
        } else{
            echo "<option value=''>Auswählen</option>";
        }
        foreach ($values as $i => $value) {
            if(is_numeric($value)){
                if($value == 0){
                    continue;
                }
            } else if(strlen($value) == 0){
                continue;
            }
            if($selected){
                if($_GET[$column] == $value){
                    echo "<option value='$value' selected>$value</option>";
                    continue;
                }
            }
            echo "<option value='$value'>$value</option>";
        }
        echo "</select></p>";
    }
    include_once "../footer.php";
?>