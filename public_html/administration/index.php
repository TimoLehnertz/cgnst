<?php
    include "../includes/permissions.inc.php";
    requirePermission("permission_admin");
    $dependency = ["titleimg"];
    include_once "../header.php";
    
?>
    <div class="layout simple">
        <main class="main">
            <section class="section">
                <h1 class="headline">Administration</h1>
                <div class="content">
                    <a href="configureGroups.php">Group konfiguration</a>
                </div>
            </section>
        </main>
    </div>
<?php
    include "../footer.php";
?>