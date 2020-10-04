<?php
    include "../header.php";
?>
    <main>
        <style>

            main{
                width: 100%;
                margin: 0;
            }

            #pressKey{
                color: white;
                display: none;
                position: fixed;
                text-align: center;
                width: 100vw;
                height: 100vh;
                padding-top: 40vh;
                background-color: rgba(152, 175, 180, 0.829);
                font-size: 60pt;
                z-index: 100;
            }

            #deleteLastTriggerBtn{
                background-color: white;
                font-size: 20pt;
                width: 500px;
                float: right;
                padding: 20px;
                margin-top: 100px;
                margin-right: 20px;
                border: gray 2px solid;
            }

            #input{
                padding-top: 15vh;
            }

            #triggerKey{
                margin-left: 10px;
                border: 2px solid gray;
                padding: 7px;
            }

            #triggerKey:before{
                content: "current key:  ";
            }

            .fadeOut{
                animation: fadeOutToTop 1s forwards;
            }

            #eingabefeld{
                float: left;
                width: 400px;
                margin-right: 100px;
            }

            #readyStart{
                position: absolute;
                text-align: center;
                width: 100vw;
                height: 100vh;
                color: white;
                line-height: 100vh;
                background-color: #000;
                font-size: 60pt;
            }

            #vorgabezeit input{
                width: 95px;
            }

            .zebra{
                background-color: #DDD;
            }

            .form input{
                padding: 3px;
                float: right;
            }

            #form{
                display: inline-block;
            }

            .form div{
                padding: 10px;
                border-bottom: 2px solid black;
            }

            #ausgabefeld{
                float: left;
                width: 400px;
            }

            #ausgabefeld input{
                width: 100px;
            }

            #rundenÜbrig{
                text-align: center;
                height: 100vh;
                font-size: 300pt;
                float: left;
                width: 30vw;
                border-right: black 10px solid;
                margin: 0;
            }

            #plusMinus{
                color: white;
                margin-top: 5vh;
                font-size: 170pt;
                margin-left: 35vw;
            }

            #plusMinus span{
                font-size: 100pt;
            }

            #middle{
                padding: 0;
                background-color: black;
                width: 98vw;
                height: 100vh;
                margin: 0;
                border: 1px solid black;
            }

            .timingTable{
                padding-top: 20px;
                display: inline;
                border-collapse: collapse;
            }

            .timingTable:first-child{
                text-transform: uppercase;
                border-top: 5px solid chocolate;
            }

            .timingTable:first-child td{
                border-left: none;
                border-right: none;
                border-bottom: 2px solid black;
            }

            .timingTable td{
                padding: 10px;
                padding-left: 20px;
                font-size: 17pt;
                margin: 0;
                border-collapse: collapse;
            }

            #savedRaceInfo{
                font-size: 11pt;
            }

            .timingArticle{
                padding: 20px;
                margin-bottom: 5vh;
                background-color: #FFF;
            }

            @keyframes fadeInFromTop{
                from{top: -50px; opacity: 0;}
                to{
                    top: 0; opacity: 1;
                }
            }

            @keyframes fadeOutToTop{
                from{top: 0px; opacity: 1;}
                to{
                    top: -100px; opacity: 0;
                }
            }

        </style>
        <div id="pressKey">Press key to set trigger</div>
        <article id="input" class="timingArticle">
            <form id="form">
                <div id="eingabefeld"  class="form">
                    <div class="zebra">
                        <label for="sportlerName">Sportler Name:</label>
                        <input type="text" id="sportlerName" onchange="change()" onblur="change();" value="Mustermann"></input>
                    </div>
                    <div>
                        <label for="streckenlänge">Streckenlänge:</label>
                    <input type="number" step="1" id="streckenlänge" onchange="change()" onblur="change();" value="3000" min="0"></input>
                    </div>
                    <div class="zebra">
                        <label for="rundenlänge">Rundenlänge:</label>
                        <input type="number" step="0.1" id="rundenlänge" onchange="change()" onblur="change();" value="304"></input>
                    </div>
                    <div id="vorgabezeit">
                        <label for="Vorgabezeit">Vorgabezeit(min sek):</label>
                        <input type="number" step="0.01" id="VorgabezeitSek" onchange="change()" onblur="change();" value="0"></input>
                        <input type="number" step="1" id="VorgabezeitMin" onchange="change()" onblur="change();" value="5"></input>
                    </div>
                    <div class="zebra">
                        <label for="startzugabe">Startzugabe(sek):</label>
                        <input type="number" step="0.1" id="startzugabe" onchange="change()" onblur="change();" value="2"></input>
                    </div>
                    <span id="warnings" style="color: red; margin-top: 10px;"></span>
                </div>
                <div id="ausgabefeld" class="form">
                    <div class="zebra">
                        <label for="runden">Runden:</label>
                        <input type="text" id ="runden" readonly></input>
                    </div>
                    <div>
                        <label for="restMeter">Rest meter:</label>
                        <input type="text" id="restMeter" readonly></input>
                    </div>
                    <div class="zebra">
                        <label for="kmh">Km/h:</label>
                        <input type="text" id="kmh"  readonly></input>
                    </div>
                    <div>
                        <label for="gesSek">Ges. Sek:</label>
                        <input type="text" id="gesSek" readonly></input>
                    </div>
                    <div class="zebra">
                        <label for="durchschittZeit">standart runde (sek):</label>
                        <input type="text" id="durchschittZeit"  readonly></input>
                    </div>
                    <div>
                        <label for="ersteRundeSek">erste runde(sek):</label>
                        <input type="text" id="ersteRundeSek"  readonly></input>
                    </div>
                </div>
                <div id="settings">
                    <button id="triggerButton" type="button" onclick="changeTrigger()">Change trigger key</button>
                    <span id="triggerKey"></span>
                    <label for="enterFullscreen">Enter Fullscreen on start</label>
                    <input type="checkbox" id="enterFullscreen" onchange="enterFullscreenChanged()">
                    <p>Select way of triggering</p>
                    <label for="local">Use local keyboard</label>
                    <input type="radio" id="local" name="triggering" checked="true">
                    <label for="lichtschranke">Use Lichtschranke (Only available when connected)</label>
                    <input type="radio" id="lichtschranke" name="triggering">
                </div>
            </form>
        </article>
        <article class="timingArticle">
            <p><b>Note</b>: This is a beta version. I appologize for potential bugs</p>
        </article>
        <article id="middle">
            <div id="readyStart">Press SPACE to start</div>
            <div id="rundenÜbrig">
                start
            </div>
            <div id="plusMinus">
                plus
            </div>
            <button type="button" id="deleteLastTriggerBtn" onclick="deleteLastTrigger()">Delete last input(ALTGr)</button>
        </article>
        <article class="timingArticle">
            <table id="result" class="timingTable"></table>
        </article>
        <article class="timingArticle">
            <label for="savedRace">letzte Rennen</label>
            <input id="savedRace" type="number" onchange="loadRace(savedRace.value)">
            <div id="savedRaceInfo"></div>
            <table class="timingTable" id="savedRaceTable"></table> 
            <button type="button" onclick="downloadTable()">Download Race</button>
        </article>
        <script src="/js/timingScript.js"></script>
    </main>
<?php
    include "../footer.php";
?>