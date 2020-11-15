<?php
    include "../includes/permissions.inc.php";
    requirePermission("permission_admin");
    $dependency = ["group-user-dragndrop", "titleimg"];
    include_once "../header.php";
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
            <h1 class="headline">Administration <span class="color secondary"><i class="fas fa-arrow-right margin left right"></i>Gruppen konfigurieren</span></h1>
                <div class="content">
                    <div class="user-group-config">
                        <div class="group-user-dragndrop">
                            <h3>Users</h3>
                            <hr>
                            <div class="group-user-dragndrop__header">
                                <input class="group-user-dragndrop__search-input" type="text" placeholder="Suchen..">
                                <button class="group-user-dragndrop__search-button">Suchen</button>
                            </div>
                            <div class="group-user-dragndrop__list"></div>
                        </div>
                        <div class="group-list">
                            <h3>Gruppen</h3>
                            <hr>
                            <div class="add-group-section">
                                <input type="text" class="add-group-name-input" placeholder="Gruppenname">
                                <input type="text" class="add-group-description-input" placeholder="Beschreibung">
                                <button type="button" class="add-group-button">+</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="submit-button">Übernehmen</button>
                    <button type="button" class="undo-button">Undo</button>
                    <button type="button" class="redo-button">Redo</button>
                    <div class="error-message"></div>
                    </div>
            </section>
        </main>
    </div>
<?php
    include "../footer.php";
?>