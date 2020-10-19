<?php
    $dependency = ["titleimg"];
    include_once "../header.php";
    requirePermission("permission_administration");
?>
    <main>
        <h1>Administration</h1>

        <a href="configureGroups.php">Group konfiguration</a>
    </main>
<?php
    #include "../footer.php";
?>