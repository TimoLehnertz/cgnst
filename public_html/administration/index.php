<?php
    include "../header.php";
    if(!isset($_SESSION["permission_administration"])){
        header("location: /index.php");
        exit();
    }
?>
    <main>
        <h1>Administration</h1>
        <hr>

        <a href="configureGroups.php">Group konfiguration</a>
    </main>
<?php
    #include "../footer.php";
?>