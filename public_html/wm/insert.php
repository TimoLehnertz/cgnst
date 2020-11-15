<?php
    include_once "../includes/permissions.inc.php";
    requirePermission("permission_wmdata", 1, '/wm/index.php');
    $dependency = ["titleimg"];
    include_once "../header.php";
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h1 class="headline">Wm Datenbank <span class="color secondary"><i class="fas fa-arrow-right margin left right"></i>Daten einfügen</span></h1>
                <div class="content">
                    <p>Beispiel:<hr>
                        "ID";"Jahr";"Ort";"Klasse";"gesch";"strecke";"platz";"zeit";"Nachname";"Land";"Vorname";"Zeit_Kon";"zeit1";"Feld13";"Feld14"
                        1;2011,00;"YEOSU";"sen";"w";"500";;"47.688";"Gustafsson";"Schweden";"Helena";;"00:47.688";;
                        2;2011,00;"YEOSU";"sen";"w";"500";;"49.379";"Pedari";"IRI ";"Mahrokh";;"00:49.379";;
                    </p>
                    <div class="loading-area"></div>
                    <button onclick="process()">Einfügen</button>
                    <p id="error" style="padding: 20px; background: white; max-height: 100px; overflow: auto"></p>
                    <label for="insertText">Csv(; seperiert) einfügen</label>
                    <textarea name="insert" id="insertText" cols="90%" rows="10" placeholder="Daten einfügen..."></textarea>
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
                    location: null,
                    category: null,
                    sex: null,
                    discipline: null,
                    place: null,
                    seconds: null,
                    surename: null,
                    country: null,
                    name: null,
                };
                for (let c = 0; c < elems.length; c++) {
                    const elem = preprocess(elems[c]);
                    if(elem.length <= 0){
                        warning += `Element bei zeile ${i}, spalte ${c} ist NULL. (übersprungen)\n`
                        continue;
                    }
                    switch(c){
                        case 0://id (skipped)
                        break;
                        case 1://Year
                            const year = parseInt(elem.split(",")[0]);
                            if(!isNaN(year) && year != null){
                                rowJson.year = year ;
                            } else{
                                warning += `Yahr ist keine zahl bei zeile ${i}, spalte ${c}. gegeben: ${elem}\n`
                            }
                        break;
                        case 2://Ort
                            rowJson.location = elem;
                        break;
                        case 3://Klasse
                            rowJson.category = elem;
                        break;
                        case 4://gesch
                            if(elem.length == 1){
                                rowJson.sex = elem;
                            } else{
                                warning += `Gechlecht ist kein character(m,w,d) bei zeile ${i}, spalte ${c}. gegeben: ${elem}, length: ${elem.length}\n`
                            }
                        break;
                        case 5://strecke
                            rowJson.discipline = elem;
                        break;
                        case 6://platz
                            const place = parseInt(elem.split(",")[0]);
                            if(!isNaN(place) && place != null){
                                rowJson.place = place;
                            } else{
                                warning += `Platz ist keine zahl bei zeile ${i}, spalte ${c}. gegeben: ${elem}\n`
                            }
                        break;
                        case 7://zeit(skip because old format)
                        break;
                        case 8://nachname
                            rowJson.surename = elem;
                        break;
                        case 9://land
                            rowJson.country = elem;
                        break;
                        case 10://Vorname
                            rowJson.name = elem;
                        break;
                        case 11://Zeit(corrected format)
                            const millis  = parseInt(elem.split(",")[1]);
                            const seconds = parseInt(elem.split(",")[0].split(":")[1]);
                            const minutes = parseInt(elem.split(",")[0].split(":")[0]);
                            if(!(isNaN(millis) || isNaN(seconds) || isNaN(seconds) || millis == null || seconds == null || minutes == null)){//NaN check
                                rowJson.seconds = millis * 0.001 + seconds + minutes * 60;
                            } else{
                                warning += `Zeit ist im Falschen format(MM:ss,mmm)bei zeile ${i}, spalte ${c}. gegeben: ${elem}\n`
                                // warning += `${i}\n`;
                            }
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
            sendWmData(json, ()=>{
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