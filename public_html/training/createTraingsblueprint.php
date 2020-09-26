<?php
    include "../header.php";
    if(!isset($_SESSION["username"])){
        header("location: /youNeedToLogin.php");
        exit();
    }
?>
    <main>
        <h3>Trainingsvorlage erstellen</h3>
        <div class="tip shrinkable">
            <ul>
                <li>Eine trainingsvorlage besteht aus einer oder mehreren Aufgabengruppen</li>
                <li>Eine Aufgaben gruppe besteht aus einer oder mehreren Aufgaben</li>
            </ul>
            <details name="Beispiel">
                <summary>
                    Beispiel
                </summary>
                <ul>
                    <li>Warmup(Aufgabengruppe)
                        <ul>
                            <li>5 Min Skaten, 5 min pause(Aufgabe)</li>
                            <li>3 Steigerungen, 10 min pause(Aufgabe)</li>
                        </ul>
                    </li>
                    <li>Training(Aufgabengruppe)
                        <ul>
                            <li>8 * doubinsprint, 5 min pause(Aufgabe)</li>
                        </ul>
                    </li>
                    <li>cooldown(Aufgabengruppe)
                        <ul>
                            <li>5 Min Skaten, 0 min pause(Aufgabe)</li>
                        </ul>
                    </li>
                </ul>
            </details>
        </div>
    </main>
<?php
    include "../footer.php";
?>