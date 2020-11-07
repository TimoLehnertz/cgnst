<?php
    include "../includes/permissions.inc.php";
    requirePermission("permission_admin");
    $dependency = ["titleimg"];
    include_once "../header.php";
    
?>
    <main>
        <h1>Administration</h1>

        <a href="configureGroups.php">Group konfiguration</a>
    </main>
<?php
    #include "../footer.php";
?>