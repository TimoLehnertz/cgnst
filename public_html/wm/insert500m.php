<?php
    include_once "../includes/permissions.inc.php";
    #requirePermission("permission_wmdata", 1, '/wm/index.php');
    $dependency = ["titleimg"];
    include_once "../header.php";
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h1 class="headline">Wm Datenbank <span class="color secondary"><i class="fas fa-arrow-right margin left right"></i>Daten für 500m einfügen</span></h1>
                <div class="content">
                    <p>Beispiel:
                        "ID";"Jahr";"Ort";"Klasse";"gesch";"strecke";"platz";"zeit";"Nachname";"Land";"Vorname";"Zeit_Kon";"zeit1";"Feld13";"Feld14"
                        1;2011,00;"YEOSU";"sen";"w";"500";;"47.688";"Gustafsson";"Schweden";"Helena";;"00:47.688";;
                        2;2011,00;"YEOSU";"sen";"w";"500";;"49.379";"Pedari";"IRI ";"Mahrokh";;"00:49.379";;
                    </p>
                    <div class="loading-area"></div>
                    <button onclick="process()">Einfügen</button>
         	     	<p id="error" style="padding: 20px; background: white; max-height: 100px; overflow: auto"></p>
                    <label for="insertText">Csv(; seperiert) einfügen</label>
                    <textarea name="insert" id="insertText" cols="90%" rows="10" placeholder="Daten einfügen...">
                    <?php
                        include "Auswertung500.csv";
                    ?>
                    </textarea>
                </div>
            </section>
        </main>
    </div>
    <script>
        function process(){
            let warning = "";
            const text = $("#insertText").val();
            const rows = text.split(/\r?\n/);
            const json = [];
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                elems = row.split(";");
                const rowJson = {
                    year: null,
                    competition: null,
                    category: null,
                    sex: null,
                    afterStart1: null,
                    afterStart2: null,
                    afterStart3: null,
                    afterStart4: null,
                    beforeFinish1: null,
                    beforeFinish2: null,
                    beforeFinish3: null,
                    beforeFinish4: null,
                    finish1: null,
                    finish2: null,
                    finish3: null,
                    finish4: null,
                    link: null
                };
                for (let c = 0; c < elems.length; c++) {
                    const elem = preprocess(elems[c]);
                    if(elem.length <= 0){
                        warning += `Element bei zeile ${i}, spalte ${c} ist NULL. (übersprungen)\n`
                        continue;
                    }
                    switch(c){
                        case 0://Year
                            rowJson.year = elem;
                        break;
                        case 1://competition
                            rowJson.competition = elem;
                        break;
                        case 2://Klasse
                            rowJson.category = elem;
                        break;
                        case 3://gesch
                            if(elem.length == 1){
                                rowJson.sex = elem;
                            } else{
                                warning += `Gechlecht ist kein character(m,w,d) bei zeile ${i}, spalte ${c}. gegeben: ${elem}, length: ${elem.length}\n`
                            }
                        break;
                        case 10://afterStart
                            rowJson.afterStart1 = elem;
                        break;
                        case 11://afterStart
                            rowJson.afterStart2 = elem;
                        break;
                        case 12://afterStart
                            rowJson.afterStart3 = elem;
                        break;
                        case 13://afterStart
                            rowJson.afterStart4 = elem;
                        break;
                        case 14://beforeFinish
                            rowJson.beforeFinish1 = elem;
                        break;
                        case 15://beforeFinish
                            rowJson.beforeFinish2 = elem;
                        break;
                        case 16://beforeFinish
                            rowJson.beforeFinish3 = elem;
                        break;
                        case 17://beforeFinish
                            rowJson.beforeFinish4 = elem;
                        break;
                        case 18://finisg
                            rowJson.finish1 = elem;
                        break;
                        case 19://finisg
                            rowJson.finish2 = elem;
                        break;
                        case 20://finisg
                            rowJson.finish3 = elem;
                        break;
                        case 21://finisg
                            rowJson.finish4 = elem;
                        break;
                        case 23://link
                            rowJson.link = elem;
                        break;
                    }
                }
                json.push(rowJson);
            }
            const warnCount = warning.split("\n").length;
            const nullCount = warning.split("NULL").length;
            $("#error").html(`${warnCount} warnungen | ${nullCount} Felder leer | ${warnCount - nullCount} Formatierungsfehler:<hr><pre>${warning}</pre>`);
            $(".loading-area").empty();
            $(".loading-area").append(getLoadingElem());
            console.log(json);
            send500mData(json, ()=>{
                $(".loading-area").empty();
                $(".loading-area").append(`<i class="fas fa-check"></i>`);
            });
        }

        function preprocess(text){
            text = text.replaceAll(`"`, '');
            text = text.trim();
            return text;
        }
    </script>
<?php
    include_once "../footer.php";
?>