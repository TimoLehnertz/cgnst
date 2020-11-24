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
                        <div class="headline">
                            <button class="btn default round switch-direction"><i class="fas fa-sync-alt margin left right"></i>Richtung Umshalten</button>
                            <button class="btn default round switch-subColors"><i class="fas fa-sync-alt margin left right"></i>Subfarben benutzen</button>
                        </div>
                        <div class="content">
                            <div class="layerDiagram">
                                <!-- Layer Diagramm -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- ASIDE -->
        <aside class="aside">
            <div class="content">
            <a href="index.php" class="btn slide default center">Zur Wm datenbank</a>
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
    <script src="/js/layerDiagram.js"></script>
<?php
    include_once "../footer.php";
?>