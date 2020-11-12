<?php
    include "../includes/permissions.inc.php";
    requirePermission("permission_admin");
    $dependency = ["group-user-dragndrop", "titleimg"];
    include_once "../header.php";
?>
    <main class="main">
        <section>
            <h1>Administration - Configure groups</h1>
            <hr>
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
                        <input type="text" class="add-group-name-input">
                        <input type="text" class="add-group-description-input">
                        <button type="button" class="add-group-button">+</button>
                    </div>
                </div>
            </div>
            <button type="button" class="submit-button">Übernehmen</button>
            <button type="button" class="undo-button">Undo</button>
            <button type="button" class="redo-button">Redo</button>
            <div class="error-message"></div>
        </section>
    </main>
<?php
    include "../footer.php";
?>