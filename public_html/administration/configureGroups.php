<?php
    $dependency = ["group-user-dragndrop", "titleimg"];
    include_once "../header.php";
    requirePermission("permission_administration");
?>
    <main>
        <section>
            <h1>Administration - Configure groups</h1>
            <hr>
            <?php include "../users/group-user-dragndrop.html"?>
        </section>
    </main>
<?php
    include "../footer.php";
?>